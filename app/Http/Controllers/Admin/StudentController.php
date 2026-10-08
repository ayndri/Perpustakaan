<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Support\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q'));

        $students = Student::query()
            ->withCount(['borrowings as open_loans' => fn ($q) => $q->where('status', 'active')])
            ->when($search !== '', fn ($q) => $q->where(fn ($q) => $q
                ->whereLike('name', "%{$search}%")
                ->orWhereLike('nim', "%{$search}%")
                ->orWhereLike('email', "%{$search}%")))
            ->when($request->query('jurusan'), fn ($q, $j) => $q->where('jurusan', $j))
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        return view('admin.students.index', [
            'students' => $students,
            'search' => $search,
            'majors' => Student::distinct()->orderBy('jurusan')->pluck('jurusan'),
        ]);
    }

    public function show(Student $student)
    {
        return view('admin.students.show', [
            'student' => $student,
            'loans' => $student->borrowings()->with('book')->latest()->get(),
            'outstandingFine' => $student->outstandingFine(),
        ]);
    }

    public function verification()
    {
        return view('admin.students.verification', [
            'students' => Student::where('verification_status', 'pending')->oldest('updated_at')->get(),
        ]);
    }

    /** KTM tidak pernah punya URL publik; rute ini (khusus admin) yang membukanya. */
    public function ktm(Student $student)
    {
        abort_if(blank($student->ktm_image), 404);

        if ($path = Media::localPrivatePath($student->ktm_image)) {
            abort_unless(is_file($path), 404);

            return response()->file($path, ['Cache-Control' => 'private, no-store']);
        }

        return redirect()->away(Media::url($student->ktm_image));
    }

    public function approve(Student $student)
    {
        $student->update(['verification_status' => 'verified', 'rejection_reason' => null]);

        return back()->with('success', $student->name.' terverifikasi dan sudah bisa meminjam.');
    }

    public function reject(Request $request, Student $student)
    {
        $data = $request->validate(['reason' => 'required|string|max:255']);

        $student->update(['verification_status' => 'rejected', 'rejection_reason' => $data['reason']]);

        return back()->with('success', 'Verifikasi '.$student->name.' ditolak. Alasannya tampil di akun mahasiswa.');
    }

    public function create()
    {
        return view('admin.students.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'nim' => 'required|string|max:20|unique:students,nim',
            'email' => 'required|email|unique:students,email',
            'gender' => 'required|in:L,P',
            'jurusan' => 'required|string|max:100',
        ]);

        // Password sementara acak, ditampilkan sekali ke admin untuk diberikan ke mahasiswa.
        $password = Str::password(10, symbols: false);

        $student = Student::create($data + [
            'password' => $password,
            'verification_status' => 'verified',
        ]);

        return redirect()->route('admin.students.show', $student)
            ->with('success', "Anggota terdaftar. Password sementara: {$password} (berikan ke mahasiswa, tidak akan ditampilkan lagi).");
    }
}
