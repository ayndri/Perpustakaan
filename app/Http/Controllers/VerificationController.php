<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VerificationController extends Controller
{
    public function index()
    {
        $student = Auth::guard('student')->user();
        return view('verification.index', compact('student'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ktm_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $student = Auth::guard('student')->user();

        if ($student->ktm_image) {
            Storage::disk('public')->delete($student->ktm_image);
        }

        $path = $request->file('ktm_image')->store('ktm_uploads', 'public');

        $student->update([
            'ktm_image' => $path,
            'verification_status' => 'pending',
            'rejection_reason' => null
        ]);

        return redirect()->back()->with('success', 'KTM berhasil diunggah. Mohon tunggu verifikasi Admin.');
    }
}
