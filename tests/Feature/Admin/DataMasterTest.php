<?php

namespace Tests\Feature\Admin;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Periode;
use App\Models\Siswa;
use App\Models\SiswaKelasHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DataMasterTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_data_master_pages(): void
    {
        $admin = User::factory()->admin()->create();

        foreach (['periode.index', 'kelas.index', 'siswa.index', 'guru.index', 'users.index'] as $route) {
            $this->actingAs($admin)->get(route($route))->assertOk();
        }
    }

    public function test_non_admin_cannot_open_data_master_pages(): void
    {
        $guru = User::factory()->guru()->create();

        $this->actingAs($guru)->get(route('siswa.index'))->assertForbidden();
    }

    public function test_set_active_period_deactivates_other_periods(): void
    {
        $admin = User::factory()->admin()->create();
        $old = Periode::query()->create(['tahun_ajaran' => '2024/2025', 'semester' => 'genap', 'is_active' => true]);
        $new = Periode::query()->create(['tahun_ajaran' => '2025/2026', 'semester' => 'ganjil', 'is_active' => false]);

        $this->actingAs($admin)->patch(route('periode.set-active', $new))->assertRedirect();

        $this->assertFalse($old->fresh()->is_active);
        $this->assertTrue($new->fresh()->is_active);
    }

    public function test_moving_student_records_class_history(): void
    {
        $admin = User::factory()->admin()->create();
        $periode = Periode::query()->create(['tahun_ajaran' => '2025/2026', 'semester' => 'ganjil', 'is_active' => true]);
        $oldClass = Kelas::query()->create(['nama_kelas' => 'X IPA 1']);
        $newClass = Kelas::query()->create(['nama_kelas' => 'X IPS 1']);
        $siswa = Siswa::query()->create([
            'nama' => 'Andi Pratama',
            'nis' => '2526001001',
            'kelas_id' => $oldClass->id,
            'is_active' => true,
        ]);

        $this->actingAs($admin)->put(route('siswa.update', $siswa), [
            'nama' => $siswa->nama,
            'nis' => $siswa->nis,
            'kelas_id' => $newClass->id,
            'is_active' => true,
            'keterangan' => 'pindah kelas',
        ])->assertRedirect();

        $this->assertSame($newClass->id, $siswa->fresh()->kelas_id);
        $this->assertDatabaseHas('siswa_kelas_history', [
            'siswa_id' => $siswa->id,
            'kelas_id' => $oldClass->id,
            'periode_id' => $periode->id,
            'keterangan' => 'pindah kelas',
        ]);
        $this->assertSame(1, SiswaKelasHistory::query()->count());
    }

    public function test_destroy_student_and_teacher_only_deactivates_them(): void
    {
        $admin = User::factory()->admin()->create();
        $kelas = Kelas::query()->create(['nama_kelas' => 'X IPA 1']);
        $siswa = Siswa::query()->create([
            'nama' => 'Andi Pratama',
            'nis' => '2526001001',
            'kelas_id' => $kelas->id,
            'is_active' => true,
        ]);
        $guruUser = User::factory()->guru()->create();
        $guru = Guru::query()->create([
            'user_id' => $guruUser->id,
            'nama' => 'Budi Santoso',
            'nip' => null,
            'is_active' => true,
        ]);

        $this->actingAs($admin)->delete(route('siswa.destroy', $siswa))->assertRedirect();
        $this->actingAs($admin)->delete(route('guru.destroy', $guru))->assertRedirect();

        $this->assertFalse($siswa->fresh()->is_active);
        $this->assertFalse($guru->fresh()->is_active);
        $this->assertDatabaseHas('siswa', ['id' => $siswa->id]);
        $this->assertDatabaseHas('guru', ['id' => $guru->id]);
    }
}
