<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Periode;
use App\Models\Session;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class KepalaSekolahAbsensiTest extends TestCase
{
    use RefreshDatabase;

    public function test_kepala_sekolah_can_view_readonly_absensi_detail_page(): void
    {
        $kepalaSekolah = User::factory()->kepalaSekolah()->create();
        $absensi = $this->absensiRecord();

        $this->actingAs($kepalaSekolah)->get(route('absensi.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Absensi/KepalaSekolah/Index')
                ->has('details.data', 2)
                ->where('details.data.0.absensi_id', $absensi->id)
                ->has('details.data.0.siswa')
                ->has('details.data.0.absensi.kelas')
                ->has('details.data.0.absensi.guru')
                ->has('details.data.0.absensi.session')
                ->has('details.data.0.absensi.periode'));
    }

    public function test_kepala_sekolah_absensi_detail_page_can_be_filtered(): void
    {
        $kepalaSekolah = User::factory()->kepalaSekolah()->create();
        $absensi = $this->absensiRecord();
        $otherClass = Kelas::query()->create(['nama_kelas' => 'X IPS 1']);
        $student = Siswa::query()->create(['nama' => 'Taufik Hidayat', 'nis' => '2526002001', 'kelas_id' => $otherClass->id, 'is_active' => true]);

        $otherAbsensi = Absensi::query()->create([
            'tanggal' => '2026-04-28',
            'kelas_id' => $otherClass->id,
            'guru_id' => $absensi->guru_id,
            'session_id' => $absensi->session_id,
            'periode_id' => $absensi->periode_id,
        ]);
        $otherAbsensi->absensiDetail()->create(['siswa_id' => $student->id, 'status' => 'alfa']);

        $this->actingAs($kepalaSekolah)->get(route('absensi.index', [
            'tanggal' => '2026-04-28',
            'kelas_id' => $otherClass->id,
            'periode_id' => $absensi->periode_id,
        ]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Absensi/KepalaSekolah/Index')
                ->has('details.data', 1)
                ->where('details.data.0.status', 'alfa')
                ->where('filters.tanggal', '2026-04-28'));
    }

    public function test_kepala_sekolah_cannot_open_absensi_edit_page(): void
    {
        $kepalaSekolah = User::factory()->kepalaSekolah()->create();
        $absensi = $this->absensiRecord();

        $this->actingAs($kepalaSekolah)
            ->get(route('absensi.edit', $absensi))
            ->assertForbidden();
    }

    private function absensiRecord(): Absensi
    {
        $guruUser = User::factory()->guru()->create();
        $guru = Guru::query()->create(['user_id' => $guruUser->id, 'nama' => 'Budi Santoso', 'is_active' => true]);
        $kelas = Kelas::query()->create(['nama_kelas' => 'X IPA 1']);
        $periode = Periode::query()->create(['tahun_ajaran' => '2025/2026', 'semester' => 'ganjil', 'is_active' => true]);
        $session = Session::query()->create(['nama_sesi' => 'Jam 1', 'jam_mulai' => '07:00', 'jam_selesai' => '07:45']);
        $students = [
            Siswa::query()->create(['nama' => 'Andi Pratama', 'nis' => '2526001001', 'kelas_id' => $kelas->id, 'is_active' => true]),
            Siswa::query()->create(['nama' => 'Bella Safitri', 'nis' => '2526001002', 'kelas_id' => $kelas->id, 'is_active' => true]),
        ];

        $absensi = Absensi::query()->create([
            'tanggal' => '2026-04-27',
            'kelas_id' => $kelas->id,
            'guru_id' => $guru->id,
            'session_id' => $session->id,
            'periode_id' => $periode->id,
        ]);
        $absensi->absensiDetail()->createMany([
            ['siswa_id' => $students[0]->id, 'status' => 'hadir'],
            ['siswa_id' => $students[1]->id, 'status' => 'sakit'],
        ]);

        return $absensi;
    }
}
