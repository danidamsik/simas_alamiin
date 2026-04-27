<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JadwalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        $rows = [
            [1, 1, 1, 'Matematika', 'senin', 1],
            [2, 1, 2, 'Bahasa Indonesia', 'senin', 3],
            [3, 2, 4, 'Ekonomi', 'senin', 1],
            [4, 2, 3, 'Fisika', 'senin', 4],
            [5, 3, 1, 'Matematika', 'senin', 4],
            [6, 3, 3, 'Biologi', 'senin', 6],
            [7, 4, 2, 'Bahasa Indonesia', 'senin', 2],
            [8, 4, 4, 'Sosiologi', 'senin', 5],
            [9, 1, 3, 'Fisika', 'selasa', 2],
            [10, 1, 4, 'Ekonomi', 'selasa', 5],
            [11, 2, 1, 'Matematika', 'selasa', 3],
            [12, 2, 2, 'Bahasa Indonesia', 'selasa', 6],
            [13, 3, 2, 'Bahasa Indonesia', 'selasa', 1],
            [14, 3, 4, 'Ekonomi', 'selasa', 3],
            [15, 4, 1, 'Matematika', 'selasa', 4],
            [16, 4, 3, 'Kimia', 'selasa', 7],
            [17, 1, 1, 'Matematika', 'rabu', 2],
            [18, 1, 3, 'Biologi', 'rabu', 4],
            [19, 2, 4, 'Sosiologi', 'rabu', 2],
            [20, 2, 1, 'Matematika', 'rabu', 5],
            [21, 3, 3, 'Fisika', 'rabu', 2],
            [22, 3, 1, 'Matematika', 'rabu', 5],
            [23, 4, 2, 'Bahasa Indonesia', 'rabu', 3],
            [24, 4, 4, 'Ekonomi', 'rabu', 6],
            [25, 1, 2, 'Bahasa Indonesia', 'kamis', 1],
            [26, 1, 4, 'Sosiologi', 'kamis', 4],
            [27, 2, 3, 'Kimia', 'kamis', 1],
            [28, 2, 2, 'Bahasa Indonesia', 'kamis', 5],
            [29, 3, 4, 'Geografi', 'kamis', 3],
            [30, 3, 2, 'Bahasa Indonesia', 'kamis', 6],
            [31, 4, 1, 'Matematika', 'kamis', 2],
            [32, 4, 3, 'Fisika', 'kamis', 5],
            [33, 1, 3, 'Kimia', 'jumat', 1],
            [34, 1, 2, 'Bahasa Indonesia', 'jumat', 3],
            [35, 2, 1, 'Matematika', 'jumat', 1],
            [36, 2, 4, 'Geografi', 'jumat', 3],
            [37, 3, 1, 'Matematika', 'jumat', 2],
            [38, 3, 3, 'Biologi', 'jumat', 4],
            [39, 4, 4, 'Sosiologi', 'jumat', 2],
            [40, 4, 2, 'Bahasa Indonesia', 'jumat', 4],
        ];

        DB::table('jadwal')->insert(array_map(fn ($row) => [
            'id' => $row[0],
            'kelas_id' => $row[1],
            'guru_id' => $row[2],
            'mata_pelajaran' => $row[3],
            'hari' => $row[4],
            'session_id' => $row[5],
            'periode_id' => 2,
            'created_at' => $now,
            'updated_at' => $now,
        ], $rows));
    }
}
