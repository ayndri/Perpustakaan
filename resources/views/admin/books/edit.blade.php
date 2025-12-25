@extends('admin.layout')

@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Edit Buku</h1>
    <a href="{{ route('admin.books.index') }}" class="btn btn-secondary btn-sm shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="{{ route('admin.books.update', $book->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Judul Buku</label>
                        <input type="text" name="title" class="form-control" value="{{ $book->title }}" required>
                    </div>

                    <div class="form-group">
                        <label>Penulis</label>
                        <input type="text" name="author" class="form-control" value="{{ $book->author }}" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tahun Terbit</label>
                                <input type="number" name="year" class="form-control" value="{{ $book->year }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kategori</label>
                                <select name="category_id" class="form-control" required>
                                    @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ $book->category_id == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
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
                                <input type="number" name="stock" class="form-control" value="{{ $book->stock }}" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Slot Online (Lisensi)</label>
                                <input type="number" name="stock_online" class="form-control" value="{{ $book->stock_online }}" min="0" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Link Digital (E-Book)</label>
                        <input type="url" name="digital_link" class="form-control" value="{{ $book->digital_link }}">
                    </div>

                    <div class="form-group">
                        <label>Deskripsi / Sinopsis</label>
                        <textarea name="description" class="form-control" rows="4">{{ $book->description }}</textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Lokasi Lantai</label>
                            <select name="floor" class="form-control">
                                <option value="1" {{ (old('floor', $book->floor ?? '') == 1) ? 'selected' : '' }}>Lantai 1</option>
                                <option value="2" {{ (old('floor', $book->floor ?? '') == 2) ? 'selected' : '' }}>Lantai 2</option>
                                <option value="3" {{ (old('floor', $book->floor ?? '') == 3) ? 'selected' : '' }}>Lantai 3</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Kode Rak / Baris</label>
                            <input type="text" name="shelf_code" class="form-control"
                                value="{{ old('shelf_code', $book->shelf_code ?? '') }}"
                                placeholder="Contoh: RAK-A-05" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Cover Buku</label>

                        <div class="row mb-2">
                            @if($book->cover)
                            <div class="col-md-4" id="old-cover-container">
                                <small class="d-block text-muted mb-1">Cover Saat Ini:</small>
                                <img src="{{ asset('storage/' . $book->cover) }}" class="img-thumbnail" style="max-height: 150px;">
                            </div>
                            @endif

                            <div class="col-md-4">
                                <img id="img-preview" src="#" alt="Preview Baru" class="img-thumbnail" style="max-height: 150px; display: none;">
                                <small id="preview-label" class="d-block text-success font-weight-bold mb-1" style="display: none;">Akan Diganti Menjadi:</small>
                            </div>
                        </div>

                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="cover" name="cover" accept="image/*" onchange="previewImage(this)">
                            <label class="custom-file-label" for="cover">Pilih gambar baru...</label>
                        </div>
                        <small class="text-muted">Biarkan kosong jika tidak ingin mengganti cover.</small>
                    </div>

                </div>
            </div>

            <hr>
            <button type="submit" class="btn btn-warning shadow-sm" style="color: white; font-weight: 600;">
                <i class="fas fa-save mr-1"></i> Update Perubahan
            </button>
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
                var label = document.getElementById('preview-label');

                preview.src = e.target.result;
                preview.style.display = 'block';
                label.style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    }
</script>

@endsection