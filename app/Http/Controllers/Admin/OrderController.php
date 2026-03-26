<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

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

    public function update(Request $request, $id)
    {
        $order = \App\Models\Order::findOrFail($id);

        $request->validate([
            'status' => 'required|string',
            'shipping_carrier' => 'nullable|string',
            'tracking_number' => 'nullable|string',
        ]);

        $order->status = $request->status;
        
        if (in_array($request->status, ['enviado', 'entregado'])) {
            $order->shipping_carrier = $request->shipping_carrier;
            $order->tracking_number = $request->tracking_number;
            
            if ($request->status === 'enviado' && is_null($order->shipped_at)) {
                $order->shipped_at = now();
            }
        } else {
            $order->shipping_carrier = null;
            $order->tracking_number = null;
            $order->shipped_at = null;
        }
        $order->save();

        return redirect()->back()->with('success', 'Estatus y guía de envío actualizados correctamente.');
    }
}