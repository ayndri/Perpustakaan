<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminStudentController extends Controller
{
    public function index()
    {
        $pendingStudents = Student::where('verification_status', 'pending')->orderBy('updated_at', 'asc')->get();
        return view('admin.students.verification', compact('pendingStudents'));
    }

    public function listanggota()
    {
        $students = Student::with(['borrowings' => function ($query) {
            $query->latest('updated_at');
        }])->get();

        return view('admin.students.index', compact('students'));
    }

    public function approve($id)
    {
        $student = Student::findOrFail($id);
        $student->update(['verification_status' => 'verified']);
        return back()->with('success', 'Mahasiswa berhasil diverifikasi.');
    }

    public function reject(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        $student->update([
            'verification_status' => 'rejected',
            'rejection_reason' => $request->reason
        ]);
        return back()->with('success', 'Verifikasi mahasiswa ditolak.');
    }

    public function create()
    {
        return view('admin.students.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nim' => 'required|unique:students,nim',
            'email' => 'required|email|unique:students,email',
            'gender' => 'required|in:L,P',
            'jurusan' => 'required|string',
        ]);

        Student::create([
            'name' => $request->name,
            'nim' => $request->nim,
            'email' => $request->email,
            'gender' => $request->gender,
            'jurusan' => $request->jurusan,
            'password' => Hash::make('password123'),
            'verification_status' => 'verified',
            'ktm_image' => null,
        ]);

        return redirect()->route('admin.students.create')->with('success', 'Mahasiswa berhasil didaftarkan. Password default: password123');
    }

    public function history($id)
    {
        $history = Borrowing::with('book')
            ->where('student_id', $id)
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json($history);
    }
}
