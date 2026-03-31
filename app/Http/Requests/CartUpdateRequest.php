<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; //cualquier usuario autenticado puede gestionar su carrito
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
            'quantity' => 'required|integer|min:1|max:100', // Asegura que la cantidad sea un entero entre 1 y 100, para evitar cantidades excesivas
            
        ];
    }
    public function messages()
    {
        return [
            'quantity.required' => 'La cantidad es requerida.',
            'quantity.integer' => 'La cantidad debe ser un número entero.',
            'quantity.min' => 'La cantidad debe ser al menos 1.',
            'quantity.max' => 'La cantidad no puede ser mayor a 100.',
        ];
    }
}
