@extends('admin.layout')

@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Manajemen Buku</h1>
    <a href="{{ route('admin.books.create') }}" class="btn btn-primary shadow-sm" style="background: #2c3e50; border:none;">
        <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Buku Baru
    </a>
</div>

@if(session('success'))
<div class="alert alert-success border-left-success" role="alert">
    {{ session('success') }}
</div>
@endif

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th width="5%">No</th>
                        <th>Judul Buku</th>
                        <th>Kategori</th>
                        <th>Stok Fisik</th>
                        <th>Stok Online</th>
                        <th>Lokasi</th>
                        <th width="15%" class="no-sort">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($books as $book)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <span style="font-weight: 600; color: #333;">{{ $book->title }}</span><br>
                            <small class="text-muted">{{ $book->author }} ({{ $book->year }})</small>
                        </td>
                        <td><span class="badge badge-secondary">{{ $book->category->name }}</span></td>
                        <td>
                            {{ $book->stock }}
                            @if($book->stock < 3)
                                <i class="fas fa-exclamation-circle text-danger" title="Stok Menipis"></i>
                                @endif
                        </td>
                        <td>{{ $book->stock_online }}</td>
                        <td>
                            <span class="badge badge-light border">Lt. {{ $book->floor }}</span><br>
                            <small class="font-weight-bold">{{ $book->shelf_code }}</small>
                        </td>
                        <td>
                            <div class="d-flex" style="gap: 5px;">
                                <a href="{{ route('admin.books.edit', $book->id) }}" class="btn btn-warning btn-sm btn-circle" title="Edit">
                                    <i class="fas fa-pen"></i>
                                </a>

                                <form action="{{ route('admin.books.destroy', $book->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus buku ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm btn-circle" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
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
            // Konfigurasi
            "columnDefs": [{
                    "targets": 5,
                    "orderable": false
                } // Matikan sorting di kolom Aksi (index 5)
            ],
            "language": {
                "search": "Cari Buku:",
                "lengthMenu": "Tampilkan _MENU_ data",
                "zeroRecords": "Data tidak ditemukan",
                "info": "Hal _PAGE_ dari _PAGES_",
                "infoEmpty": "Kosong",
                "infoFiltered": "(filter dari _MAX_ total)",
                "paginate": {
                    "next": ">",
                    "previous": "<"
                }
            }
        });
    });
</script>
@endpush