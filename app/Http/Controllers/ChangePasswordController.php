<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class ChangePasswordController extends Controller
{
    public function show()
    {
        return view('auth.change-password');
    }

    public function update(Request $request)
    {
        $request->validate([
            'password' => [
                'required',
                'confirmed',
                Rules\Password::defaults(),
                function ($attribute, $value, $fail) {
                    if ($value === 'ChangeMe@123') {
                        $fail('Password baru tidak boleh sama dengan password default.');
                    }
                },
            ],
        ]);

        $user = $request->user();
        $user->update([
            'password' => Hash::make($request->password),
            'must_change_password' => false,
        ]);

        return redirect()->route('dashboard')
            ->with('status', 'Password Anda berhasil diperbarui. Selamat datang!');
    }
}
