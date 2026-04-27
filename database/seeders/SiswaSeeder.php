<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        $rows = [
            [1, 'Andi Pratama', '2526001001', 1],
            [2, 'Bella Safitri', '2526001002', 1],
            [3, 'Cahya Nugroho', '2526001003', 1],
            [4, 'Dina Marlina', '2526001004', 1],
            [5, 'Eko Setiawan', '2526001005', 1],
            [6, 'Farah Aulia', '2526001006', 1],
            [7, 'Gilang Ramadan', '2526001007', 1],
            [8, 'Hani Permatasari', '2526001008', 1],
            [9, 'Ilham Maulana', '2526001009', 1],
            [10, 'Jasmine Putri', '2526001010', 1],
            [11, 'Kevin Adriansyah', '2526001011', 1],
            [12, 'Laila Nurfadilah', '2526001012', 1],
            [13, 'Muhammad Rizky', '2526001013', 1],
            [14, 'Nadia Khairunnisa', '2526001014', 1],
            [15, 'Oscar Firmansyah', '2526001015', 1],
            [16, 'Putri Ramadhani', '2526001016', 1],
            [17, 'Rafi Akbar', '2526001017', 1],
            [18, 'Salsabila Zahra', '2526001018', 1],
            [19, 'Taufik Hidayat', '2526002001', 2],
            [20, 'Ulfa Nuraini', '2526002002', 2],
            [21, 'Vino Saputra', '2526002003', 2],
            [22, 'Winda Agustina', '2526002004', 2],
            [23, 'Xander Pratomo', '2526002005', 2],
            [24, 'Yani Kusumawati', '2526002006', 2],
            [25, 'Zaki Maulana', '2526002007', 2],
            [26, 'Alya Rahmawati', '2526002008', 2],
            [27, 'Bram Septian', '2526002009', 2],
            [28, 'Citra Dewi', '2526002010', 2],
            [29, 'Dafa Ardiansyah', '2526002011', 2],
            [30, 'Elsa Fitriani', '2526002012', 2],
            [31, 'Farhan Nugraha', '2526002013', 2],
            [32, 'Gita Puspita', '2526002014', 2],
            [33, 'Hendra Kurniawan', '2526002015', 2],
            [34, 'Indah Permata', '2526002016', 2],
            [35, 'Jaka Santosa', '2526002017', 2],
            [36, 'Kiki Amalia', '2526002018', 2],
            [37, 'Luki Prasetyo', '2425003001', 3],
            [38, 'Maya Anggraini', '2425003002', 3],
            [39, 'Nando Satria', '2425003003', 3],
            [40, 'Olivia Sari', '2425003004', 3],
            [41, 'Panji Wicaksono', '2425003005', 3],
            [42, 'Qori Fadillah', '2425003006', 3],
            [43, 'Rangga Pratama', '2425003007', 3],
            [44, 'Silvana Mardiana', '2425003008', 3],
            [45, 'Tri Wahyudi', '2425003009', 3],
            [46, 'Uswatun Hasanah', '2425003010', 3],
            [47, 'Wahyu Setiadi', '2425003011', 3],
            [48, 'Yessica Amalia', '2425003012', 3],
            [49, 'Zulfikar Rahman', '2425003013', 3],
            [50, 'Aditya Firmansyah', '2425003014', 3],
            [51, 'Bunga Citra', '2425003015', 3],
            [52, 'Candra Wijaya', '2425003016', 3],
            [53, 'Dini Pratiwi', '2425003017', 3],
            [54, 'Ervan Maulana', '2425004001', 4],
            [55, 'Fitria Hasanah', '2425004002', 4],
            [56, 'Galih Supriyanto', '2425004003', 4],
            [57, 'Hilda Febriani', '2425004004', 4],
            [58, 'Ivan Kurniawan', '2425004005', 4],
            [59, 'Junita Sari', '2425004006', 4],
            [60, 'Khairul Anwar', '2425004007', 4],
            [61, 'Linda Apriani', '2425004008', 4],
            [62, 'Mirza Fathullah', '2425004009', 4],
            [63, 'Nurhaliza', '2425004010', 4],
            [64, 'Okta Ridwan', '2425004011', 4],
            [65, 'Pita Lestari', '2425004012', 4],
            [66, 'Qodri Maulana', '2425004013', 4],
            [67, 'Rahmi Oktaviani', '2425004014', 4],
            [68, 'Sandi Permana', '2425004015', 4],
            [69, 'Tari Setyowati', '2425004016', 4],
            [70, 'Ujang Sopian', '2425004017', 4],
        ];

        DB::table('siswa')->insert(array_map(fn ($row) => [
            'id' => $row[0],
            'nama' => $row[1],
            'nis' => $row[2],
            'kelas_id' => $row[3],
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ], $rows));
    }
}
