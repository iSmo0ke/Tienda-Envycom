<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

use App\Exceptions\StockInsuficienteException;

use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmed;
use Illuminate\Support\Facades\Http;

class CheckoutController extends Controller
{
    private function calculateShipping($cart)
    {
        if (empty($cart)) return 0;

        $hasCTProducts = false;
        $hasLocalProducts = false;

        foreach ($cart as $item) {
            // Buscamos el producto para verificar su 'source'
            $producto = Product::find($item['id']);
            
            // Si el source es 'ct' o viene de la sync de CT
            if ($producto && $producto->source === 'ct') {
                $hasCTProducts = true;
            } else {
                // Si no tiene source o es local (manual)
                $hasLocalProducts = true;
            }
        }

        // Prioridad 1: Si hay productos locales, aplicamos la regla de Alonso ($250)
        if ($hasLocalProducts) {
            return 250.00;
        }

        // Prioridad 2: Si solo son de CT, aplicamos tarifa plana de contingencia
        if ($hasCTProducts) {
            return 200.00; 
        }

        return 150.00; // Backup por si acaso
    }
    
    public function searchSepomexByZip(Request $request)
    {
        $validated = $request->validate([
            'zip_code' => 'required|digits:5',
        ]);

        $results = DB::table('postal_codes')
            ->select('id', 'zip_code', 'settlement', 'settlement_type', 'municipality', 'state', 'city')
            ->where('zip_code', $validated['zip_code'])
            ->orderBy('settlement')
            ->limit(100)
            ->get();

        return response()->json([
            'results' => $results,
        ]);
    }

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

        $costoEnvio = $this->calculateShipping($cart);
        $total = $subtotal + $costoEnvio;
        
        $direcciones = Auth::user()->addresses()->with('postalCode')->get();

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

            $validated = $request->validate([
                'alias' => 'nullable|string|max:150',
                'receptor_name' => 'required|string|max:150',
                'telefono' => 'required|string|max:30',
                'calle' => 'required|string|max:255',
                'numero_exterior' => 'nullable|string|max:50',
                'numero_interior' => 'nullable|string|max:50',
                'referencias' => 'nullable|string|max:1000',
                'zip_code' => 'required|digits:5',
                'sepomex_id' => 'required|uuid|exists:postal_codes,id',
            ]);

            $sepomex = DB::table('postal_codes')
                ->where('id', $validated['sepomex_id'])
                ->first();

            if (!$sepomex || $sepomex->zip_code !== $validated['zip_code']) {
                return redirect()->back()
                    ->withErrors(['sepomex_id' => 'Selecciona una opción válida del catálogo SEPOMEX para ese código postal.'])
                    ->withInput();
            }

            $nuevaDireccion = Address::create([
                'user_id' => $user->id,
                'sepomex_id' => $sepomex->id,
                'calle' => $validated['calle'],
                'alias' => $validated['alias'] ?? null,
                'is_default' => Address::where('user_id', $user->id)->count() === 0,
                'numero_exterior' => $validated['numero_exterior'] ?? null,
                'receptor_name' => $validated['receptor_name'] ?? $user->name,
                'numero_interior' => $validated['numero_interior'] ?? null,
                'referencias' => $validated['referencias'] ?? null,
                'telefono' => $validated['telefono'],
            ]);

             $numeroExterior = $nuevaDireccion->numero_exterior ? ' ' . $nuevaDireccion->numero_exterior : '';
            $numeroInterior = $nuevaDireccion->numero_interior ? ' Int. ' . $nuevaDireccion->numero_interior : '';
            $referencias = $nuevaDireccion->referencias ?: 'Sin referencias';

