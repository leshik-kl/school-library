<?php

declare(strict_types=1);

namespace App\MoonShine\Layouts;

use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\ColorManager\Palettes\PurplePalette;
use MoonShine\ColorManager\ColorManager;
use MoonShine\Contracts\ColorManager\ColorManagerContract;
use MoonShine\Contracts\ColorManager\PaletteContract;
use App\MoonShine\Resources\Author\AuthorResource;
use MoonShine\MenuManager\MenuItem;
use App\MoonShine\Resources\Publisher\PublisherResource;
use App\MoonShine\Resources\Category\CategoryResource;
use App\MoonShine\Resources\Book\BookResource;
use App\MoonShine\Resources\Reader\ReaderResource;
use App\MoonShine\Resources\Loan\LoanResource;
use App\MoonShine\Resources\Test\TestResource;

final class MoonShineLayout extends AppLayout
{
    /**
     * @var null|class-string<PaletteContract>
     */
    protected ?string $palette = PurplePalette::class;

    protected function assets(): array
    {
        return [
            ...parent::assets(),
        ];
    }

    protected function menu(): array
    {
        return [
            ...parent::menu(),
            MenuItem::make(AuthorResource::class, 'Authors'),
            MenuItem::make(PublisherResource::class, 'Publishers'),
            MenuItem::make(CategoryResource::class, 'Categories'),
            MenuItem::make(BookResource::class, 'Books'),
            MenuItem::make(ReaderResource::class, 'Readers'),
            MenuItem::make(LoanResource::class, 'Loans'),
            MenuItem::make(TestResource::class, 'Tests'),
        ];
    }

    /**
     * @param ColorManager $colorManager
     */
    protected function colors(ColorManagerContract $colorManager): void
    {
        parent::colors($colorManager);

        // $colorManager->primary('#00000');
    }
}
