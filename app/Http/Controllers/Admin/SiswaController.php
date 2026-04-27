<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Periode;
use App\Models\Siswa;
use App\Models\SiswaKelasHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class SiswaController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->only('search', 'kelas_id', 'status');

        return Inertia::render('Admin/Siswa/Index', [
            'siswas' => Siswa::query()
                ->with('kelas:id,nama_kelas')
                ->when($filters['search'] ?? null, function ($query, string $search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('nama', 'like', "%{$search}%")
                            ->orWhere('nis', 'like', "%{$search}%");
                    });
                })
                ->when($filters['kelas_id'] ?? null, fn ($query, $kelasId) => $query->where('kelas_id', $kelasId))
                ->when(($filters['status'] ?? 'active') !== 'all', fn ($query) => $query->where('is_active', true))
                ->orderBy('nama')
                ->paginate(25)
                ->withQueryString(),
            'kelasOptions' => Kelas::query()->orderBy('nama_kelas')->get(['id', 'nama_kelas']),
            'filters' => $filters,
        ]);
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('siswa.index');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nis' => ['required', 'string', 'max:255', Rule::unique('siswa', 'nis')],
            'kelas_id' => ['required', Rule::exists('kelas', 'id')],
            'is_active' => ['boolean'],
        ]);

        Siswa::query()->create([
            ...$validated,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return back()->with('success', 'Siswa berhasil ditambahkan.');
    }

    public function show(Siswa $siswa): RedirectResponse
    {
        return redirect()->route('siswa.index');
    }

    public function edit(Siswa $siswa): RedirectResponse
    {
        return redirect()->route('siswa.index');
    }

    public function update(Request $request, Siswa $siswa): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nis' => ['required', 'string', 'max:255', Rule::unique('siswa', 'nis')->ignore($siswa->id)],
            'kelas_id' => ['required', Rule::exists('kelas', 'id')],
            'is_active' => ['boolean'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($siswa, $validated) {
            if ((int) $validated['kelas_id'] !== (int) $siswa->kelas_id) {
                $activePeriode = Periode::query()->where('is_active', true)->first();

                SiswaKelasHistory::query()->create([
                    'siswa_id' => $siswa->id,
                    'kelas_id' => $siswa->kelas_id,
                    'periode_id' => $activePeriode?->id ?? Periode::query()->value('id'),
                    'tanggal_masuk' => $siswa->created_at?->toDateString() ?? now()->toDateString(),
                    'tanggal_keluar' => now()->toDateString(),
                    'keterangan' => $validated['keterangan'] ?: 'pindah kelas',
                ]);
            }

            $siswa->update([
                'nama' => $validated['nama'],
                'nis' => $validated['nis'],
                'kelas_id' => $validated['kelas_id'],
                'is_active' => $validated['is_active'] ?? true,
            ]);
        });

        return back()->with('success', 'Siswa berhasil diperbarui.');
    }

    public function destroy(Siswa $siswa): RedirectResponse
    {
        $siswa->update(['is_active' => false]);

        return back()->with('success', 'Siswa berhasil dinonaktifkan.');
    }
}
