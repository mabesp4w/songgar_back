<?php

namespace Database\Seeders;

use App\Models\Major;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MajorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Major::create([
            'major_nm' => 'Agribisnis',
        ]);

        Major::create([
            'major_nm' => 'Agroteknologi',
        ]);

        Major::create([
            'major_nm' => 'Kehutanan',
        ]);

        Major::create([
            'major_nm' => 'Manajemen Sumber Daya Perairan',
        ]);
    }
}
