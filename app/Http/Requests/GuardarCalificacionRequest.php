<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GuardarCalificacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return session()->has('calificacion.acceso_id');
    }

    public function rules(): array
    {
        return [
            'servicio'   => 'required|integer|min:1|max:5',
            'atencion'   => 'required|integer|min:1|max:5',
            'lugar'      => 'required|integer|min:1|max:5',
            'calidad'    => 'required|integer|min:1|max:5',
            'comentario' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            // Campos obligatorios
            '*.required' => 'Debe calificar todos los aspectos del servicio.',

            // Rango permitido
            '*.min' => 'La calificación mínima es de 1 estrella.',
            '*.max' => 'La calificación máxima es de 5 estrellas.',

            // Tipo de dato
            '*.integer' => 'La calificación debe ser un número válido.',

            // Comentario
            'comentario.string' => 'El comentario debe ser texto válido.',
            'comentario.max'    => 'El comentario no puede superar los 500 caracteres.',
        ];
    }
}
