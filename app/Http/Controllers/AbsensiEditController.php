<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\AuditLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AbsensiEditController extends Controller
{
    public function edit(Request $request, Absensi $absensi): Response
    {
        $absensi = $this->loadAbsensi($absensi);
        [$canEdit, $lockedReason] = $this->editState($absensi, $request->user());

        return Inertia::render('Absensi/Edit', [
            'absensi' => $absensi,
            'canEdit' => $canEdit,
            'lockedReason' => $lockedReason,
        ]);
    }

    public function update(Request $request, Absensi $absensi): RedirectResponse
    {
        $absensi = $this->loadAbsensi($absensi);
        [$canEdit, $lockedReason] = $this->editState($absensi, $request->user());

        if (! $canEdit) {
            throw ValidationException::withMessages([
                'edit' => $lockedReason ?? 'Absensi tidak dapat diedit.',
            ]);
        }

        $detailIds = $absensi->absensiDetail->pluck('siswa_id');
        $validated = $request->validate([
            'details' => ['required', 'array', 'size:'.$detailIds->count()],
            'details.*.siswa_id' => ['required', Rule::in($detailIds->all())],
            'details.*.status' => ['required', Rule::in(['hadir', 'izin', 'sakit', 'alfa'])],
        ]);

        $submittedDetails = collect($validated['details'])->keyBy('siswa_id');
        if ($submittedDetails->count() !== $detailIds->count()) {
            throw ValidationException::withMessages([
                'details' => 'Setiap siswa pada absensi ini harus memiliki tepat satu status.',
            ]);
        }

        DB::transaction(function () use ($absensi, $request, $submittedDetails) {
            $oldValue = $this->snapshot($absensi);

            foreach ($absensi->absensiDetail as $detail) {
                $detail->update([
                    'status' => $submittedDetails->get($detail->siswa_id)['status'],
                ]);
            }

            $absensi->load('absensiDetail.siswa:id,nama,nis');
            $newValue = $this->snapshot($absensi);

            if ($request->user()->hasRole(User::ROLE_ADMIN) && $oldValue !== $newValue) {
                AuditLog::query()->create([
                    'user_id' => $request->user()->id,
                    'action' => 'update',
                    'model' => Absensi::class,
                    'model_id' => $absensi->id,
                    'old_value' => $oldValue,
                    'new_value' => $newValue,
                    'ip_address' => $request->ip(),
                    'created_at' => now(),
                ]);
            }
        });

        return redirect()
            ->route($request->user()->hasRole(User::ROLE_ADMIN) ? 'absensi.index' : 'absensi.riwayat')
            ->with('success', 'Absensi berhasil diperbarui.');
    }

    private function loadAbsensi(Absensi $absensi): Absensi
    {
        return $absensi->load([
            'kelas:id,nama_kelas',
            'guru:id,nama,user_id',
            'session:id,nama_sesi,jam_mulai,jam_selesai',
            'periode:id,tahun_ajaran,semester,is_active',
            'absensiDetail.siswa:id,nama,nis',
        ]);
    }

    /**
     * @return array{0: bool, 1: string|null}
     */
    private function editState(Absensi $absensi, User $user): array
    {
        if ($user->hasRole(User::ROLE_ADMIN)) {
            return [true, null];
        }

        if ((int) $absensi->guru_id !== (int) ($user->guru?->id ?? 0)) {
            abort(403);
        }

        if (! $absensi->tanggal->isSameDay(now())) {
            return [false, 'Waktu edit telah berakhir, hubungi admin'];
        }

        $now = now();
        $start = Carbon::parse($now->toDateString().' '.$absensi->session->jam_mulai->format('H:i:s'));
        $deadline = Carbon::parse($now->toDateString().' '.$absensi->session->jam_selesai->format('H:i:s'))->addMinutes(30);

        if (! $now->betweenIncluded($start, $deadline)) {
            return [false, 'Waktu edit telah berakhir, hubungi admin'];
        }

        return [true, null];
    }

    private function snapshot(Absensi $absensi): array
    {
        return [
            'tanggal' => $absensi->tanggal->toDateString(),
            'kelas_id' => $absensi->kelas_id,
            'guru_id' => $absensi->guru_id,
            'session_id' => $absensi->session_id,
            'periode_id' => $absensi->periode_id,
            'details' => $absensi->absensiDetail
                ->sortBy('siswa_id')
                ->map(fn ($detail) => [
                    'siswa_id' => $detail->siswa_id,
                    'nama' => $detail->siswa?->nama,
                    'nis' => $detail->siswa?->nis,
                    'status' => $detail->status,
                ])
                ->values()
                ->all(),
        ];
    }
}
