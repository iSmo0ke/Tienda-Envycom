<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Requests\AddToCartRequest;

class CartController extends Controller
{
    public function index()
    {
        // CAMBIO: 'carrito' a 'cart'
        $cart = session()->get('cart', []);
        return view('carrito', compact('cart')); // compact también cambia a 'cart'
    }

    public function add(AddToCartRequest $request)
    {
        // MAP: Obtenemos solo los campos validados
        $validated = $request->only(['product_id', 'quantity']);

        $product = Product::findOrFail($validated['product_id']);
        $cart = session()->get('cart', []);

        // Lógica para agregar o actualizar cantidad en la sesión
        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $validated['quantity'];
        } else {
            $cart[$product->id] = [
                "id" => $product->id,
                "name" => $product->nombre,
                "quantity" => $validated['quantity'],
                "price" => $product->precio,
                "image" => $product->imagen_url
            ];
        }

        session()->put('cart', $cart);
        return back()->with('success', '¡Producto agregado al carrito!');
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