<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookRequest;
use Illuminate\Http\Request;

class BookRequestController extends Controller
{
    public const STATUSES = [
        'pending' => 'Menunggu ditinjau',
        'approved' => 'Disetujui, akan dibeli',
        'available' => 'Sudah tersedia',
        'rejected' => 'Ditolak',
    ];

    public function index(Request $request)
    {
        $status = array_key_exists($request->query('status'), self::STATUSES) ? $request->query('status') : null;

        return view('admin.requests.index', [
            'requests' => BookRequest::with('student')
                ->when($status, fn ($q) => $q->where('status', $status))
                ->latest()
                ->paginate(20)
                ->withQueryString(),
            'status' => $status,
            'statuses' => self::STATUSES,
        ]);
    }

    public function update(Request $request, BookRequest $bookRequest)
    {
        $data = $request->validate(['status' => 'required|in:'.implode(',', array_keys(self::STATUSES))]);

        $bookRequest->update($data);

        return back()->with('success', 'Usulan "'.$bookRequest->title.'" ditandai: '.strtolower(self::STATUSES[$data['status']]).'.');
    }
}
