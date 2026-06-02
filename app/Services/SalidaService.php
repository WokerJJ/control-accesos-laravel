<?php

namespace App\Services;

use App\Models\Persona;

class SalidaService
{
    public function __construct(
        private AccesoService $accesoService
    ) {}

    public function registrar(int $personaId)
    {
        return $this->accesoService->finalizar($personaId);
    }
}
