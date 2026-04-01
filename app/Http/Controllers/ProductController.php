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
        $search = $request->validated()['q'];

        if (empty($search)) {
           return redirect()->route('products.index');
    }

        $products = Product::where('activo', true)
           ->where(function ($query) use ($search) {
            $query->where('nombre', 'like', "%{$search}%")
               ->orWhere('numParte', 'like', "%{$search}%")
                ->orWhere('marca', 'like', "%{$search}%")
                ->orWhere('descripcion_corta', 'like', "%{$search}%");
       })
            ->paginate(20);

          $products->appends(['q' => $search]);

          return view('products.resultados', compact('products', 'search'));

        return view('products.resultados', compact('productos', 'query'));
        $products->appends(['search' => $search]);

        return view('products.resultados', compact('products', 'search'));
    }

    public function show($id)
    {
        // Buscamos el producto pero aseguramos que esté activo para el cliente
        $product = Product::where('activo', true)->findOrFail($id);

        return view('products.show', compact('product'));
    }
}