<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\AbsensiDetail;
use App\Models\Kelas;
use App\Models\Periode;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AbsensiHistoryController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->only('tanggal', 'kelas_id', 'periode_id');

        if ($request->user()->hasRole(User::ROLE_KEPALA_SEKOLAH)) {
            return $this->kepalaSekolahIndex($request, $filters);
        }

        return Inertia::render('Absensi/Riwayat/Index', [
            'absensis' => Absensi::query()
                ->with([
                    'kelas:id,nama_kelas',
                    'guru:id,nama,user_id',
                    'session:id,nama_sesi,jam_mulai,jam_selesai',
                    'periode:id,tahun_ajaran,semester,is_active',
                    'absensiDetail:id,absensi_id,status',
                ])
                ->when($request->user()->hasRole(User::ROLE_GURU), function ($query) use ($request) {
                    $query->where('guru_id', $request->user()->guru?->id ?? 0);
                })
                ->when($filters['tanggal'] ?? null, fn ($query, $tanggal) => $query->whereDate('tanggal', $tanggal))
                ->when($filters['kelas_id'] ?? null, fn ($query, $kelasId) => $query->where('kelas_id', $kelasId))
                ->when($filters['periode_id'] ?? null, fn ($query, $periodeId) => $query->where('periode_id', $periodeId))
                ->latest('tanggal')
                ->latest()
                ->paginate(25)
                ->withQueryString(),
            'kelasOptions' => Kelas::query()->orderBy('nama_kelas')->get(['id', 'nama_kelas']),
            'periodeOptions' => Periode::query()->orderByDesc('is_active')->orderByDesc('id')->get(['id', 'tahun_ajaran', 'semester', 'is_active']),
            'filters' => $filters,
        ]);
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function kepalaSekolahIndex(Request $request, array $filters): Response
    {
        return Inertia::render('Absensi/KepalaSekolah/Index', [
            'details' => AbsensiDetail::query()
                ->select('absensi_detail.*')
                ->join('absensi', 'absensi.id', '=', 'absensi_detail.absensi_id')
                ->join('siswa', 'siswa.id', '=', 'absensi_detail.siswa_id')
                ->with([
                    'siswa:id,nama,nis',
                    'absensi:id,tanggal,kelas_id,guru_id,session_id,periode_id',
                    'absensi.kelas:id,nama_kelas',
                    'absensi.guru:id,nama,user_id',
                    'absensi.session:id,nama_sesi,jam_mulai,jam_selesai',
                    'absensi.periode:id,tahun_ajaran,semester,is_active',
                ])
                ->when($filters['tanggal'] ?? null, fn ($query, $tanggal) => $query->whereDate('absensi.tanggal', $tanggal))
                ->when($filters['kelas_id'] ?? null, fn ($query, $kelasId) => $query->where('absensi.kelas_id', $kelasId))
                ->when($filters['periode_id'] ?? null, fn ($query, $periodeId) => $query->where('absensi.periode_id', $periodeId))
                ->orderByDesc('absensi.tanggal')
                ->orderBy('absensi.kelas_id')
                ->orderBy('absensi.session_id')
                ->orderBy('siswa.nama')
                ->paginate(25)
                ->withQueryString(),
            'kelasOptions' => Kelas::query()->orderBy('nama_kelas')->get(['id', 'nama_kelas']),
            'periodeOptions' => Periode::query()->orderByDesc('is_active')->orderByDesc('id')->get(['id', 'tahun_ajaran', 'semester', 'is_active']),
            'filters' => $filters,
        ]);
    }
}
