<?php
// database/factories/ReaderFactory.php

namespace Database\Factories;

use App\Models\Reader;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReaderFactory extends Factory
{
    protected $model = Reader::class;

    public function definition(): array
    {
        $firstName = $this->faker->firstName;
        $lastName = $this->faker->lastName;
        $email = strtolower($firstName . '.' . $lastName . '@' . $this->faker->freeEmailDomain);

        // Создаем объект DateTime для registration_date
        $registrationDate = $this->faker->dateTimeBetween('-1 year', 'now');

        return [
            'library_card_number' => 'LIB-' . date('Y') . '-' . str_pad($this->faker->unique()->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'middle_name' => $this->faker->optional()->firstName,
            'birth_date' => $this->faker->date('Y-m-d', '-15 years'),
            'class' => $this->faker->randomElement(['5', '6', '7', '8', '9', '10', '11']),
            'phone' => $this->faker->optional()->phoneNumber,
            'email' => $email,
            'address' => $this->faker->optional()->address,
            'photo' => null,
            'registration_date' => $registrationDate,
            'expiry_date' => function (array $attributes) use ($registrationDate) {
                // Используем объект DateTime напрямую
                $expiryDate = clone $registrationDate;
                $expiryDate->modify('+1 year');
                return $expiryDate->format('Y-m-d');
            },
            'status' => 'active',
            'notes' => $this->faker->optional()->sentence,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    // Добавим дополнительные состояния для разных типов читателей
    public function teacher()
    {
        return $this->state(function (array $attributes) {
            return [
                'class' => 'teacher',
            ];
        });
    }

    public function staff()
    {
        return $this->state(function (array $attributes) {
            return [
                'class' => 'staff',
            ];
        });
    }

    public function expired()
    {
        return $this->state(function (array $attributes) {
            $registrationDate = $this->faker->dateTimeBetween('-2 years', '-1 year');
            $expiryDate = clone $registrationDate;
            $expiryDate->modify('+1 year');

            return [
                'registration_date' => $registrationDate,
                'expiry_date' => $expiryDate,
                'status' => 'expired',
            ];
        });
    }

    public function blocked()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'blocked',
            ];
        });
    }
}
