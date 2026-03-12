<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;

use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmed;

class CheckoutController extends Controller
{
    public function index()
    {
        // 1. Obtener el carrito de la sesión
        $carrito = session()->get('carrito', []);

        // Si el carrito está vacío, regresamos a la tienda
        if (empty($carrito)) {
            return redirect()->route('products.index')->with('error', 'Tu carrito está vacío.');
        }

        // 2. Calcular el subtotal
        $subtotal = 0;
        foreach ($carrito as $item) {
            $subtotal += $item['precio'] * $item['cantidad'];
        }

        // 3. Definir costo de envío (puedes hacerlo dinámico después)
        $costoEnvio = 150.00;
        $total = $subtotal + $costoEnvio;

        // 4. Obtener las direcciones del usuario autenticado
        $direcciones = Auth::user()->addresses;

        return view('checkout', compact('carrito', 'subtotal', 'costoEnvio', 'total', 'direcciones'));
    }

    public function process(Request $request)
    {
        // 1. Obtener el carrito de la sesión
        $carrito = session()->get('carrito', []);
        
        if (empty($carrito)) {
            return redirect()->route('products.index')->with('error', 'Tu carrito está vacío.');
        }

        // 2. Validar qué dirección seleccionó (existente o nueva)
        $request->validate([
            'address_id' => 'required',
        ]);

        $user = Auth::user();

        // Iniciamos transacción para evitar registros huérfanos si algo falla
        DB::beginTransaction();

        try {
            // 3. Procesar la dirección y generar el Snapshot de texto
            $direccionSnapshot = '';

            if ($request->address_id === 'new') {
                // Guardar nueva dirección en la tabla addresses
                $nuevaDireccion = Address::create([
                    'user_id' => $user->id,
                    'receptor_name' => $request->receptor_name ?? $user->name,
                    'phone' => $request->phone,
                    'calle_numero' => $request->calle_numero,
                    'colonia' => $request->colonia,
                    'municipio_alcaldia' => $request->municipio_alcaldia,
                    'estado' => $request->estado,
                    'codigo_postal' => $request->codigo_postal,
                    'referencias' => $request->referencias,
                    'is_default' => Address::where('user_id', $user->id)->count() === 0 ? true : false,
                ]);

                // Crear el texto de la dirección para el historial del pedido
                $direccionSnapshot = "Recibe: {$nuevaDireccion->receptor_name}. Calle: {$nuevaDireccion->calle_numero}, Col. {$nuevaDireccion->colonia}, {$nuevaDireccion->municipio_alcaldia}, {$nuevaDireccion->estado}. CP: {$nuevaDireccion->codigo_postal}. Tel: {$nuevaDireccion->phone}. Refs: {$nuevaDireccion->referencias}";
            } else {
                // Usar dirección existente
                $direccionExistente = Address::findOrFail($request->address_id);
                
                // Seguridad: Verificar que la dirección sí pertenece al usuario
                if ($direccionExistente->user_id !== $user->id) {
                    abort(403, 'Acción no autorizada.');
                }

                $direccionSnapshot = "Recibe: {$direccionExistente->receptor_name}. Calle: {$direccionExistente->calle_numero}, Col. {$direccionExistente->colonia}, {$direccionExistente->municipio_alcaldia}, {$direccionExistente->estado}. CP: {$direccionExistente->codigo_postal}. Tel: {$direccionExistente->phone}. Refs: {$direccionExistente->referencias}";
            }

            // 4. Calcular los totales de nuevo en el backend (por seguridad)
            $subtotal = 0;
            foreach ($carrito as $item) {
                $subtotal += $item['precio'] * $item['cantidad'];
            }
            $costoEnvio = 150.00; // Esto puede venir del request (envío express vs estándar) en el futuro
            $total = $subtotal + $costoEnvio;

            // 5. Generar Folio del Pedido (Ej: ENV-2026-0001)
            $ultimoPedido = Order::latest('id')->first();
            $siguienteId = $ultimoPedido ? $ultimoPedido->id + 1 : 1;
            $orderNumber = 'ENV-' . date('Y') . '-' . str_pad($siguienteId, 4, '0', STR_PAD_LEFT);

            // 6. Guardar la Orden en la base de datos
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => $orderNumber,
                'status' => 'pendiente',
                'subtotal' => $subtotal,
                'shipping_cost' => $costoEnvio,
                'total' => $total,
                'shipping_address' => $direccionSnapshot,
                // payment_method y payment_id se quedan null por ahora
            ]);

            // 7. Guardar los Items del pedido
            foreach ($carrito as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['cantidad'],
                    'price' => $item['precio'], // El precio histórico al momento de comprar
                ]);
            }

            // Confirmamos que todo salió bien y guardamos en BD
            DB::commit();
            // Enviar correo de confirmacion

            try {
                Mail::to($user->email)->send(new OrderConfirmed($order));
            } catch (\Exception $e) {
                // Opcional: Registramos en el log si falla el correo, pero NO cancelamos la orden
                \Illuminate\Support\Facades\Log::error('Fallo al enviar correo de orden: ' . $e->getMessage());
            }

            // 8. Vaciar el carrito de sesión
            session()->forget('carrito');

            // 9. Mandamos al usuario a la página de éxito
            return redirect()->route('pedido.confirmado')->with('success', '¡Pedido creado con éxito! Tu folio es: ' . $orderNumber);

        } catch (\Exception $e) {
            // Si algo falla, cancelamos todos los registros a medias
            DB::rollBack();
            return redirect()->back()->with('error', 'Ocurrió un error al procesar el pedido: ' . $e->getMessage());
        }
    }
}