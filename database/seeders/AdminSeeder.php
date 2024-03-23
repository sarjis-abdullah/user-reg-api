<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Branch;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserRole;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $admins = [
            0 => [
                'name' => 'Super admin',
                'email' => 'superadmin@reformedtech.org',
                'phone' => "01303094897",
                'roleId' => 1,
                'level' => Admin::LEVEL_SUPER
            ],
            1 => [
                'name' => 'Standard admin',
                'email' => 'standardadmin@reformedtech.org',
                'phone' => "01822270500",
                'roleId' => 2,
                'level' => Admin::LEVEL_STANDARD
            ],
            2 => [
                'name' => 'Limited admin',
                'email' => 'limitedadmin@reformedtech.org',
                'phone' => "01521484414",
                'roleId' => 3,
                'level' => Admin::LEVEL_LIMITED
            ],

        ];

        $branch = Branch::factory()->create([
            'name' => 'RT Admin Branch',
            'address' => 'Dhaka, Bangladesh',
            'phone' => '01303-094897',
        ]);

        foreach ($admins as $key => $value) {

            $user = User::factory()->create([
                'name' => $value['name'],
                'email' => $value['email'],
                'phone' => $value['phone'],
                'locale' => 'en',
                'password' => 'password',
                'isActive' => 1,
            ]);

            $userRole = UserRole::factory()->create([
                'userId' => $user->id,
                'roleId' => $value['roleId'],
                'branchId' => $branch->id,
            ]);

            Admin::factory()->create([
                'userId' => $user->id,
                'userRoleId' => $userRole->id,
                'level' => $value['level']
            ]);

            UserProfile::factory()->create([
                'userId' => $user->id,
                'gender' => UserProfile::GENDER_MALE
            ]);
        }
    }
}
