<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // 1. Filtramos categorías para ignorar nulos y textos vacíos ("")
        $categorias = \App\Models\Product::whereNotNull('categoria')
                                         ->where('categoria', '!=', '') // Ignora las que están en blanco
                                         ->where('categoria', '!=', ' ') // Ignora las que solo tienen un espacio
                                         ->where('activo', true)
                                         ->distinct()
                                         ->pluck('categoria');

        // 2. Traemos los productos usando nuestro nuevo Scope 'filtrar'
        $products = \App\Models\Product::where('activo', true)
                                        ->filtrar(request(['buscar', 'categoria', 'marca', 'ordenar']))
                                        ->paginate(12);

        return view('products.index', compact('products', 'categorias'));
    }
    
    public function show($id)
    {
        $product = Product::where('activo', true)->findOrFail($id);

        return view('products.show', compact('product'));
    }
}