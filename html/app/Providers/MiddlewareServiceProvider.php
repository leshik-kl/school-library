<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use MoonShine\Laravel\Http\Middleware\Authenticate;

class MiddlewareServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Регистрируем middleware в роутере
        Route::aliasMiddleware('auth.moonshine', Authenticate::class);
        
        // Также добавляем в глобальный массив middleware
        $this->app['router']->aliasMiddleware('auth.moonshine', Authenticate::class);
    }

    public function register(): void
    {
        //
    }
}
