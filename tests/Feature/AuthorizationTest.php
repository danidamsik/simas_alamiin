<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Periode;
use App\Models\Session;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_reset_user_password_manually(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->guru()->create();

        $response = $this->actingAs($admin)
            ->from('/dashboard')
            ->put(route('admin.users.password.update', $target), [
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response->assertRedirect('/dashboard');
        $this->assertTrue(Hash::check('new-password', $target->fresh()->password));
    }

    public function test_non_admin_cannot_reset_user_password_manually(): void
    {
        $guru = User::factory()->guru()->create();
        $target = User::factory()->create();

        $this->actingAs($guru)
            ->put(route('admin.users.password.update', $target), [
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertForbidden();
    }

    public function test_kepala_sekolah_cannot_reset_user_password_manually(): void
    {
        $kepalaSekolah = User::factory()->kepalaSekolah()->create();
        $target = User::factory()->create();

        $this->actingAs($kepalaSekolah)
            ->put(route('admin.users.password.update', $target), [
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertForbidden();
    }

    public function test_absensi_policy_limits_guru_to_their_own_absensi(): void
    {
        $guruUser = User::factory()->guru()->create();
        $otherGuruUser = User::factory()->guru()->create();
        $ownAbsensi = $this->createAbsensiFor($guruUser, '2025-04-21');
        $otherAbsensi = $this->createAbsensiFor($otherGuruUser, '2025-04-22');

        $this->assertTrue($guruUser->can('view', $ownAbsensi));
        $this->assertTrue($guruUser->can('update', $ownAbsensi));
        $this->assertFalse($guruUser->can('view', $otherAbsensi));
        $this->assertFalse($guruUser->can('update', $otherAbsensi));
        $this->assertFalse($guruUser->can('delete', $ownAbsensi));
    }

    public function test_kepala_sekolah_can_view_absensi_but_cannot_modify_it(): void
    {
        $kepalaSekolah = User::factory()->kepalaSekolah()->create();
        $guruUser = User::factory()->guru()->create();
        $absensi = $this->createAbsensiFor($guruUser, '2025-04-21');

        $this->assertTrue($kepalaSekolah->can('view', $absensi));
        $this->assertFalse($kepalaSekolah->can('create', Absensi::class));
        $this->assertFalse($kepalaSekolah->can('update', $absensi));
        $this->assertFalse($kepalaSekolah->can('delete', $absensi));
    }

    public function test_admin_can_manage_any_absensi(): void
    {
        $admin = User::factory()->admin()->create();
        $guruUser = User::factory()->guru()->create();
        $absensi = $this->createAbsensiFor($guruUser, '2025-04-21');

        $this->assertTrue($admin->can('view', $absensi));
        $this->assertTrue($admin->can('create', Absensi::class));
        $this->assertTrue($admin->can('update', $absensi));
        $this->assertTrue($admin->can('delete', $absensi));
    }

    private function createAbsensiFor(User $guruUser, string $tanggal): Absensi
    {
        $kelas = Kelas::query()->firstOrCreate(['nama_kelas' => 'X IPA 1']);
        $periode = Periode::query()->firstOrCreate([
            'tahun_ajaran' => '2025/2026',
            'semester' => 'ganjil',
        ], [
            'is_active' => true,
        ]);
        $session = Session::query()->firstOrCreate([
            'nama_sesi' => 'Jam 1',
        ], [
            'jam_mulai' => '07:00:00',
            'jam_selesai' => '07:45:00',
        ]);
        $guru = Guru::query()->create([
            'user_id' => $guruUser->id,
            'nama' => $guruUser->name,
            'is_active' => true,
        ]);

        return Absensi::query()->create([
            'tanggal' => $tanggal,
            'kelas_id' => $kelas->id,
            'guru_id' => $guru->id,
            'session_id' => $session->id,
            'periode_id' => $periode->id,
        ]);
    }
}
