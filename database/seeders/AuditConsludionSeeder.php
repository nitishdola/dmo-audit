<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AuditConclusion;

class AuditConsludionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AuditConclusion::create([
            'name' => 'Genuine',
        ]);

        AuditConclusion::create([
            'name' => 'Not Genuine',
        ]);

        AuditConclusion::create([
            'name' => 'Suspicious',
        ]);

        AuditConclusion::create([
            'name' => 'Out of pocket expenditure',
        ]);

        AuditConclusion::create([
            'name' => 'Unable to connect',
        ]);
    }
}
