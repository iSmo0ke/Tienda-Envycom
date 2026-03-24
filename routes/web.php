<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;

Route::get('/', function () {
    return view('welcome');
});



Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

//Productos de home
Route::get('/', function () {
    // Traemos los últimos 18 productos para tener 3 "páginas" en el carrusel (6 por página)
    $productosDestacados = Product::latest()->take(18)->get();

    return view('welcome', compact('productosDestacados'));
    // Nota: cambia 'welcome' por el nombre real de tu vista si se llama diferente
});

// Ruta para la búsqueda de productos
Route::get('/buscar', function (Request $request) {
    // 1. Capturamos lo que el usuario escribió
    $search = $request->input('search');

    // 2. Si hay algo escrito, buscamos en la base de datos. Si no, traemos todo (o nada, según prefieras).
    if ($search) {
        // Busca productos donde el nombre contenga la palabra escrita
        $products = Product::where('nombre', 'LIKE', "%{$search}%")
            ->orWhere('marca', 'LIKE', "%{$search}%") // Opcional: buscar también por marca
            ->get();
    } else {
        // Si entran a /buscar sin escribir nada, podemos mostrar los últimos 12 productos
        $products = Product::latest()->take(12)->get();
    }

    // 3. Enviamos los resultados a la vista
    return view('products.resultados', compact('search', 'products'));
})->name('productos.buscar');

// Ruta para el carrito (para evitar el siguiente error)
Route::get('/carrito', function () {
    return view('carrito');
})->name('carrito');

Route::get('/productos', [ProductController::class, 'index'])->name('products.index');
Route::get('/buscar', [ProductController::class, 'buscar'])->name('productos.buscar');


Route::get('/carrito', [CartController::class, 'index'])->name('carrito');
Route::post('/carrito/agregar/{id}', [CartController::class, 'add'])->name('carrito.add');
Route::post('/carrito/actualizar/{id}', [CartController::class, 'update'])->name('carrito.update');
Route::delete('/carrito/eliminar/{id}', [CartController::class, 'remove'])->name('carrito.remove');
Route::delete('/carrito/vaciar', [CartController::class, 'clear'])->name('carrito.clear');

// Ruta para ver el detalle de un producto específico
Route::get('/producto/{id}', [ProductController::class, 'show'])->name('products.show');

Route::get('/terminos-y-condiciones', function () {
    return view('legal.terminos');
})->name('legal.terminos');

Route::get('/aviso-de-privacidad', function () {
    return view('legal.privacidad');
})->name('legal.privacidad');

//Rutas protegidas
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //ruta para el checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout/procesar', [CheckoutController::class, 'process'])->name('checkout.process');


    Route::get('/pedido-confirmado', function () {
        // Buscamos el último pedido registrado de este usuario
        // $order = \App\Models\Order::where('user_id', auth()->id())->latest()->first();
        // $order = \App\Models\Order::where('user_id', auth()->user()->id)->latest()->first();
        $order = \App\Models\Order::where('user_id', \Illuminate\Support\Facades\Auth::id())->latest()->first();

        // Si por alguna razón alguien entra a esta URL directo sin haber comprado, lo mandamos al inicio
        if (!$order) {
            return redirect()->route('products.index');
        }

        // Le pasamos el pedido a la vista
        return view('pedido-confirmado', compact('order'));
    })->name('pedido.confirmado');

    Route::get('/mi-cuenta/pedidos', function () {
        // Consultamos directamente el modelo Order usando el ID del usuario logueado
        $pedidos = \App\Models\Order::with('items.product')
                    ->where('user_id', \Illuminate\Support\Facades\Auth::id())
                    ->orderBy('created_at', 'desc')
                    ->get();

        return view('profile.pedidos', compact('pedidos'));
    })->name('profile.pedidos');

    // Ruta para ver un pedido específico
    Route::get('/mi-cuenta/pedidos/{id}', function ($id) {
        // Buscamos el pedido asegurándonos de que le pertenezca al usuario logueado (¡Seguridad primero!)
        $pedido = \App\Models\Order::with('items.product')
                    ->where('id', $id)
                    ->where('user_id', \Illuminate\Support\Facades\Auth::id())
                    ->firstOrFail(); // Si alguien intenta poner un ID que no es suyo, dará error 404

        return view('profile.pedido-detalle', compact('pedido'));
    })->name('profile.pedido.detalle');

    // Flujo de Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/address', [CheckoutController::class, 'processAddress'])->name('checkout.processAddress'); // Guarda dirección
    Route::get('/checkout/payment', [CheckoutController::class, 'payment'])->name('checkout.payment'); // Muestra Openpay
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process'); // Cobra




    // Rutas exclusivas de Administrador
    Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

        // NUEVO: Ahora /admin carga la vista del dashboard de administrador
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
