<?php

namespace App\MoonShine\Resources;

use App\Models\Author;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\HasMany;
use MoonShine\Laravel\Fields\Relationships\BelongsToMany;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;

class AuthorResource extends ModelResource
{
    protected string $model = Author::class;

    protected string $title = 'Авторы';

    protected string $column = 'full_name';

    protected array $with = ['books'];

    public function fields(): array
    {
        return [
            Grid::make([
                Column::make([
                    Flex::make([
                        ID::make()->sortable(),
                        Text::make('Фамилия', 'last_name')->required()->sortable(),
                        Text::make('Имя', 'first_name')->required()->sortable(),
                        Text::make('Отчество', 'middle_name'),
                    ]),

                    Flex::make([
                        Date::make('Дата рождения', 'birth_date')->format('d.m.Y'),
                        Date::make('Дата смерти', 'death_date')->format('d.m.Y'),
                    ]),

                    Textarea::make('Биография', 'biography')->hideOnIndex(),
                    Image::make('Фото', 'photo')->disk('public')->dir('authors'),
                ])->columnSpan(8),

                Column::make([
                    BelongsToMany::make('Книги', 'books', resource: \App\MoonShine\Resources\BookResource::class)
                        ->selectMode()
                        ->asyncSearch(),
                ])->columnSpan(4),
            ]),

            Tabs::make([
                Tab::make('Книги автора', [
                    HasMany::make('Книги', 'books', resource: \App\MoonShine\Resources\BookResource::class)
                        ->creatable()
                        ->async(),
                ]),
            ]),
        ];
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'biography' => ['nullable', 'string'],
            'birth_date' => ['nullable', 'date'],
            'death_date' => ['nullable', 'date', 'after:birth_date'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ];
    }

    public function filters(): array
    {
        return [
            Text::make('Фамилия', 'last_name'),
            Date::make('Дата рождения', 'birth_date'),
        ];
    }

    public function search(): array
    {
        return ['last_name', 'first_name', 'middle_name'];
    }
}
