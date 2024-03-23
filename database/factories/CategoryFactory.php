<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'createdByUserId'=>$this->faker->numberBetween(0,5),
            'name' => $this->faker->name(),
            'code' => $this->faker->numberBetween(1000, 9999)
        ];
    }
}
