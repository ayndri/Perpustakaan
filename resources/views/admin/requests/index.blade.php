@extends('admin.layout')

@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Usulan Pengadaan Buku (Wishlist)</h1>
</div>

@if(session('success'))
<div class="alert alert-success border-left-success shadow-sm">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Request Mahasiswa</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Mahasiswa</th>
                        <th style="width: 80px;">Sampul</th>
                        <th>Detail Buku</th>
                        <th>Alasan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests as $req)
                    <tr>
                        <td>{{ $req->created_at->format('d/m/Y') }}</td>
                        <td>
                            <div class="font-weight-bold">{{ $req->student->name }}</div>
                            <small>{{ $req->student->nim }}</small>
                        </td>
                        <td class="text-center">
                            @if($req->image)
                            <a href="{{ asset('storage/' . $req->image) }}" target="_blank">
                                <img src="{{ asset('storage/' . $req->image) }}" alt="Sampul"
                                    class="img-thumbnail shadow-sm"
                                    style="width: 60px; height: 85px; object-fit: cover;">
                            </a>
                            @else
                            <span class="text-muted small font-italic">-</span>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $req->title }}</strong><br>
                            <small>Penulis: {{ $req->author }}</small><br>
                            @if($req->publisher) <small>Penerbit: {{ $req->publisher }}</small> @endif
                        </td>
                        <td>{{ $req->reason ?? '-' }}</td>
                        <td>
                            @if($req->status == 'pending')
                            <span class="badge badge-warning">Menunggu</span>
                            @elseif($req->status == 'approved')
                            <span class="badge badge-primary">Disetujui</span>
                            @elseif($req->status == 'available')
                            <span class="badge badge-success">Sudah Ada</span>
                            @else
                            <span class="badge badge-danger">Ditolak</span>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-sm btn-info dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Ubah Status
                            </button>
                            <div class="dropdown-menu animated--fade-in" aria-labelledby="dropdownMenuButton">
                                <form action="{{ route('admin.requests.update', $req->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" name="status" value="approved" class="dropdown-item text-primary"><i class="fas fa-check mr-2"></i> Setujui</button>
                                    <button type="submit" name="status" value="rejected" class="dropdown-item text-danger"><i class="fas fa-times mr-2"></i> Tolak</button>
                                    <div class="dropdown-divider"></div>
                                    <button type="submit" name="status" value="available" class="dropdown-item text-success"><i class="fas fa-book mr-2"></i> Sudah Tersedia</button>
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
<link href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>

<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "order": [
                [0, "desc"]
            ] // Urutkan tanggal terbaru
        });
    });
</script>
@endpush