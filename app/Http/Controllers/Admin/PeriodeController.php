<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Periode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class PeriodeController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Periode/Index', [
            'periodes' => Periode::query()
                ->latest('is_active')
                ->latest()
                ->paginate(25)
                ->withQueryString(),
        ]);
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('periode.index');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tahun_ajaran' => ['required', 'string', 'max:255'],
            'semester' => ['required', Rule::in(['ganjil', 'genap'])],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'is_active' => ['boolean'],
        ]);

        DB::transaction(function () use ($validated) {
            if ($validated['is_active'] ?? false) {
                Periode::query()->update(['is_active' => false]);
            }

            Periode::query()->create([
                ...$validated,
                'is_active' => $validated['is_active'] ?? false,
            ]);
        });

        return back()->with('success', 'Periode berhasil ditambahkan.');
    }

    public function show(Periode $periode): RedirectResponse
    {
        return redirect()->route('periode.index');
    }

    public function edit(Periode $periode): RedirectResponse
    {
        return redirect()->route('periode.index');
    }

    public function update(Request $request, Periode $periode): RedirectResponse
    {
        $validated = $request->validate([
            'tahun_ajaran' => ['required', 'string', 'max:255'],
            'semester' => ['required', Rule::in(['ganjil', 'genap'])],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'is_active' => ['boolean'],
        ]);

        DB::transaction(function () use ($periode, $validated) {
            if ($validated['is_active'] ?? false) {
                Periode::query()->whereKeyNot($periode->id)->update(['is_active' => false]);
            }

            $periode->update([
                ...$validated,
                'is_active' => $validated['is_active'] ?? false,
            ]);
        });

        return back()->with('success', 'Periode berhasil diperbarui.');
    }

    public function destroy(Periode $periode): RedirectResponse
    {
        if ($periode->is_active) {
            return back()->with('error', 'Periode aktif tidak dapat dihapus.');
        }

        $periode->delete();

        return back()->with('success', 'Periode berhasil dihapus.');
    }

    public function setActive(Periode $periode): RedirectResponse
    {
        DB::transaction(function () use ($periode) {
            Periode::query()->whereKeyNot($periode->id)->update(['is_active' => false]);
            $periode->update(['is_active' => true]);
        });

        return back()->with('success', 'Periode aktif berhasil diperbarui.');
    }
}
