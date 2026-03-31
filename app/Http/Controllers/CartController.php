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
            $quantity = (int) $request->cantidad;

            if ($quantity <= 0) {
                unset($cart[$id]);
            } else {
                $cart[$id]['quantity'] = $quantity;
            }

            session()->put('cart', $cart);
        }

        return redirect()->route('carrito');
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