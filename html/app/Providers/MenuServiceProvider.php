<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use MoonShine\Laravel\DependencyInjection\MoonShine;
use MoonShine\MenuManager\MenuGroup;
use MoonShine\MenuManager\MenuItem;
use App\MoonShine\Resources\Author\AuthorResource;
use App\MoonShine\Resources\Book\BookResource;
use App\MoonShine\Resources\Category\CategoryResource;
use App\MoonShine\Resources\Loan\LoanResource;
use App\MoonShine\Resources\Publisher\PublisherResource;
use App\MoonShine\Resources\Reader\ReaderResource;

class MenuServiceProvider extends ServiceProvider
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

        // Регистрируем меню через конфиг
        $this->registerMenuConfig();
    }

    protected function registerMenuConfig(): void
    {
        // Сохраняем меню в конфигурацию
        config(['moonshine.menu' => [
            [
                'title' => 'Библиотека',
                'items' => [
                    ['title' => 'Книги', 'resource' => BookResource::class],
                    ['title' => 'Авторы', 'resource' => AuthorResource::class],
                    ['title' => 'Категории', 'resource' => CategoryResource::class],
                    ['title' => 'Издательства', 'resource' => PublisherResource::class],
                ],
            ],
            [
                'title' => 'Читатели',
                'items' => [
                    ['title' => 'Читатели', 'resource' => ReaderResource::class],
                    ['title' => 'Выдачи', 'resource' => LoanResource::class],
                ],
            ],
        ]]);
    }

    public function register(): void
    {
        //
    }
}
