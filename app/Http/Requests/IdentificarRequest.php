<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IdentificarRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'doc_identidad' => 'required|string|min:6|max:20'
        ];
    }

    public function messages()
    {
        return [
            'doc_identidad.required' => 'Debe ingresar un documento',
            'doc_identidad.min' => 'El documento es demasiado corto',
        ];
    }
}
