<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Author;
use App\Models\Publisher;
use App\Models\Category;
use App\Models\Book;
use App\Models\Reader;
use App\Models\Loan;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Создаем издательства
        $publishers = Publisher::factory(10)->create();

        // Создаем авторов
        $authors = Author::factory(50)->create();

        // Создаем категории
        $categories = Category::factory(15)->create();

        // Создаем книги
        $books = Book::factory(200)
            ->recycle($publishers)
            ->recycle($authors)
            ->recycle($categories)
            ->create();

        // Создаем читателей
        $readers = Reader::factory(100)->create();

        // Создаем выдачи
        foreach ($readers->random(50) as $reader) {
            $book = $books->random();

            // Убедимся, что книга доступна
            if ($book->quantity_available > 0) {
                Loan::factory()
                    ->for($reader)
                    ->for($book)
                    ->create([
                        'loan_date' => Carbon::now()->subDays(rand(1, 30)),
                        'due_date' => Carbon::now()->addDays(rand(1, 14)),
                    ]);

                // Уменьшаем количество доступных книг
                $book->decrement('quantity_available');
            }
        }

        // Создаем несколько просроченных выдач
        foreach ($readers->random(10) as $reader) {
            $book = $books->random();

            if ($book->quantity_available > 0) {
                Loan::factory()
                    ->overdue()
                    ->for($reader)
                    ->for($book)
                    ->create([
                        'loan_date' => Carbon::now()->subDays(20),
                        'due_date' => Carbon::now()->subDays(5),
                    ]);

                $book->decrement('quantity_available');
            }
        }

        // Создаем несколько возвращенных книг
        foreach ($readers->random(30) as $reader) {
            $book = $books->random();

            Loan::factory()
                ->returned()
                ->for($reader)
                ->for($book)
                ->create([
                    'loan_date' => Carbon::now()->subDays(rand(30, 60)),
                    'due_date' => Carbon::now()->subDays(rand(15, 30)),
                ]);
        }
    }
}
