<?php

namespace App\Http\Controllers;

use App\Models\BookRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentBookRequestController extends Controller
{
    public function index()
    {
        $studentId = Auth::guard('student')->id() ?? Auth::id();

        $myRequests = BookRequest::where('student_id', $studentId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('student.requests.index', compact('myRequests'));
    }

    public function create()
    {
        return view('student.requests.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'     => 'required|string|max:255',
            'author'    => 'required|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'category'  => 'nullable|string|max:100',
            'reason'    => 'required|string|min:10',
            'image'     => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = [
            'student_id' => Auth::guard('student')->id(),
            'title'      => $request->title,
            'author'     => $request->author,
            'publisher'  => $request->publisher,
            'category'   => $request->category,
            'reason'     => $request->reason,
            'status'     => 'pending',
        ];

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('request_images', 'public');
            $data['image'] = $path;
        }

        BookRequest::create($data);

        return redirect()->route('student.requests.index')
            ->with('success', 'Usulan buku berhasil dikirim! Pantau statusnya di sini.');
    }
}
