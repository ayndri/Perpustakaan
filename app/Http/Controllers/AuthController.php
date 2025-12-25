<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nim' => 'required|unique:students,nim',
            'name' => 'required',
            'email' => 'required|email|unique:students,email',
            'jurusan' => 'required',
            'password' => 'required|min:6|confirmed',
            'gender' => 'required|in:L,P'
        ]);

        Student::create([
            'nim' => $request->nim,
            'name' => $request->name,
            'email' => $request->email,
            'jurusan' => $request->jurusan,
            'gender' => $request->gender,
            'password' => Hash::make($request->password),
        ]);

        // Auto Login setelah register (Opsional)
        // Auth::guard('student')->attempt(['email' => $request->email, 'password' => $request->password]);

        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login.');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('student')->attempt(['email' => $request->email, 'password' => $request->password])) {
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('student')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
