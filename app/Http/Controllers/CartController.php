<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        return view('carrito', compact('cart'));
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
                'sku' => $product->sku ?? 'N/A',
                'price' => $product->precio,
                'quantity' => 1,
                'image' => $product->imagen ?? null,
            ];
        }

        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Producto agregado al carrito');
    }

    public function update(Request $request, $id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            // ACEPTAMOS EL CAMBIO: Usamos 'quantity' como lo configuró tu compañera en la vista
            $quantity = (int) $request->input('quantity');
            
            // MANTENEMOS NUESTRA SEGURIDAD: Verificamos contra la BD real de CT
            $product = Product::find($id);
            $stockData = is_array($product->existencia) ? $product->existencia : json_decode($product->existencia, true);
            $stockDisponible = $stockData['total'] ?? 0;

            if ($quantity <= 0) {
                unset($cart[$id]);
            } elseif ($quantity > $stockDisponible) {
                return redirect()->route('carrito')->with('error', "Solo tenemos {$stockDisponible} unidades de {$product->nombre} en existencia.");
            } else {
                $cart[$id]['quantity'] = $quantity;
            }

            session()->put('cart', $cart);
        }

        // Regresamos con el mensaje de éxito
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