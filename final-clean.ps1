# final-clean.ps1
Write-Host "🧹 Финальная чистка провайдеров" -ForegroundColor Green

cd C:\Users\lexa\Desktop\school-library

Write-Host "`n🗑️ Удаление лишних провайдеров..." -ForegroundColor Yellow
docker compose exec php bash -c "cd /var/www/html && rm -f app/Providers/MoonShineMenuProvider.php app/Providers/MoonShineRouteServiceProvider.php app/Providers/MenuServiceProvider.php"

Write-Host "`n📝 Обновление config/app.php..." -ForegroundColor Yellow
docker compose exec php bash -c "cat > /var/www/html/config/app.php << 'EOF'
<?php

return [
    'name' => env('APP_NAME', 'School Library'),
    'env' => env('APP_ENV', 'local'),
    'debug' => (bool) env('APP_DEBUG', true),
    'url' => env('APP_URL', 'http://localhost'),
    'timezone' => 'UTC',
    'locale' => 'ru',
    'fallback_locale' => 'en',
    'faker_locale' => 'ru_RU',
    'key' => env('APP_KEY'),
    'cipher' => 'AES-256-CBC',

    'providers' => [
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,

        MoonShine\Laravel\Providers\MoonShineServiceProvider::class,

        App\Providers\AppServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
    ],
];
EOF
"

Write-Host "`n📝 Обновление config/auth.php..." -ForegroundColor Yellow
docker compose exec php bash -c "cat > /var/www/html/config/auth.php << 'EOF'
<?php

return [
    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
        'moonshine' => [
            'driver' => 'session',
            'provider' => 'moonshine',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],
        'moonshine' => [
            'driver' => 'eloquent',
            'model' => MoonShine\Laravel\Models\MoonshineUser::class,
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,
];
EOF
"

Write-Host "`n🔄 Очистка кэша..." -ForegroundColor Yellow
docker compose exec php bash -c "cd /var/www/html && composer dump-autoload && php artisan optimize:clear"

Write-Host "`n🔄 Перезапуск PHP..." -ForegroundColor Yellow
docker compose restart php

Write-Host "`n✅ Готово! Теперь должно работать." -ForegroundColor Green
Write-Host "1. Откройте http://localhost/admin/login в режиме инкогнито" -ForegroundColor Cyan
Write-Host "2. Войдите с admin@library.local / password" -ForegroundColor Cyan
Write-Host "3. Проверьте ресурс: http://localhost/admin/resource/author-resource/crud" -ForegroundColor Cyan