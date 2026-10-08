<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'nim' => 'required|string|max:20|unique:students,nim',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email',
            'jurusan' => 'required|string|max:100',
            'gender' => 'required|in:L,P',
            'password' => 'required|min:8|confirmed',
        ]);

        // Password di-hash oleh cast 'hashed' di model.
        $student = Student::create($data);

        Auth::guard('student')->login($student);
        $request->session()->regenerate();

        return redirect()->route('verification.index')
            ->with('success', 'Akun dibuat. Satu langkah lagi: unggah KTM supaya bisa meminjam.');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('student')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('home'));
        }

        return back()->withErrors(['email' => 'Email atau password tidak cocok.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('student')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
