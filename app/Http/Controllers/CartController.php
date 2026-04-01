<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Requests\CartUpdateRequest;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);

        // Calcular el subtotal
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        // Definir el costo de envío (150 o gratis si quieres poner una promoción después)
        $envio = 150.00;
        
        // Calcular el total
        $total = $subtotal + $envio;

        // Pasar TODAS las variables a la vista
        return view('carrito', compact('cart', 'subtotal', 'envio', 'total'));
    }

    public function add($id)
    {
        $product = Product::where('activo', true)->findOrFail($id);
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                'id' => $product->id,
                'name' => $product->nombre,
                'brand' => $product->marca, 
                'numParte' => $product->numparte ?? 'N/A',
                'price' => $product->precio,
                'quantity' => 1,
                'image' => $product->imagen ?? null,
            ];
        }

        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Producto agregado al carrito');
    }

    public function update(CartUpdateRequest $request, $id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            // Como ya pasó por el CartUpdateRequest, sabemos seguro que es un número entre 1 y 100
            $quantity = (int) $request->input('quantity');
            
            // MANTENEMOS NUESTRA SEGURIDAD: Verificamos contra la BD real de CT
            $product = Product::find($id);
            $stockData = is_array($product->existencia) ? $product->existencia : json_decode($product->existencia, true);
            $stockDisponible = $stockData['total'] ?? 0;

            // Verificamos si nos está pidiendo más de lo que hay en CT
            if ($quantity > $stockDisponible) {
                return redirect()->route('carrito')->with('error', "Solo tenemos {$stockDisponible} unidades de {$product->nombre} en existencia.");
            } else {
                $cart[$id]['quantity'] = $quantity;
            }

            session()->put('cart', $cart);
        }

        return redirect()->route('carrito')->with('success', 'Cantidad actualizada');
    }

    public function remove($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()->route('carrito')->with('success', 'Producto eliminado');
    }

    public function clear()
    {
        session()->forget('cart');
        return redirect()->route('carrito')->with('success', 'Carrito vaciado');
    }
}