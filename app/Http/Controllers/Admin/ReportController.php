<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index', [
            'majors' => Student::distinct()->orderBy('jurusan')->pluck('jurusan'),
        ]);
    }

    public function borrowings(Request $request)
    {
        $data = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $borrowings = Borrowing::with(['student', 'book'])
            ->whereNotNull('handed_over_at')
            ->whereBetween('handed_over_at', [
                $request->date('start_date')->startOfDay(),
                $request->date('end_date')->endOfDay(),
            ])
            ->orderBy('handed_over_at')
            ->get();

        return Pdf::loadView('admin.reports.pdf_borrowings', [
            'borrowings' => $borrowings,
            'startDate' => $request->date('start_date'),
            'endDate' => $request->date('end_date'),
        ])
            ->setPaper('a4', 'landscape')
            ->stream("laporan-peminjaman-{$data['start_date']}-{$data['end_date']}.pdf");
    }

    public function members(Request $request)
    {
        $jurusan = $request->query('jurusan');

        $students = Student::orderBy('name')
            ->when($jurusan, fn ($q) => $q->where('jurusan', $jurusan))
            ->get();

        return Pdf::loadView('admin.reports.pdf_members', compact('students', 'jurusan'))
            ->setPaper('a4', 'portrait')
            ->stream('laporan-anggota.pdf');
    }

    public function memberCard(Student $student)
    {
        return Pdf::loadView('admin.reports.pdf_member_card', compact('student'))
            ->setPaper([0, 0, 242.65, 153])
            ->stream("kartu-{$student->nim}.pdf");
    }
}
