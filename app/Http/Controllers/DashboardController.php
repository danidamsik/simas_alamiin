<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\AbsensiDetail;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Periode;
use App\Models\Siswa;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    private const STATUSES = ['hadir', 'izin', 'sakit', 'alfa'];

    private const DAYS = [
        1 => 'senin',
        2 => 'selasa',
        3 => 'rabu',
        4 => 'kamis',
        5 => 'jumat',
        6 => 'sabtu',
        7 => 'minggu',
    ];

    public function __invoke(Request $request): Response
    {
        $user = $request->user();
        $activePeriode = Periode::query()
            ->where('is_active', true)
            ->first(['id', 'tahun_ajaran', 'semester', 'is_active']);

        $props = [
            'role' => $user->role,
            'today' => now()->toDateString(),
            'hari' => $this->todayName(),
            'activePeriode' => $activePeriode,
        ];

        if ($user->hasRole(User::ROLE_GURU)) {
            $props['guruDashboard'] = $this->guruDashboard($user, $activePeriode);
        } else {
            $props['schoolDashboard'] = $this->schoolDashboard($activePeriode);
        }

        return Inertia::render('Dashboard', $props);
    }

    /**
     * @return array<string, mixed>
     */
    private function schoolDashboard(?Periode $activePeriode): array
    {
        $today = now()->toDateString();
        $emptyCounts = $this->emptyStatusCounts();

        if (! $activePeriode) {
            return [
                'totalSiswaAktif' => Siswa::query()->where('is_active', true)->count(),
                'todayCounts' => $emptyCounts,
                'todayPercentage' => 0,
                'trend' => $this->emptyTrend(),
                'lowestClasses' => [],
            ];
        }

        $todayCounts = $this->statusCounts($today, $today, $activePeriode->id);
        $todayTotal = array_sum($todayCounts);

        return [
            'totalSiswaAktif' => Siswa::query()->where('is_active', true)->count(),
            'todayCounts' => $todayCounts,
            'todayPercentage' => $todayTotal > 0
                ? round(($todayCounts['hadir'] / $todayTotal) * 100, 1)
                : 0,
            'trend' => $this->trend($activePeriode->id),
            'lowestClasses' => $this->lowestClasses($today, $activePeriode->id),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function guruDashboard(User $user, ?Periode $activePeriode): array
    {
        $user->loadMissing('guru:id,user_id,nama');

        if (! $user->guru || ! $activePeriode) {
            return [
                'guru' => $user->guru,
                'jadwals' => [],
            ];
        }

        $today = now()->toDateString();
        $jadwals = Jadwal::query()
            ->with([
                'kelas:id,nama_kelas',
                'guru:id,nama,user_id',
                'session:id,nama_sesi,jam_mulai,jam_selesai',
                'periode:id,tahun_ajaran,semester,is_active',
            ])
            ->where('guru_id', $user->guru->id)
            ->where('periode_id', $activePeriode->id)
            ->where('hari', $this->todayName())
            ->orderBy('session_id')
            ->get();

        $absensis = Absensi::query()
            ->with([
                'absensiDetail:id,absensi_id,status',
                'kelas:id,nama_kelas',
                'session:id,nama_sesi,jam_mulai,jam_selesai',
            ])
            ->whereDate('tanggal', $today)
            ->where('guru_id', $user->guru->id)
            ->where('periode_id', $activePeriode->id)
            ->when($jadwals->isNotEmpty(), function ($query) use ($jadwals) {
                $query
                    ->whereIn('kelas_id', $jadwals->pluck('kelas_id')->unique())
                    ->whereIn('session_id', $jadwals->pluck('session_id')->unique());
            }, fn ($query) => $query->whereRaw('1 = 0'))
            ->get()
            ->keyBy(fn (Absensi $absensi) => $this->attendanceKey($absensi->kelas_id, $absensi->session_id, $absensi->periode_id));

        return [
            'guru' => $user->guru,
            'jadwals' => $jadwals->map(function (Jadwal $jadwal) use ($absensis) {
                $absensi = $absensis->get($this->attendanceKey($jadwal->kelas_id, $jadwal->session_id, $jadwal->periode_id));
                $counts = $absensi ? $this->countsFromDetails($absensi->absensiDetail) : $this->emptyStatusCounts();
                $total = array_sum($counts);

                return [
                    'id' => $jadwal->id,
                    'mata_pelajaran' => $jadwal->mata_pelajaran,
                    'kelas' => $jadwal->kelas,
                    'session' => $jadwal->session,
                    'already_input' => (bool) $absensi,
                    'absensi_id' => $absensi?->id,
                    'counts' => $counts,
                    'total_hadir' => $counts['hadir'],
                    'total_tidak_hadir' => max($total - $counts['hadir'], 0),
                ];
            })->values(),
        ];
    }

    /**
     * @return array<string, int>
     */
    private function statusCounts(string $startDate, string $endDate, int $periodeId): array
    {
        $rows = AbsensiDetail::query()
            ->join('absensi', 'absensi.id', '=', 'absensi_detail.absensi_id')
            ->whereBetween('absensi.tanggal', [$startDate, $endDate])
            ->where('absensi.periode_id', $periodeId)
            ->groupBy('absensi_detail.status')
            ->select('absensi_detail.status', DB::raw('COUNT(*) as total'))
            ->pluck('total', 'status');

        return collect(self::STATUSES)
            ->mapWithKeys(fn (string $status) => [$status => (int) ($rows[$status] ?? 0)])
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function trend(int $periodeId): array
    {
        $start = now()->subDays(6)->toDateString();
        $end = now()->toDateString();

        $rows = AbsensiDetail::query()
            ->join('absensi', 'absensi.id', '=', 'absensi_detail.absensi_id')
            ->whereBetween('absensi.tanggal', [$start, $end])
            ->where('absensi.periode_id', $periodeId)
            ->groupBy('absensi.tanggal', 'absensi_detail.status')
            ->orderBy('absensi.tanggal')
            ->select('absensi.tanggal', 'absensi_detail.status', DB::raw('COUNT(*) as total'))
            ->get()
            ->groupBy(fn ($row) => Carbon::parse($row->tanggal)->toDateString());

        return collect(range(6, 0))
            ->map(function (int $daysAgo) use ($rows) {
                $date = now()->subDays($daysAgo);
                $dateKey = $date->toDateString();
                $dayRows = $rows->get($dateKey, collect())->keyBy('status');

                return [
                    'date' => $dateKey,
                    'label' => $date->format('d M'),
                    'counts' => collect(self::STATUSES)
                        ->mapWithKeys(fn (string $status) => [$status => (int) ($dayRows[$status]->total ?? 0)])
                        ->all(),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function emptyTrend(): array
    {
        return collect(range(6, 0))
            ->map(fn (int $daysAgo) => [
                'date' => now()->subDays($daysAgo)->toDateString(),
                'label' => now()->subDays($daysAgo)->format('d M'),
                'counts' => $this->emptyStatusCounts(),
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function lowestClasses(string $today, int $periodeId): array
    {
        return Kelas::query()
            ->select('id', 'nama_kelas')
            ->with([
                'absensi' => fn ($query) => $query
                    ->select('id', 'kelas_id', 'tanggal', 'periode_id')
                    ->whereDate('tanggal', $today)
                    ->where('periode_id', $periodeId),
                'absensi.absensiDetail:id,absensi_id,status',
            ])
            ->whereHas('absensi', fn ($query) => $query
                ->whereDate('tanggal', $today)
                ->where('periode_id', $periodeId))
            ->get()
            ->map(function (Kelas $kelas) {
                $details = $kelas->absensi->flatMap->absensiDetail;
                $counts = $this->countsFromDetails($details);
                $total = array_sum($counts);

                return [
                    'id' => $kelas->id,
                    'nama_kelas' => $kelas->nama_kelas,
                    'counts' => $counts,
                    'total' => $total,
                    'attendance_percentage' => $total > 0 ? round(($counts['hadir'] / $total) * 100, 1) : 0,
                ];
            })
            ->sortBy('attendance_percentage')
            ->take(5)
            ->values()
            ->all();
    }

    /**
     * @param  iterable<int, \App\Models\AbsensiDetail>  $details
     * @return array<string, int>
     */
    private function countsFromDetails(iterable $details): array
    {
        $grouped = collect($details)->countBy('status');

        return collect(self::STATUSES)
            ->mapWithKeys(fn (string $status) => [$status => (int) ($grouped[$status] ?? 0)])
            ->all();
    }

    /**
     * @return array<string, int>
     */
    private function emptyStatusCounts(): array
    {
        return collect(self::STATUSES)
            ->mapWithKeys(fn (string $status) => [$status => 0])
            ->all();
    }

    private function attendanceKey(int $kelasId, int $sessionId, int $periodeId): string
    {
        return "{$kelasId}-{$sessionId}-{$periodeId}";
    }

    private function todayName(): string
    {
        return self::DAYS[now()->dayOfWeekIso];
    }
}
