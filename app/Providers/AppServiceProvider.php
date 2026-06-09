<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Shared admin menu for sidebar component
        $menu = [
            [
                'label' => 'Dashboard',
                'icon'  => 'fas fa-tachometer-alt',
                'route' => 'admin.dashboard',
                'match' => 'admin.dashboard*',
            ],
            [
                'label' => 'Casilleros',
                'icon'  => 'fas fa-box',
                'route' => 'admin.casilleros.index',
                'match' => 'admin.casilleros*',
            ],
            [
                'label' => 'Actividades',
                'icon'  => 'fas fa-tasks',
                'route' => 'admin.actividades.index',
                'match' => 'admin.actividades*',
            ],
            [
                'label' => 'Accesos',
                'icon'  => 'fas fa-door-open',
                'route' => 'admin.accesos.index',
                'match' => 'admin.accesos*',
            ],
            [
                'label' => 'Usuarios',
                'icon'  => 'fas fa-users',
                'route' => 'admin.usuarios.index',
                'match' => 'admin.usuarios*',
            ],
            [
                'label'    => 'Reportes',
                'icon'     => 'fas fa-chart-bar',
                'match'    => 'admin.reportes*',
                'children' => [
                    [
                        'label' => 'Resumen del día',
                        'route' => 'admin.reportes.accesos.resumen',
                        'match' => 'admin.reportes.accesos.resumen*',
                        'icon'  => 'fas fa-clipboard-list',
                    ],
                    [
                        'label' => 'Flujo por horas',
                        'route' => 'admin.reportes.accesos.flujo',
                        'match' => 'admin.reportes.accesos.flujo*',
                        'icon'  => 'fas fa-stream',
                    ],
                    [
                        'label' => 'Histórico',
                        'route' => 'admin.reportes.accesos.historico',
                        'match' => 'admin.reportes.accesos.historico*',
                        'icon'  => 'fas fa-history',
                    ],
                    [
                        'label' => 'Más usadas',
                        'route' => 'admin.reportes.actividades.usadas',
                        'match' => 'admin.reportes.actividades.usadas*',
                        'icon'  => 'fas fa-fire',
                    ],
                    [
                        'label' => 'Ocupación',
                        'route' => 'admin.reportes.locaciones.ocupacion',
                        'match' => 'admin.reportes.locaciones.ocupacion*',
                        'icon'  => 'fas fa-map-marker-alt',
                    ],
                ],
            ],
            [
                'label' => 'Ajustes',
                'icon'  => 'fas fa-cog',
                'route' => 'admin.ajustes.index',
                'match' => 'admin.ajustes*',
            ]
        ];

        View::composer('components.admin.sidebar', function ($view) use ($menu) {
            $view->with('menu', $menu);
        });
    }
}
