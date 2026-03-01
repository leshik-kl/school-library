<?php

use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use MoonShine\ColorManager\Palettes\PurplePalette;
use MoonShine\Crud\Forms\FiltersForm;
use MoonShine\Crud\Forms\LoginForm;
use MoonShine\Laravel\Exceptions\MoonShineNotFoundException;
use MoonShine\Laravel\Http\Middleware\Authenticate;
use MoonShine\Laravel\Http\Middleware\ChangeLocale;
use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\Laravel\Models\MoonshineUser;
use MoonShine\Laravel\Pages\Dashboard;
use MoonShine\Laravel\Pages\ErrorPage;
use MoonShine\Laravel\Pages\LoginPage;
use MoonShine\Laravel\Pages\ProfilePage;
use App\MoonShine\Resources\AuthorResource;
use App\MoonShine\Resources\BookResource;
use App\MoonShine\Resources\CategoryResource;
use App\MoonShine\Resources\LoanResource;
use App\MoonShine\Resources\PublisherResource;
use App\MoonShine\Resources\ReaderResource;


return [
    'title' => env('MOONSHINE_TITLE', 'MoonShine'),
    'logo' => '/vendor/moonshine/logo-small.svg',
    'logo_small' => '/vendor/moonshine/logo-small.svg',

    'favicons' => [
        'apple-touch' => '/vendor/moonshine/apple-touch-icon.png',
        '32' => '/vendor/moonshine/favicon-32x32.png',
        '16' => '/vendor/moonshine/favicon-16x16.png',
        'safari-pinned-tab' => '/vendor/moonshine/safari-pinned-tab.svg',
    ],

    'use_migrations' => true,
    'use_notifications' => true,
    'use_database_notifications' => true,
    'use_routes' => true,
    'use_profile' => true,

    'domain' => env('MOONSHINE_DOMAIN'),
    'prefix' => env('MOONSHINE_ROUTE_PREFIX', 'admin'),
    'page_prefix' => env('MOONSHINE_PAGE_PREFIX', 'page'),
    'resource_prefix' => env('MOONSHINE_RESOURCE_PREFIX', 'resource'),
    'home_route' => 'moonshine.index',

    'not_found_exception' => MoonShineNotFoundException::class,

    'middleware' => [
        EncryptCookies::class,
        AddQueuedCookiesToResponse::class,
        StartSession::class,
        AuthenticateSession::class,
        ShareErrorsFromSession::class,
        VerifyCsrfToken::class,
        SubstituteBindings::class,
        ChangeLocale::class,
        \App\Http\Middleware\RedirectIndexPage::class,
    ],

    'disk' => 'public',
    'disk_options' => [],
    'cache' => 'file',

    'auth' => [
        'enabled' => true,
        'guard' => 'moonshine',
        'model' => MoonshineUser::class,
        'middleware' => [
            Authenticate::class,
        ],
        'pipelines' => [],
    ],

    'route' => [
        'prefix' => 'admin',
        'middleware' => ['web', 'auth.moonshine'],
        'home' => 'moonshine.index',
    ],

    'user_fields' => [
        'username' => 'email',
        'password' => 'password',
        'name' => 'name',
        'avatar' => 'avatar',
    ],

    'layout' => App\MoonShine\Layouts\MoonShineLayout::class,
    'palette' => PurplePalette::class,

    'forms' => [
        'login' => LoginForm::class,
        'filters' => FiltersForm::class,
    ],

    'pages' => [
        'dashboard' => App\MoonShine\Pages\Dashboard::class,
        'profile' => ProfilePage::class,
        'login' => LoginPage::class,
        'error' => ErrorPage::class,
    ],

    'locale' => 'en',
    'locale_key' => ChangeLocale::KEY,
    'locales' => [],

    'resources' => [
        App\MoonShine\Resources\AuthorResource::class,
        App\MoonShine\Resources\BookResource::class,
        App\MoonShine\Resources\CategoryResource::class,
        App\MoonShine\Resources\LoanResource::class,
        App\MoonShine\Resources\PublisherResource::class,
        App\MoonShine\Resources\ReaderResource::class,
    ],

    'menu' => [
        [
            'label' => 'Библиотека',
            'items' => [
                ['label' => 'Книги', 'resource' => BookResource::class],
                ['label' => 'Авторы', 'resource' => AuthorResource::class],
                ['label' => 'Категории', 'resource' => CategoryResource::class],
                ['label' => 'Издательства', 'resource' => PublisherResource::class],
            ],
        ],
        [
            'label' => 'Читатели',
            'items' => [
                ['label' => 'Читатели', 'resource' => ReaderResource::class],
                ['label' => 'Выдачи', 'resource' => LoanResource::class],
            ],
        ],
    ],
];
