<?php

namespace App\Http\Controllers;

use App\Models\BookRequest;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $student = Auth::guard('student')->user();

        $pendingBorrows = Borrowing::with('book')
            ->where('student_id', $student->id)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        $activeBorrows = Borrowing::with('book')
            ->where('student_id', $student->id)
            ->whereIn('status', ['active', 'return_pending'])
            ->orderBy('created_at', 'desc')
            ->get();

        $historyBorrows = Borrowing::with('book')
            ->where('student_id', $student->id)
            ->whereIn('status', ['returned', 'rejected'])
            ->orderBy('created_at', 'desc')
            ->get();

        $myRequests = BookRequest::where('student_id', $student->id)
            ->whereIn('status', ['pending', 'approved', 'available'])
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        return view('profile', compact('student', 'pendingBorrows', 'activeBorrows', 'historyBorrows', 'myRequests'));
    }

    public function update(Request $request)
    {
        $student = Auth::guard('student')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'photo' => 'nullable|image|max:2048',
            'password' => 'nullable|min:6|confirmed',
            'gender' => 'required|in:L,P',
            'email' => 'required|email|unique:students,email,' . $student->id,
        ]);

        $student->name = $request->name;
        $student->email = $request->email;
        $student->gender = $request->gender;

        if ($request->hasFile('photo')) {
            if ($student->photo && Storage::disk('public')->exists($student->photo)) {
                Storage::disk('public')->delete($student->photo);
            }

            $path = $request->file('photo')->store('profile_photos', 'public');
            $student->photo = $path;
        }

        if ($request->filled('password')) {
            $student->password = Hash::make($request->password);
        }

        $student->save();

        return redirect()->route('profile')->with('success', 'Profil berhasil diperbarui!');
    }

    public function markAsRead($id)
    {
        $notification = auth('student')->user()->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();
        }

        return back();
    }
}
