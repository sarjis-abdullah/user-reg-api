<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\Product;
use App\Models\Tax;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AdminSeeder::class);
        $this->call(ManagerSeeder::class);
        $this->call(CategorySeeder::class);
        Discount::factory(10)->create();
        Tax::factory(10)->create();
        Company::factory(10)->create();
        Brand::factory(10)->create();
        $this->call(UnitSeeder::class);
        Customer::factory(5)->create();
        Product::factory(10)->create();


    }
}
