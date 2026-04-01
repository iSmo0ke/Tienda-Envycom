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
<<<<<<< Updated upstream

=======
use App\Exceptions\StockInsuficienteException;
>>>>>>> Stashed changes
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmed;
use Illuminate\Support\Facades\Http;

class CheckoutController extends Controller
{
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

        $costoEnvio = 150.00;
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

<<<<<<< Updated upstream
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
=======
        foreach ($carrito as $item) {
            $producto = Product::find($item['id']);
            $stockData = json_decode($producto->existencia, true);
            $stockReal = is_array($stockData) ? array_sum($stockData) : (int)$stockData;
            if (!$producto || (int)$stockReal < (int)$item['cantidad']) {
                throw new StockInsuficienteException();
            }

            if ((float)$producto->precio !== (float)$item['precio']) {
                Log::alert("INTENTO DE FRAUDE: Usuario ID {$user->id} intentó alterar precio. Producto: {$producto->nombre}.");
                return redirect()->route('carrito.index')->with('error', 'Los precios de algunos productos han cambiado. Por favor, verifica tu carrito.');
            }
        }

        // Iniciamos transacción para evitar registros huérfanos si algo falla
        DB::beginTransaction();

        try {
            // 3. Procesar la dirección y generar el Snapshot de texto
            $direccionSnapshot = '';

            if ($request->address_id === 'new') {
                $receptiorLimpio = strip_tags($request->receptor_name ?? $user->name);
                $calleNumeroLimpio = strip_tags($request->calle_numero);
                $coloniaLimpio = strip_tags($request->colonia);
                $refsLimpio = strip_tags($request->referencias);

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
>>>>>>> Stashed changes
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

                $p = Product::find($item['id']);

                $producto = Product::find($item['id']);
                $stockData = json_decode($producto->existencia, true);
                $stockActual = is_array($stockData) ? array_sum($stockData) : (int)$stockData;
                $nuevaExistencia = (int)$stockActual - (int)$item['cantidad'];
                $producto->update(['existencia' => $nuevaExistencia]);
            }

            DB::commit();

            try {
                Mail::to($user->email)->send(new OrderConfirmed($order));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Fallo correo: ' . $e->getMessage());
            }

            session()->forget(['cart', 'checkout_address']);

<<<<<<< Updated upstream
            return redirect()->route('pedido.confirmado')->with('success', '¡Pago exitoso! Tu folio es: ' . $orderNumber);
=======
            if ($request->expectsJson() || $request->is('api/*')) {
                return $this->successResponse([
                    'order_number' => $orderNumber,
                    'total' => $total,
                ], '¡Pedido creado con éxito!', 201);
            }

            // 9. Mandamos al usuario a la página de éxito
            return redirect()->route('pedido.confirmado')->with('success', '¡Pedido creado con éxito! Tu folio es: ' . $orderNumber);
>>>>>>> Stashed changes

        } catch (\Exception $e) {
            DB::rollBack();
<<<<<<< Updated upstream
            return redirect()->route('checkout.payment')->with('error', 'Error interno: ' . $e->getMessage());
=======
            Log::critical("FALLO DE INTEGRIDAD EN DB: " . $e->getMessage(), ['user_id' => $user->id]);
            return redirect()->back()->with('error', 'Error de integridad de datos. La operación ha sido bloqueada por seguridad.');

                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Ocurrió un error al procesar el pedido: ' . $e->getMessage(),
                        'code' => 500
                    ], 500);
                }
                
            return redirect()->back()->with('error', 'Ocurrió un error al procesar el pedido: ' . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error general en Checkout: ' . $e->getMessage());
            
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
            }
                
            return redirect()->back()->with('error', 'Ocurrió un problema al procesar el pedido: ' . $e->getMessage());
>>>>>>> Stashed changes
        }
    }
}