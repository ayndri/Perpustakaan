@extends('admin.layout')

@section('content')

<h1 class="h3 mb-4 text-gray-800 font-weight-bold">Verifikasi Pengembalian Buku</h1>

@if(session('success'))
<div class="alert alert-success border-left-success shadow-sm" role="alert">
    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
</div>
@endif

<div class="card shadow mb-4">
    <div class="card-header py-3 bg-white border-bottom-primary d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Request Pengembalian</h6>
        <span class="badge badge-warning">{{ $returns->count() }} Menunggu</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTableVerification" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th>Tanggal Pinjam</th>
                        <th>Mahasiswa</th>
                        <th>Buku</th>
                        <th>Tipe / Tiket</th>
                        <th class="text-center" style="width: 15%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($returns as $item)
                    <tr>
                        <td data-sort="{{ $item->borrow_date }}">
                            <div style="font-weight: 600;">{{ \Carbon\Carbon::parse($item->borrow_date)->format('d M Y') }}</div>
                            <small class="text-muted">
                                Request: {{ $item->updated_at->diffForHumans() }}
                            </small>
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
                        <td class="text-center">
                            <form action="{{ route('admin.borrows.verify', $item->id) }}" method="POST" onsubmit="return confirm('Pastikan kondisi buku fisik baik (jika offline). Lanjutkan verifikasi?')">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm shadow-sm btn-block">
                                    <i class="fas fa-check mr-1"></i> Terima
                                </button>
                            </form>
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
        $('#dataTableVerification').DataTable({
            // Urutan default: Kolom ke-0 (Tanggal) secara Ascending (Terlama dulu)
            // Agar admin memproses yang request duluan
            "order": [
                [0, "asc"]
            ],

            // Non-aktifkan sorting di kolom Aksi (index 4)
            "columnDefs": [{
                "targets": 4,
                "orderable": false
            }],

            // Bahasa Indonesia
            "language": {
                "search": "Cari Request:",
                "lengthMenu": "Tampilkan _MENU_ antrian",
                "zeroRecords": "Tidak ada permintaan pengembalian saat ini",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ permintaan",
                "infoEmpty": "Kosong",
                "infoFiltered": "(difilter dari _MAX_ total data)",
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