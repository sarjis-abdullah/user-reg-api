<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Manager;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserRole;
use Illuminate\Database\Seeder;

class ManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $managers = [
            0 => [
                'name' => 'Super manager',
                'email' => 'supermanager@reformedtech.org',
                'phone' => "01303094898",
                'roleId' => 4,
                'level' => Manager::LEVEL_SUPER
            ],
            1 => [
                'name' => 'Standard manager',
                'email' => 'standardmanager@reformedtech.org',
                'phone' => "01822270502",
                'roleId' => 5,
                'level' => Manager::LEVEL_STANDARD
            ],
            2 => [
                'name' => 'Restricted manager',
                'email' => 'restrictedmanager@reformedtech.org',
                'phone' => "01521484415",
                'roleId' => 6,
                'level' => Manager::LEVEL_RESTRICTED
            ],

        ];

        $branch = Branch::factory()->create([
            'name' => 'RT Manager Test Branch',
            'address' => 'Dhaka, Bangladesh',
            'phone' => '01822270500',
        ]);

        foreach ($managers as $key => $value) {
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

            Manager::factory()->create([
                'userId' => $user->id,
                'userRoleId' => $userRole->id,
                'level' => $value['level'],
                'title' => $value['name'],
                'branchId' => $branch->id,
            ]);

            UserProfile::factory()->create([
                'userId' => $user->id,
                'gender' => UserProfile::GENDER_MALE
            ]);
        }
    }
}
