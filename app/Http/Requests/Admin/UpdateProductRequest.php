<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
        $productId = $this->route('product') ? $this->route('product')->id : null;
        return [
            //
            'nombre' => 'required|string|max:100',
            'numParte' => 'required|string|unique:products,numParte,' . $productId,
            'precio' => 'required|numeric|min:0',
            'marca' => 'required|string',
            'categoria' => 'nullable|string',
            'modelo' => 'nullable|string',
            'descripcion_corta' => 'nullable|string|max:500',
            'activo' => 'sometimes|boolean',
        ];
    }

    public function messages()
    {
        return [
            'numParte.unique' => 'El número de parte ya existe. Debe ser único.',
            'precio.min' => 'El precio debe ser un número positivo.',
            'nombre.required' => 'El nombre del producto es obligatorio.',
        ];
    }
}