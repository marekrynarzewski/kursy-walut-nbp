<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory
 */
class CurrencyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->currencyCode(),
            'currencyCode' => $this->faker->currencyCode(),
            'exchangeRate' => $this->faker->numberBetween(),
        ];
    }
}
