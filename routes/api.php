<?php

use Illuminate\Support\Facades\Route;

Route::get('/products', [\App\Http\Controllers\Api\ProductController::class, 'index']);

Route::post('/purchase', [\App\Http\Controllers\Api\UserController::class, 'purchase']);

Route::get('/summary/{order}', [\App\Http\Controllers\OrderController::class, 'summary']);

Route::get('/payment/{order}', [\App\Http\Controllers\OrderController::class, 'payment']);
