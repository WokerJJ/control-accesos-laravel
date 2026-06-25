<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ImportarEstudiantesExcel implements WithMultipleSheets
{
    private ?RolesImport     $rolesImport     = null;
    private ?LocacionesImport $locacionesImport = null;
    private ?ActividadesImport $actividadesImport = null;
    private ?CasillerosImport $casillerosImport = null;
    private ?EstudiantesImport $estudiantesImport = null;

    public function __construct(
        bool $importarRoles      = true,
        bool $importarLocaciones = true,
        bool $importarActividades = true,
        bool $importarCasilleros = true,
        bool $importarEstudiantes = true,
    ) {
        if ($importarRoles) {
            $this->rolesImport = new RolesImport();
        }
        if ($importarLocaciones) {
            $this->locacionesImport = new LocacionesImport();
        }
        if ($importarActividades) {
            $this->actividadesImport = new ActividadesImport();
        }
        if ($importarCasilleros) {
            $this->casillerosImport = new CasillerosImport();
        }
        if ($importarEstudiantes) {
            $this->estudiantesImport = new EstudiantesImport();
        }
    }

    public function sheets(): array
    {
        $sheets = [];

        // Orden importante: primero catálogos base, luego dependencias
        if ($this->rolesImport) {
            $sheets['Roles'] = $this->rolesImport;
        }
        if ($this->locacionesImport) {
            $sheets['Locaciones'] = $this->locacionesImport;
        }
        if ($this->actividadesImport) {
            $sheets['Actividades'] = $this->actividadesImport;
        }
        if ($this->casillerosImport) {
            $sheets['Casilleros'] = $this->casillerosImport;
        }
        if ($this->estudiantesImport) {
            $sheets['Estudiantes'] = $this->estudiantesImport;
        }

        return $sheets;
    }

    // ── Getters para el resumen final ───────────────────────────

    public function getRolesImport(): ?RolesImport
    {
        return $this->rolesImport;
    }

    public function getLocacionesImport(): ?LocacionesImport
    {
        return $this->locacionesImport;
    }

    public function getActividadesImport(): ?ActividadesImport
    {
        return $this->actividadesImport;
    }

    public function getCasillerosImport(): ?CasillerosImport
    {
        return $this->casillerosImport;
    }

    public function getEstudiantesImport(): ?EstudiantesImport
    {
        return $this->estudiantesImport;
    }
}
