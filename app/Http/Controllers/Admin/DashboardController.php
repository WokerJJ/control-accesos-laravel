<?php

// app/Http/Controllers/Admin/DashboardController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\EstadisticasService;

class DashboardController extends Controller
{
    public function __construct(
        private EstadisticasService $estadisticas
    ) {}

    public function index()
    {
        return view('admin.dashboard.index', [
            'stats' => $this->estadisticas->resumen()
        ]);
    }
}
