<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Autorizar request
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Preparar datos antes de validar
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'doc_identidad' => trim($this->doc_identidad),
        ]);
    }

    /**
     * Reglas de validación
     */
    public function rules(): array
    {
        return [
            'doc_identidad' => [
                'required',
                'string',
                'min:6',
                'max:20',
                'regex:/^[0-9]+$/'
            ],
            'password' => [
                'required',
                'string',
                'min:6',
                'max:100'
            ],
        ];
    }

    /**
     * Mensajes personalizados
     */
    public function messages(): array
    {
        return [
            'doc_identidad.required' => 'El documento es obligatorio.',
            'doc_identidad.regex'    => 'El documento solo debe contener números.',
            'doc_identidad.min'      => 'El documento es demasiado corto.',
            'doc_identidad.max'      => 'El documento es demasiado largo.',

            'password.required' => 'La contraseña es obligatoria.',
            'password.min'      => 'La contraseña debe tener al menos 6 caracteres.',
        ];
    }

    /**
     * Nombres amigables (UX)
     */
    public function attributes(): array
    {
        return [
            'doc_identidad' => 'documento',
            'password'      => 'contraseña',
        ];
    }
}
