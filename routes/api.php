<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AuthenticateApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Usuarios
Route::resource('users', UserController::class);

// Autenticacion
Route::post('/login', [AuthController::class, 'login']);

// Crud de Productos
Route::resource('products', ProductController::class);

Route::middleware(AuthenticateApi::class)->group(function () {
    // Rutas para imagenes de productos
    Route::prefix('products/images')->group(function () {
        Route::post('/', [ProductImageController::class, 'store']);
        Route::delete('{id}', [ProductImageController::class, 'destroy']);
    });

    // Rutas para ordenes
    Route::resource('orders', OrderController::class);
    Route::patch('orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.cancel');

    // Cerrar seisón
    Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
});
