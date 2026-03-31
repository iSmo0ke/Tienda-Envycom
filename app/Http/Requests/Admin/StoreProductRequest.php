<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
        'nombre'            => 'required|string|max:255',
        'numParte'          => 'required|string|unique:products,numParte', // Evita duplicados
        'precio'            => 'required|numeric|min:0',
        'marca'             => 'required|string',
        'categoria'         => 'nullable|string',
        'modelo'            => 'nullable|string',
        'descripcion_corta' => 'nullable|string|max:1000',
        'activo'            => 'boolean',
    ];
    }

    public function messages()
    {
        return [
            'numParte.unique' => 'El número de parte ya existe. Por favor, elige uno diferente.',
            'precio.numeric' => 'El precio debe ser un número válido.',
            'precio.min' => 'El precio no puede ser negativo.',
        ];
    }
}