<?php

use Illuminate\Support\Facades\Route;
use MoonShine\Laravel\Http\Middleware\Authenticate;

Route::middleware(['web', Authenticate::class])
    ->prefix('admin')
    ->group(function () {
        // Маршруты будут добавлены автоматически Moonshine
        // Но мы гарантируем, что middleware применяется
    });
