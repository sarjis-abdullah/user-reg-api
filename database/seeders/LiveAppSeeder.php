<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\AppSetting;
use App\Models\Customer;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserRole;
use Illuminate\Database\Seeder;

class LiveAppSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@reformedtech.org',
            'phone' => '01303094897',
            'locale' => 'en',
            'password' => 'password',
            'isActive' => 1,
        ]);

        $userRole = UserRole::factory()->create([
            'userId' => $user->id,
            'roleId' => 1
        ]);

        Admin::factory()->create([
            'userId' => $user->id,
            'userRoleId' => $userRole->id,
            'level' => Admin::LEVEL_SUPER
        ]);

        UserProfile::factory()->create([
            'userId' => $user->id,
            'gender' => UserProfile::GENDER_MALE
        ]);

        Customer::factory()->create([
            'name' => 'Walk in customer',
            'email' => 'walkincustomer@reformedtech.org',
            'phone' => '01303094897',
            'type' => Customer::TYPE_WALK_IN,
            'status' => 'active',
            'createdByUserId' => $user->id,
        ]);

        //Create pos general setting
        AppSetting::factory()->create([
            'type'=> AppSetting::TYPE_GENERAL,
            'settings' => "{
                \"businessName\":\"\",
                \"currency\":\"taka\",
                \"currencySymbol\":\"৳\",
                \"phone\":\"\",
                \"address\":\"\",
                \"posType\":\"Shop\"
            }"
        ]);
    }
}
