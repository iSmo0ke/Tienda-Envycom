<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'short_description' => 'nullable|string',
            'stock' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Validamos que sea imagen (máx 2MB)
        ]);

        // Lógica de subida de imagen
        $imagePath = null;
        if ($request->hasFile('image')) {
            // Se guardará en storage/app/public/products
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product = new Product();
        $product->idProducto = rand(10000000, 99999999); 
        $product->nombre = $validatedData['name'];
        $product->numParte = $validatedData['sku'];
        $product->marca = $validatedData['brand'];
        $product->precio = $validatedData['price'];
        $product->descripcion_corta = $validatedData['short_description'];
        $product->activo = true;
        $product->existencia = ['local' => $validatedData['stock'] ?? 0]; 
        $product->imagen = $imagePath; // Guardamos la ruta
        $product->source = 'local'; // Etiquetamos como producto nuestro
        
        $product->save();

        return redirect()->route('admin.products.index')
                         ->with('success', 'Producto creado exitosamente.');
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(UpdateProductRequest $request, Product $product)
  {
    $validatedData = $request->validated();

    // Imagen actual
    $imagePath = $product->imagen;

    if ($request->hasFile('image')) {
        if ($product->imagen && !str_starts_with($product->imagen, 'http')) {
            Storage::disk('public')->delete($product->imagen);
        }

        $imagePath = $request->file('image')->store('products', 'public');
      }

    $product->update([
        'nombre' => $validatedData['name'],
        'numParte' => $validatedData['sku'],
        'marca' => $validatedData['brand'],
        'precio' => $validatedData['price'],
        'descripcion_corta' => $validatedData['short_description'],
        'existencia' => ['local' => $validatedData['stock']],
        'imagen' => $imagePath,
    ]);

    return redirect()->route('admin.products.index')
                     ->with('success', 'Producto actualizado exitosamente.');
  }

    public function destroy(DestroyProductRequest $request, Product $product)
    {
        // Verificación de seguridad adicional: No permitir borrar productos de CT desde el panel manual
        if ($product->source === 'CT') {
            return back()->with('error', 'No puedes eliminar productos sincronizados de CT. Desactívalos desde el catálogo.');
        }

        $product->delete();

        return redirect()->route('admin.products.index')
                         ->with('success', 'Producto eliminado exitosamente.');
    }
}