<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegistroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $area = $this->input('area');

        return [
            'area'                   => 'required|in:estudiante,administrativo,profesor,externo',
            'tipo_identificacion_id' => 'required|exists:tipo_identificacion,id',
            'doc_identidad'          => 'required|string|min:5|max:20|unique:personas,doc_identidad',
            'primer_nombre'          => 'required|string|max:50',
            'segundo_nombre'         => 'nullable|string|max:50',
            'primer_apellido'        => 'required|string|max:50',
            'segundo_apellido'       => 'nullable|string|max:50',
            'email'                  => 'required|email|max:100|unique:personas,email',
            'celular'                => 'nullable|string|max:15|regex:/^[0-9]+$/',
            'programa_academico_id'  => $area === 'estudiante'
                ? 'required|exists:programas_academicos,id'
                : 'nullable|exists:programas_academicos,id',
            'codigo_institucional'   => $area === 'externo'
                ? 'nullable|string|max:20'
                : 'required|string|max:20',
            'municipio_id'           => 'nullable|exists:municipio,id',
        ];
    }

    public function messages(): array
    {
        return [
            'area.required' => 'Selecciona un área.',
            'area.in'       => 'El área seleccionada no es válida.',

            'tipo_identificacion_id.required' => 'Selecciona un tipo de identificación.',
            'tipo_identificacion_id.exists'   => 'El tipo de identificación seleccionado no es válido.',

            'doc_identidad.required' => 'El número de documento es obligatorio.',
            'doc_identidad.min'      => 'El documento debe tener al menos :min caracteres.',
            'doc_identidad.max'      => 'El documento no puede exceder :max caracteres.',
            'doc_identidad.unique'   => 'Este documento ya está registrado en el sistema.',

            'primer_nombre.required' => 'El primer nombre es obligatorio.',
            'primer_nombre.max'      => 'El nombre no puede exceder :max caracteres.',

            'primer_apellido.required' => 'El primer apellido es obligatorio.',
            'primer_apellido.max'      => 'El apellido no puede exceder :max caracteres.',

            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email'    => 'Ingresa un correo electrónico válido.',
            'email.max'      => 'El correo no puede exceder :max caracteres.',
            'email.unique'   => 'Este correo ya está registrado. Si ya tienes cuenta, inicia sesión.',

            'programa_academico_id.required' => 'Selecciona un programa académico.',
            'programa_academico_id.exists'   => 'El programa seleccionado no es válido.',

            'codigo_institucional.required' => 'El carnet / código institucional es obligatorio.',
            'codigo_institucional.max'      => 'El código institucional no puede exceder :max caracteres.',

            'municipio_id.exists' => 'El municipio seleccionado no es válido.',
        ];
    }
}
