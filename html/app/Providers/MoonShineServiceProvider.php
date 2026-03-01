<?php
// app/Providers/MoonShineMenuProvider.php

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
use App\MoonShine\Resources\Test\TestResource;

class MoonShineMenuProvider extends ServiceProvider
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
                TestResource::class,
            ]);
        });

        // Добавляем JavaScript для исправления ссылок
        $this->addMenuFixScript();
    }

    protected function addMenuFixScript(): void
    {
        // Добавляем скрипт в секцию head через view composer
        view()->composer('moonshine::layouts.shared.head', function ($view) {
            $script = <<<JS
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Исправляем все ссылки в меню
                    setTimeout(function() {
                        document.querySelectorAll('.menu a').forEach(function(link) {
                            if (link.href.includes('index-page')) {
                                link.href = link.href.replace('index-page', 'crud');
                            }
                        });
                    }, 100);
                });
            </script>
            JS;

            echo $script;
        });
    }

    public function register(): void
    {
        //
    }
}
