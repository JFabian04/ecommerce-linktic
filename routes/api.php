<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Route::prefix('products')->group(function (){
Route::resource('products', ProductController::class);
// });

// Crud de ordenes
Route::resource('orders', OrderController::class);
Route::patch('orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.cancel'); //Endpoint para cancelar la orden
