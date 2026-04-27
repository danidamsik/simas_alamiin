<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\AuditLog;
use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Periode;
use App\Models\Session;
use App\Models\Siswa;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AbsensiInputTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_guru_can_store_absensi_for_their_current_session(): void
    {
        [$guruUser, $jadwal, $students] = $this->attendanceDependencies();
        Carbon::setTestNow(Carbon::parse('2026-04-27 07:15:00'));

        $this->actingAs($guruUser)->post(route('absensi.input.store', $jadwal), [
            'details' => [
                ['siswa_id' => $students[0]->id, 'status' => 'hadir'],
                ['siswa_id' => $students[1]->id, 'status' => 'sakit'],
            ],
        ])->assertRedirect(route('absensi.riwayat'));

        $this->assertDatabaseHas('absensi', [
            'tanggal' => '2026-04-27',
            'kelas_id' => $jadwal->kelas_id,
            'guru_id' => $jadwal->guru_id,
            'session_id' => $jadwal->session_id,
            'periode_id' => $jadwal->periode_id,
        ]);
        $this->assertDatabaseHas('absensi_detail', ['siswa_id' => $students[1]->id, 'status' => 'sakit']);
    }

    public function test_admin_absensi_creation_is_audited(): void
    {
        [, $jadwal, $students] = $this->attendanceDependencies();
        $admin = User::factory()->admin()->create();
        Carbon::setTestNow(Carbon::parse('2026-04-27 07:15:00'));

        $this->actingAs($admin)->post(route('absensi.input.store', $jadwal), [
            'details' => [
                ['siswa_id' => $students[0]->id, 'status' => 'hadir'],
                ['siswa_id' => $students[1]->id, 'status' => 'izin'],
            ],
        ])->assertRedirect(route('absensi.riwayat'));

        $log = AuditLog::query()->first();
        $this->assertNotNull($log);
        $this->assertSame($admin->id, $log->user_id);
        $this->assertSame('create', $log->action);
        $this->assertSame(Absensi::class, $log->model);
        $this->assertNull($log->old_value);
        $this->assertSame('izin', collect($log->new_value['details'])->firstWhere('siswa_id', $students[1]->id)['status']);
    }

    public function test_absensi_cannot_be_input_outside_session_time(): void
    {
        [$guruUser, $jadwal, $students] = $this->attendanceDependencies();
        Carbon::setTestNow(Carbon::parse('2026-04-27 08:00:00'));

        $this->actingAs($guruUser)->from(route('absensi.input.index'))->post(route('absensi.input.store', $jadwal), [
            'details' => [
                ['siswa_id' => $students[0]->id, 'status' => 'hadir'],
                ['siswa_id' => $students[1]->id, 'status' => 'hadir'],
            ],
        ])->assertRedirect(route('absensi.input.index'))
            ->assertSessionHasErrors(['session']);
    }

    public function test_guru_cannot_input_another_teacher_schedule(): void
    {
        [, $jadwal, $students] = $this->attendanceDependencies();
        $otherUser = User::factory()->guru()->create();
        Guru::query()->create(['user_id' => $otherUser->id, 'nama' => 'Siti Rahayu', 'is_active' => true]);
        Carbon::setTestNow(Carbon::parse('2026-04-27 07:15:00'));

        $this->actingAs($otherUser)->post(route('absensi.input.store', $jadwal), [
            'details' => [
                ['siswa_id' => $students[0]->id, 'status' => 'hadir'],
                ['siswa_id' => $students[1]->id, 'status' => 'hadir'],
            ],
        ])->assertForbidden();
    }

    public function test_absensi_requires_active_period(): void
    {
        [$guruUser, $jadwal, $students] = $this->attendanceDependencies(activePeriod: false);
        Carbon::setTestNow(Carbon::parse('2026-04-27 07:15:00'));

        $this->actingAs($guruUser)->from(route('absensi.input.index'))->post(route('absensi.input.store', $jadwal), [
            'details' => [
                ['siswa_id' => $students[0]->id, 'status' => 'hadir'],
                ['siswa_id' => $students[1]->id, 'status' => 'hadir'],
            ],
        ])->assertRedirect(route('absensi.input.index'))
            ->assertSessionHasErrors(['periode']);
    }

    public function test_duplicate_absensi_is_rejected(): void
    {
        [$guruUser, $jadwal, $students] = $this->attendanceDependencies();
        Carbon::setTestNow(Carbon::parse('2026-04-27 07:15:00'));

        $payload = [
            'details' => [
                ['siswa_id' => $students[0]->id, 'status' => 'hadir'],
                ['siswa_id' => $students[1]->id, 'status' => 'hadir'],
            ],
        ];

        $this->actingAs($guruUser)->post(route('absensi.input.store', $jadwal), $payload)->assertRedirect(route('absensi.riwayat'));

        $this->actingAs($guruUser)->from(route('absensi.input.index'))->post(route('absensi.input.store', $jadwal), $payload)
            ->assertRedirect(route('absensi.input.index'))
            ->assertSessionHasErrors(['jadwal']);

        $this->assertSame(1, Absensi::query()->count());
    }

    /**
     * @return array{0: User, 1: Jadwal, 2: array<int, Siswa>}
     */
    private function attendanceDependencies(bool $activePeriod = true): array
    {
        $guruUser = User::factory()->guru()->create();
        $guru = Guru::query()->create(['user_id' => $guruUser->id, 'nama' => 'Budi Santoso', 'is_active' => true]);
        $kelas = Kelas::query()->create(['nama_kelas' => 'X IPA 1']);
        $periode = Periode::query()->create(['tahun_ajaran' => '2025/2026', 'semester' => 'ganjil', 'is_active' => $activePeriod]);
        $session = Session::query()->create(['nama_sesi' => 'Jam 1', 'jam_mulai' => '07:00', 'jam_selesai' => '07:45']);
        $students = [
            Siswa::query()->create(['nama' => 'Andi Pratama', 'nis' => '2526001001', 'kelas_id' => $kelas->id, 'is_active' => true]),
            Siswa::query()->create(['nama' => 'Bella Safitri', 'nis' => '2526001002', 'kelas_id' => $kelas->id, 'is_active' => true]),
        ];
        $jadwal = Jadwal::query()->create([
            'kelas_id' => $kelas->id,
            'guru_id' => $guru->id,
            'mata_pelajaran' => 'Matematika',
            'hari' => 'senin',
            'session_id' => $session->id,
            'periode_id' => $periode->id,
        ]);

        return [$guruUser, $jadwal, $students];
    }
}
