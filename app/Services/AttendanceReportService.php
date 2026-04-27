<?php

namespace App\Services;

use App\Models\AbsensiDetail;
use App\Models\Kelas;
use App\Models\Periode;

class AttendanceReportService
{
    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function build(array $filters): array
    {
        $periode = filled($filters['periode_id'] ?? null)
            ? Periode::query()->findOrFail($filters['periode_id'])
            : null;
        $kelas = Kelas::query()->findOrFail($filters['kelas_id']);

        $rows = AbsensiDetail::query()
            ->select('absensi_detail.id', 'absensi_detail.siswa_id', 'absensi_detail.status')
            ->with('siswa:id,nama,nis')
            ->whereHas('absensi', function ($query) use ($filters) {
                $query
                    ->where('kelas_id', $filters['kelas_id'])
                    ->when($filters['periode_id'] ?? null, fn ($query, $periodeId) => $query->where('periode_id', $periodeId))
                    ->when($filters['tanggal_mulai'] ?? null, fn ($query, $date) => $query->whereDate('tanggal', '>=', $date))
                    ->when($filters['tanggal_selesai'] ?? null, fn ($query, $date) => $query->whereDate('tanggal', '<=', $date));
            })
            ->get()
            ->groupBy('siswa_id')
            ->map(function ($details) {
                $student = $details->first()->siswa;
                $counts = $details->countBy('status');
                $hadir = (int) ($counts['hadir'] ?? 0);
                $izin = (int) ($counts['izin'] ?? 0);
                $sakit = (int) ($counts['sakit'] ?? 0);
                $alfa = (int) ($counts['alfa'] ?? 0);
                $total = $hadir + $izin + $sakit + $alfa;

                return [
                    'siswa_id' => $student->id,
                    'nama' => $student->nama,
                    'nis' => $student->nis,
                    'hadir' => $hadir,
                    'izin' => $izin,
                    'sakit' => $sakit,
                    'alfa' => $alfa,
                    'total' => $total,
                    'persentase' => $total > 0 ? round(($hadir / $total) * 100, 1) : 0,
                ];
            })
            ->sortBy('nama')
            ->values();

        return [
            'periode' => $periode,
            'kelas' => $kelas,
            'filters' => $filters,
            'rows' => $rows,
            'totals' => [
                'hadir' => $rows->sum('hadir'),
                'izin' => $rows->sum('izin'),
                'sakit' => $rows->sum('sakit'),
                'alfa' => $rows->sum('alfa'),
                'total' => $rows->sum('total'),
            ],
        ];
    }
}
