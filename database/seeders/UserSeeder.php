<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Enums\UserRole;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'mobile' => '9706125041',
            'role' => UserRole::ADMIN,
        ]);

        User::create([
            'name' => 'DMO Officer1',
            'mobile' => '7002843656',
            'role' => UserRole::DMO,
        ]);

        User::create([
            'name' => 'DMO Officer2',
            'mobile' => '7002843655',
            'role' => UserRole::DMO,
        ]);
    }
}