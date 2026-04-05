<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::where('source', 'local')
                           ->orderBy('created_at', 'desc')
                           ->paginate(15);
        
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = \App\Models\Product::whereNotNull('categoria')->distinct()->pluck('categoria');
        return view('admin.products.create', compact('categories'));
    }

public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'short_description' => 'nullable|string',
            'stock' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'spec_labels' => 'nullable|array',
            'spec_labels.*' => 'nullable|string|max:255',
            'spec_values' => 'nullable|array',
            'spec_values.*' => 'nullable|string|max:255',
        ]);

        // Lógica de subida de imagen
        $imagePath = null;
        if ($request->hasFile('image')) {
            // Se guardará en storage/app/public/products
            $imagePath = $request->file('image')->store('products', 'public');
        }
        $specifications = [];
        if (!empty($request->spec_labels) && !empty($request->spec_values)) {
            foreach ($request->spec_labels as $index => $label) {
                // Solo si ambas tienen texto, las agregamos al arreglo
                if (!empty($label) && !empty($request->spec_values[$index])) {
                    $specifications[] = [
                        'label' => $label,
                        'value' => $request->spec_values[$index]
                    ];
                }
            }
        }

        $product = new Product();
        $product->idProducto = rand(10000000, 99999999); 
        $product->nombre = $validatedData['name'];
        $product->numParte = $validatedData['sku'];
        $product->modelo = $validatedData['model'] ?? null;
        $product->marca = $validatedData['brand'];
        $product->categoria = $validatedData['category'] ?? null;
        $product->precio = $validatedData['price'];
        $product->descripcion_corta = $validatedData['short_description'];
        $product->activo = true;
        $product->existencia = ['local' => $validatedData['stock'] ?? 0]; 
        $product->especificaciones = $specifications;
        $product->imagen = $imagePath;
        $product->source = 'local'; 
        $product->save();

        return redirect()->route('admin.products.index')
                         ->with('success', 'Producto creado exitosamente.');
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        // 1. Validamos los datos de entrada
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'short_description' => 'nullable|string',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Validación de imagen
        ]);

        // 2. Manejo de la imagen
        // Conservamos la ruta de la imagen actual por defecto
        $imagePath = $product->imagen; 

        if ($request->hasFile('image')) {
            // Si el producto ya tenía una imagen local, la borramos del disco para ahorrar espacio
            if ($product->imagen && !str_starts_with($product->imagen, 'http')) {
                Storage::disk('public')->delete($product->imagen);
            }
            
            // Subimos la nueva imagen
            $imagePath = $request->file('image')->store('products', 'public');
        }

        // 3. Recuperamos el arreglo JSON de existencia y lo modificamos
        $existencia = $product->existencia ?? [];
        $existencia['local'] = $validatedData['stock'];

        // 4. Actualizamos el producto en la base de datos
        $product->update([
            'nombre' => $validatedData['name'],
            'numParte' => $validatedData['sku'],
            'marca' => $validatedData['brand'],
            'precio' => $validatedData['price'],
            'descripcion_corta' => $validatedData['short_description'],
            'existencia' => $existencia,
            'imagen' => $imagePath,
        ]);

        // 5. Redireccionamos con mensaje de éxito
        return redirect()->route('admin.products.index')
                         ->with('success', 'Producto actualizado exitosamente.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')
                         ->with('success', 'Producto eliminado exitosamente.');
    }
}