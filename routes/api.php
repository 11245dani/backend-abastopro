<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UsuarioController;
use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\MarcaController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/verificar/{token}', [AuthController::class, 'verificarCorreo']);
Route::get('/verificar/{token}', [AuthController::class, 'verificarCorreoWeb']);

Route::get('/categorias', [CategoriaController::class, 'index']);
Route::get('/marcas', [MarcaController::class, 'index']);
Route::post('marcas', [MarcaController::class, 'store']);
Route::post('categorias', [CategoriaController::class, 'store']);


Route::get('/productos-disponibles', [ProductoController::class, 'listarDisponibles']);



Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::middleware('auth:sanctum')->put('/usuario/actualizar', [AuthController::class, 'actualizarUsuario']);
    Route::put('/admin/aprobar-gestor-despacho/{id}', [AdminController::class, 'aprobarGestorDespacho']);

});

Route::middleware(['auth:sanctum', 'rol:admin'])->group(function () {
    Route::get('/usuarios', [UsuarioController::class, 'index']);
    Route::put('/usuarios/{id}', [UsuarioController::class, 'update']);
    Route::middleware('auth:sanctum')->get('/usuarios', [AuthController::class, 'listarUsuarios']);

});

Route::middleware(['auth:sanctum', 'es_admin'])->put('/usuarios/{id}/cambiar-rol', [UsuarioController::class, 'cambiarRol']);
Route::middleware('auth:sanctum')->group(function () {
Route::post('/usuario/solicitar-cambio-rol', [UsuarioController::class, 'solicitarCambioRol']);
});

Route::middleware(['auth:sanctum', 'es_admin'])->group(function () {
    Route::post('/admin/aprobar-cambio-rol/{id}', [AdminController::class, 'aprobarCambioRol']);
});

Route::get('/admin/solicitudes-pendientes', [AdminController::class, 'listarSolicitudesPendientes']);
Route::post('/admin/rechazar-cambio-rol/{id}', [AdminController::class, 'rechazarCambioRol']);

Route::middleware(['auth:sanctum', 'gestor'])->group(function () {
    Route::get('/productos', [ProductoController::class, 'index']);
    Route::post('/productos', [ProductoController::class, 'store']);
    Route::put('/productos/{producto}', [ProductoController::class, 'update']);
    Route::delete('/productos/{producto}', [ProductoController::class, 'destroy']);

});

