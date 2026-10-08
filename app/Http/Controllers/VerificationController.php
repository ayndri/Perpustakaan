<?php

namespace App\Http\Controllers;

use App\Support\Media;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function index()
    {
        return view('verification.index', ['student' => auth('student')->user()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'ktm_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $student = auth('student')->user();

        if ($student->isVerified()) {
            return back()->with('error', 'Akunmu sudah terverifikasi.');
        }

        Media::delete($student->ktm_image);

        // KTM memuat data pribadi, jadi selalu disimpan privat dan hanya bisa dibuka admin.
        $student->update([
            'ktm_image' => Media::store($request->file('ktm_image'), 'ktm', private: true),
            'verification_status' => 'pending',
            'rejection_reason' => null,
        ]);

        return back()->with('success', 'KTM terkirim. Admin biasanya memeriksa dalam satu hari kerja.');
    }
}
