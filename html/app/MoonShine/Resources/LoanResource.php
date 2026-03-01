<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\Loan;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Textarea;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Flex;

class LoanResource extends ModelResource
{
    protected string $model = Loan::class;

    protected string $title = 'Выдачи';

    protected string $column = 'loan_number';

    protected array $with = ['book', 'reader'];

    public function fields(): array
    {
        return [
            Grid::make([
                Column::make([
                    Flex::make([
                        ID::make()->sortable(),
                        Text::make('Номер выдачи', 'loan_number')->sortable()->required(),
                    ]),

                    Flex::make([
                        BelongsTo::make('Книга', 'book', resource: \App\MoonShine\Resources\BookResource::class)
                            ->required()
                            ->asyncSearch(),

                        BelongsTo::make('Читатель', 'reader', resource: \App\MoonShine\Resources\ReaderResource::class)
                            ->required()
                            ->asyncSearch(),
                    ]),

                    Flex::make([
                        Date::make('Дата выдачи', 'loan_date')
                            ->required()
                            ->format('d.m.Y'),

                        Date::make('Срок возврата', 'due_date')
                            ->required()
                            ->format('d.m.Y'),

                        Date::make('Дата возврата', 'return_date')
                            ->format('d.m.Y')
                            ->nullable(),
                    ]),

                    Flex::make([
                        Select::make('Статус', 'status')
                            ->options([
                                'active' => 'Активна',
                                'returned' => 'Возвращена',
                                'overdue' => 'Просрочена',
                                'lost' => 'Утеряна',
                                'damaged' => 'Повреждена',
                            ])
                            ->required(),

                        Number::make('Штраф', 'fine_amount')
                            ->step(0.01)
                            ->min(0)
                            ->default(0),

                        Select::make('Штраф оплачен', 'fine_paid')
                            ->options([
                                0 => 'Нет',
                                1 => 'Да',
                            ])
                            ->default(0),
                    ]),

                    Textarea::make('Примечания', 'notes')
                        ->hideOnIndex(),
                ])->columnSpan(12),
            ]),
        ];
    }

    public function rules(): array
    {
        return [
            'loan_number' => ['required', 'string', 'unique:loans,loan_number,' . $this->getItem()?->id],
            'book_id' => ['required', 'exists:books,id'],
            'reader_id' => ['required', 'exists:readers,id'],
            'loan_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after:loan_date'],
            'return_date' => ['nullable', 'date'],
            'status' => ['required', 'in:active,returned,overdue,lost,damaged'],
            'fine_amount' => ['numeric', 'min:0'],
            'fine_paid' => ['boolean'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function filters(): array
    {
        return [
            Text::make('Номер выдачи', 'loan_number'),
            Select::make('Статус', 'status')
                ->options([
                    'active' => 'Активна',
                    'returned' => 'Возвращена',
                    'overdue' => 'Просрочена',
                    'lost' => 'Утеряна',
                    'damaged' => 'Повреждена',
                ]),
            Date::make('Дата выдачи', 'loan_date'),
            Date::make('Срок возврата', 'due_date'),
            BelongsTo::make('Читатель', 'reader', resource: \App\MoonShine\Resources\ReaderResource::class)
                ->asyncSearch(),
        ];
    }

    public function search(): array
    {
        return ['loan_number'];
    }

    public function metrics(): array
    {
        return [
            ValueMetric::make('Активных выдач')
                ->value(Loan::whereIn('status', ['active', 'overdue'])->count()),

            ValueMetric::make('Просрочено')
                ->value(Loan::where('status', 'overdue')->count()),

            ValueMetric::make('Возвращено сегодня')
                ->value(Loan::whereDate('return_date', today())->count()),
        ];
    }
}
