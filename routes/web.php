<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;

Route::get('/verificar/{token}', [AuthController::class, 'verificarCorreoWeb']);
