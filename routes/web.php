<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\LocationController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('locations', LocationController::class);
    Route::post('locations/{location}/toggle-ac', [LocationController::class, 'toggleAc'])->name('locations.toggle-ac');
    Route::post('locations/{location}/toggle-dehumidifier', [LocationController::class, 'toggleDehumidifier'])->name('locations.toggle-dehumidifier');
    
    Route::resource('items', ItemController::class);
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
