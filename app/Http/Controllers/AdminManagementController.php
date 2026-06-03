<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AdminManagementController extends Controller
{
    public function index()
    {
        $admins = User::where('role', 'admin')->with('admin')->latest()->get();
        return view('admin.manage.admins.index', compact('admins'));
    }

    public function create()
    {
        return view('admin.manage.admins.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'nip' => ['required', 'numeric', 'digits:18'],
            'department' => ['required', 'string', 'max:255'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
        ]);

        Admin::create([
            'user_id' => $user->id,
            'nip' => $request->nip,
            'department' => $request->department,
        ]);

        return redirect()->route('admin.manage.admins.index')
            ->with('status', 'Admin account created successfully.');
    }

    public function edit(User $admin)
    {
        $admin->load('admin');
        return view('admin.manage.admins.edit', compact('admin'));
    }

    public function update(Request $request, User $admin)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$admin->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'nip' => ['required', 'numeric', 'digits:18'],
            'department' => ['required', 'string', 'max:255'],
        ]);

        $admin->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $admin->update([
                'password' => Hash::make($request->password),
            ]);
        }

        $admin->admin()->updateOrCreate(
            ['user_id' => $admin->id],
            [
                'nip' => $request->nip,
                'department' => $request->department,
            ]
        );

        return redirect()->route('admin.manage.admins.index')
            ->with('status', 'Admin account updated successfully.');
    }

    public function destroy(User $admin)
    {
        // Prevent admin from deleting themselves
        if (auth()->id() === $admin->id) {
            return redirect()->route('admin.manage.admins.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $admin->delete(); // This will cascade delete their profile

        return redirect()->route('admin.manage.admins.index')
            ->with('status', 'Admin account deleted successfully.');
    }
}
