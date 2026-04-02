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
        return true;
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
            'address_id'      => 'required|string',
            'sepomex_id'      => $isNew ? 'required|exists:postal_codes,id' : 'nullable', // ¡CRÍTICO!
            'alias'           => 'nullable|string|max:50',
            'receptor_name'   => $isNew ? 'required|string|max:150' : 'nullable',
            'telefono'        => $isNew ? 'required|string|max:20' : 'nullable',
            'zip_code'        => $isNew ? 'required|numeric|digits:5' : 'nullable',
            'calle'           => $isNew ? 'required|string|max:255' : 'nullable',
            'numero_exterior' => $isNew ? 'required|string|max:50' : 'nullable',
            'numero_interior' => 'nullable|string|max:50',
            'refencias'       => 'nullable|string|max:500', // Mantengo tu nombre 'refencias'
        ];
    }

    public function messages(): array
    {
        return [
            'address_id.exists' => 'La ubicación seleccionada no es válida en nuestro catálogo postal.',
            'address_id.required' => 'Debes seleccionar una colonia válida de la lista.',
        ];
    }
    
}
