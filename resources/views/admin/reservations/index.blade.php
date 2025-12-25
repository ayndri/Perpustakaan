@extends('admin.layout')

@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Daftar Antrian Booking</h1>
</div>

@if(session('success'))
<div class="alert alert-success border-left-success shadow-sm">
    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger border-left-danger shadow-sm">
    <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
</div>
@endif

<div class="card shadow mb-4">
    <div class="card-header py-3 bg-white">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Request Booking</h6>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="tableAntrianBooking" width="100%">
                <thead class="bg-light">
                    <tr>
                        <th>Tanggal Booking</th>
                        <th>Mahasiswa</th>
                        <th>Buku</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reservations as $res)
                    <tr>
                        <td data-sort="{{ $res->created_at->timestamp }}">
                            <strong>{{ $res->created_at->format('d M Y') }}</strong><br>
                            <small class="text-muted">{{ $res->created_at->format('H:i') }} WIB</small>
                        </td>

                        <td>
                            <strong>{{ $res->student->name }}</strong><br>
                            <small class="text-muted">{{ $res->student->nim }}</small>
                        </td>

                        <td>
                            {{ $res->book->title }}
                        </td>

                        <td>
                            @if($res->status === 'pending')
                            <div class="d-flex">
                                <form action="{{ route('admin.reservations.available', $res->id) }}" method="POST" class="mr-1">
                                    @csrf
                                    <button class="btn btn-sm btn-warning">
                                        <i class="fas fa-bell"></i> Available
                                    </button>
                                </form>

                                <form action="{{ route('admin.reservations.cancel', $res->id) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-sm btn-danger"
                                        onclick="return confirm('Batalkan booking ini?')">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            </div>

                            @elseif($res->status === 'available')
                            <span class="badge badge-success d-block mb-2">Ready</span>
                            <form action="{{ route('admin.reservations.complete', $res->id) }}" method="POST">
                                @csrf
                                <button class="btn btn-sm btn-success btn-block">
                                    <i class="fas fa-check"></i> Serahkan
                                </button>
                            </form>

                            @else
                            <span class="badge badge-secondary">
                                {{ ucfirst($res->status) }}
                            </span>
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
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>

<script>
    $(document).ready(function() {

        $('#tableAntrianBooking').DataTable({
            order: [
                [0, 'asc']
            ],
            columnDefs: [{
                targets: 3,
                orderable: false
            }],
            language: {
                emptyTable: "Tidak ada permintaan booking saat ini",
                lengthMenu: "Tampilkan _MENU_ antrian",
                search: "Cari Request:",
                zeroRecords: "Tidak ada data",
                info: "Hal _PAGE_ dari _PAGES_",
                infoEmpty: "",
                infoFiltered: "(difilter dari _MAX_ data)",
                paginate: {
                    next: ">",
                    previous: "<"
                }
            }
        });

    });
</script>
@endpush