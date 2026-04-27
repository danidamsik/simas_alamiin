<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Periode;
use App\Models\Session;
use App\Models\Siswa;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_admin_dashboard_shows_school_attendance_summary(): void
    {
        $admin = User::factory()->admin()->create();
        [$kelas, $guru, $periode, $session] = $this->baseDependencies();
        $otherClass = Kelas::query()->create(['nama_kelas' => 'X IPS 1']);
        Carbon::setTestNow(Carbon::parse('2026-04-27 09:00:00'));

        $students = [
            Siswa::query()->create(['nama' => 'Andi Pratama', 'nis' => '2526001001', 'kelas_id' => $kelas->id, 'is_active' => true]),
            Siswa::query()->create(['nama' => 'Bella Safitri', 'nis' => '2526001002', 'kelas_id' => $kelas->id, 'is_active' => true]),
            Siswa::query()->create(['nama' => 'Taufik Hidayat', 'nis' => '2526002001', 'kelas_id' => $otherClass->id, 'is_active' => true]),
        ];

        $firstAbsensi = Absensi::query()->create([
            'tanggal' => '2026-04-27',
            'kelas_id' => $kelas->id,
            'guru_id' => $guru->id,
            'session_id' => $session->id,
            'periode_id' => $periode->id,
        ]);
        $firstAbsensi->absensiDetail()->createMany([
            ['siswa_id' => $students[0]->id, 'status' => 'hadir'],
            ['siswa_id' => $students[1]->id, 'status' => 'sakit'],
        ]);

        $secondAbsensi = Absensi::query()->create([
            'tanggal' => '2026-04-27',
            'kelas_id' => $otherClass->id,
            'guru_id' => $guru->id,
            'session_id' => $session->id,
            'periode_id' => $periode->id,
        ]);
        $secondAbsensi->absensiDetail()->create([
            'siswa_id' => $students[2]->id,
            'status' => 'alfa',
        ]);

        $this->actingAs($admin)->get(route('dashboard'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Dashboard')
                ->where('role', User::ROLE_ADMIN)
                ->where('schoolDashboard.totalSiswaAktif', 3)
                ->where('schoolDashboard.todayCounts.hadir', 1)
                ->where('schoolDashboard.todayCounts.sakit', 1)
                ->where('schoolDashboard.todayCounts.alfa', 1)
                ->where('schoolDashboard.todayPercentage', 33.3)
                ->has('schoolDashboard.trend', 7)
                ->has('schoolDashboard.lowestClasses', 2));
    }

    public function test_guru_dashboard_shows_today_schedule_and_attendance_status(): void
    {
        [$kelas, $guru, $periode, $session, $guruUser] = $this->baseDependencies(withUser: true);
        Carbon::setTestNow(Carbon::parse('2026-04-27 09:00:00'));

        Jadwal::query()->create([
            'kelas_id' => $kelas->id,
            'guru_id' => $guru->id,
            'mata_pelajaran' => 'Matematika',
            'hari' => 'senin',
            'session_id' => $session->id,
            'periode_id' => $periode->id,
        ]);

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
            ['siswa_id' => $students[1]->id, 'status' => 'izin'],
        ]);

        $this->actingAs($guruUser)->get(route('dashboard'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Dashboard')
                ->where('role', User::ROLE_GURU)
                ->has('guruDashboard.jadwals', 1)
                ->where('guruDashboard.jadwals.0.already_input', true)
                ->where('guruDashboard.jadwals.0.total_hadir', 1)
                ->where('guruDashboard.jadwals.0.total_tidak_hadir', 1));
    }

    /**
     * @return array<int, mixed>
     */
    private function baseDependencies(bool $withUser = false): array
    {
        $guruUser = User::factory()->guru()->create();
        $guru = Guru::query()->create(['user_id' => $guruUser->id, 'nama' => 'Budi Santoso', 'is_active' => true]);

        $dependencies = [
            Kelas::query()->create(['nama_kelas' => 'X IPA 1']),
            $guru,
            Periode::query()->create(['tahun_ajaran' => '2025/2026', 'semester' => 'ganjil', 'is_active' => true]),
            Session::query()->create(['nama_sesi' => 'Jam 1', 'jam_mulai' => '07:00', 'jam_selesai' => '07:45']),
        ];

        if ($withUser) {
            $dependencies[] = $guruUser;
        }

        return $dependencies;
    }
}
