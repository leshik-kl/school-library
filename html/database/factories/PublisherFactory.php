<?php
// database/factories/PublisherFactory.php

namespace Database\Factories;

use App\Models\Publisher;
use Illuminate\Database\Eloquent\Factories\Factory;

class PublisherFactory extends Factory
{
    protected $model = Publisher::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'address' => $this->faker->optional()->address,
            'phone' => $this->faker->optional()->phoneNumber,
            'email' => $this->faker->optional()->companyEmail,
            'website' => $this->faker->optional()->url,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
