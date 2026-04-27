<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        foreach ([
            'audit_log',
            'siswa_kelas_history',
            'absensi_detail',
            'absensi',
            'jadwal',
            'sessions',
            'siswa',
            'guru',
            'users',
            'kelas',
            'periode',
        ] as $table) {
            DB::table($table)->truncate();
        }

        Schema::enableForeignKeyConstraints();

        $this->call([
            PeriodeSeeder::class,
            KelasSeeder::class,
            UserSeeder::class,
            GuruSeeder::class,
            SiswaSeeder::class,
            SessionSeeder::class,
            JadwalSeeder::class,
            AbsensiSeeder::class,
        ]);
    }
}
