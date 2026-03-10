<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Product;

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

require __DIR__.'/auth.php';
