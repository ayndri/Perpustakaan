<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Support\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $student = auth('student')->user();

        $loans = $student->borrowings()->with('book')->latest()->get();
        $queueLengths = Reservation::where('status', 'waiting')
            ->whereIn('book_id', $loans->where('status', 'active')->pluck('book_id'))
            ->pluck('book_id')
            ->countBy();

        return view('profile.index', [
            'student' => $student,
            'tickets' => $loans->where('status', 'pending'),
            'active' => $loans->where('status', 'active')->sortBy('due_at'),
            'history' => $loans->whereIn('status', ['returned', 'expired', 'rejected', 'cancelled'])->take(20),
            'outstandingFine' => $student->outstandingFine(),
            'queueLengths' => $queueLengths,
            'reservations' => $student->reservations()->with('book')->where('status', 'waiting')->oldest()->get(),
            'notifications' => $student->unreadNotifications()->take(5)->get(),
            'requests' => $student->bookRequests()->latest()->take(3)->get(),
        ]);
    }

    public function edit()
    {
        return view('profile.edit', ['student' => auth('student')->user()]);
    }

    public function update(Request $request)
    {
        $student = auth('student')->user();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email,'.$student->id,
            'gender' => 'required|in:L,P',
            'photo' => 'nullable|image|max:2048',
            'password' => 'nullable|min:8|confirmed',
        ]);

        $student->fill(collect($data)->only('name', 'email', 'gender')->all());

        if ($request->hasFile('photo')) {
            Media::delete($student->photo);
            $student->photo = Media::store($request->file('photo'), 'profile_photos');
        }

        if ($request->filled('password')) {
            $student->password = Hash::make($request->password);
        }

        $student->save();

        return redirect()->route('profile')->with('success', 'Profil diperbarui.');
    }

    public function markAsRead(string $id)
    {
        auth('student')->user()->notifications()->whereKey($id)->first()?->markAsRead();

        return back();
    }
}
