<?php

namespace App\Providers;

use App\MoonShine\Resources\Author\AuthorResource;
use App\MoonShine\Resources\Book\BookResource;
use App\MoonShine\Resources\Category\CategoryResource;
use App\MoonShine\Resources\Loan\LoanResource;
use App\MoonShine\Resources\Publisher\PublisherResource;
use App\MoonShine\Resources\Reader\ReaderResource;
use Illuminate\Support\ServiceProvider;
use MoonShine\Laravel\DependencyInjection\MoonShine;
use MoonShine\Laravel\DependencyInjection\MoonShineConfigurator;
use MoonShine\MenuManager\MenuGroup;
use MoonShine\MenuManager\MenuItem;
use MoonShine\Laravel\Http\Middleware\Authenticate;

class MoonShineServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Регистрируем ресурсы
        $this->callAfterResolving(MoonShine::class, function (MoonShine $moonshine) {
            $moonshine->resources([
                AuthorResource::class,
                BookResource::class,
                CategoryResource::class,
                LoanResource::class,
                PublisherResource::class,
                ReaderResource::class,
            ]);
        });

        // Регистрируем меню через конфигурацию
        $this->registerMenu();
    }

    protected function registerMenu(): void
    {
        $this->app->booted(function () {
            $moonshine = app(MoonShine::class);

            // В Moonshine 4.8 меню регистрируется через конфиг или через MenuManager
            $menu = [
                MenuGroup::make('Библиотека', [
                    MenuItem::make('Книги', BookResource::class),
                    MenuItem::make('Авторы', AuthorResource::class),
                    MenuItem::make('Категории', CategoryResource::class),
                    MenuItem::make('Издательства', PublisherResource::class),
                ]),

                MenuGroup::make('Читатели', [
                    MenuItem::make('Читатели', ReaderResource::class),
                    MenuItem::make('Выдачи', LoanResource::class),
                ]),
            ];

            // Сохраняем меню в конфиг
            config(['moonshine.menu' => $menu]);
        });
    }

    public function register(): void
    {
        //
    }
}
