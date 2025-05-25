<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// IoT device scanning endpoint
Route::post('/scan', [ItemController::class, 'scan'])->middleware('auth:sanctum');
