<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        DB::table('kelas')->insert([
            ['id' => 1, 'nama_kelas' => 'X IPA 1', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'nama_kelas' => 'X IPS 1', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'nama_kelas' => 'XI IPA 1', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'nama_kelas' => 'XI IPS 1', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
