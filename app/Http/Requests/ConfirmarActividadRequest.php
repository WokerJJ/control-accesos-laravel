<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmarActividadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Siempre debe venir actividad_id (ya sea fija, programada o personalizada existente)
            // O los datos para crear una nueva personalizada
            'actividad_id' => 'nullable|exists:actividades,id',

            // Si no hay actividad_id, entonces se crea una personalizada
            'nombre' => 'required_without:actividad_id|string|max:255',
            'descripcion' => 'nullable|string|max:500',
            'locacion_id' => 'nullable|exists:locacion,id',
            'tipo_actividad_id' => 'nullable|exists:tipos_actividad,id',
            'estado' => 'nullable|in:pendiente,en_curso,cancelada,finalizada',
        ];
    }

    public function messages(): array
    {
        return [
            'actividad_id.exists' => 'La actividad seleccionada no existe.',
            'nombre.required_without' => 'El nombre es obligatorio para crear una actividad.',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres.',
            'descripcion.max' => 'La descripción no puede exceder 500 caracteres.',
            'locacion_id.exists' => 'La locación seleccionada no existe.',
        ];
    }
}
