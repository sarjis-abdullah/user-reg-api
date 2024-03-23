<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::factory(2)->create();
//        Category::create([
//            'name'=> 'box',
//            'createdByUserId'=> 1,
//            'code'=> '8ctgry',
//            'details'=> 'this is a box category'
//        ]);
    }
}
