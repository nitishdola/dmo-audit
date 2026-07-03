<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\DmoDistrict;

class TestUserDistrict extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            4 => [29, 15, 7],
            5 => [9, 3, 4],
            6 => [1, 3],
        ];

        foreach ($data as $userId => $districts) {
            foreach ($districts as $districtId) {
                DmoDistrict::create([
                    'user_id' => $userId,
                    'district_id' => $districtId,
                ]);
            }
        }
    }
}
