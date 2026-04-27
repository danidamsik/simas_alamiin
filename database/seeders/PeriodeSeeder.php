<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PeriodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        DB::table('periode')->insert([
            [
                'id' => 1,
                'tahun_ajaran' => '2024/2025',
                'semester' => 'genap',
                'tanggal_mulai' => '2025-01-01',
                'tanggal_selesai' => '2025-06-30',
                'is_active' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'tahun_ajaran' => '2025/2026',
                'semester' => 'ganjil',
                'tanggal_mulai' => '2025-07-01',
                'tanggal_selesai' => '2025-12-31',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
