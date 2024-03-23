<?php

namespace Database\Factories;

use App\Models\Discount;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class DiscountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Discount::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $rand = array(5,10);
        return [
            'title' => 'Discount '.$this->faker->title(),
            'type' => $this->faker->randomElement(['percentage','flat']),
            'createdByUserId' => $this->faker->randomNumber(1,6),
            'amount' => $rand[array_rand($rand, 1)],
            'startDate' => Carbon::now(),
            'endDate' => Carbon::now()
        ];
    }
}
