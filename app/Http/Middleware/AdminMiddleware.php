<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Revisamos si el usuario está logueado y si su rol es 'admin'
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request); // Déjalo pasar
        }

        // Si no es admin, lo pateamos a la página principal con un error 403 (Prohibido)
        // o lo redirigimos al inicio. Redirigir es más amigable:
        return redirect('/')->with('error', 'Acceso denegado. Área exclusiva para administradores.');
    }
}