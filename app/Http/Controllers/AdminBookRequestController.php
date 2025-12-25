<?php

namespace App\Http\Controllers;

use App\Models\BookRequest;
use Illuminate\Http\Request;

class AdminBookRequestController extends Controller
{
    public function index()
    {
        $requests = BookRequest::with('student')->orderBy('created_at', 'desc')->get();
        return view('admin.requests.index', compact('requests'));
    }

    public function updateStatus(Request $request, $id)
    {
        $bookRequest = BookRequest::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,approved,rejected,available'
        ]);

        $bookRequest->update(['status' => $request->status]);

        $msg = 'Status berhasil diperbarui.';
        if ($request->status == 'approved') $msg = 'Usulan disetujui! Masukkan ke rencana belanja.';
        if ($request->status == 'available') $msg = 'Buku sudah tersedia. Notifikasi dikirim ke mahasiswa (Next Feature).';

        return redirect()->back()->with('success', $msg);
    }
}
