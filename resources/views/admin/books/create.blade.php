@extends('admin.layout')

@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Tambah Buku Baru</h1>
    <a href="{{ route('admin.books.index') }}" class="btn btn-secondary btn-sm shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="{{ route('admin.books.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Judul Buku</label>
                        <input type="text" name="title" class="form-control" placeholder="Masukan judul..." required>
                    </div>

                    <div class="form-group">
                        <label>Penulis</label>
                        <input type="text" name="author" class="form-control" placeholder="Nama penulis..." required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tahun Terbit</label>
                                <input type="number" name="year" class="form-control" placeholder="2024" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kategori</label>
                                <select name="category_id" class="form-control" required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Stok Fisik (Offline)</label>
                                <input type="number" name="stock" class="form-control" value="0" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Slot Online (Lisensi)</label>
                                <input type="number" name="stock_online" class="form-control" value="0" min="0" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Link Digital (E-Book)</label>
                        <input type="url" name="digital_link" class="form-control" placeholder="https://...">
                        <small class="text-muted">Kosongkan jika buku hanya tersedia offline.</small>
                    </div>

                    <div class="form-group">
                        <label>Deskripsi / Sinopsis</label>
                        <textarea name="description" class="form-control" rows="4"></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Lokasi Lantai</label>
                            <select name="floor" class="form-control">
                                <option value="1">Lantai 1</option>
                                <option value="2">Lantai 2</option>
                                <option value="3">Lantai 3</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Kode Rak / Baris</label>
                            <input type="text" name="shelf_code" class="form-control" placeholder="Contoh: RAK-A-05" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Cover Buku</label>

                        <div class="mb-2">
                            <img id="img-preview" src="https://via.placeholder.com/150x200?text=Preview+Cover" alt="Preview" class="img-thumbnail" style="max-height: 200px; display: none;">
                        </div>

                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="cover" name="cover" accept="image/*" onchange="previewImage(this)">
                            <label class="custom-file-label" for="cover">Pilih gambar...</label>
                        </div>
                        <small class="text-muted">Format: JPG, PNG. Maksimal 2MB.</small>
                    </div>

                </div>
            </div>

            <hr>
            <button type="submit" class="btn btn-primary" style="background: #2c3e50; border:none;">Simpan Buku</button>
        </form>
    </div>
</div>

<script>
    function previewImage(input) {
        var file = input.files[0];

        if (file) {
            var fileName = file.name;
            var nextSibling = input.nextElementSibling;
            nextSibling.innerText = fileName;

            var reader = new FileReader();
            reader.onload = function(e) {
                var preview = document.getElementById('img-preview');
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    }
</script>

@endsection