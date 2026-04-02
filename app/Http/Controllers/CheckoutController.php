<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\PaymentProcessRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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

    // PASO 1: Mostrar la pantalla de dirección
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

    //storeAddressRequest es el nuevo FormRequest que creamos para validar la dirección
    public function storeAddress(StoreAddressRequest $request)
    {
        $validated = $request->validated();

        $postalCode = DB::table('postal_codes')->where('id', $request->address_id)->first();

        if (!$postalCode) {
            return back()->withErrors(['address_id' => 'Error técnico: El ID de la colonia no coincide con la base de datos.'])->withInput();
        }

        // 3. Guardamos la dirección en la SESIÓN o Base de Datos
        // Usamos 'put' para que la vista de pago sepa a dónde enviar el paquete
        session(['checkout_address' => [
            'receptor_name' => $request->receptor_name,
            'telefono' => $request->phone,
            'calle_numero' => $request->calle_numero,
            'colonia' => $postalCode->d_asenta,
            'municipio' => $postalCode->d_mnpio,
            'estado' => $postalCode->d_estado,
            'cp' => $postalCode->d_codigo,
            'referencias' => $request->referencias,
        ]]);

        // 4. ¡LA REDIRECCIÓN MÁGICA!
        return redirect()->route('checkout.payment');
    }

    public function processAddress(StoreAddressRequest $request)
    {
        $user = auth()->user();
        $validated = $request->validated();


        if ($request->address_id === 'new') {
            $nuevaDireccion = Address::create([
                'user_id'         => $user->id,
                'sepomex_id'      => $request->sepomex_id,
                'receptor_name'   => $validated['receptor_name'],
                'telefono'        => $validated['telefono'], 
                'calle'           => $validated['calle'],
                'numero_exterior' => $validated['numero_exterior'] ?? null,
                'numero_interior' => $validated['numero_interior'] ?? null,
                'referencias'     => $validated['refencias'] ?? null, 
                'alias'           => $validated['alias'] ?? 'Casa',
                'is_default'      => $user->addresses()->count() === 0,
            ]);

            $sepomex = DB::table('postal_codes')->where('id', $request->sepomex_id)->first();
            $direccionSnapshot = "Recibe: {$nuevaDireccion->receptor_name}. Calle: {$nuevaDireccion->calle} #{$nuevaDireccion->numero_exterior}, Col. {$sepomex->settlement}, {$sepomex->municipality}, {$sepomex->state}. CP: {$sepomex->zip_code}";
        }
        
        else {
            $direccion = Address::with('postalCode')->findOrFail($request->address_id);
            if ($direccion->user_id !== $user->id) abort(403);

            $sepomex = $direccion->postalCode;
            $direccionSnapshot = "Recibe: {$direccion->receptor_name}. Calle: {$direccion->calle} #{$direccion->numero_exterior}, Col. {$sepomex->settlement}, {$sepomex->municipality}, {$sepomex->state}. CP: {$sepomex->zip_code}";
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

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        $costoEnvio = 150.00;
        $total = $subtotal + $costoEnvio;

        return view('payment', compact('total'));
    }

    public function process(PaymentProcessRequest $request)
    {
        // MAP REAL: Usamos únicamente lo que el Request validó
        $validated = $request->validated();

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('products.index');
        }

        // Abstracción de totales
        $subtotal = $this->calculateTotal($cart);
        $costoEnvio = 150.00;
        $total = $subtotal + $costoEnvio;

        $user = Auth::user();

        try {
            // PETICIÓN A OPENPAY usando la variable protegida $validated
            $response = Http::withBasicAuth(config('services.openpay.private_key'), '')
                ->post('https://sandbox-api.openpay.mx/v1/' . config('services.openpay.merchant_id') . '/charges', [
                    'method'            => 'card',
                    'source_id'         => $validated['token_id'], // <--- AQUÍ se aplica MAP
                    'device_session_id' => $validated['device_session_id'], // <--- AQUÍ se aplica MAP
                    'amount'            => (float) $total,
                    'currency'          => 'MXN',
                    'description'       => 'Compra en Tienda ENVYCOM',
                    'customer' => [
                        'name'  => $user->name,
                        'email' => $user->email,
                    ]
                ]);

            if ($response->failed()) {
                $error = $response->json();
                return redirect()->route('checkout.payment')
                    ->with('error', 'Pago declinado: ' . ($error['description'] ?? 'Error bancario.'));
            }

            $charge = $response->json();

            return DB::transaction(function () use ($user, $subtotal, $costoEnvio, $total, $cart, $charge) {
                $direccionSnapshot = session()->get('checkout_address', 'Dirección no registrada');

                // Generar número de orden (Lógica idéntica a la tuya)
                $orderNumber = $this->generateOrderNumber();

                $order = Order::create([
                    'user_id'          => $user->id,
                    'order_number'     => $orderNumber,
                    'status'           => 'pagado',
                    'subtotal'         => $subtotal,
                    'shipping_cost'    => $costoEnvio,
                    'total'            => $total,
                    'shipping_address' => $direccionSnapshot,
                    'payment_method'   => 'openpay_card',
                    'payment_id'       => $charge['id'],
                ]);

                foreach ($cart as $item) {
                    $order->items()->create([ // Usando la relación del modelo
                        'product_id' => $item['id'],
                        'quantity'   => $item['quantity'],
                        'price'      => $item['price'],
                    ]);
                }

                Mail::to($user->email)->send(new OrderConfirmed($order));
                session()->forget(['cart', 'checkout_address']);

                return redirect()->route('pedido.confirmado')
                    ->with('success', '¡Pago exitoso! Folio: ' . $orderNumber);
            });
        } catch (\Exception $e) {
            Log::error("Error Checkout: " . $e->getMessage());
            return redirect()->route('checkout.payment')->with('error', 'Error interno: ' . $e->getMessage());
        }
    }

    private function calculateTotal($cart)
    {
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        return $subtotal;
    }

    private function generateOrderNumber()
    {
        return 'ORD-' . date('Ymd') . '-' . strtoupper(uniqid());
    }
}
