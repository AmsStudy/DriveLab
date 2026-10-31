<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'admin');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $admins = $query->withCount('soals')->latest()->paginate(20);

        return view('admin.users.index', compact('admins'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'role' => 'admin',
            'status' => 'active',
        ]);

        return back()->with('success', 'Asisten/Admin baru berhasil ditambahkan.');
    }

    public function show(User $user)
    {
        $user->loadCount('soals');
        $soals = $user->soals()->with('category')->latest()->paginate(10);

        return view('admin.users.show', compact('user', 'soals'));
    }

    public function approve(User $user)
    {
        $user->update(['status' => 'active']);
        return back()->with('success', "Akun admin \"{$user->name}\" berhasil diaktifkan.");
    }

    public function reject(User $user)
    {
        $user->update(['status' => 'inactive']);
        return back()->with('success', "Akun admin \"{$user->name}\" telah dinonaktifkan.");
    }

    public function resetPassword(User $user)
    {
        if ($user->isSuperAdmin() || $user->role !== 'admin') {
            abort(403, 'Password akun ini tidak dapat direset.');
        }

        $default = config('app.admin_default_password') ?: 'labdrive123';
        $user->update(['password' => $default]);

        return back()->with(
            'success',
            "Password \"{$user->name}\" direset ke default ({$default}). Minta asisten login dan segera ganti di menu Profil."
        );
    }

    public function destroy(User $user)
    {
        if ($user->isSuperAdmin()) {
            abort(403, 'Super Admin tidak dapat dihapus.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('success', "Admin \"{$user->name}\" berhasil dihapus.");
    }
}
