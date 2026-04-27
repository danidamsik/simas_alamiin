<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GuruSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        DB::table('guru')->insert([
            ['id' => 1, 'user_id' => 2, 'nama' => 'Budi Santoso', 'nip' => '198501012010011001', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'user_id' => 3, 'nama' => 'Siti Rahayu', 'nip' => '198703152012012002', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'user_id' => 4, 'nama' => 'Ahmad Fauzi', 'nip' => '199002202014011003', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'user_id' => 5, 'nama' => 'Dewi Lestari', 'nip' => '199105102015012004', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
