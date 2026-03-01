<?php
// database/factories/BookFactory.php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Publisher;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    protected $model = Book::class;

    public function definition(): array
    {
        return [
            'isbn' => $this->faker->unique()->isbn13,
            'title' => $this->faker->sentence(3),
            'subtitle' => $this->faker->optional()->sentence(2),
            'description' => $this->faker->optional()->paragraphs(3, true),
            'publisher_id' => Publisher::factory(),
            'publication_year' => $this->faker->optional()->numberBetween(1900, 2024),
            'pages' => $this->faker->optional()->numberBetween(50, 1000),
            'language' => $this->faker->randomElement(['ru', 'en', 'fr', 'de']),
            'cover_image' => null,
            'format' => $this->faker->optional()->randomElement(['Твердый переплет', 'Мягкая обложка', 'Электронная книга']),
            'price' => $this->faker->optional()->randomFloat(2, 100, 5000),
            'quantity_total' => $this->faker->numberBetween(1, 20),
            'quantity_available' => function (array $attributes) {
                return $attributes['quantity_total'];
            },
            'quantity_lost' => 0,
            'quantity_damaged' => 0,
            'location' => $this->faker->optional()->word,
            'shelf' => $this->faker->optional()->bothify('??-##'),
            'acquisition_date' => $this->faker->optional()->dateTimeBetween('-2 years', 'now'),
            'status' => 'available',
            'notes' => $this->faker->optional()->sentence,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Book $book) {
            // Прикрепляем случайных авторов
            $authors = \App\Models\Author::inRandomOrder()->limit(rand(1, 3))->get();
            $book->authors()->attach($authors);

            // Прикрепляем случайные категории
            $categories = \App\Models\Category::inRandomOrder()->limit(rand(1, 2))->get();
            $book->categories()->attach($categories);
        });
    }
}
