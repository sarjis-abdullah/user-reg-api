<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UnitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
//        $name = $this->faker->randomElement(['kg','piece','litre']);
        return [
//            'name' => $name,
//            'isFraction' => $name == 'piece' ? 0 : 1
        ];
    }
}
