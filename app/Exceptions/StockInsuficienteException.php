<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;

class StockInsuficienteException extends Exception
{
    public function render(Request $request)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Lo sentimos, uno de los productos ya no tiene stock suficiente.',
                'code'    => 409
            ], 409);
        }
        return back()->withErrors([
            'existencia' => 'Lo sentimos, uno de los productos ya no tiene stock suficiente.'
        ]);
    }
}