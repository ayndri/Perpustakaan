<?php

namespace App\Http\Controllers;

use App\Models\BookRequest;
use App\Support\Media;
use Illuminate\Http\Request;

class StudentBookRequestController extends Controller
{
    public function index()
    {
        return view('requests.index', [
            'requests' => auth('student')->user()->bookRequests()->latest()->get(),
        ]);
    }

    public function create()
    {
        return view('requests.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:100',
            'reason' => 'required|string|min:10|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = Media::store($request->file('image'), 'request_images');
        }

        BookRequest::create($data + [
            'student_id' => auth('student')->id(),
            'status' => 'pending',
        ]);

        return redirect()->route('student.requests.index')
            ->with('success', 'Usulan terkirim. Statusnya bisa kamu pantau di halaman ini.');
    }
}
