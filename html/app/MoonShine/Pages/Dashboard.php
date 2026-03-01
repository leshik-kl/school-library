<?php

namespace App\MoonShine\Pages;

use MoonShine\Laravel\Pages\Dashboard as BaseDashboard;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Metrics\Wrapped\ValueMetric;
use App\Models\Book;
use App\Models\Loan;
use App\Models\Reader;

class Dashboard extends BaseDashboard
{
    public function components(): array
    {
        return [
            Grid::make([
                Column::make([
                    ValueMetric::make('Книги в библиотеке')
                        ->value(Book::sum('quantity_total'))
                        ->icon('book-open'),
                ])->columnSpan(3),

                Column::make([
                    ValueMetric::make('Доступно книг')
                        ->value(Book::sum('quantity_available'))
                        ->icon('check-circle'),
                ])->columnSpan(3),

                Column::make([
                    ValueMetric::make('Активных читателей')
                        ->value(Reader::where('status', 'active')->count())
                        ->icon('users'),
                ])->columnSpan(3),

                Column::make([
                    ValueMetric::make('Книг на руках')
                        ->value(Loan::whereIn('status', ['active', 'overdue'])->count())
                        ->icon('bookmark'),
                ])->columnSpan(3),
            ]),
        ];
    }
}
