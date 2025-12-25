@extends('admin.layout')

@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Tambah Peminjaman Offline</h1>
    <a href="{{ route('admin.borrows.index') }}" class="btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Form Peminjaman Fisik</h6>
            </div>
            <div class="card-body">

                @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form action="{{ route('admin.borrows.store') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="student_id" class="font-weight-bold">Nama Mahasiswa / NIM</label>
                        <select name="student_id" id="student_id" class="form-control select2" required>
                            <option value="">-- Cari Mahasiswa --</option>
                            @foreach($students as $student)
                            <option value="{{ $student->id }}">
                                {{ $student->name }} ({{ $student->nim }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="book_id" class="font-weight-bold">Judul Buku</label>
                        <select name="book_id" id="book_id" class="form-control select2" required>
                            <option value="">-- Cari Buku --</option>
                            @foreach($books as $book)
                            <option value="{{ $book->id }}">
                                {{ $book->title }} (Stok: {{ $book->stock }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="duration" class="font-weight-bold">Durasi Peminjaman (Hari)</label>
                        <input type="number" name="duration" class="form-control" value="7" min="1" max="30" required>
                        <small class="text-muted">Default: 7 Hari</small>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save"></i> Simpan Peminjaman
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: "Ketik untuk mencari...",
            allowClear: true
        });
    });
</script>
@endpush