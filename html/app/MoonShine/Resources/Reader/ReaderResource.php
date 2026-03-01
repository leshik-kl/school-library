<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Reader;

use App\Models\Reader;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Email;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\HasMany;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Flex;

class ReaderResource extends ModelResource
{
    protected string $model = Reader::class;

    protected string $title = 'Читатели';

    protected string $column = 'full_name';

    public function fields(): array
    {
        return [
            Grid::make([
                Column::make([
                    Flex::make([
                        ID::make()->sortable(),
                        Text::make('Номер читательского билета', 'library_card_number')
                            ->required()
                            ->sortable(),
                    ]),

                    Flex::make([
                        Text::make('Фамилия', 'last_name')->required(),
                        Text::make('Имя', 'first_name')->required(),
                        Text::make('Отчество', 'middle_name'),
                    ]),

                    Flex::make([
                        Date::make('Дата рождения', 'birth_date')->required(),
                        Select::make('Класс', 'class')->options([
                            '1' => '1 класс', '2' => '2 класс', '3' => '3 класс', '4' => '4 класс',
                            '5' => '5 класс', '6' => '6 класс', '7' => '7 класс', '8' => '8 класс',
                            '9' => '9 класс', '10' => '10 класс', '11' => '11 класс',
                            'teacher' => 'Учитель', 'staff' => 'Сотрудник',
                        ])->required(),
                    ]),

                    Flex::make([
                        Text::make('Телефон', 'phone'),
                        Email::make('Email', 'email')->required(),
                    ]),

                    Text::make('Адрес', 'address'),
                    Image::make('Фото', 'photo')->disk('public')->dir('readers'),

                    Flex::make([
                        Date::make('Дата регистрации', 'registration_date')->required(),
                        Date::make('Дата окончания', 'expiry_date')->required(),
                    ]),

                    Select::make('Статус', 'status')->options([
                        'active' => 'Активен',
                        'blocked' => 'Заблокирован',
                        'expired' => 'Просрочен',
                    ])->required(),

                    Text::make('Примечания', 'notes')->hideOnIndex(),
                ])->columnSpan(8),

                Column::make([
                    HasMany::make('Выдачи', 'loans', resource: \App\MoonShine\Resources\Loan\LoanResource::class)
                        ->async(),
                ])->columnSpan(4),
            ]),
        ];
    }

    public function rules(): array
    {
        return [
            'library_card_number' => ['required', 'string', 'unique:readers,library_card_number,' . $this->getItem()?->id],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['required', 'date'],
            'class' => ['required', 'string'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'email', 'unique:readers,email,' . $this->getItem()?->id],
            'address' => ['nullable', 'string', 'max:500'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'registration_date' => ['required', 'date'],
            'expiry_date' => ['required', 'date'],
            'status' => ['required', 'in:active,blocked,expired'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function filters(): array
    {
        return [
            Text::make('Номер билета', 'library_card_number'),
            Text::make('Фамилия', 'last_name'),
            Email::make('Email', 'email'),
            Select::make('Статус', 'status')->options([
                'active' => 'Активен',
                'blocked' => 'Заблокирован',
                'expired' => 'Просрочен',
            ]),
            Select::make('Класс', 'class')->options([
                '1' => '1 класс', '2' => '2 класс', '3' => '3 класс', '4' => '4 класс',
                '5' => '5 класс', '6' => '6 класс', '7' => '7 класс', '8' => '8 класс',
                '9' => '9 класс', '10' => '10 класс', '11' => '11 класс',
                'teacher' => 'Учитель', 'staff' => 'Сотрудник',
            ]),
        ];
    }

    public function search(): array
    {
        return ['library_card_number', 'first_name', 'last_name', 'email'];
    }
}
