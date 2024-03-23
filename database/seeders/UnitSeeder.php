<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            [
                'name' => 'kg',
                'isFraction' => 1,
            ],
            [
                'name' => 'piece',
                'isFraction' => 0,
            ],
            [
                'name' => 'litre',
                'isFraction' => 1,
            ]
        ];
        foreach ($array as $item){
            Unit::create($item);
        }
    }
}
