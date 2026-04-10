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
use App\Models\User;
use App\Services\WarehouseService;

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
        $estadoCliente = '';

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
            $estadoCliente = $sepomex->state;

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
            $estadoCliente = $sepomex->state ?? 'N/D';
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
        session()->put('checkout_state', $estadoCliente);

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

        // Cambiamos el nombre de la variable para englobar falta de stock o cambios de precio
        $huboCambios = false;

        foreach ($cart as $id => $item) {
            // Buscamos el producto real en la base de datos
            $productoReal = Product::find($id);

            // 1. Revisamos si no existe, si está inactivo, O si ya no hay stock
            if (!$productoReal || !$productoReal->activo || $productoReal->stock_disponible < $item['quantity']) {
                unset($cart[$id]);
                $huboCambios = true;
            } else {
                // 2. NUEVO: Validamos si el precio en la sesión es distinto al precio actual (MXN con tipo de cambio)
                if ((float) $item['price'] !== (float) $productoReal->precio) {
                    // Actualizamos el carrito con el precio más reciente
                    $cart[$id]['price'] = $productoReal->precio;
                    $huboCambios = true;
                }
            }
        }

        // Si quitamos stock o actualizamos precios cambiarios, actualizamos la sesión y regresamos al cliente con un aviso
        if ($huboCambios) {
            session()->put('cart', $cart);
            return redirect()->route('carrito')->with('error', 'Tu carrito ha sido actualizado. Algunos productos se agotaron o sufrieron ajustes por el tipo de cambio. Por favor, revisa tu nuevo total.');
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
        $request->validate([
            'token_id' => 'required',
            'device_session_id' => 'required',
        ]);

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('products.index')->with('error', 'Tu carrito esta vacio.');
        }

        $user = Auth::user();
        $estadoCliente = session()->get('checkout_state', 'N/D');
        $warehouseService = new WarehouseService();

        foreach ($cart as $item) {
            $producto = Product::find($item['id']);

            if (!$producto || !$producto->activo) return redirect()->route('carrito')->with('error', "El producto '{$item['name']}' ya no esta disponible.");
            if ((float) $producto->precio !== (float) $item['price']) {
                $cart[$item['id']]['price'] = $producto->precio;
                session()->put('cart', $cart);
                return redirect()->route('carrito')->with('error', 'El precio de algunos productos se ajustó al tipo de cambio actual. Verifica tu total.');
            }

            // LÓGICA FASE 2: VALIDACIÓN DE ALMACÉN Y STOCK EN TIEMPO REAL API
            if ($producto->source === 'CT') {
                $existenciasCT = $producto->existencia['total'] ?? [];
                
                // 1. Buscamos qué almacén nos va a surtir basado en el Estado del cliente
                $mejorAlmacen = $warehouseService->getBestWarehouse($existenciasCT, $estadoCliente, $item['quantity']);
                
                if (!$mejorAlmacen) {
                    return redirect()->route('carrito')->with('error', "El producto '{$item['name']}' no tiene suficientes piezas en un solo almacén para surtir tu pedido completo.");
                }

                // 2. Validamos el stock EN VIVO con el API de CT
                $isLiveStockOk = $warehouseService->checkLiveStock($producto->numParte, $mejorAlmacen, $item['quantity']);
                
                if (!$isLiveStockOk) {
                    // Si el API dice que no hay, lo quitamos del carrito
                    unset($cart[$item['id']]);
                    session()->put('cart', $cart);
                    return redirect()->route('carrito')->with('error', "Lo sentimos, el proveedor acaba de agotar el stock de '{$item['name']}'. Tu carrito fue actualizado.");
                }
            } else {
                // Producto Local
                if ($producto->stock_disponible < $item['quantity']) {
                    return redirect()->route('carrito')->with('error', "El producto '{$item['name']}' no tiene inventario local suficiente.");
                }
            }
        }

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $costoEnvio = $this->calculateShipping($cart);
        $total = $subtotal + $costoEnvio;

        try {
            $redirectString = route('checkout.openpay.callback');
            $response = Http::withBasicAuth(config('services.openpay.private_key'), '')
                ->post('https://sandbox-api.openpay.mx/v1/' . config('services.openpay.merchant_id') . '/charges', [
                    'method' => 'card',
                    'source_id' => $request->token_id,
                    'device_session_id' => $request->device_session_id,
                    // Aquí mandamos el total validado final en MXN
                    'amount' => (float) $total,
                    'currency' => 'MXN',
                    'description' => 'Compra en Tienda ENVYCOM',
                    'redirect_url' => $redirectString,
                    'use_3d_secure' => true,
                    'customer' => [
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                ]);

            if ($response->failed()) {
                $error = $response->json();
                Log::warning('Intento fallido de pago Openpay: ' . ($error['description'] ?? 'Desconocido') . ' | Usuario ID: ' . $user->id);
                return redirect()->route('checkout.payment')->with('error', 'Tarjeta declinada, comuníquese con su banco por favor.');
            }

            $charge = $response->json();
            $chargeStatus = $charge['status'] ?? null;
            $secureRedirectUrl = $charge['payment_method']['url'] ?? null;

            if ($chargeStatus === 'charge_pending' && $secureRedirectUrl) {
                session()->put('openpay_pending_checkout', [
                    'charge_id' => $charge['id'] ?? null,
                    'cart' => $cart,
                    'subtotal' => $subtotal,
                    'shipping_cost' => $costoEnvio,
                    'total' => $total,
                    'user_id' => $user->id,
                    'shipping_address' => session()->get('checkout_address', 'Direccion no registrada'),
                ]);

                return redirect()->away($secureRedirectUrl);
            }

            if ($chargeStatus === 'completed') {
                $order = $this->finalizeSuccessfulPayment(
                    $user->id,
                    $cart,
                    (float) $subtotal,
                    (float) $costoEnvio,
                    (float) $total,
                    $charge['id'] ?? null,
                    session()->get('checkout_address', 'Direccion no registrada')
                );

                $this->sendOrderConfirmationEmail($order, $user->id);

                session()->forget(['cart', 'checkout_address', 'openpay_pending_checkout']);
                session()->put('last_order_id', $order->id);

                return redirect()->route('pedido.confirmado')->with('success', 'Pago exitoso. Folio: ' . $order->order_number);
            }

            return redirect()->route('checkout.payment')->with('error', 'No fue posible confirmar el pago. Estado: ' . ($chargeStatus ?? 'desconocido'));
        } catch (\Throwable $e) {
            Log::critical('ERROR CRITICO EN PAGO OPENPAY: ' . $e->getMessage());
            return redirect()->route('checkout.payment')->with('error', 'Hubo un problema al iniciar el pago. Intenta nuevamente.');
        }
    }

    public function handle3DSecureReturn(Request $request)
    {
        $pendingCheckout = session()->get('openpay_pending_checkout');
        $chargeIdFromOpenpay = $request->query('id');

        if (!$pendingCheckout || empty($pendingCheckout['charge_id'])) {
            return redirect()->route('checkout.payment')->with('error', 'No encontramos una autenticacion 3D Secure pendiente.');
        }

        if ($chargeIdFromOpenpay && $chargeIdFromOpenpay !== $pendingCheckout['charge_id']) {
            Log::warning('Openpay devolvio un charge id distinto al de la sesion.', [
                'charge_id_callback' => $chargeIdFromOpenpay,
                'charge_id_session' => $pendingCheckout['charge_id'],
            ]);
        }

        $chargeId = $pendingCheckout['charge_id'];

        try {
            $response = Http::withBasicAuth(config('services.openpay.private_key'), '')
                ->get($this->openpayBaseUrl() . '/v1/' . config('services.openpay.merchant_id') . '/charges/' . $chargeId);

            if ($response->failed()) {
                $error = $response->json();
                $mensaje = $error['description'] ?? 'No pudimos validar el estado final del pago.';
                return redirect()->route('checkout.payment')->with('error', $mensaje);
            }

            $charge = $response->json();
            $chargeStatus = $charge['status'] ?? null;

            if ($chargeStatus === 'completed') {
                $order = $this->finalizeSuccessfulPayment(
                    (int) $pendingCheckout['user_id'],
                    $pendingCheckout['cart'] ?? [],
                    (float) ($pendingCheckout['subtotal'] ?? 0),
                    (float) ($pendingCheckout['shipping_cost'] ?? 0),
                    (float) ($pendingCheckout['total'] ?? 0),
                    $charge['id'] ?? $chargeId,
                    $pendingCheckout['shipping_address'] ?? 'Direccion no registrada'
                );

                $this->sendOrderConfirmationEmail($order, (int) $pendingCheckout['user_id']);

                session()->forget(['cart', 'checkout_address', 'openpay_pending_checkout']);
                session()->put('last_order_id', $order->id);

                return redirect()->route('pedido.confirmado')->with('success', 'Pago exitoso. Folio: ' . $order->order_number);
            }

            session()->forget('openpay_pending_checkout');

            $mensaje = $charge['description'] ?? 'La autenticacion 3D Secure no se completo correctamente.';
            return redirect()->route('checkout.payment')->with('error', 'Pago no completado: ' . $mensaje);
        } catch (\Throwable $e) {
            Log::critical('ERROR VALIDANDO RETORNO 3D SECURE OPENPAY: ' . $e->getMessage() . $e->getLine());
            return redirect()->route('checkout.payment')->with('error', 'No pudimos confirmar tu pago. Intenta nuevamente.');
        }
    }

    private function finalizeSuccessfulPayment(
        int $userId, array $cart, float $subtotal, float $shippingCost, float $total, ?string $chargeId, string $shippingAddress
    ): Order {
        return DB::transaction(function () use ($userId, $cart, $subtotal, $shippingCost, $total, $chargeId, $shippingAddress) {
            if (empty($cart)) throw new \RuntimeException('No hay carrito para finalizar el pago.');

            $estadoCliente = session()->get('checkout_state', 'N/D');
            $warehouseService = new WarehouseService();

            $order = Order::create([
                'user_id' => $userId,
                'order_number' => $this->generateOrderNumber(),
                'status' => 'en_proceso',
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'total' => $total,
                'shipping_address' => $shippingAddress,
                'payment_method' => 'openpay_card',
                'payment_id' => $chargeId,
            ]);

            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);

                $product = Product::find($item['id']);
                if ($product->source === 'local') continue; 

                // Descuento Fase 2: Exacto al almacén enrutado
                $actualStock = $product->existencia; 
                if (isset($actualStock['total']) && is_array($actualStock['total'])) {
                    
                    $mejorAlmacen = $warehouseService->getBestWarehouse($actualStock['total'], $estadoCliente, $item['quantity']);
                    
                    if ($mejorAlmacen) {
                        // Le restamos la cantidad comprada a ese almacén específico
                        $actualStock['total'][$mejorAlmacen] -= $item['quantity'];
                        $product->existencia = $actualStock;
                        $product->save();
                    }
                }
            }

            return $order;
        });
    }

    private function sendOrderConfirmationEmail(Order $order, int $userId): void
    {
        $user = User::find($userId);
        if (!$user || !$user->email) {
            return;
        }

        try {
            Mail::to($user->email)->send(new OrderConfirmed($order));
        } catch (\Throwable $e) {
            Log::error('Fallo envio de correo de confirmacion: ' . $e->getMessage());
        }
    }

    private function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'ENV-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(3)));
        } while (Order::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    private function openpayBaseUrl(): string
    {
        return config('services.openpay.production')
            ? 'https://api.openpay.mx'
            : 'https://sandbox-api.openpay.mx';
    }
}