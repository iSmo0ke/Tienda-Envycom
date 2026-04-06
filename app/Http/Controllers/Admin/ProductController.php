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
        $categories = \App\Models\Product::whereNotNull('categoria')->distinct()->pluck('categoria');
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        // 1. Validamos los datos
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'subcategory' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'short_description' => 'nullable|string',
            'stock' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'spec_labels' => 'nullable|array',
            'spec_labels.*' => 'nullable|string|max:255',
            'spec_values' => 'nullable|array',
            'spec_values.*' => 'nullable|string|max:255',
        ]);

        // 2. Manejo de la imagen
        $imagePath = $product->imagen; 
        if ($request->hasFile('image')) {
            if ($product->imagen && !str_starts_with($product->imagen, 'http')) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($product->imagen);
            }
            $imagePath = $request->file('image')->store('products', 'public');
        }

        // 3. Re-armamos el arreglo de especificaciones
        $specifications = [];
        if (!empty($request->spec_labels) && !empty($request->spec_values)) {
            foreach ($request->spec_labels as $index => $label) {
                if (!empty($label) && !empty($request->spec_values[$index])) {
                    $specifications[] = [
                        'label' => $label,
                        'value' => $request->spec_values[$index]
                    ];
                }
            }
        }

        // 4. Modificamos el stock (solo tocamos la llave 'local')
        $existencia = $product->existencia ?? [];
        $existencia['local'] = $validatedData['stock'] ?? 0;

        // 5. Actualizamos el producto (Mapeando Request -> BD)
        $product->update([
            'nombre' => $validatedData['name'],
            'numParte' => $validatedData['sku'],
            'modelo' => $validatedData['model'] ?? null,
            'marca' => $validatedData['brand'],
            'categoria' => $validatedData['category'] ?? null,
            'subcategoria' => $validatedData['subcategory'] ?? null,
            'precio' => $validatedData['price'],
            'descripcion_corta' => $validatedData['short_description'],
            'existencia' => $existencia,
            'especificaciones' => $specifications,
            'imagen' => $imagePath,
        ]);

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