<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        // Filtramos para no mostrar a los administradores en la lista de clientes.
        // Asumiendo que le pusiste 'role' a tu base de datos y los clientes son 'user' o 'cliente'.
        // Si tu columna se llama distinto, ajústalo aquí.
        $users = User::where('role', '!=', 'admin')
                     ->withCount('orders')
                     ->orderBy('created_at', 'desc')
                     ->paginate(15);

        return view('admin.users.index', compact('users'));
    }
}