<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class BrandFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Brand::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
//
        $companyIds = Company::where("id", '!=', null)->pluck('id')->toArray();
        return [
            'name' => $this->faker->name(),
            'origin' => $this->faker->randomElement(['Cumilla','Chittagong','Dhaka']),
            'createdByUserId' => rand(1,6),
            'companyId'=>  $companyIds[array_rand($companyIds, 1)]

        ];
    }
}
