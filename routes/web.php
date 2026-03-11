<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;


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

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
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


require __DIR__.'/auth.php';
