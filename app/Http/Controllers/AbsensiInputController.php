<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\AuditLog;
use App\Models\Jadwal;
use App\Models\Periode;
use App\Models\Siswa;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AbsensiInputController extends Controller
{
    private const DAYS = [
        1 => 'senin',
        2 => 'selasa',
        3 => 'rabu',
        4 => 'kamis',
        5 => 'jumat',
        6 => 'sabtu',
        7 => 'minggu',
    ];

    public function index(Request $request): Response
    {
        $activePeriode = Periode::query()->where('is_active', true)->first();
        $today = now()->toDateString();
        $hari = $this->todayName();

        $jadwals = collect();

        if ($activePeriode) {
            $jadwals = Jadwal::query()
                ->with([
                    'kelas:id,nama_kelas',
                    'guru:id,nama,user_id',
                    'session:id,nama_sesi,jam_mulai,jam_selesai',
                    'periode:id,tahun_ajaran,semester,is_active',
                ])
                ->where('periode_id', $activePeriode->id)
                ->where('hari', $hari)
                ->when($request->user()->hasRole(User::ROLE_GURU), function ($query) use ($request) {
                    $query->where('guru_id', $request->user()->guru?->id ?? 0);
                })
                ->orderBy('session_id')
                ->get()
                ->map(function (Jadwal $jadwal) use ($today, $request) {
                    $jadwal->setAttribute('can_input_now', $this->canInputNow($jadwal, $request->user()));
                    $jadwal->setAttribute('already_input', Absensi::query()
                        ->where('tanggal', $today)
                        ->where('kelas_id', $jadwal->kelas_id)
                        ->where('session_id', $jadwal->session_id)
                        ->where('periode_id', $jadwal->periode_id)
                        ->exists());

                    return $jadwal;
                });
        }

        return Inertia::render('Absensi/Input/Index', [
            'activePeriode' => $activePeriode,
            'today' => $today,
            'hari' => $hari,
            'jadwals' => $jadwals,
        ]);
    }

    public function create(Request $request, Jadwal $jadwal): Response|RedirectResponse
    {
        $jadwal->load(['kelas:id,nama_kelas', 'guru:id,nama,user_id', 'session:id,nama_sesi,jam_mulai,jam_selesai', 'periode:id,tahun_ajaran,semester,is_active']);

        try {
            $this->validateJadwalForInput($jadwal, $request->user());
        } catch (ValidationException $exception) {
            return redirect()->route('absensi.input.index')->with('error', collect($exception->errors())->flatten()->first());
        }

        $students = Siswa::query()
            ->where('kelas_id', $jadwal->kelas_id)
            ->where('is_active', true)
            ->orderBy('nama')
            ->get(['id', 'nama', 'nis']);

        return Inertia::render('Absensi/Input/Create', [
            'jadwal' => $jadwal,
            'students' => $students,
            'today' => now()->toDateString(),
        ]);
    }

    public function store(Request $request, Jadwal $jadwal): RedirectResponse
    {
        $jadwal->load(['kelas:id,nama_kelas', 'guru:id,nama,user_id', 'session:id,nama_sesi,jam_mulai,jam_selesai', 'periode:id,tahun_ajaran,semester,is_active']);
        $this->validateJadwalForInput($jadwal, $request->user());

        $students = Siswa::query()
            ->where('kelas_id', $jadwal->kelas_id)
            ->where('is_active', true)
            ->pluck('id');

        $validated = $request->validate([
            'details' => ['required', 'array', 'size:'.$students->count()],
            'details.*.siswa_id' => ['required', Rule::in($students->all())],
            'details.*.status' => ['required', Rule::in(['hadir', 'izin', 'sakit', 'alfa'])],
        ]);

        $detailRows = collect($validated['details'])->keyBy('siswa_id');
        if ($detailRows->count() !== $students->count()) {
            throw ValidationException::withMessages([
                'details' => 'Setiap siswa aktif harus memiliki tepat satu status absensi.',
            ]);
        }

        try {
            DB::transaction(function () use ($jadwal, $detailRows, $request) {
                $absensi = Absensi::query()->create([
                    'tanggal' => now()->toDateString(),
                    'kelas_id' => $jadwal->kelas_id,
                    'guru_id' => $jadwal->guru_id,
                    'session_id' => $jadwal->session_id,
                    'periode_id' => $jadwal->periode_id,
                ]);

                $now = now();
                DB::table('absensi_detail')->insert($detailRows->map(fn ($detail) => [
                    'absensi_id' => $absensi->id,
                    'siswa_id' => $detail['siswa_id'],
                    'status' => $detail['status'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->values()->all());

                if ($request->user()->hasRole(User::ROLE_ADMIN)) {
                    AuditLog::query()->create([
                        'user_id' => $request->user()->id,
                        'action' => 'create',
                        'model' => Absensi::class,
                        'model_id' => $absensi->id,
                        'old_value' => null,
                        'new_value' => [
                            'tanggal' => $absensi->tanggal->toDateString(),
                            'kelas_id' => $absensi->kelas_id,
                            'guru_id' => $absensi->guru_id,
                            'session_id' => $absensi->session_id,
                            'periode_id' => $absensi->periode_id,
                            'details' => $detailRows
                                ->sortKeys()
                                ->map(fn ($detail) => [
                                    'siswa_id' => $detail['siswa_id'],
                                    'status' => $detail['status'],
                                ])
                                ->values()
                                ->all(),
                        ],
                        'ip_address' => $request->ip(),
                        'created_at' => now(),
                    ]);
                }
            });
        } catch (QueryException $exception) {
            if ($exception->getCode() === '23000') {
                throw ValidationException::withMessages([
                    'jadwal' => 'Absensi untuk kelas dan session ini sudah pernah diinput.',
                ]);
            }

            throw $exception;
        }

        return redirect()->route('absensi.riwayat')->with('success', 'Absensi berhasil disimpan.');
    }

    private function validateJadwalForInput(Jadwal $jadwal, User $user): void
    {
        $activePeriode = Periode::query()->where('is_active', true)->first();
        if (! $activePeriode) {
            throw ValidationException::withMessages(['periode' => 'Tidak ada periode aktif.']);
        }

        if ((int) $jadwal->periode_id !== (int) $activePeriode->id) {
            throw ValidationException::withMessages(['periode' => 'Absensi hanya dapat diinput untuk periode aktif.']);
        }

        if ($user->hasRole(User::ROLE_GURU) && (int) $jadwal->guru_id !== (int) ($user->guru?->id ?? 0)) {
            abort(403);
        }

        if ($jadwal->hari !== $this->todayName()) {
            throw ValidationException::withMessages(['hari' => 'Absensi hanya dapat diinput pada hari sesuai jadwal.']);
        }

        if (! $this->isWithinSessionTime($jadwal)) {
            throw ValidationException::withMessages(['session' => 'Absensi hanya dapat diinput saat session berlangsung.']);
        }
    }

    private function canInputNow(Jadwal $jadwal, User $user): bool
    {
        try {
            $this->validateJadwalForInput($jadwal, $user);

            return true;
        } catch (ValidationException) {
            return false;
        }
    }

    private function isWithinSessionTime(Jadwal $jadwal): bool
    {
        $now = now();
        $start = Carbon::parse($now->toDateString().' '.$jadwal->session->jam_mulai->format('H:i:s'));
        $end = Carbon::parse($now->toDateString().' '.$jadwal->session->jam_selesai->format('H:i:s'));

        return $now->betweenIncluded($start, $end);
    }

    private function todayName(): string
    {
        return self::DAYS[now()->dayOfWeekIso];
    }
}
