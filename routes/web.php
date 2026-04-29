use App\Http\Controllers\AccesoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

Route::get('/', [AccesoController::class, 'index']);
Route::post('/ingresar', [AccesoController::class, 'ingresar']);
Route::post('/salida', [AccesoController::class, 'salida']);

Route::get('/login', [AuthController::class, 'index']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/dashboard', [DashboardController::class, 'index']);
