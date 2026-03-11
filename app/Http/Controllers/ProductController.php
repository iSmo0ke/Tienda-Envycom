<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        // Traemos los productos activos, paginados de 12 en 12
        $products = Product::where('activo', true)->paginate(12);

        return view('products.index', compact('products'));
    }

    public function buscar(Request $request)
    {
        $search = $request->search;

        $products = Product::where('activo', true)
            ->where(function ($query) use ($search) {
                $query->where('nombre', 'like', '%' . $search . '%')
                      ->orWhere('descripcion_corta', 'like', '%' . $search . '%')
                      ->orWhere('marca', 'like', '%' . $search . '%');
            })
            ->paginate(12);

        return view('products.resultados', compact('products', 'search'));
    }

    public function show($id)
    {
        // Buscamos el producto por ID, si no existe lanza error 404
        $product = Product::findOrFail($id);

        // dd($product->toArray());

        return view('products.show', compact('product'));
    }
}