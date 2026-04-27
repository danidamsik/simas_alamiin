<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class GuruController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->only('search', 'status');

        return Inertia::render('Admin/Guru/Index', [
            'gurus' => Guru::query()
                ->with('user:id,name,email,role')
                ->when($filters['search'] ?? null, function ($query, string $search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('nama', 'like', "%{$search}%")
                            ->orWhere('nip', 'like', "%{$search}%");
                    });
                })
                ->when(($filters['status'] ?? 'active') !== 'all', fn ($query) => $query->where('is_active', true))
                ->orderBy('nama')
                ->paginate(25)
                ->withQueryString(),
            'userOptions' => User::query()
                ->where('role', User::ROLE_GURU)
                ->whereDoesntHave('guru')
                ->orderBy('name')
                ->get(['id', 'name', 'email']),
            'filters' => $filters,
        ]);
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('guru.index');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', Rule::exists('users', 'id'), Rule::unique('guru', 'user_id')],
            'nama' => ['required', 'string', 'max:255'],
            'nip' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ]);

        Guru::query()->create([
            ...$validated,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return back()->with('success', 'Guru berhasil ditambahkan.');
    }

    public function show(Guru $guru): RedirectResponse
    {
        return redirect()->route('guru.index');
    }

    public function edit(Guru $guru): RedirectResponse
    {
        return redirect()->route('guru.index');
    }

    public function update(Request $request, Guru $guru): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', Rule::exists('users', 'id'), Rule::unique('guru', 'user_id')->ignore($guru->id)],
            'nama' => ['required', 'string', 'max:255'],
            'nip' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ]);

        $guru->update([
            ...$validated,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return back()->with('success', 'Guru berhasil diperbarui.');
    }

    public function destroy(Guru $guru): RedirectResponse
    {
        $guru->update(['is_active' => false]);

        return back()->with('success', 'Guru berhasil dinonaktifkan.');
    }
}
