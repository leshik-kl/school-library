<?php

namespace App\MoonShine\Resources;

use App\Models\Book;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Enum;
use MoonShine\UI\Fields\HasMany;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Fields\Relationships\BelongsToMany;

class BookResource extends ModelResource
{
    protected string $model = Book::class;

    protected string $title = 'Книги';

    protected string $column = 'title';

    protected array $with = ['authors', 'publisher', 'categories'];

    public function fields(): array
    {
        return [
            Grid::make([
                Column::make([
                    Tabs::make([
                        Tab::make('Основная информация', [
                            Flex::make([
                                ID::make()->sortable(),
                                Text::make('ISBN', 'isbn')->sortable()->required(),
                                Text::make('Название', 'title')->required()->sortable(),
                            ]),

                            Text::make('Подзаголовок', 'subtitle'),
                            Textarea::make('Описание', 'description')->hideOnIndex(),

                            BelongsTo::make('Издательство', 'publisher',
                                resource: \App\MoonShine\Resources\PublisherResource::class)
                                ->asyncSearch()
                                ->nullable(),

                            Flex::make([
                                Number::make('Год издания', 'publication_year')->min(1000)->max(date('Y')),
                                Number::make('Страниц', 'pages')->min(1),
                                Text::make('Язык', 'language')->default('ru'),
                            ]),

                            Flex::make([
                                Text::make('Формат', 'format'),
                                Number::make('Цена', 'price')->step(0.01)->min(0),
                            ]),
                        ]),

                        Tab::make('Учет', [
                            Flex::make([
                                Number::make('Всего', 'quantity_total')->min(0)->default(1)->required(),
                                Number::make('Доступно', 'quantity_available')->min(0)->default(1)->required(),
                            ]),

                            Flex::make([
                                Number::make('Потеряно', 'quantity_lost')->min(0)->default(0),
                                Number::make('Повреждено', 'quantity_damaged')->min(0)->default(0),
                            ]),

                            Flex::make([
                                Text::make('Место хранения', 'location'),
                                Text::make('Полка', 'shelf'),
                            ]),

                            Date::make('Дата поступления', 'acquisition_date')->format('d.m.Y'),

                            Enum::make('Статус', 'status')
                                ->options([
                                    'available' => 'Доступна',
                                    'reserved' => 'Зарезервирована',
                                    'repair' => 'На ремонте',
                                    'lost' => 'Утеряна',
                                ])
                                ->default('available'),
                        ]),

                        Tab::make('Медиа', [
                            Image::make('Обложка', 'cover_image')
                                ->disk('public')
                                ->dir('books/covers')
                                ->allowedExtensions(['jpg', 'png', 'jpeg', 'gif'])
                                ->removable(),
                        ]),

                        Tab::make('Заметки', [
                            Textarea::make('Примечания', 'notes'),
                        ]),
                    ]),
                ])->columnSpan(8),

                Column::make([
                    BelongsToMany::make('Авторы', 'authors',
                        resource: \App\MoonShine\Resources\AuthorResource::class)
                        ->selectMode()
                        ->asyncSearch()
                        ->required(),

                    BelongsToMany::make('Категории', 'categories',
                        resource: \App\MoonShine\Resources\CategoryResource::class)
                        ->selectMode()
                        ->asyncSearch(),
                ])->columnSpan(4),
            ]),

            HasMany::make('Выдачи', 'loans',
                resource: \App\MoonShine\Resources\LoanResource::class)
                ->async(),
        ];
    }

    public function rules(): array
    {
        return [
            'isbn' => ['required', 'string', 'unique:books,isbn,' . $this->getItem()?->id, 'max:13'],
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'publisher_id' => ['nullable', 'exists:publishers,id'],
            'publication_year' => ['nullable', 'integer', 'min:1000', 'max:' . date('Y')],
            'pages' => ['nullable', 'integer', 'min:1'],
            'language' => ['nullable', 'string', 'max:3'],
            'cover_image' => ['nullable', 'image', 'max:2048'],
            'format' => ['nullable', 'string', 'max:50'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'quantity_total' => ['required', 'integer', 'min:0'],
            'quantity_available' => ['required', 'integer', 'min:0'],
            'quantity_lost' => ['required', 'integer', 'min:0'],
            'quantity_damaged' => ['required', 'integer', 'min:0'],
            'location' => ['nullable', 'string', 'max:100'],
            'shelf' => ['nullable', 'string', 'max:50'],
            'acquisition_date' => ['nullable', 'date'],
            'status' => ['required', 'in:available,reserved,repair,lost'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function filters(): array
    {
        return [
            Text::make('Название', 'title'),
            Text::make('ISBN', 'isbn'),
            Enum::make('Статус', 'status')
                ->options([
                    'available' => 'Доступна',
                    'reserved' => 'Зарезервирована',
                    'repair' => 'На ремонте',
                    'lost' => 'Утеряна',
                ]),
            BelongsTo::make('Издательство', 'publisher',
                resource: \App\MoonShine\Resources\PublisherResource::class)
                ->asyncSearch(),
        ];
    }

    public function search(): array
    {
        return ['title', 'isbn', 'subtitle'];
    }

    public function metrics(): array
    {
        return [
            ValueMetric::make('Всего книг')->value(Book::count()),
            ValueMetric::make('Доступно книг')->value(Book::where('quantity_available', '>', 0)->count()),
            ValueMetric::make('На руках')->value(Book::whereHas('loans', function($q) {
                $q->whereIn('status', ['active', 'overdue']);
            })->count()),
            ValueMetric::make('Утеряно')->value(Book::sum('quantity_lost')),
        ];
    }
}
