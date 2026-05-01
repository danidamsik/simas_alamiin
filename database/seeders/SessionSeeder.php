<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        DB::table('sessions')->insert([
            ['id' => 1, 'nama_sesi' => 'sesi 1', 'jam_mulai' => '07:00:00', 'jam_selesai' => '07:45:00', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'nama_sesi' => 'sesi 2', 'jam_mulai' => '07:45:00', 'jam_selesai' => '08:30:00', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'nama_sesi' => 'sesi 3', 'jam_mulai' => '08:30:00', 'jam_selesai' => '09:15:00', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'nama_sesi' => 'sesi 4', 'jam_mulai' => '09:30:00', 'jam_selesai' => '10:15:00', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'nama_sesi' => 'sesi 5', 'jam_mulai' => '10:15:00', 'jam_selesai' => '11:00:00', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'nama_sesi' => 'sesi 6', 'jam_mulai' => '11:00:00', 'jam_selesai' => '11:45:00', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 7, 'nama_sesi' => 'sesi 7', 'jam_mulai' => '12:30:00', 'jam_selesai' => '13:15:00', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 8, 'nama_sesi' => 'sesi 8', 'jam_mulai' => '13:15:00', 'jam_selesai' => '14:00:00', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
