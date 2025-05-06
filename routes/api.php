<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UsuarioController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/verificar/{token}', [AuthController::class, 'verificarCorreo']);
Route::get('/verificar/{token}', [AuthController::class, 'verificarCorreoWeb']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware(['auth:sanctum', 'rol:admin'])->group(function () {
    Route::get('/usuarios', [UsuarioController::class, 'index']);
    Route::put('/usuarios/{id}', [UsuarioController::class, 'update']);
});
