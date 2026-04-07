<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;


Route::get('/', function () {return view('welcome');});
Route::get('/dashboard', function (Request $request) {

    $pedidos = \App\Models\Order::where('user_id', $request->user()->id)
                                ->orderBy('created_at', 'desc')
                                ->get();
                                
    return view('dashboard', compact('pedidos'));})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/terminos-y-condiciones', function () {return view('legal.terminos');})->name('legal.terminos');
Route::get('/aviso-de-privacidad', function () {return view('legal.privacidad');})->name('legal.privacidad');
Route::get('/devoluciones', function () {return view('legal.devoluciones');})->name('legal.devoluciones');


//Productos
Route::get('/productos', [ProductController::class, 'index'])->name('products.index');
//Productos destacados en home
Route::get('/', function () {
    $productosDestacados = Product::latest()->take(18)->get();
    return view('welcome', compact('productosDestacados'));
});
// Ruta para la búsqueda de productos
Route::get('/buscar', [ProductController::class, 'buscar'])->name('productos.buscar');


//Carrito
Route::get('/carrito', function () {return view('carrito');})->name('carrito');
Route::get('/carrito', [CartController::class, 'index'])->name('carrito');
Route::post('/carrito/agregar/{id}', [CartController::class, 'add'])->name('carrito.add');
Route::post('/carrito/actualizar/{id}', [CartController::class, 'update'])->name('carrito.update');
Route::delete('/carrito/eliminar/{id}', [CartController::class, 'remove'])->name('carrito.remove');
Route::delete('/carrito/vaciar', [CartController::class, 'clear'])->name('carrito.clear');
// Ruta para ver el detalle de un producto específico
Route::get('/producto/{id}', [ProductController::class, 'show'])->name('products.show');


//Rutas protegidas
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //ruta para el checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout/procesar', [CheckoutController::class, 'process'])->name('checkout.process');

    // Ruta para ver un pedido específico
    Route::get('/mi-cuenta/pedidos/{id}', function ($id) {
        $pedido = \App\Models\Order::with('items.product')
                    ->where('id', $id)
                    ->where('user_id', \Illuminate\Support\Facades\Auth::id())
                    ->firstOrFail();

        return view('profile.pedido-detalle', compact('pedido'));
    })->name('profile.pedido.detalle');

    // Flujo de Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::get('/checkout/sepomex/search', [CheckoutController::class, 'searchSepomexByZip'])->name('checkout.sepomex.search');
    Route::post('/checkout/address', [CheckoutController::class, 'processAddress'])->name('checkout.processAddress'); // Guarda dirección
    Route::get('/checkout/payment', [CheckoutController::class, 'payment'])->name('checkout.payment'); // Muestra Openpay
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process'); // Cobra
    Route::get('/checkout/openpay/3d-secure-callback', [CheckoutController::class, 'handle3DSecureReturn'])->name('checkout.openpay.callback');

    //Perfil usuario
    Route::post('/profile/address', [\App\Http\Controllers\ProfileController::class, 'storeAddress'])->name('profile.address.store')->middleware('auth');


    // Rutas exclusivas de Administrador
    Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

        Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

        // Rutas para gestionar productos
        Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);
        Route::get('/orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
        Route::put('/orders/{order}/status', [\App\Http\Controllers\Admin\OrderController::class, 'update'])->name('orders.update');

        Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    });
});


require __DIR__ . '/auth.php';
