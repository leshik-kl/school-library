<?php

namespace App\MoonShine\Resources;

use App\Models\Publisher;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\HasMany;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Components\Layout\Column;

class PublisherResource extends ModelResource
{
    protected string $model = Publisher::class;

    protected string $title = 'Издательства';

    protected string $column = 'name';

    public function fields(): array
    {
        return [
            Grid::make([
                Column::make([
                    ID::make()->sortable(),
                    Text::make('Название', 'name')->required()->sortable(),
                    Text::make('Адрес', 'address'),
                    Text::make('Телефон', 'phone'),
                    Text::make('Email', 'email'),
                    Text::make('Веб-сайт', 'website'),
                ])->columnSpan(8),

                Column::make([
                    HasMany::make('Книги', 'books', resource: \App\MoonShine\Resources\BookResource::class)
                        ->async(),
                ])->columnSpan(4),
            ]),
        ];
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
        ];
    }
}
