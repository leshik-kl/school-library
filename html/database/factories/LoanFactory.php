<?php
// database/factories/LoanFactory.php

namespace Database\Factories;

use App\Models\Loan;
use App\Models\Book;
use App\Models\Reader;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoanFactory extends Factory
{
    protected $model = Loan::class;

    public function definition(): array
    {
        $loanDate = $this->faker->dateTimeBetween('-3 months', 'now');
        $dueDate = clone $loanDate;
        $dueDate->modify('+14 days');

        return [
            'book_id' => Book::factory(),
            'reader_id' => Reader::factory(),
            'loan_date' => $loanDate,
            'due_date' => $dueDate,
            'return_date' => null,
            'status' => 'active',
            'notes' => $this->faker->optional()->sentence,
            'fine_amount' => 0,
            'fine_paid' => false,
            'issued_by' => 1,
            'returned_by' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function returned()
    {
        return $this->state(function (array $attributes) {
            $returnDate = $this->faker->dateTimeBetween($attributes['loan_date'], $attributes['due_date']);

            return [
                'return_date' => $returnDate,
                'status' => 'returned',
            ];
        });
    }

    public function overdue()
    {
        return $this->state(function (array $attributes) {
            return [
                'due_date' => $this->faker->dateTimeBetween('-10 days', '-1 day'),
                'status' => 'overdue',
            ];
        });
    }

    public function lost()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'lost',
                'fine_amount' => $this->faker->randomFloat(2, 500, 2000),
            ];
        });
    }
}
