<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaxFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $rand = array(5,10);
        return [
            'title' => 'Vat '.$this->faker->title(),
            'type' => $this->faker->randomElement(['percentage','flat']),
            'createdByUserId' => $this->faker->randomNumber(1,6),
            'action' => $this->faker->randomElement(['exclusive','inclusive']),
            'amount' => $rand[array_rand($rand, 1)],
        ];
    }
}
