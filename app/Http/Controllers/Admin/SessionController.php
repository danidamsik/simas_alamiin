<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Session;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class SessionController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Session/Index', [
            'sessions' => Session::query()
                ->orderBy('jam_mulai')
                ->paginate(25)
                ->withQueryString(),
        ]);
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('sessions.index');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);
        $this->ensureNoOverlap($validated['jam_mulai'], $validated['jam_selesai']);

        Session::query()->create($validated);

        return back()->with('success', 'Session berhasil ditambahkan.');
    }

    public function show(Session $session): RedirectResponse
    {
        return redirect()->route('sessions.index');
    }

    public function edit(Session $session): RedirectResponse
    {
        return redirect()->route('sessions.index');
    }

    public function update(Request $request, Session $session): RedirectResponse
    {
        $validated = $this->validated($request);
        $this->ensureNoOverlap($validated['jam_mulai'], $validated['jam_selesai'], $session);

        $session->update($validated);

        return back()->with('success', 'Session berhasil diperbarui.');
    }

    public function destroy(Session $session): RedirectResponse
    {
        if ($session->jadwal()->exists() || $session->absensi()->exists()) {
            return back()->with('error', 'Session yang masih digunakan jadwal atau absensi tidak dapat dihapus.');
        }

        $session->delete();

        return back()->with('success', 'Session berhasil dihapus.');
    }

    /**
     * @return array<string, string>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'nama_sesi' => ['required', 'string', 'max:255'],
            'jam_mulai' => ['required', 'date_format:H:i', 'before:jam_selesai'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
        ]);
    }

    private function ensureNoOverlap(string $jamMulai, string $jamSelesai, ?Session $current = null): void
    {
        $conflict = Session::query()
            ->when($current, fn ($query) => $query->whereKeyNot($current->id))
            ->where('jam_mulai', '<', $jamSelesai)
            ->where('jam_selesai', '>', $jamMulai)
            ->orderBy('jam_mulai')
            ->first();

        if ($conflict) {
            throw ValidationException::withMessages([
                'jam_mulai' => "Waktu tumpang tindih dengan {$conflict->nama_sesi} ({$conflict->jam_mulai->format('H:i')}-{$conflict->jam_selesai->format('H:i')}).",
            ]);
        }
    }
}
