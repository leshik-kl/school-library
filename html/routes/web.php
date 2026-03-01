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

// Временные заглушки для маршрутов аутентификации
Route::get('/login', function() {
    return redirect('/catalog')->with('info', 'Для входа обратитесь к библиотекарю');
})->name('login');

Route::get('/register', function() {
    return redirect('/catalog')->with('info', 'Регистрация временно недоступна');
})->name('register');

Route::post('/logout', function() {
    return redirect('/catalog');
})->name('logout');

// Редирект для старых ссылок
Route::get('admin/resource/{resource}/index-page', function($resource) {
    return redirect("admin/resource/{$resource}/crud");
})->where('resource', '.*');

// Редирект для старых ссылок с index-page на crud
Route::get('admin/resource/{resource}/index-page', function($resource) {
    return redirect("admin/resource/{$resource}/crud");
})->where('resource', '.*');

// Редирект для всех страниц с index-page
Route::get('admin/resource/{resource}/{page}', function($resource, $page) {
    if ($page === 'index-page') {
        return redirect("admin/resource/{$resource}/crud");
    }
    abort(404);
})->where('resource', '.*')->where('page', '.*');

// Редирект для старых ссылок с index-page на crud
Route::get('admin/resource/{resource}/index-page', function($resource) {
    $resourceMap = [
        'author-resource' => 'author-resource',
        'book-resource' => 'book-resource',
        'category-resource' => 'category-resource',
        'loan-resource' => 'loan-resource',
        'publisher-resource' => 'publisher-resource',
        'reader-resource' => 'reader-resource',
    ];
    
    if (isset($resourceMap[$resource])) {
        return redirect("/admin/resource/{$resourceMap[$resource]}/crud");
    }
    
    return redirect("/admin/resource/{$resource}/crud");
})->where('resource', '.*');

// Тестовый маршрут для проверки аутентификации
Route::get('/test-auth', function() {
    return [
        'authenticated' => auth()->check(),
        'guard' => auth()->getDefaultDriver(),
        'user' => auth()->user(),
        'session' => session()->all(),
    ];
});

Route::get('/test-moonshine-auth', function() {
    return [
        'authenticated' => auth()->guard('moonshine')->check(),
        'user' => auth()->guard('moonshine')->user(),
        'session' => session()->all(),
    ];
});

// Тестовый маршрут для проверки доступа к ресурсу
Route::get('/test-resource-access', function() {
    $guards = [
        'web' => auth()->guard('web')->check(),
        'moonshine' => auth()->guard('moonshine')->check(),
    ];
    
    $user = [
        'web' => auth()->guard('web')->user(),
        'moonshine' => auth()->guard('moonshine')->user(),
    ];
    
    return [
        'authenticated' => $guards,
        'users' => [
            'web' => $user['web'] ? $user['web']->email : null,
            'moonshine' => $user['moonshine'] ? $user['moonshine']->email : null,
        ],
        'session' => session()->all(),
        'cookies' => request()->cookies->all(),
    ];
});

// Простой тест middleware
Route::get('/test-middleware', function() {
    return [
        'auth_moonshine_check' => auth()->guard('moonshine')->check(),
        'auth_web_check' => auth()->guard('web')->check(),
        'user_moonshine' => auth()->guard('moonshine')->user()?->email,
        'session' => session()->all(),
    ];
})->middleware('auth.moonshine');

// Тест без middleware
Route::get('/test-no-middleware', function() {
    return [
        'auth_moonshine_check' => auth()->guard('moonshine')->check(),
        'auth_web_check' => auth()->guard('web')->check(),
        'user_moonshine' => auth()->guard('moonshine')->user()?->email,
    ];
});

// Тест с полным именем класса
Route::get('/test-middleware-full', function() {
    return [
        'auth_moonshine_check' => auth()->guard('moonshine')->check(),
        'user_moonshine' => auth()->guard('moonshine')->user()?->email,
    ];
})->middleware(\MoonShine\Laravel\Http\Middleware\Authenticate::class);
