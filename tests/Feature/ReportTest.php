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

class ReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_preview_attendance_report(): void
    {
        $admin = User::factory()->admin()->create();
        [$periode, $kelas] = $this->reportRecord();

        $this->actingAs($admin)->get(route('laporan.index', [
            'periode_id' => $periode->id,
            'kelas_id' => $kelas->id,
        ]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Laporan/Index')
                ->where('periodeOptions.0.tanggal_mulai', '2025-07-01')
                ->where('periodeOptions.0.tanggal_selesai', '2025-12-31')
                ->where('filters.tanggal_mulai', '2025-07-01')
                ->where('filters.tanggal_selesai', '2025-12-31')
                ->where('report.kelas.id', $kelas->id)
                ->where('report.periode.id', $periode->id)
                ->where('report.periode_label', '2025/2026 ganjil')
                ->has('report.rows', 2)
                ->where('report.rows.0.hadir', 1)
                ->where('report.rows.0.persentase', 50)
                ->where('report.totals.hadir', 2)
                ->where('report.totals.izin', 1)
                ->where('report.totals.sakit', 1));
    }

    public function test_admin_can_preview_attendance_report_for_all_periods(): void
    {
        $admin = User::factory()->admin()->create();
        [, $kelas] = $this->reportRecord();
        $secondPeriod = Periode::query()->create([
            'tahun_ajaran' => '2025/2026',
            'semester' => 'genap',
            'tanggal_mulai' => '2026-01-01',
            'tanggal_selesai' => '2026-06-30',
            'is_active' => false,
        ]);

        $thirdAbsensi = Absensi::query()->create([
            'tanggal' => '2026-05-01',
            'kelas_id' => $kelas->id,
            'guru_id' => Guru::query()->firstOrFail()->id,
            'session_id' => Session::query()->firstOrFail()->id,
            'periode_id' => $secondPeriod->id,
        ]);
        $thirdAbsensi->absensiDetail()->createMany(
            Siswa::query()
                ->where('kelas_id', $kelas->id)
                ->orderBy('nama')
                ->get(['id'])
                ->map(fn (Siswa $siswa) => ['siswa_id' => $siswa->id, 'status' => 'hadir'])
                ->all()
        );

        $this->actingAs($admin)->get(route('laporan.index', [
            'kelas_id' => $kelas->id,
        ]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Laporan/Index')
                ->where('report.periode', null)
                ->where('report.periode_label', 'Semua Periode')
                ->has('report.rows', 2)
                ->where('report.totals.hadir', 4)
                ->where('report.totals.izin', 1)
                ->where('report.totals.sakit', 1));
    }

    public function test_kepala_sekolah_can_export_pdf_and_excel_report(): void
    {
        $kepalaSekolah = User::factory()->kepalaSekolah()->create();
        [$periode, $kelas] = $this->reportRecord();
        $query = ['periode_id' => $periode->id, 'kelas_id' => $kelas->id];

        $this->actingAs($kepalaSekolah)
            ->get(route('laporan.export.pdf', $query))
            ->assertOk()
            ->assertDownload('laporan-absensi-x-ipa-1-2025-2026-ganjil.pdf');

        $this->actingAs($kepalaSekolah)
            ->get(route('laporan.export.excel', $query))
            ->assertOk()
            ->assertDownload('laporan-absensi-x-ipa-1-2025-2026-ganjil.xlsx');
    }

    public function test_report_requires_mandatory_filters_before_export(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->from(route('laporan.index'))
            ->get(route('laporan.export.pdf'))
            ->assertRedirect(route('laporan.index'))
            ->assertSessionHasErrors(['kelas_id'])
            ->assertSessionDoesntHaveErrors(['periode_id']);
    }

    public function test_empty_report_cannot_be_exported(): void
    {
        $admin = User::factory()->admin()->create();
        $periode = Periode::query()->create([
            'tahun_ajaran' => '2025/2026',
            'semester' => 'ganjil',
            'tanggal_mulai' => '2025-07-01',
            'tanggal_selesai' => '2025-12-31',
            'is_active' => true,
        ]);
        $kelas = Kelas::query()->create(['nama_kelas' => 'X IPA 1']);

        $this->actingAs($admin)
            ->get(route('laporan.export.excel', ['periode_id' => $periode->id, 'kelas_id' => $kelas->id]))
            ->assertRedirect(route('laporan.index', ['periode_id' => $periode->id, 'kelas_id' => $kelas->id]))
            ->assertSessionHas('error', 'Laporan tidak dapat diekspor karena data absensi kosong.');
    }

    public function test_guru_cannot_access_report_page(): void
    {
        $guru = User::factory()->guru()->create();

        $this->actingAs($guru)->get(route('laporan.index'))->assertForbidden();
    }

    /**
     * @return array{0: Periode, 1: Kelas}
     */
    private function reportRecord(): array
    {
        $guruUser = User::factory()->guru()->create();
        $guru = Guru::query()->create(['user_id' => $guruUser->id, 'nama' => 'Budi Santoso', 'is_active' => true]);
        $kelas = Kelas::query()->create(['nama_kelas' => 'X IPA 1']);
        $periode = Periode::query()->create([
            'tahun_ajaran' => '2025/2026',
            'semester' => 'ganjil',
            'tanggal_mulai' => '2025-07-01',
            'tanggal_selesai' => '2025-12-31',
            'is_active' => true,
        ]);
        $session = Session::query()->create(['nama_sesi' => 'Jam 1', 'jam_mulai' => '07:00', 'jam_selesai' => '07:45']);
        $students = [
            Siswa::query()->create(['nama' => 'Andi Pratama', 'nis' => '2526001001', 'kelas_id' => $kelas->id, 'is_active' => true]),
            Siswa::query()->create(['nama' => 'Bella Safitri', 'nis' => '2526001002', 'kelas_id' => $kelas->id, 'is_active' => true]),
        ];

        $firstAbsensi = Absensi::query()->create([
            'tanggal' => '2025-07-21',
            'kelas_id' => $kelas->id,
            'guru_id' => $guru->id,
            'session_id' => $session->id,
            'periode_id' => $periode->id,
        ]);
        $firstAbsensi->absensiDetail()->createMany([
            ['siswa_id' => $students[0]->id, 'status' => 'hadir'],
            ['siswa_id' => $students[1]->id, 'status' => 'hadir'],
        ]);

        $secondAbsensi = Absensi::query()->create([
            'tanggal' => '2025-07-22',
            'kelas_id' => $kelas->id,
            'guru_id' => $guru->id,
            'session_id' => $session->id,
            'periode_id' => $periode->id,
        ]);
        $secondAbsensi->absensiDetail()->createMany([
            ['siswa_id' => $students[0]->id, 'status' => 'izin'],
            ['siswa_id' => $students[1]->id, 'status' => 'sakit'],
        ]);

        return [$periode, $kelas];
    }
}
