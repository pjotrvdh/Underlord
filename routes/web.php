<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\SaveController;
use Illuminate\Support\Facades\Route;

Route::get('/', [GameController::class, 'show'])->name('game');
Route::get('/config.json', [GameController::class, 'config'])->name('config');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:10,1');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/save', [SaveController::class, 'show'])->name('save.show');
    Route::put('/save', [SaveController::class, 'update'])->name('save.update');
});
