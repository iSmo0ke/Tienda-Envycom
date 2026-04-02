<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::where('activo', true)->paginate(12);

        return view('products.index', compact('products'));
    }

    public function buscar(Request $request)
    {
        $search = $request->search;

        if (empty($search)) {
            return redirect()->url('/productos'); 
        }

        $products = Product::where('activo', true)
            ->where(function ($query) use ($search) {
                $query->where('nombre', 'like', '%' . $search . '%')
                      ->orWhere('descripcion_corta', 'like', '%' . $search . '%')
                      ->orWhere('marca', 'like', '%' . $search . '%');
            })
            ->paginate(12);

        $products->appends(['search' => $search]);

        return view('products.resultados', compact('products', 'search'));
    }

    public function show($id)
    {
        $product = Product::where('activo', true)->findOrFail($id);

        return view('products.show', compact('product'));
    }
}