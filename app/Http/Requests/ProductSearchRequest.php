<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductSearchRequest extends FormRequest
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
            //
            'q' => 'required|string|min:3|max:50|regex:/^[a-zA-Z0-9\sñÑáéíóúÁÉÍÓÚ]+$/u',
        ];
    }

    public function messages(): array
    {
        return [
        'q.required' => 'Debes ingresar un término de búsqueda.',
        'q.min'      => 'La búsqueda debe tener al menos 3 caracteres.',
        'q.regex'    => 'La búsqueda no puede contener caracteres especiales (solo letras y números).',
        ];
    }
}
