<?php
// database/factories/AuthorFactory.php

namespace Database\Factories;

use App\Models\Author;
use Illuminate\Database\Eloquent\Factories\Factory;

class AuthorFactory extends Factory
{
    protected $model = Author::class;

    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'middle_name' => $this->faker->optional()->firstName,
            'biography' => $this->faker->optional()->paragraphs(3, true),
            'birth_date' => $this->faker->optional()->date('Y-m-d', '-30 years'),
            'death_date' => $this->faker->optional(0.2)->date('Y-m-d', '-5 years'),
            'photo' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
