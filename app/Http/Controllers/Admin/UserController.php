<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->only('search', 'role');

        return Inertia::render('Admin/User/Index', [
            'users' => User::query()
                ->with('guru:id,user_id,nama')
                ->when($filters['search'] ?? null, function ($query, string $search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
                })
                ->when($filters['role'] ?? null, fn ($query, $role) => $query->where('role', $role))
                ->orderBy('name')
                ->paginate(25)
                ->withQueryString(),
            'roles' => User::ROLES,
            'filters' => $filters,
        ]);
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('users.index');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')],
            'role' => ['required', Rule::in(User::ROLES)],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'User berhasil ditambahkan.');
    }

    public function show(User $user): RedirectResponse
    {
        return redirect()->route('users.index');
    }

    public function edit(User $user): RedirectResponse
    {
        return redirect()->route('users.index');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in(User::ROLES)],
        ]);

        $user->update($validated);

        return back()->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'User yang sedang login tidak dapat dihapus.');
        }

        if ($user->guru()->exists()) {
            return back()->with('error', 'User yang masih terhubung ke data guru tidak dapat dihapus.');
        }

        $user->delete();

        return back()->with('success', 'User berhasil dihapus.');
    }
}
