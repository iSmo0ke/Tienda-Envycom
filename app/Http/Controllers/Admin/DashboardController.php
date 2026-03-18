<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Sumamos el total de ventas del mes actual (ignorando los pedidos cancelados)
        $ventasMes = Order::whereMonth('created_at', Carbon::now()->month)
                          ->whereYear('created_at', Carbon::now()->year)
                          ->where('status', '!=', 'cancelado')
                          ->sum('total');

        // 2. Contamos cuántos pedidos están en estatus "pendiente"
        $pedidosPendientes = Order::where('status', 'pendiente')->count();

        // 3. Contamos cuántos productos propios (locales) tiene el jefe
        $productosLocales = Product::where('source', 'local')->count();

        // 4. Traemos solo los últimos 5 pedidos para la tabla rápida
        $ultimosPedidos = Order::with('user')
                               ->orderBy('created_at', 'desc')
                               ->take(5)
                               ->get();

        return view('admin.dashboard', compact(
            'ventasMes', 
            'pedidosPendientes', 
            'productosLocales', 
            'ultimosPedidos'
        ));
    }
}