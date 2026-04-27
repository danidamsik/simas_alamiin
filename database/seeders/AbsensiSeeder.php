<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AbsensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        $headers = [
            [1, '2025-04-21', 1, 1, 1],
            [2, '2025-04-21', 1, 2, 3],
            [3, '2025-04-21', 2, 4, 1],
            [4, '2025-04-21', 2, 3, 4],
            [5, '2025-04-21', 3, 1, 4],
            [6, '2025-04-21', 3, 3, 6],
            [7, '2025-04-21', 4, 2, 2],
            [8, '2025-04-21', 4, 4, 5],
            [9, '2025-04-22', 1, 3, 2],
            [10, '2025-04-22', 1, 4, 5],
            [11, '2025-04-22', 2, 1, 3],
            [12, '2025-04-22', 2, 2, 6],
            [13, '2025-04-22', 3, 2, 1],
            [14, '2025-04-22', 3, 4, 3],
            [15, '2025-04-22', 4, 1, 4],
            [16, '2025-04-22', 4, 3, 7],
            [17, '2025-04-23', 1, 1, 2],
            [18, '2025-04-23', 1, 3, 4],
            [19, '2025-04-23', 2, 4, 2],
            [20, '2025-04-23', 2, 1, 5],
            [21, '2025-04-23', 3, 3, 2],
            [22, '2025-04-23', 3, 1, 5],
            [23, '2025-04-23', 4, 2, 3],
            [24, '2025-04-23', 4, 4, 6],
            [25, '2025-04-24', 1, 2, 1],
            [26, '2025-04-24', 1, 4, 4],
            [27, '2025-04-24', 2, 3, 1],
            [28, '2025-04-24', 2, 2, 5],
            [29, '2025-04-24', 3, 4, 3],
            [30, '2025-04-24', 3, 2, 6],
            [31, '2025-04-24', 4, 1, 2],
            [32, '2025-04-24', 4, 3, 5],
            [33, '2025-04-25', 1, 3, 1],
            [34, '2025-04-25', 1, 2, 3],
            [35, '2025-04-25', 2, 1, 1],
            [36, '2025-04-25', 2, 4, 3],
            [37, '2025-04-25', 3, 1, 2],
            [38, '2025-04-25', 3, 3, 4],
            [39, '2025-04-25', 4, 4, 2],
            [40, '2025-04-25', 4, 2, 4],
        ];

        DB::table('absensi')->insert(array_map(fn ($row) => [
            'id' => $row[0],
            'tanggal' => $row[1],
            'kelas_id' => $row[2],
            'guru_id' => $row[3],
            'session_id' => $row[4],
            'periode_id' => 2,
            'created_at' => $now,
            'updated_at' => $now,
        ], $headers));

        $detailId = 1;
        $details = [];
        $exampleStatuses = [
            1 => 'hadir',
            2 => 'hadir',
            3 => 'sakit',
            4 => 'hadir',
            5 => 'hadir',
            6 => 'izin',
            7 => 'hadir',
            8 => 'hadir',
            9 => 'hadir',
            10 => 'hadir',
            11 => 'alfa',
            12 => 'hadir',
            13 => 'hadir',
            14 => 'hadir',
            15 => 'sakit',
            16 => 'hadir',
            17 => 'hadir',
            18 => 'hadir',
        ];

        foreach ($exampleStatuses as $siswaId => $status) {
            $details[] = [
                'id' => $detailId++,
                'absensi_id' => 1,
                'siswa_id' => $siswaId,
                'status' => $status,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        $studentIdsByClass = DB::table('siswa')
            ->select('id', 'kelas_id')
            ->orderBy('id')
            ->get()
            ->groupBy('kelas_id')
            ->map(fn ($students) => $students->pluck('id')->all());

        mt_srand(20250421);

        foreach (array_slice($headers, 1) as $header) {
            [$absensiId, , $kelasId] = $header;

            foreach ($studentIdsByClass[$kelasId] as $siswaId) {
                $details[] = [
                    'id' => $detailId++,
                    'absensi_id' => $absensiId,
                    'siswa_id' => $siswaId,
                    'status' => $this->weightedStatus(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        foreach (array_chunk($details, 500) as $chunk) {
            DB::table('absensi_detail')->insert($chunk);
        }
    }

    private function weightedStatus(): string
    {
        $roll = mt_rand(1, 100);

        return match (true) {
            $roll <= 85 => 'hadir',
            $roll <= 91 => 'sakit',
            $roll <= 96 => 'izin',
            default => 'alfa',
        };
    }
}
