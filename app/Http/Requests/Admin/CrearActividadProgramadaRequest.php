<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CrearActividadProgramadaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipo_actividad_id' => 'required|exists:tipos_actividad,id',
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string|max:500',
            'locacion_id' => 'required|exists:locacion,id',
            'fecha_inicio' => 'required|date|after_or_equal:today',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i',
        ];
    }

    public function messages(): array
    {
        return [
            'fecha_inicio.after_or_equal' => 'La fecha de inicio no puede ser anterior a hoy.',
            'fecha_fin.after_or_equal' => 'La fecha de fin debe ser igual o posterior al inicio.',
            'hora_fin.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $horaInicio = $this->input('hora_inicio');
            $horaFin    = $this->input('hora_fin');
            if ($this->input('fecha_inicio') === $this->input('fecha_fin')) {
                if ($horaInicio && $horaFin && $horaFin <= $horaInicio) {
                    $validator->errors()->add(
                        'hora_fin',
                        'La hora de fin debe ser posterior a la hora de inicio.'
                    );
                }
            }
        });
    }

}
