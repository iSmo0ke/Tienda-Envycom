<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\UpdateOrderStatusRequest;

class OrderController extends Controller
{
    public function index()
    {
        // Traemos los pedidos con la info del cliente, del más nuevo al más viejo
        $orders = Order::with('user')->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        // Cargamos los productos que vienen dentro del pedido para ver el detalle
        $order->load('items.product', 'user');
        return view('admin.orders.show', compact('order'));
    }

    public function update(UpdateOrderStatusRequest $request, Order $order)
    {
        // Actualizamos el estado del pedido
        $order->update($request->only(['status']));
        return back()->with('success', 'El estado del pedido ha sido actualizado.');
    }
}