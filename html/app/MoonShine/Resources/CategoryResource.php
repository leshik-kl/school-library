<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\Category;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use MoonShine\Laravel\Fields\Relationships\BelongsToMany;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Flex;

class CategoryResource extends ModelResource
{
    protected string $model = Category::class;

    protected string $title = 'Категории';

    protected string $column = 'name';

    public function fields(): array
    {
        return [
            Grid::make([
                Column::make([
                    Flex::make([
                        ID::make()->sortable(),
                        Text::make('Название', 'name')->required(),
                        Text::make('Slug', 'slug')->required()->unique(),
                    ]),

                    Textarea::make('Описание', 'description'),

                    Flex::make([
                        BelongsTo::make('Родительская категория', 'parent', resource: self::class)
                            ->nullable()
                            ->asyncSearch(),

                        HasMany::make('Дочерние категории', 'children', resource: self::class)
                            ->async(),
                    ]),

                    BelongsToMany::make('Книги', 'books', resource: \App\MoonShine\Resources\BookResource::class)
                        ->async(),
                ])->columnSpan(12),
            ]),
        ];
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:categories,slug,' . $this->getItem()?->id],
            'description' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'exists:categories,id'],
        ];
    }

    public function filters(): array
    {
        return [
            Text::make('Название', 'name'),
            BelongsTo::make('Родительская категория', 'parent', resource: self::class)
                ->asyncSearch(),
        ];
    }

    public function search(): array
    {
        return ['name', 'slug'];
    }
}
