<?php

namespace App\Http\Middleware;

use App\Services\Admin\AccesoAdminService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarCierreDiario
{
    public function __construct(
        private AccesoAdminService $service,
    ) {}
    public function handle(Request $request, Closure $next): Response
    {
        $this->service->verificarCierrePendiente();
        return $next($request);
    }
}
