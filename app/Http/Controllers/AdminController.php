<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Category;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalBooks = Book::count();
        $totalStudents = Student::count();
        $activeBorrowings = Borrowing::where('status', 'active')->count();
        // $totalFine = Fine::where('status', 'unpaid')->sum('amount');

        $borrowChartData = Borrowing::select(
            DB::raw('MONTH(borrow_date) as month'),
            DB::raw('COUNT(*) as total')
        )
            ->whereYear('borrow_date', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $monthlyCounts = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyCounts[] = $borrowChartData[$i] ?? 0;
        }

        $categories = Category::withCount('books')
            ->orderBy('books_count', 'desc')
            ->take(5)
            ->get();

        $categoryLabels = $categories->pluck('name');
        $categoryData = $categories->pluck('books_count');

        return view('admin.dashboard', compact(
            'totalBooks',
            'totalStudents',
            'activeBorrowings',
            // 'totalFine',
            'monthlyCounts',
            'categoryLabels',
            'categoryData'
        ));
    }
}
