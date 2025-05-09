<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\ResetPasswordController;
use App\Mail\VerifyUpdatedEmail;
use App\Models\Usuario;



Route::get('/verificar/{token}', [AuthController::class, 'verificarCorreoWeb']);
Route::get('/verificar-correo/{token}', [AuthController::class, 'verificarCorreoWeb']);

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/restablecer-contrasena/{token}', [ResetPasswordController::class, 'mostrarFormulario'])->name('password.reset');
Route::post('/restablecer-contrasena', [ResetPasswordController::class, 'cambiarContrasena'])->name('password.update');
