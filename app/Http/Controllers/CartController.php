<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    public function index()
    {
        // CAMBIO: 'carrito' a 'cart'
        $cart = session()->get('cart', []);
        return view('carrito', compact('cart')); // compact también cambia a 'cart'
    }

    public function add($id)
    {
        $product = Product::findOrFail($id);
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity']++; // CAMBIO: 'cantidad' a 'quantity'
        } else {
            $cart[$id] = [
                'id' => $product->id,
                'name' => $product->nombre, // CAMBIO: 'nombre' a 'name' (asumo que tu DB todavía tiene 'nombre', si no, cambia a $product->name)
                'brand' => $product->marca, // CAMBIO: 'marca' a 'brand'
                'sku' => $product->sku ?? 'N/A',
                'price' => $product->precio, // CAMBIO: 'precio' a 'price'
                'quantity' => 1,            // CAMBIO: 'cantidad' a 'quantity'
                'image' => $product->imagen ?? null, // CAMBIO: 'imagen' a 'image'
            ];
        }

        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Producto agregado al carrito');
    }

    public function update(Request $request, $id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = $request->input('quantity'); // CAMBIO: 'cantidad' a 'quantity'
            session()->put('cart', $cart);

            return redirect()->route('carrito')->with('success', 'Cantidad actualizada');
        }
        return redirect()->route('carrito')->with('error', 'Producto no encontrado en el carrito');
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