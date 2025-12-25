@extends('admin.layout')

@section('content')

<h1 class="h3 mb-4 text-gray-800 font-weight-bold">Verifikasi KTM Mahasiswa</h1>

@if(session('success'))
<div class="alert alert-success border-left-success shadow-sm">
    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
</div>
@endif

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Antrian Verifikasi</h6>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="tableVerifikasiKTM" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Mahasiswa</th>
                        <th>Foto KTM</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingStudents as $student)
                    <tr>
                        <td>
                            <div class="font-weight-bold">{{ $student->name }}</div>
                            <div class="small text-muted">{{ $student->nim }}</div>
                            <div class="small text-muted">{{ $student->jurusan }}</div>
                        </td>

                        <td class="text-center">
                            <button type="button"
                                class="btn btn-sm btn-info"
                                data-toggle="modal"
                                data-target="#ktmModal{{ $student->id }}">
                                <i class="fas fa-image"></i> Lihat KTM
                            </button>

                            <!-- Modal KTM -->
                            <div class="modal fade" id="ktmModal{{ $student->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-body text-center">
                                            <img src="{{ asset('storage/' . $student->ktm_image) }}"
                                                class="img-fluid rounded">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button"
                                                class="btn btn-secondary"
                                                data-dismiss="modal">
                                                Tutup
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>

                        <td>
                            <div class="d-flex" style="gap: 5px;">
                                <form action="{{ route('admin.students.approve', $student->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('Verifikasi mahasiswa ini?')">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="fas fa-check"></i> Terima
                                    </button>
                                </form>

                                <button type="button"
                                    class="btn btn-danger btn-sm"
                                    data-toggle="modal"
                                    data-target="#rejectModal{{ $student->id }}">
                                    <i class="fas fa-times"></i> Tolak
                                </button>
                            </div>

                            <!-- Modal Tolak -->
                            <div class="modal fade" id="rejectModal{{ $student->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.students.reject', $student->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Tolak Verifikasi</h5>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>Alasan Penolakan</label>
                                                    <textarea name="reason"
                                                        class="form-control"
                                                        required
                                                        placeholder="Contoh: Foto buram / Bukan KTM asli"></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-danger">
                                                    Kirim Penolakan
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
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
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>

<script>
    $(document).ready(function() {

        $('#tableVerifikasiKTM').DataTable({
            order: [],
            columnDefs: [{
                targets: 2,
                orderable: false
            }],
            language: {
                emptyTable: "Tidak ada antrian verifikasi KTM.",
                lengthMenu: "Tampilkan _MENU_ data",
                search: "Cari Mahasiswa:",
                zeroRecords: "Data tidak ditemukan",
                info: "Hal _PAGE_ dari _PAGES_",
                infoEmpty: "Kosong",
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