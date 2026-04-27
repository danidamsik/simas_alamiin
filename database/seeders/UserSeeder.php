<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        DB::table('users')->insert([
            [
                'id' => 1,
                'name' => 'Administrator',
                'email' => 'admin@alamiin.sch.id',
                'password' => Hash::make('admin12345'),
                'role' => 'admin',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@alamiin.sch.id',
                'password' => Hash::make('guru12345'),
                'role' => 'guru',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'name' => 'Siti Rahayu',
                'email' => 'siti.rahayu@alamiin.sch.id',
                'password' => Hash::make('guru12345'),
                'role' => 'guru',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'name' => 'Ahmad Fauzi',
                'email' => 'ahmad.fauzi@alamiin.sch.id',
                'password' => Hash::make('guru12345'),
                'role' => 'guru',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 5,
                'name' => 'Dewi Lestari',
                'email' => 'dewi.lestari@alamiin.sch.id',
                'password' => Hash::make('guru12345'),
                'role' => 'guru',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 6,
                'name' => 'Dr. H. Suparman',
                'email' => 'kepsek@alamiin.sch.id',
                'password' => Hash::make('kepsek12345'),
                'role' => 'kepala_sekolah',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
