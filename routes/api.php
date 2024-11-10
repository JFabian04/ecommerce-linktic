<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AuthenticateApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Usuarios
Route::resource('users', UserController::class);

// Autenticacion
Route::post('/login', [AuthController::class, 'login']);

// Crud de Productos
Route::resource('products', ProductController::class);

Route::middleware(AuthenticateApi::class)->group(function () {
    Route::prefix('products/images')->group(function () {
        Route::post('/', [ProductImageController::class, 'store']);
        Route::delete('{id}', [ProductImageController::class, 'destroy']);
    });

    Route::resource('orders', OrderController::class);
    Route::patch('orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.cancel');
});
