<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Customer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' =>$this->faker->email(),
            'type' => $this->faker->randomElement(['regular','walk-in']),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'createdByUserId' => rand(1,6),
        ];
    }
}
