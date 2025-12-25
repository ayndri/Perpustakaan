@extends('admin.layout')

@section('content')

<h1 class="h3 mb-4 text-gray-800 font-weight-bold">Daftar Anggota Perpustakaan</h1>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
        <h6 class="m-0 font-weight-bold text-primary">Data Mahasiswa</h6>
        <a href="{{ route('admin.students.create') }}" class="btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Anggota
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead class="bg-light">
                    <tr>
                        <th>Mahasiswa</th>
                        <th>Status Akun</th>
                        <th>Terakhir Aktif</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="mr-3">
                                    @if($student->photo)
                                    <img src="{{ asset('storage/' . $student->photo) }}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover; border: 1px solid #eee;">
                                    @else
                                    <div class="rounded-circle bg-gradient-primary text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-weight: bold; font-size: 1.2rem;">
                                        {{ substr($student->name, 0, 1) }}
                                    </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="font-weight-bold text-dark">{{ $student->name }}</div>
                                    <div class="small text-muted">
                                        {{ $student->nim }} • {{ $student->jurusan }}
                                    </div>
                                    <div class="small text-muted"><i class="fas fa-envelope mr-1"></i> {{ $student->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($student->verification_status == 'verified')
                            <span class="badge badge-success"><i class="fas fa-check-circle"></i> Verified</span>
                            @elseif($student->verification_status == 'pending')
                            <span class="badge badge-warning"><i class="fas fa-clock"></i> Pending</span>
                            @elseif($student->verification_status == 'rejected')
                            <span class="badge badge-danger"><i class="fas fa-times-circle"></i> Ditolak</span>
                            @else
                            <span class="badge badge-secondary">Belum Verif</span>
                            @endif
                        </td>

                        @php
                        $lastActivity = $student->borrowings->first();
                        $sortValue = $lastActivity ? $lastActivity->updated_at->timestamp : 0;
                        @endphp

                        <td data-order="{{ $sortValue }}">
                            @if($lastActivity)
                            <div class="font-weight-bold text-gray-800">
                                {{ $lastActivity->updated_at->diffForHumans() }}
                            </div>
                            <div class="small text-muted">
                                @if($lastActivity->status == 'returned')
                                <span class="text-success">Mengembalikan Buku</span>
                                @elseif($lastActivity->status == 'active')
                                <span class="text-primary">Meminjam Buku</span>
                                @else
                                <span>{{ ucfirst($lastActivity->status) }}</span>
                                @endif
                            </div>
                            @else
                            <span class="text-muted small font-italic">- Belum ada aktivitas -</span>
                            @endif
                        </td>

                        <td class="text-center">
                            <button type="button" class="btn btn-info btn-sm btn-circle btn-history shadow-sm"
                                data-id="{{ $student->id }}"
                                data-name="{{ $student->name }}"
                                title="Detail Riwayat">
                                <i class="fas fa-history"></i>
                            </button>
                            <a href="{{ route('admin.students.print_card', $student->id) }}" target="_blank" class="btn btn-warning btn-sm btn-circle" title="Cetak Kartu">
                                <i class="fas fa-id-card"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="historyModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-history mr-2"></i> Riwayat: <span id="modalStudentName"></span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0" id="modalTable">
                        <thead class="bg-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Buku</th>
                                <th>Tipe</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="modalTableBody">
                        </tbody>
                    </table>
                </div>
                <div id="loadingSpinner" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Memuat data...</p>
                </div>
                <div id="emptyState" class="text-center py-5 d-none">
                    <i class="fas fa-folder-open fa-3x text-gray-300 mb-3"></i>
                    <p class="text-muted">Mahasiswa ini belum memiliki riwayat peminjaman.</p>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
            </div>
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
        // 1. Inisialisasi DataTables
        $('#dataTable').DataTable({
            "language": {
                "search": "Cari Mahasiswa:",
                "lengthMenu": "Tampilkan _MENU_ data",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ mahasiswa",
                "paginate": {
                    "first": "Awal",
                    "last": "Akhir",
                    "next": "Lanjut",
                    "previous": "Kembali"
                },
                "emptyTable": "Tidak ada data mahasiswa ditemukan"
            },
            "order": [
                [2, "desc"]
            ] // Default sort berdasarkan kolom "Terakhir Aktif" (Index 2) secara Descending
        });

        // 2. Handle Klik Tombol History (AJAX)
        // Gunakan 'body on click' agar tetap jalan saat pindah halaman pagination DataTables
        $('body').on('click', '.btn-history', function() {
            var studentId = $(this).data('id');
            var studentName = $(this).data('name');

            // Reset Modal UI
            $('#modalStudentName').text(studentName);
            $('#modalTableBody').html('');
            $('#loadingSpinner').removeClass('d-none');
            $('#emptyState').addClass('d-none');
            $('#modalTable').addClass('d-none');

            // Tampilkan Modal
            $('#historyModal').modal('show');

            // Fetch Data
            $.ajax({
                url: '/admin/students/' + studentId + '/history',
                method: 'GET',
                success: function(response) {
                    $('#loadingSpinner').addClass('d-none');

                    if (response.length > 0) {
                        $('#modalTable').removeClass('d-none');

                        var rows = '';
                        // Loop data JSON
                        $.each(response, function(index, item) {

                            // Format Tanggal (Sederhana)
                            var date = new Date(item.created_at).toLocaleDateString('id-ID', {
                                year: 'numeric',
                                month: 'short',
                                day: 'numeric'
                            });

                            // Badge Status
                            var statusBadge = '';
                            if (item.status == 'active') statusBadge = '<span class="badge badge-success">Dipinjam</span>';
                            else if (item.status == 'returned') statusBadge = '<span class="badge badge-secondary">Kembali</span>';
                            else if (item.status == 'pending') statusBadge = '<span class="badge badge-warning">Pending</span>';
                            else statusBadge = '<span class="badge badge-danger">' + item.status + '</span>';

                            // Tipe Badge
                            var typeBadge = item.type == 'offline' ?
                                '<span class="badge badge-info"><i class="fas fa-box"></i> Fisik</span>' :
                                '<span class="badge badge-primary"><i class="fas fa-wifi"></i> Online</span>';

                            rows += '<tr>';
                            rows += '<td>' + date + '</td>';
                            rows += '<td class="font-weight-bold">' + (item.book ? item.book.title : 'Buku dihapus') + '</td>';
                            rows += '<td>' + typeBadge + '</td>';
                            rows += '<td>' + statusBadge + '</td>';
                            rows += '</tr>';
                        });

                        $('#modalTableBody').html(rows);
                    } else {
                        $('#emptyState').removeClass('d-none');
                    }
                },
                error: function() {
                    $('#loadingSpinner').addClass('d-none');
                    alert('Gagal mengambil data riwayat.');
                }
            });
        });
    });
</script>
@endpush