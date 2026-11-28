<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = $request->user()->isSuperAdmin()
            ? User::orderBy('name')->get()
            : collect([$request->user()]);

        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->isSuperAdmin(), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'jabatan' => ['nullable', 'string', 'max:120'],
            'role' => ['required', Rule::in(['superadmin', 'admin_dpm'])],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'jabatan' => $validated['jabatan'] ?? null,
            'role' => $validated['role'],
            'password' => $validated['password'],
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Akun pengurus DPM berhasil ditambahkan.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $request->user()->update([
            'password' => $validated['password'],
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Kata sandi Anda berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        abort_unless($request->user()->isSuperAdmin(), 403);

        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun yang sedang digunakan.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Akun pengurus berhasil dihapus.');
    }
}
