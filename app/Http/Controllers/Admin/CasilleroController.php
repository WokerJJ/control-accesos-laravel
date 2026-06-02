<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CasilleroService;
use Illuminate\View\View;

class CasilleroController extends Controller
{
    public function __construct(
        private CasilleroService $casilleroService
    ) {}

    public function index(): View
    {
        return view('admin.casilleros.index', [
            'stats' => $this->casilleroService->resumen(),
            'mapa'  => $this->casilleroService->mapa(),
        ]);
    }
}
