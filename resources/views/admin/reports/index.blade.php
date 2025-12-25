@extends('admin.layout')

@section('content')

<h1 class="h3 mb-4 text-gray-800 font-weight-bold">Laporan Perpustakaan</h1>

<div class="row">
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold"><i class="fas fa-file-alt mr-2"></i> Laporan Transaksi Peminjaman</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.reports.print_borrowings') }}" method="GET" target="_blank">
                    <div class="form-group">
                        <label>Dari Tanggal</label>
                        <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-01') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Sampai Tanggal</label>
                        <input type="date" name="end_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-print mr-2"></i> Cetak PDF
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-success text-white">
                <h6 class="m-0 font-weight-bold"><i class="fas fa-users mr-2"></i> Laporan Data Anggota</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.reports.print_members') }}" method="GET" target="_blank">

                    <div class="form-group">
                        <label>Filter Jurusan</label>
                        <select name="jurusan" class="form-control">
                            <option value="Semua">-- Semua Jurusan --</option>
                            <option value="Teknik Informatika">Teknik Informatika</option>
                            <option value="Sistem Informasi">Sistem Informasi</option>
                            <option value="Teknik Sipil">Teknik Sipil</option>
                            <option value="Manajemen">Manajemen</option>
                            <option value="Akuntansi">Akuntansi</option>
                        </select>
                        <small class="text-muted">Pilih "Semua Jurusan" untuk mencetak seluruh data.</small>
                    </div>

                    <button type="submit" class="btn btn-success btn-block">
                        <i class="fas fa-print mr-2"></i> Cetak PDF Anggota
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection