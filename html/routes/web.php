<?php
// routes/web.php

use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ReaderController;
use Illuminate\Support\Facades\Route;

// Публичные маршруты
Route::get('/', function () {
    return redirect()->route('catalog.index');
})->name('home');

Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/catalog/{id}', [CatalogController::class, 'show'])->name('catalog.show');

// Маршруты для авторизованных пользователей
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ReaderController::class, 'profile'])->name('reader.profile');
    Route::post('/request-loan', [ReaderController::class, 'requestLoan'])->name('reader.request-loan');
});

// Маршруты аутентификации (если используете Laravel Breeze или Jetstream)
// require __DIR__.'/auth.php';
