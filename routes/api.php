<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UsuarioController;
use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\API\ForgotPasswordController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\SubpedidoController;

use App\Http\Controllers\GestorController;

// ✅ Autenticación
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/verificar/{token}', [AuthController::class, 'verificarCorreo']);
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);

// ✅ Público: productos, marcas, categorías
Route::get('/categorias', [CategoriaController::class, 'index']);
Route::get('/marcas', [MarcaController::class, 'index']);
Route::post('marcas', [MarcaController::class, 'store']);
Route::post('categorias', [CategoriaController::class, 'store']);

Route::middleware('auth:sanctum')->put('/pedidos/{id}/estado', [PedidoController::class, 'actualizarEstado']);

Route::get('/productos-disponibles', [ProductoController::class, 'listarDisponibles']);

// ✅ Protegidas por autenticación
Route::middleware('auth:sanctum')->group(function () {

    // Usuario autenticado
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/usuario/actualizar', [AuthController::class, 'actualizarUsuario']);

    // Carrito
    Route::get('/carrito', [CarritoController::class, 'ver']);
    Route::post('/carrito/agregar', [CarritoController::class, 'agregar']);
    Route::delete('/carrito/eliminar/{item}', [CarritoController::class, 'eliminar']);
    Route::post('/carrito/confirmar', [CarritoController::class, 'confirmar']);

    // Pedidos tienda (tendero)
    Route::get('/historial-pedidos', [PedidoController::class, 'historialPedidosTienda']);
    Route::get('/pedidos/tienda', [PedidoController::class, 'historialPedidosTienda']); // alias
    Route::get('/pedidos/ver-pago/{id}', [PedidoController::class, 'verPago']);

    // Confirmar estado o pago (gestor_despacho)
    Route::put('/pedidos/{id}/estado', [PedidoController::class, 'actualizarEstado']);
    Route::post('/pedidos/confirmar-pago/{id}', [PedidoController::class, 'confirmarPago']);

    // Pedidos asignados al distribuidor (gestor_despacho)
    Route::get('/pedidos-distribuidor', [PedidoController::class, 'pedidosDistribuidor']);
    Route::get('/gestor/pedidos-nuevos', [PedidoController::class, 'totalPedidosNuevos']);
    Route::get('/gestor/informes', [GestorController::class, 'obtenerInformes']);

    // Subpedidos
    Route::get('/pedidos/distribuidor', [SubpedidoController::class, 'pedidosDistribuidor']);
    Route::patch('/subpedidos/{id}/estado', [SubpedidoController::class, 'actualizarEstado']);
    Route::get('/factura/{subpedido}', [SubpedidoController::class, 'descargarFactura']);

    // Cambio de rol
    Route::post('/usuario/solicitar-cambio-rol', [UsuarioController::class, 'solicitarCambioRol']);
});

// ✅ Rutas exclusivas para rol "admin"
Route::middleware(['auth:sanctum', 'rol:admin'])->group(function () {
    Route::get('/usuarios', [UsuarioController::class, 'index']);
    Route::put('/usuarios/{id}', [UsuarioController::class, 'update']);
    Route::put('/usuarios/{id}/cambiar-rol', [UsuarioController::class, 'cambiarRol']);
    Route::get('/admin/solicitudes-pendientes', [AdminController::class, 'listarSolicitudesPendientes']);
    Route::post('/admin/aprobar-cambio-rol/{id}', [AdminController::class, 'aprobarCambioRol']);
    Route::post('/admin/rechazar-cambio-rol/{id}', [AdminController::class, 'rechazarCambioRol']);
    Route::put('/admin/aprobar-gestor-despacho/{id}', [AdminController::class, 'aprobarGestorDespacho']);
    Route::put('/admin/rechazar-gestor-despacho/{id}', [AdminController::class, 'rechazarGestorDespacho']);
    // routes/api.php
    Route::middleware(['auth:sanctum', 'rol:admin'])->get('/admin/reportes', [\App\Http\Controllers\AdminController::class, 'generarReportes']);
    Route::get('/reportes', [AdminController::class, 'generarReportes'])->middleware(['auth:sanctum', 'admin']);
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->middleware(['auth:sanctum', 'admin']);

});

// ✅ Rutas exclusivas para rol "gestor_despacho"
Route::middleware(['auth:sanctum', 'gestor'])->group(function () {
    Route::get('/productos', [ProductoController::class, 'index']);
    Route::post('/productos', [ProductoController::class, 'store']);
    Route::put('/productos/{producto}', [ProductoController::class, 'update']);
    Route::delete('/productos/{producto}', [ProductoController::class, 'destroy']);
});

Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);

Route::middleware('auth:sanctum')->get('/pedidos/tienda', [PedidoControllerr::class, 'pedidosTienda']);
Route::middleware('auth:sanctum')->get('/pedidos/distribuidor', [SubpedidoController::class, 'pedidosDistribuidor']);
Route::patch('/subpedidos/{id}/estado', [SubpedidoController::class, 'actualizarEstado']);
Route::get('/factura/{subpedido}', [SubpedidoController::class, 'descargarFactura']);

Route::middleware(['auth:sanctum'])->get('/gestor/pedidos-nuevos', [PedidoController::class, 'totalPedidosNuevos']);
Route::middleware('auth:sanctum')->get('/gestor/informes', [GestorController::class, 'obtenerInformes']);
