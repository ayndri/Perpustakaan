@extends('admin.layout')

@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Riwayat & Manajemen Peminjaman</h1>
    <a href="{{ route('admin.borrows.create') }}" class="btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50"></i> Peminjaman Offline Baru
    </a>
</div>

@if(session('success'))
<div class="alert alert-success border-left-success shadow-sm" role="alert">
    <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
</div>
@endif

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter"></i> Filter Data</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.borrows.index') }}" method="GET" class="form-inline">
            <div class="form-group mb-2 mr-3">
                <label class="mr-2 text-gray-600 font-weight-bold">Status:</label>
                <select name="status" class="form-control form-control-sm">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Sedang Dipinjam</option>
                    <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Dikembalikan</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    <option value="return_pending" {{ request('status') == 'return_pending' ? 'selected' : '' }}>Request Kembali</option>
                </select>
            </div>

            <div class="form-group mb-2 mr-3">
                <label class="mr-2 text-gray-600 font-weight-bold">Tipe:</label>
                <select name="type" class="form-control form-control-sm">
                    <option value="">Semua Tipe</option>
                    <option value="offline" {{ request('type') == 'offline' ? 'selected' : '' }}>Fisik (Offline)</option>
                    <option value="online" {{ request('type') == 'online' ? 'selected' : '' }}>E-Book (Online)</option>
                </select>
            </div>

            <button type="submit" class="btn btn-sm btn-primary mb-2 shadow-sm" style="background: #2c3e50; border: none;">
                <i class="fas fa-filter"></i> Terapkan
            </button>
            <a href="{{ route('admin.borrows.index') }}" class="btn btn-sm btn-light mb-2 ml-2 text-secondary shadow-sm">
                Reset
            </a>
        </form>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 bg-white">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Peminjaman</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th>Tgl Pinjam</th>
                        <th>Mahasiswa</th>
                        <th>Buku</th>
                        <th>Tipe / Tiket</th>
                        <th>Status</th>
                        <th class="no-sort text-center" style="width: 15%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($borrowings as $item)
                    <tr>
                        <td data-sort="{{ $item->created_at }}">
                            <div style="font-weight: 600;">{{ \Carbon\Carbon::parse($item->borrow_date)->format('d/m/Y') }}</div>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($item->borrow_date)->format('H:i') }}</small>
                        </td>
                        <td>
                            <div class="font-weight-bold">{{ $item->student->name }}</div>
                            <small class="text-muted">{{ $item->student->nim }}</small>
                        </td>
                        <td>{{ $item->book->title }}</td>
                        <td>
                            @if($item->type == 'offline')
                            <span class="badge badge-warning"><i class="fas fa-box"></i> Fisik</span>
                            @if($item->ticket_number)
                            <div class="mt-1 small bg-light border rounded px-1 text-monospace text-center">
                                {{ $item->ticket_number }}
                            </div>
                            @endif
                            @else
                            <span class="badge badge-info"><i class="fas fa-wifi"></i> Online</span>
                            @endif
                        </td>
                        <td>
                            @if($item->status == 'pending')
                            <span class="badge badge-warning">Menunggu</span>
                            @elseif($item->status == 'active')
                            <span class="badge badge-success">Dipinjam</span>
                            <div class="text-danger small mt-1">Due: {{ \Carbon\Carbon::parse($item->return_date)->format('d/m') }}</div>
                            @elseif($item->status == 'returned')
                            <span class="badge badge-secondary">Selesai</span>
                            @elseif($item->status == 'rejected')
                            <span class="badge badge-danger">Ditolak</span>
                            @elseif($item->status == 'return_pending')
                            <span class="badge badge-info">Ajuan Kembali</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($item->status == 'pending')
                            <form action="{{ route('admin.borrows.approve', $item->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-success btn-sm btn-circle" title="Terima"><i class="fas fa-check"></i></button>
                            </form>
                            <form action="{{ route('admin.borrows.reject', $item->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-danger btn-sm btn-circle" title="Tolak"><i class="fas fa-times"></i></button>
                            </form>
                            @elseif(($item->status == 'active' || $item->status == 'return_pending') && $item->type == 'offline')
                            <form action="{{ route('admin.borrows.return', $item->id) }}" method="POST">
                                @csrf
                                <button class="btn btn-primary btn-sm btn-block" onclick="return confirm('Buku fisik sudah diterima?')">
                                    <i class="fas fa-undo"></i> Terima
                                </button>
                            </form>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>

<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            // Urutkan berdasarkan kolom ke-0 (Tgl Pinjam) secara Descending (Terbaru di atas)
            "order": [
                [0, "desc"]
            ],

            // Kolom ke-5 (Aksi) tidak boleh disortir
            "columnDefs": [{
                "targets": 5,
                "orderable": false
            }],

            // Terjemahan Bahasa Indonesia
            "language": {
                "search": "Cari Data:",
                "lengthMenu": "Tampilkan _MENU_ entri",
                "zeroRecords": "Tidak ada data yang cocok ditemukan",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                "infoFiltered": "(disaring dari _MAX_ total entri)",
                "paginate": {
                    "first": "Awal",
                    "last": "Akhir",
                    "next": "Lanjut",
                    "previous": "Sebelum"
                }
            }
        });
    });
</script>
@endpush