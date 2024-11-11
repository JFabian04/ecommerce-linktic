<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


// Usuarios
Route::resource('users', UserController::class);

// Autenticacion
Route::post('/login', [AuthController::class, 'login']);

// Rutas de productos publicas (Listar todos y listar por ID)
Route::resource('products', ProductController::class)->only(['index', 'show']);


Route::middleware('auth.api')->group(function () {
    // Rutas productos privadas (Registrar, actulizar)
    Route::resource('products', ProductController::class)->except(['index', 'show']);
    Route::patch('products/{id}/status', [ProductController::class, 'updateStatus']);

    // Imagenes de producto
    Route::prefix('products/images')->group(function () {
        Route::post('/', [ProductImageController::class, 'store']);
        Route::delete('{id}', [ProductImageController::class, 'destroy']);
    });

    // Rutas para ordenes
    Route::resource('orders', OrderController::class);
    Route::patch('orders/{id}/status', [OrderController::class, 'updateStatus']);
    Route::post('report/orders', [OrderController::class, 'generateReport']);
    
    // Cerrar seisón
    Route::post('/logout', [AuthController::class, 'logout']);
});
