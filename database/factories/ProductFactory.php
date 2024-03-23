<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Company;
use App\Models\Discount;
use App\Models\Product;
use App\Models\Tax;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
         $companyIds = Company::where("id", '!=', null)->pluck('id')->toArray();
         $categoryIds = Category::where("id", '!=', null)->pluck('id')->toArray();
         $brandIds = Brand::where("id", '!=', null)->pluck('id')->toArray();
         $discountIds = Discount::where("id", '!=', null)->pluck('id')->toArray();
         $taxIds = Tax::where("id", '!=', null)->pluck('id')->toArray();
         $unitIds = Unit::where("id", '!=', null)->pluck('id')->toArray();
        return [
            'name' => $this->faker->name(),
            'isDiscountApplicable' => 1,
            'createdByUserId' => rand(1,6),
            'companyId'=>  $companyIds[array_rand($companyIds, 1)],
            'categoryId'=>  $categoryIds[array_rand($categoryIds, 1)],
            'brandId'=>  $brandIds[array_rand($brandIds, 1)],
            'discountId'=>  $discountIds[array_rand($discountIds, 1)],
            'taxId'=>  $taxIds[array_rand($taxIds, 1)],
            'unitId'=>  $unitIds[array_rand($unitIds, 1)],
            'alertQuantity'=>5
        ];
    }
}
