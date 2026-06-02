<?php
use App\Http\Controllers\AccesoController;
use App\Http\Controllers\ActividadController;
use App\Http\Controllers\Admin\AjustesController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ActividadController as AdminActividadController;
use App\Http\Controllers\Admin\AccesoController as AdminAccesoController;
use App\Http\Controllers\Admin\Reportes\AccesoReporteController;
use App\Http\Controllers\Admin\Reportes\ExportController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\CalificacionController;
use App\Http\Controllers\Admin\CasilleroController;
use App\Http\Controllers\RegistroController;
use App\Http\Controllers\SalidaController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schedule;

Schedule::command('accesos:cierre-diario')
    ->dailyAt('00:00');
// ─── Inicio ───────────────────────────────────────
Route::get('/', [AccesoController::class, 'index'])->name('index');

// ─── INGRESO (Identificación) ─────────────────────
Route::prefix('ingreso')->name('ingreso.')
    ->controller(AccesoController::class)
    ->group(function () {
        // routes/web.php
        Route::get('/ingreso/{tipo}','iniciarFlujo')
            ->where('tipo', 'ingreso|salida')
            ->name('iniciar');
        Route::get('/identificar', 'identificar')
            ->name('identificar');// reenvia a la vista para identificar
        Route::post('/identificar', 'buscarUsuario')->name('buscar'); // procesa a la persona
        Route::get('/confirmacion', 'confirmacion')
            ->name('confirmacion');//
});

// ─── ACTIVIDADES ──────────────────────────────────
Route::prefix('actividad')->name('actividad.')
    ->controller(ActividadController::class)
    ->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/','confirmarActividad')->name('confirmar');
});

// ─── SALIDA ───────────────────────────────────────
Route::prefix('salida')->name('salida.')
    ->controller(SalidaController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'registrar')->name('registrar');
    });

// ─── CALIFICACIÓN ─────────────────────────────────
Route::prefix('calificacion')->name('calificacion.')
    ->controller(CalificacionController::class)
    ->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/', 'guardar')->name('guardar');
});

Route::prefix('registro')->controller(RegistroController::class)
    ->name('registro.')->group(function () {
    Route::get('/', 'create')->name('create');
    Route::post('/', 'store')->name('store');
});

// ─── AUTH ADMIN ─────────────────────────────
Route::prefix('admin')
    ->name('admin.')
    ->controller(AuthController::class)
    ->group(function () {

        Route::get('/login', 'index')->name('login');
        Route::post('/login', 'login')->name('login.post');

        Route::post('/logout', 'logout')->name('logout');
    });


// ─────────────────────────────
// PANEL ADMIN (PROTEGIDO)
// ─────────────────────────────
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth.dashboard', 'rol:1'])
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');
        Route::get('/accesos', [AdminAccesoController::class, 'index'])
            ->name('accesos.index');
        Route::get('/accesos/{acceso}', [AdminAccesoController::class, 'show'])
            ->name('accesos.show');
        Route::get('/usuarios', [UsuarioController::class, 'index'])
            ->name('usuarios.index');
        Route::get('/usuarios',          [UsuarioController::class, 'index'])->name('usuarios.index');
        Route::get('/usuarios/{id}',     [UsuarioController::class, 'show'])->name('usuarios.show');
        Route::put('/usuarios/{id}',     [UsuarioController::class, 'update'])->name('usuarios.update');
        Route::get('/casilleros', [CasilleroController::class, 'index'])
            ->name('casilleros.index');
        Route::prefix('actividades')
            ->name('actividades.')
            ->controller(AdminActividadController::class)
            ->group(function () {
                Route::get('/', 'index')
                    ->name('index');
                Route::post('/', 'programar')
                    ->name('programar');
                Route::post('programar','programar')->name('programar');
                Route::put('{actividad}', 'actualizar')->name('actualizar');
                Route::delete('{actividad}','eliminar')->name('eliminar');
            });
        Route::prefix('reportes')->name('reportes.')
            ->controller(AccesoReporteController::class)->group(function () {
            Route::prefix('accesos')->name('accesos.')->group(function () {
                Route::get('resumen', 'resumen')
                    ->name('resumen');
                Route::get('flujo', 'flujo')
                    ->name('flujo');
                Route::get('historico', 'historico')
                    ->name('historico');
            });
            Route::prefix('locaciones')->name('locaciones.')->group(function () {
                    Route::get('ocupacion', 'locacionesOcupacion')->name('ocupacion');
                });
            Route::prefix('actividades')->name('actividades.')->group(function () {
                Route::get('usadas', 'actividadesUsadas')
                    ->name('usadas');
            });
            Route::prefix('export')->name('export.')->controller(ExportController::class)->group(function () {
                Route::get('historico/csv', 'historicoCsv')->name('historico.csv');
                Route::get('historico/pdf', 'historicoPdf')->name('historico.pdf');
                Route::get('actividades/csv', 'actividadesCsv')->name('actividades.csv');
                Route::get('locaciones/csv', 'locacionesCsv')->name('locaciones.csv');
                Route::get('locaciones/pdf', 'locacionesPdf')->name('locaciones.pdf');
            });
        });
        Route::prefix('ajustes')->name('ajustes.')->controller(AjustesController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::put('/', 'actualizar')->name('actualizar');
            Route::put('password','cambiarPassword')->name('password');
        });
    });
