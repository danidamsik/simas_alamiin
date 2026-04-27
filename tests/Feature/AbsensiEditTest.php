<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\AuditLog;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Periode;
use App\Models\Session;
use App\Models\Siswa;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AbsensiEditTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_guru_can_edit_own_absensi_during_tolerance_window(): void
    {
        [$guruUser, $absensi, $students] = $this->attendanceRecord();
        Carbon::setTestNow(Carbon::parse('2026-04-27 08:10:00'));

        $this->actingAs($guruUser)->put(route('absensi.update', $absensi), [
            'details' => [
                ['siswa_id' => $students[0]->id, 'status' => 'izin'],
                ['siswa_id' => $students[1]->id, 'status' => 'hadir'],
            ],
        ])->assertRedirect(route('absensi.riwayat'));

        $this->assertDatabaseHas('absensi_detail', [
            'absensi_id' => $absensi->id,
            'siswa_id' => $students[0]->id,
            'status' => 'izin',
        ]);
        $this->assertDatabaseCount('audit_log', 0);
    }

    public function test_guru_cannot_edit_absensi_after_tolerance_window(): void
    {
        [$guruUser, $absensi, $students] = $this->attendanceRecord();
        Carbon::setTestNow(Carbon::parse('2026-04-27 08:20:00'));

        $this->actingAs($guruUser)->get(route('absensi.edit', $absensi))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Absensi/Edit')
                ->where('canEdit', false)
                ->where('lockedReason', 'Waktu edit telah berakhir, hubungi admin'));

        $this->actingAs($guruUser)->from(route('absensi.edit', $absensi))->put(route('absensi.update', $absensi), [
            'details' => [
                ['siswa_id' => $students[0]->id, 'status' => 'izin'],
                ['siswa_id' => $students[1]->id, 'status' => 'hadir'],
            ],
        ])->assertRedirect(route('absensi.edit', $absensi))
            ->assertSessionHasErrors(['edit']);

        $this->assertDatabaseHas('absensi_detail', [
            'absensi_id' => $absensi->id,
            'siswa_id' => $students[0]->id,
            'status' => 'hadir',
        ]);
    }

    public function test_guru_cannot_edit_another_teacher_absensi(): void
    {
        [, $absensi, $students] = $this->attendanceRecord();
        $otherUser = User::factory()->guru()->create();
        Guru::query()->create(['user_id' => $otherUser->id, 'nama' => 'Siti Rahayu', 'is_active' => true]);
        Carbon::setTestNow(Carbon::parse('2026-04-27 08:10:00'));

        $this->actingAs($otherUser)->put(route('absensi.update', $absensi), [
            'details' => [
                ['siswa_id' => $students[0]->id, 'status' => 'izin'],
                ['siswa_id' => $students[1]->id, 'status' => 'hadir'],
            ],
        ])->assertForbidden();
    }

    public function test_admin_can_edit_anytime_and_update_is_audited(): void
    {
        [, $absensi, $students] = $this->attendanceRecord();
        $admin = User::factory()->admin()->create();
        Carbon::setTestNow(Carbon::parse('2026-05-10 10:00:00'));

        $this->actingAs($admin)->put(route('absensi.update', $absensi), [
            'details' => [
                ['siswa_id' => $students[0]->id, 'status' => 'sakit'],
                ['siswa_id' => $students[1]->id, 'status' => 'alfa'],
            ],
        ])->assertRedirect(route('absensi.index'));

        $this->assertDatabaseHas('absensi_detail', [
            'absensi_id' => $absensi->id,
            'siswa_id' => $students[1]->id,
            'status' => 'alfa',
        ]);

        $log = AuditLog::query()->first();
        $this->assertNotNull($log);
        $this->assertSame($admin->id, $log->user_id);
        $this->assertSame('update', $log->action);
        $this->assertSame(Absensi::class, $log->model);
        $this->assertSame($absensi->id, $log->model_id);
        $this->assertSame('hadir', collect($log->old_value['details'])->firstWhere('siswa_id', $students[0]->id)['status']);
        $this->assertSame('sakit', collect($log->new_value['details'])->firstWhere('siswa_id', $students[0]->id)['status']);
    }

    /**
     * @return array{0: User, 1: Absensi, 2: array<int, Siswa>}
     */
    private function attendanceRecord(): array
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
            ['siswa_id' => $students[1]->id, 'status' => 'hadir'],
        ]);

        return [$guruUser, $absensi, $students];
    }
}
