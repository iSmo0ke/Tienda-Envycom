<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Http\Requests\Admin\DestroyProductRequest;

class ProductController extends Controller
{
    public function index()
    {
        // Traemos solo los productos locales (puedes filtrarlos si agregas una columna 'source' después, 
        // por ahora traemos los más recientes)
        $products = Product::where('source', 'local')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(UpdateProductRequest $request)
    {
        // MAP: Solo permitimos campos del formulario
        $data = $request->only([
            'nombre',
            'numParte',
            'precio',
            'marca',
            'categoria',
            'modelo',
            'descripcion_corta'
        ]);

        Product::create(array_merge($data, [
            'source' => 'LOCAL',
            'activo' => $request->has('activo'),
            'existencia' => json_encode(['total' => 0]),
        ]));

        return redirect()->route('admin.products.index')->with('success', 'Producto creado.');
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        // MAP: Impedimos que cambien el 'source' o 'numParte' si es de CT
        $data = $request->only(['nombre', 'precio', 'marca', 'categoria', 'descripcion_corta']);

        $data['activo'] = $request->has('activo');

        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Producto actualizado.');
    }

    public function destroy(DestroyProductRequest $request, Product $product)
    {
        // Verificación de seguridad adicional: No permitir borrar productos de CT desde el panel manual
        if ($product->source === 'CT') {
            return back()->with('error', 'No puedes eliminar productos sincronizados de CT. Desactívalos desde el catálogo.');
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Producto eliminado correctamente.');
    }
}
