<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponser
{

    protected function successResponse($data, $message = null, $code = 200): JsonResponse
    {
        return response()->json([
            'status'  => 'success',
            'message' => $message,
            'data'    => $data,
        ], $code);
    }

    protected function errorResponse($message, $code): JsonResponse
    {
        return response()->json([
            'status'  => 'error',
            'message' => $message,
            'code'    => $code,
        ], $code);
    }
}