            $direccionSnapshot = "Recibe: 
            {$nuevaDireccion->receptor_name}. 
            Calle: {$nuevaDireccion->calle}{$numeroExterior}{$numeroInterior}, 
            Col. {$sepomex->settlement}, {$sepomex->municipality}, {$sepomex->state}. 
            CP: {$sepomex->zip_code}. Tel: {$nuevaDireccion->telefono}. 
            Refs: {$referencias}";
        } else {
            $direccionExistente = Address::with('postalCode')->findOrFail($request->address_id);
            if ($direccionExistente->user_id !== $user->id) {
                abort(403, 'Acción no autorizada.');
            }
            $sepomex = $direccionExistente->postalCode;
            $numeroExterior = $direccionExistente->numero_exterior ? ' ' . $direccionExistente->numero_exterior : '';
            $numeroInterior = $direccionExistente->numero_interior ? ' Int. ' . $direccionExistente->numero_interior : '';
            $zipCode = $sepomex->zip_code ?? 'N/D';
            $settlement = $sepomex->settlement ?? 'N/D';
            $municipality = $sepomex->municipality ?? 'N/D';
            $state = $sepomex->state ?? 'N/D';
            $telefono = $direccionExistente->telefono ?? 'N/D';
            $referencias = $direccionExistente->referencias ?: 'Sin referencias';

            $direccionSnapshot = "Recibe: 
            {$direccionExistente->receptor_name}. 
            Calle: {$direccionExistente->calle}{$numeroExterior}{$numeroInterior}, 
            Col. {$settlement}, {$municipality}, {$state}. 
            CP: {$zipCode}. 
            Tel: {$telefono}. 
            Refs: {$referencias}";
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
        $costoEnvio = $this->calculateShipping($cart);
        $total = $subtotal + $costoEnvio;

        return view('payment', compact('total'));
    }

    public function process(Request $request)
{
    // 1. Validaciones de Input de Openpay
    $request->validate([
        'token_id' => 'required',
        'device_session_id' => 'required'
    ]);

    $cart = session()->get('cart', []);
    if (empty($cart)) {
        return redirect()->route('products.index')->with('error', 'Tu carrito está vacío.');
    }

    $user = Auth::user();

    // 2. ESCUDO DE SEGURIDAD (Validación PRE-PAGO)
    foreach ($cart as $item) {
        $producto = Product::find($item['id']);
        
        // A) Validar Existencia del Producto
        if (!$producto || !$producto->activo) {
            return redirect()->route('carrito')->with('error', "El producto '{$item['name']}' ya no está disponible.");
        }

        // B) Validar Precio (Anti-Fraude)
        if ((float)$producto->precio !== (float)$item['price']) {
            // Sincronizamos el precio en la sesión para que el usuario vea el cambio al volver
            $cart[$item['id']]['price'] = $producto->precio;
            session()->put('cart', $cart);
            
            Log::alert("CAMBIO DE PRECIO DETECTADO: Usuario ID {$user->id} intentó pagar precio viejo.");
            return redirect()->route('carrito')->with('error', "Los precios de algunos productos han cambiado. Por favor, verifica tu total.");
        }

        // C) Validar Stock (Manejo de JSON o Entero)
        $stockData = json_decode($producto->existencia, true);
        $stockReal = is_array($stockData) ? array_sum($stockData) : (int)$producto->existencia;

        if ($stockReal < (int)$item['quantity']) {
            Log::warning("STOCK INSUFICIENTE: Usuario ID {$user->id} para el producto {$producto->nombre}.");
            return redirect()->route('carrito')->with('error', "Lo sentimos, el producto '{$producto->nombre}' ya no tiene stock suficiente.");
        }
    }

    // 3. Cálculos de Montos
    $subtotal = 0;
    foreach ($cart as $item) { 
        $subtotal += $item['price'] * $item['quantity']; 
    }
    $costoEnvio = $this->calculateShipping($cart);
    $total = $subtotal + $costoEnvio;


    $ultimoPedido = Order::latest('id')->first();
    $siguienteId = $ultimoPedido ? $ultimoPedido->id + 1 : 1;
    $orderNumber = 'ENV-' . date('Y') . '-' . str_pad($siguienteId, 4, '0', STR_PAD_LEFT);

    // 4. EJECUCIÓN DEL COBRO (Openpay)
        try {
            // Cobro con el total dinámico
            $response = Http::withBasicAuth(config('services.openpay.private_key'), '')
                ->post('https://sandbox-api.openpay.mx/v1/' . config('services.openpay.merchant_id') . '/charges', [
                    'method' => 'card',
                    'source_id' => $request->token_id,
                    'device_session_id' => $request->device_session_id,
                    'amount' => (float) $total,
                    'currency' => 'MXN',
                    'description' => 'Compra en Tienda ENVYCOM',
                    'redirect_url' => route('pedido.confirmado')->with('success', '¡Pago exitoso! Tu folio es: ' . $orderNumber),
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

        // 5. REGISTRO DE ORDEN (Transacción Atómica)
        DB::beginTransaction();

        $direccionSnapshot = session()->get('checkout_address', 'Dirección no registrada');
        /*$ultimoPedido = Order::latest('id')->first();
        $siguienteId = $ultimoPedido ? $ultimoPedido->id + 1 : 1;
        $orderNumber = 'ENV-' . date('Y') . '-' . str_pad($siguienteId, 4, '0', STR_PAD_LEFT);
        */
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

            // ACTUALIZACIÓN DE STOCK (Respetando JSON o Int)
            $p = Product::find($item['id']);
            $actualStock = json_decode($p->existencia, true);
            
            if (is_array($actualStock)) {
                // Restamos de la primera bodega que tenga stock (llave del JSON)
                $key = array_key_first($actualStock);
                $actualStock[$key] -= $item['quantity'];
                $p->existencia = json_encode($actualStock);
            } else {
                $p->existencia = (int)$p->existencia - $item['quantity'];
            }
            $p->save();
        }

        DB::commit();

        // 6. NOTIFICACIÓN Y LIMPIEZA
        try {
            Mail::to($user->email)->send(new OrderConfirmed($order));
        } catch (\Exception $e) {
            Log::error('Fallo envío de correo: ' . $e->getMessage());
        }

        session()->forget(['cart', 'checkout_address']);

        return redirect()->route('pedido.confirmado')->with('success', '¡Pago exitoso! Folio: ' . $orderNumber);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::critical("ERROR CRÍTICO POST-PAGO: " . $e->getMessage());
        return redirect()->route('products.index')->with('error', 'Hubo un problema al registrar tu pedido, pero el pago se realizó. ID Pago: ' . ($charge['id'] ?? 'N/A'));
    }
}
}