<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmed;
use Illuminate\Support\Facades\Http;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('products.index')->with('error', 'Tu carrito está vacío.');
        }

        $subtotal = 0;
        foreach ($cart as $item) { 
            $subtotal += $item['price'] * $item['quantity']; 
        }

        $costoEnvio = 150.00;
        $total = $subtotal + $costoEnvio;
        $direcciones = Auth::user()->addresses;

        return view('checkout', compact('cart', 'subtotal', 'costoEnvio', 'total', 'direcciones'));
    }

    public function processAddress(Request $request)
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('products.index')->with('error', 'Tu carrito está vacío.');
        }

        $request->validate([
            'address_id' => 'required',
        ]);

        $user = Auth::user();
        $direccionSnapshot = '';

        if ($request->address_id === 'new') {
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

            $direccionSnapshot = "Recibe: {$nuevaDireccion->receptor_name}. Calle: {$nuevaDireccion->calle_numero}, Col. {$nuevaDireccion->colonia}, {$nuevaDireccion->municipio_alcaldia}, {$nuevaDireccion->estado}. CP: {$nuevaDireccion->codigo_postal}. Tel: {$nuevaDireccion->phone}. Refs: {$nuevaDireccion->referencias}";
        } else {
            $direccionExistente = Address::findOrFail($request->address_id);
            if ($direccionExistente->user_id !== $user->id) {
                abort(403, 'Acción no autorizada.');
            }
            $direccionSnapshot = "Recibe: {$direccionExistente->receptor_name}. Calle: {$direccionExistente->calle_numero}, Col. {$direccionExistente->colonia}, {$direccionExistente->municipio_alcaldia}, {$direccionExistente->estado}. CP: {$direccionExistente->codigo_postal}. Tel: {$direccionExistente->phone}. Refs: {$direccionExistente->referencias}";
        }

        session()->put('checkout_address', $direccionSnapshot);

        return redirect()->route('checkout.payment');
    }

    public function payment()
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('products.index');
        }

        if (!session()->has('checkout_address')) {
            return redirect()->route('checkout.index')->with('error', 'Por favor selecciona una dirección de envío.');
        }

        $productosRemovidos = false;

        foreach ($cart as $id => $item) {
            // Buscamos el producto real en la base de datos
            $productoReal = Product::find($id);

            // Si el producto ya no existe o fue desactivado (activo = 0)
            if (!$productoReal || !$productoReal->activo) {
                unset($cart[$id]); // Lo sacamos del carrito
                $productosRemovidos = true;
            }
        }

        // Si quitamos algo, actualizamos la sesión y regresamos al cliente al carrito con un aviso
        if ($productosRemovidos) {
            session()->put('cart', $cart);
            return redirect()->route('carrito')->with('error', 'Lo sentimos, algunos productos de tu carrito se acaban de agotar y fueron removidos. Por favor, revisa tu pedido.');
        }

        $subtotal = 0;
        foreach ($cart as $item) { 
            $subtotal += $item['price'] * $item['quantity']; 
        }
        $costoEnvio = 150.00;
        $total = $subtotal + $costoEnvio;

        return view('payment', compact('total'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'token_id' => 'required',
            'device_session_id' => 'required'
        ]);
        $request->validate(['token_id' => 'required']);

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('products.index');
        }

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        $costoEnvio = 150.00;
        $total = $subtotal + $costoEnvio;

        $user = Auth::user();

        try {
            $response = Http::withBasicAuth(config('services.openpay.private_key'), '')
                ->post('https://sandbox-api.openpay.mx/v1/' . config('services.openpay.merchant_id') . '/charges', [
                    'method' => 'card',
                    'source_id' => $request->token_id,
                    'device_session_id' => $request->device_session_id,
                    'amount' => (float) $total,
                    'currency' => 'MXN',
                    'description' => 'Compra en Tienda ENVYCOM',
                    'customer' => [
                        'name' => $user->name,
                        'email' => $user->email,
                    ]
                ]);

            if ($response->failed()) {
                $error = $response->json();
                $mensaje = $error['description'] ?? 'El banco rechazó la transacción.';
                return redirect()->route('checkout.payment')->with('error', 'Pago declinado: ' . $mensaje);
            }

            $charge = $response->json();

            DB::beginTransaction();

            $direccionSnapshot = session()->get('checkout_address', 'Dirección no registrada');

            $ultimoPedido = Order::latest('id')->first();
            $siguienteId = $ultimoPedido ? $ultimoPedido->id + 1 : 1;
            $orderNumber = 'ENV-' . date('Y') . '-' . str_pad($siguienteId, 4, '0', STR_PAD_LEFT);

            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => $orderNumber,
                'status' => 'en_proceso',
                'subtotal' => $subtotal,
                'shipping_cost' => $costoEnvio,
                'total' => $total,
                'shipping_address' => $direccionSnapshot,
                'payment_method' => 'openpay_card',
                'payment_id' => $charge['id'],
            ]);

            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }

            DB::commit();

            try {
                Mail::to($user->email)->send(new OrderConfirmed($order));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Fallo correo: ' . $e->getMessage());
            }

            session()->forget(['cart', 'checkout_address']);

            return redirect()->route('pedido.confirmado')->with('success', '¡Pago exitoso! Tu folio es: ' . $orderNumber);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('checkout.payment')->with('error', 'Error interno: ' . $e->getMessage());
        }
    }
}