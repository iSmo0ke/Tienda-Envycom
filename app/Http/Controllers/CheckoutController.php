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

    public function processAddress(StoreAddressRequest $request)
    {
        $user = Auth::user();

        if ($request->address_id === 'new') {

            $validated = $request->validated();

            $nuevaDireccion = Address::create([
                'user_id' => $user->id,
                'receptor_name' => $validated['receptor_name'],
                'phone' => $validated['phone'],
                'calle_numero' => $validated['calle_numero'],
                'colonia' => $validated['colonia'],
                'municipio_alcaldia' => $validated['municipio_alcaldia'],
                'estado' => $validated['estado'],
                'codigo_postal' => $validated['codigo_postal'],
                'referencias' => $validated['referencias'] ?? null,
            ]);

            $direccionSnapshot = "Recibe: {$nuevaDireccion->receptor_name}. Calle: {$nuevaDireccion->calle_numero}, Col. {$nuevaDireccion->colonia}, {$nuevaDireccion->municipio_alcaldia}, {$nuevaDireccion->estado}, CP: {$nuevaDireccion->codigo_postal}";
        } else {

            $direccionExistente = Address::findOrFail($request->address_id);

            //Seguridad: evitar que use direcciones de otros usuarios
=======
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

            $direccionSnapshot = "Recibe: {$nuevaDireccion->receptor_name}. Calle: {$nuevaDireccion->calle}{$numeroExterior}{$numeroInterior}, Col. {$sepomex->settlement}, {$sepomex->municipality}, {$sepomex->state}. CP: {$sepomex->zip_code}. Tel: {$nuevaDireccion->telefono}. Refs: {$referencias}";
        } else {
            $direccionExistente = Address::with('postalCode')->findOrFail($request->address_id);
            if ($direccionExistente->user_id !== $user->id) {
                abort(403);
            }
            $direccionSnapshot = "Recibe: {$direccionExistente->receptor_name}. Calle: {$direccionExistente->calle_numero}, Col. {$direccionExistente->colonia}, {$direccionExistente->municipio_alcaldia}, {$direccionExistente->estado}, CP: {$direccionExistente->codigo_postal}";
=======
            $sepomex = $direccionExistente->postalCode;
            $numeroExterior = $direccionExistente->numero_exterior ? ' ' . $direccionExistente->numero_exterior : '';
            $numeroInterior = $direccionExistente->numero_interior ? ' Int. ' . $direccionExistente->numero_interior : '';
            $zipCode = $sepomex->zip_code ?? 'N/D';
            $settlement = $sepomex->settlement ?? 'N/D';
            $municipality = $sepomex->municipality ?? 'N/D';
            $state = $sepomex->state ?? 'N/D';
            $telefono = $direccionExistente->telefono ?? 'N/D';
            $referencias = $direccionExistente->referencias ?: 'Sin referencias';

            $direccionSnapshot = "Recibe: {$direccionExistente->receptor_name}. Calle: {$direccionExistente->calle}{$numeroExterior}{$numeroInterior}, Col. {$settlement}, {$municipality}, {$state}. CP: {$zipCode}. Tel: {$telefono}. Refs: {$referencias}";
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
