<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductImageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Crud de Productos
Route::resource('products', ProductController::class);

//Imagenes d productos
Route::prefix('products/images')->group(function () {
    Route::post('/', [ProductImageController::class, 'store']);
    Route::delete('{id}', [ProductImageController::class, 'destroy']);
});

// Crud de ordenes
Route::resource('orders', OrderController::class);
Route::patch('orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.cancel'); //Endpoint para cancelar la orden
