<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Periode;
use App\Models\Session;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class JadwalController extends Controller
{
    private const DAYS = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];

    public function index(Request $request): Response
    {
        $filters = $request->only('periode_id', 'hari', 'kelas_id', 'guru_id');

        $jadwals = Jadwal::query()
            ->with([
                'kelas:id,nama_kelas',
                'guru:id,nama',
                'session:id,nama_sesi,jam_mulai,jam_selesai',
                'periode:id,tahun_ajaran,semester,is_active',
            ])
            ->when($filters['periode_id'] ?? null, fn ($query, $periodeId) => $query->where('periode_id', $periodeId))
            ->when($filters['hari'] ?? null, fn ($query, $hari) => $query->where('hari', $hari))
            ->when($filters['kelas_id'] ?? null, fn ($query, $kelasId) => $query->where('kelas_id', $kelasId))
            ->when($filters['guru_id'] ?? null, fn ($query, $guruId) => $query->where('guru_id', $guruId))
            ->get()
            ->sortBy(fn (Jadwal $jadwal) => array_search($jadwal->hari, self::DAYS, true).'-'.$jadwal->session?->jam_mulai?->format('H:i').'-'.$jadwal->kelas?->nama_kelas)
            ->groupBy('hari')
            ->map(fn ($items) => $items->values());

        return Inertia::render('Admin/Jadwal/Index', [
            'jadwalsByDay' => $jadwals,
            'days' => self::DAYS,
            'kelasOptions' => Kelas::query()->orderBy('nama_kelas')->get(['id', 'nama_kelas']),
            'guruOptions' => Guru::query()->where('is_active', true)->orderBy('nama')->get(['id', 'nama']),
            'sessionOptions' => Session::query()->orderBy('jam_mulai')->get(['id', 'nama_sesi', 'jam_mulai', 'jam_selesai']),
            'periodeOptions' => Periode::query()->orderByDesc('is_active')->orderByDesc('id')->get(['id', 'tahun_ajaran', 'semester', 'is_active']),
            'filters' => $filters,
        ]);
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('jadwal.index');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);
        $this->ensureNoDuplicate($validated);

        Jadwal::query()->create($validated);

        return back()->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function show(Jadwal $jadwal): RedirectResponse
    {
        return redirect()->route('jadwal.index');
    }

    public function edit(Jadwal $jadwal): RedirectResponse
    {
        return redirect()->route('jadwal.index');
    }

    public function update(Request $request, Jadwal $jadwal): RedirectResponse
    {
        $validated = $this->validated($request);
        $this->ensureNoDuplicate($validated, $jadwal);

        $jadwal->update($validated);

        return back()->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(Jadwal $jadwal): RedirectResponse
    {
        $jadwal->delete();

        return back()->with('success', 'Jadwal berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'kelas_id' => ['required', Rule::exists('kelas', 'id')],
            'guru_id' => ['required', Rule::exists('guru', 'id')],
            'mata_pelajaran' => ['required', 'string', 'max:255'],
            'hari' => ['required', Rule::in(self::DAYS)],
            'session_id' => ['required', Rule::exists('sessions', 'id')],
            'periode_id' => ['required', Rule::exists('periode', 'id')],
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function ensureNoDuplicate(array $data, ?Jadwal $current = null): void
    {
        $base = Jadwal::query()
            ->with(['kelas:id,nama_kelas', 'guru:id,nama', 'session:id,nama_sesi'])
            ->when($current, fn ($query) => $query->whereKeyNot($current->id))
            ->where('hari', $data['hari'])
            ->where('session_id', $data['session_id'])
            ->where('periode_id', $data['periode_id']);

        $kelasConflict = (clone $base)->where('kelas_id', $data['kelas_id'])->first();
        if ($kelasConflict) {
            throw ValidationException::withMessages([
                'kelas_id' => "Kelas {$kelasConflict->kelas->nama_kelas} sudah memiliki jadwal pada {$data['hari']} {$kelasConflict->session->nama_sesi}.",
            ]);
        }

        $guruConflict = (clone $base)->where('guru_id', $data['guru_id'])->first();
        if ($guruConflict) {
            throw ValidationException::withMessages([
                'guru_id' => "Guru {$guruConflict->guru->nama} sudah mengajar pada {$data['hari']} {$guruConflict->session->nama_sesi}.",
            ]);
        }
    }
}
