<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isNew = $this->input('address_id') === 'new';

        return [
            'address_id' => 'required',
            'receptor_name' => $isNew ? 'required|string|max:255' : 'nullable',
            'phone' => $isNew ? 'required|string|min:10' : 'nullable',
            'calle_numero' => $isNew ? 'required|string' : 'nullable',
            'colonia' => $isNew ? 'required|string' : 'nullable',
            'municipio_alcaldia' => $isNew ? 'required|string' : 'nullable',
            'estado' => $isNew ? 'required|string' : 'nullable',
            'codigo_postal' => $isNew ? 'required|numeric|digits:5' : 'nullable',
            'referencias' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'address_id.required' => 'El campo address_id es obligatorio.',
            'receptor_name.required' => 'El campo receptor_name es obligatorio',
            'phone.required' => 'El campo phone es obligatorio',
            'calle_numero.required' => 'El campo calle_numero es obligatorio',
            'colonia.required' => 'El campo colonia es obligatorio',
            'municipio_alcaldia.required' => 'El campo municipio_alcaldia es obligatorio',
            'estado.required' => 'El campo estado es obligatorio',
            'codigo_postal.required' => 'El campo codigo_postal es obligatorio',
            'codigo_postal.numeric' => 'El campo codigo_postal debe ser un número.',
            'codigo_postal.digits' => 'El campo codigo_postal debe tener exactamente 5 dígitos.',
        ];
    }
    
}
