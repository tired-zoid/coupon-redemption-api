<?php

use Illuminate\Support\Facades\Route;

// Простой маршрут без middleware
Route::get('/', function () {
    return response()->json(['message' => 'API is working']);
})->withoutMiddleware(['web']);
