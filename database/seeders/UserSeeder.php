<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Enums\UserRole;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // User::create([
        //     'name' => 'Admin',
        //     'mobile' => '9706125041',
        //     'role' => UserRole::ADMIN,
        // ]);

        // User::create([
        //     'name' => 'DMO Officer1',
        //     'mobile' => '7002843656',
        //     'role' => UserRole::DMO,
        // ]);

        // User::create([
        //     'name' => 'DMO Officer2',
        //     'mobile' => '7002843655',
        //     'role' => UserRole::DMO,
        // ]);

        User::create([
            'name' => 'Trisanka Kalita',
            'mobile' => '9531055553',
            'role' => UserRole::DMO,
        ]);

        User::create([
            'name' => 'Raktim Phukan',
            'mobile' => '7838869164',
            'role' => UserRole::DMO,
        ]);

        User::create([
            'name' => 'Debashree Bora',
            'mobile' => '9101342378',
            'role' => UserRole::DMO,
        ]);
    }
}