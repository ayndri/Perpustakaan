<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function printBorrowings(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $borrowings = Borrowing::with(['student', 'book'])
            ->whereBetween('borrow_date', [$startDate, $endDate])
            ->orderBy('borrow_date', 'asc')
            ->get();

        $pdf = Pdf::loadView('admin.reports.pdf_borrowings', compact('borrowings', 'startDate', 'endDate'));

        $pdf->setPaper('a4', 'landscape');

        return $pdf->stream('Laporan-Peminjaman-' . $startDate . '-sampai-' . $endDate . '.pdf');
    }

    public function printMemberCard($id)
    {
        $student = Student::findOrFail($id);

        $pdf = Pdf::loadView('admin.reports.pdf_member_card', compact('student'));

        $customPaper = array(0, 0, 242.65, 153);

        $pdf->setPaper($customPaper);

        return $pdf->stream('Kartu-' . $student->nim . '.pdf');
    }

    public function printMembers(Request $request)
    {
        $jurusan = $request->jurusan;
        $query = Student::orderBy('name', 'asc');

        if ($jurusan && $jurusan != 'Semua') {
            $query->where('jurusan', $jurusan);
        }

        $students = $query->get();

        $pdf = Pdf::loadView('admin.reports.pdf_members', compact('students', 'jurusan'));

        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('Laporan-Anggota-' . ($jurusan ?? 'Semua') . '.pdf');
    }
}
