<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class KelasController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Kelas/Index', [
            'kelas' => Kelas::query()
                ->withCount(['siswa', 'jadwal'])
                ->orderBy('nama_kelas')
                ->paginate(25)
                ->withQueryString(),
        ]);
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('kelas.index');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_kelas' => ['required', 'string', 'max:255', Rule::unique('kelas', 'nama_kelas')],
        ]);

        Kelas::query()->create($validated);

        return back()->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function show(Kelas $kelas): RedirectResponse
    {
        return redirect()->route('kelas.index');
    }

    public function edit(Kelas $kelas): RedirectResponse
    {
        return redirect()->route('kelas.index');
    }

    public function update(Request $request, Kelas $kelas): RedirectResponse
    {
        $validated = $request->validate([
            'nama_kelas' => ['required', 'string', 'max:255', Rule::unique('kelas', 'nama_kelas')->ignore($kelas->id)],
        ]);

        $kelas->update($validated);

        return back()->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kelas): RedirectResponse
    {
        if ($kelas->siswa()->exists() || $kelas->jadwal()->exists()) {
            return back()->with('error', 'Kelas yang masih memiliki siswa atau jadwal tidak dapat dihapus.');
        }

        $kelas->delete();

        return back()->with('success', 'Kelas berhasil dihapus.');
    }
}
