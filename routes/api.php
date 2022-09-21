<?php

use Illuminate\Support\Facades\Route;

// Products
Route::get('/products', [\App\Http\Controllers\Api\ProductController::class, 'index']);

// Payment
Route::post('/purchase', [\App\Http\Controllers\Api\UserController::class, 'purchase']);
Route::get('/summary/{order}', [\App\Http\Controllers\Api\OrderController::class, 'summary']);
Route::get('/payment/{order}', [\App\Http\Controllers\Api\UserController::class, 'payment']);
