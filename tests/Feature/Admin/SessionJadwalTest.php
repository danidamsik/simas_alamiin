<?php

namespace Tests\Feature\Admin;

use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Periode;
use App\Models\Session;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SessionJadwalTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_session_and_jadwal_pages(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get(route('sessions.index'))->assertOk();
        $this->actingAs($admin)->get(route('jadwal.index'))->assertOk();
    }

    public function test_session_time_must_not_overlap_existing_session(): void
    {
        $admin = User::factory()->admin()->create();

        Session::query()->create([
            'nama_sesi' => 'Jam 1',
            'jam_mulai' => '07:00',
            'jam_selesai' => '07:45',
        ]);

        $response = $this->actingAs($admin)->from(route('sessions.index'))->post(route('sessions.store'), [
            'nama_sesi' => 'Jam Bentrok',
            'jam_mulai' => '07:30',
            'jam_selesai' => '08:00',
        ]);

        $response->assertRedirect(route('sessions.index'));
        $response->assertSessionHasErrors(['jam_mulai']);
        $this->assertSame(1, Session::query()->count());
    }

    public function test_session_time_can_be_saved_when_it_does_not_overlap(): void
    {
        $admin = User::factory()->admin()->create();

        Session::query()->create([
            'nama_sesi' => 'Jam 1',
            'jam_mulai' => '07:00',
            'jam_selesai' => '07:45',
        ]);

        $this->actingAs($admin)->post(route('sessions.store'), [
            'nama_sesi' => 'Jam 2',
            'jam_mulai' => '07:45',
            'jam_selesai' => '08:30',
        ])->assertRedirect();

        $this->assertDatabaseHas('sessions', ['nama_sesi' => 'Jam 2']);
    }

    public function test_jadwal_rejects_duplicate_class_same_day_session_period(): void
    {
        [$admin, $kelas, $guru, $otherGuru, $session, $periode] = $this->scheduleDependencies();

        Jadwal::query()->create([
            'kelas_id' => $kelas->id,
            'guru_id' => $guru->id,
            'mata_pelajaran' => 'Matematika',
            'hari' => 'senin',
            'session_id' => $session->id,
            'periode_id' => $periode->id,
        ]);

        $this->actingAs($admin)->from(route('jadwal.index'))->post(route('jadwal.store'), [
            'kelas_id' => $kelas->id,
            'guru_id' => $otherGuru->id,
            'mata_pelajaran' => 'Fisika',
            'hari' => 'senin',
            'session_id' => $session->id,
            'periode_id' => $periode->id,
        ])->assertRedirect(route('jadwal.index'))
            ->assertSessionHasErrors(['kelas_id']);
    }

    public function test_jadwal_rejects_duplicate_teacher_same_day_session_period(): void
    {
        [$admin, $kelas, $guru, , $session, $periode] = $this->scheduleDependencies();
        $otherClass = Kelas::query()->create(['nama_kelas' => 'X IPS 1']);

        Jadwal::query()->create([
            'kelas_id' => $kelas->id,
            'guru_id' => $guru->id,
            'mata_pelajaran' => 'Matematika',
            'hari' => 'senin',
            'session_id' => $session->id,
            'periode_id' => $periode->id,
        ]);

        $this->actingAs($admin)->from(route('jadwal.index'))->post(route('jadwal.store'), [
            'kelas_id' => $otherClass->id,
            'guru_id' => $guru->id,
            'mata_pelajaran' => 'Fisika',
            'hari' => 'senin',
            'session_id' => $session->id,
            'periode_id' => $periode->id,
        ])->assertRedirect(route('jadwal.index'))
            ->assertSessionHasErrors(['guru_id']);
    }

    public function test_jadwal_can_be_saved_when_class_and_teacher_are_available(): void
    {
        [$admin, $kelas, $guru, , $session, $periode] = $this->scheduleDependencies();

        $this->actingAs($admin)->post(route('jadwal.store'), [
            'kelas_id' => $kelas->id,
            'guru_id' => $guru->id,
            'mata_pelajaran' => 'Matematika',
            'hari' => 'senin',
            'session_id' => $session->id,
            'periode_id' => $periode->id,
        ])->assertRedirect();

        $this->assertDatabaseHas('jadwal', [
            'kelas_id' => $kelas->id,
            'guru_id' => $guru->id,
            'mata_pelajaran' => 'Matematika',
        ]);
    }

    /**
     * @return array{0: User, 1: Kelas, 2: Guru, 3: Guru, 4: Session, 5: Periode}
     */
    private function scheduleDependencies(): array
    {
        $admin = User::factory()->admin()->create();
        $guruUser = User::factory()->guru()->create();
        $otherGuruUser = User::factory()->guru()->create();

        return [
            $admin,
            Kelas::query()->create(['nama_kelas' => 'X IPA 1']),
            Guru::query()->create(['user_id' => $guruUser->id, 'nama' => 'Budi Santoso', 'is_active' => true]),
            Guru::query()->create(['user_id' => $otherGuruUser->id, 'nama' => 'Siti Rahayu', 'is_active' => true]),
            Session::query()->create(['nama_sesi' => 'Jam 1', 'jam_mulai' => '07:00', 'jam_selesai' => '07:45']),
            Periode::query()->create(['tahun_ajaran' => '2025/2026', 'semester' => 'ganjil', 'is_active' => true]),
        ];
    }
}
