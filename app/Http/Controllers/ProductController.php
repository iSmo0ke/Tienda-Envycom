<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Requests\ProductSearchRequest;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::where('activo', true)->paginate(12);

        return view('products.index', compact('products'));
    }

    public function buscar(ProductSearchRequest $request)
    {
        $query = $request->only(['q'])['q'];

        $productos = Product::where('activo', true)
            ->where(function ($q) use ($query) {
                $q->where('nombre', 'LIKE', "%{$query}%")
                    ->orWhere('numParte', 'LIKE', "%{$query}%")
                    ->orWhere('marca', 'LIKE', "%{$query}%");
            })
            ->paginate(20);

        return view('products.resultados', compact('productos', 'query'));
    }

    public function show($id)
    {
        // Buscamos el producto pero aseguramos que esté activo para el cliente
        $product = Product::where('activo', true)->findOrFail($id);

        return view('products.show', compact('product'));
    }
}