<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderStatusRequest extends FormRequest
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
            'status' => 'required|in:en_proceso,pagado,enviado,entregado,cancelado',
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'El campo status es obligatorio.',
            'status.in' => 'El campo status debe ser uno de los siguientes: en_proceso, pagado, enviado, entregado, cancelado.',
        ];
    }
}
