@extends('layout')

@section('title', 'Ajukan Buku Baru')

@section('content')
<div class="container main-content mt-4">

    {{-- HEADER SECTION --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 style="font-weight: 800; color: #1e293b; margin: 0; font-size: 2rem;">
                Ajukan Buku Baru
            </h1>
            <p class="text-muted mt-1 mb-0">Isi formulir di bawah untuk merekomendasikan buku ke perpustakaan.</p>
        </div>
        <a href="{{ route('student.requests.index') }}" class="btn btn-light shadow-sm" style="border-radius: 50px; font-weight: 600; padding: 10px 25px; color: #475569;">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">

                <div class="card-body p-5">

                    {{-- Alert Info --}}
                    <div class="alert fade show d-flex align-items-center mb-4" role="alert" style="background-color: #eff6ff; border: 1px solid #dbeafe; color: #1e40af; border-radius: 12px;">
                        <i class="fas fa-info-circle fa-lg mr-3"></i>
                        <div>
                            <strong>Penting:</strong> Pastikan buku yang Anda ajukan <u>belum tersedia</u> di katalog pencarian perpustakaan.
                        </div>
                    </div>

                    <form action="{{ route('student.requests.store') }}" method="POST" enctype="multipart/form-data" id="requestForm">
                        @csrf

                        {{-- Judul Buku --}}
                        <div class="form-group">
                            <label class="font-weight-bold text-dark mb-2">
                                <i class="fas fa-book mr-1 text-primary"></i> Judul Buku <span class="text-danger">*</span>
                            </label>
                            {{-- PERUBAHAN 1: Tambah @error('title') is-invalid @enderror di class --}}
                            <input type="text" name="title"
                                class="form-control form-control-lg bg-light border-0 @error('title') is-invalid @enderror"
                                placeholder="Contoh: Clean Code" style="border-radius: 10px;" required value="{{ old('title') }}">

                            {{-- PERUBAHAN 2: Tambah pesan error dibawah input --}}
                            @error('title')
                            <div class="invalid-feedback d-block pl-1">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        {{-- Penulis & Penerbit --}}
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label class="font-weight-bold text-dark mb-2">
                                    <i class="fas fa-user-edit mr-1 text-primary"></i> Penulis <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="author"
                                    class="form-control form-control-lg bg-light border-0 @error('author') is-invalid @enderror"
                                    placeholder="Contoh: Robert C. Martin" style="border-radius: 10px;" required value="{{ old('author') }}">
                                @error('author')
                                <div class="invalid-feedback d-block pl-1">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label class="font-weight-bold text-dark mb-2">
                                    <i class="fas fa-building mr-1 text-primary"></i> Penerbit
                                </label>
                                <input type="text" name="publisher"
                                    class="form-control form-control-lg bg-light border-0 @error('publisher') is-invalid @enderror"
                                    placeholder="(Opsional)" style="border-radius: 10px;" value="{{ old('publisher') }}">
                                @error('publisher')
                                <div class="invalid-feedback d-block pl-1">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>

                        {{-- INPUT FOTO DENGAN PREVIEW --}}
                        <div class="form-group">
                            <label class="font-weight-bold text-dark mb-2">
                                <i class="fas fa-camera mr-1 text-primary"></i> Foto Sampul (Opsional)
                            </label>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="custom-file">
                                        {{-- Tambahkan is-invalid di input file juga --}}
                                        <input type="file" name="image" class="custom-file-input @error('image') is-invalid @enderror" id="imageUpload" accept="image/*">
                                        <label class="custom-file-label bg-light border-0" for="imageUpload" style="border-radius: 10px; color: #6c757d;">Pilih file gambar...</label>
                                    </div>
                                    <small class="text-muted pl-1">Format: JPG, PNG. Maksimal ukuran: 2MB.</small>

                                    {{-- Pesan error gambar --}}
                                    @error('image')
                                    <div class="text-danger small pl-1 mt-1 font-weight-bold">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Area Preview Gambar --}}
                            <div class="mt-3 d-none" id="previewContainer">
                                <div class="card border-0 shadow-sm bg-light" style="width: 200px; border-radius: 15px;">
                                    <img id="imagePreview" src="#" alt="Preview" class="card-img-top"
                                        style="border-radius: 15px; width: 100%; height: 280px; object-fit: cover;">
                                    <div class="card-body p-2 text-center">
                                        <small class="text-muted font-weight-bold">Preview Sampul</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Kategori --}}
                        <div class="form-group">
                            <label class="font-weight-bold text-dark mb-2">
                                <i class="fas fa-tags mr-1 text-primary"></i> Kategori
                            </label>
                            <select name="category" class="form-control form-control-lg bg-light border-0" style="border-radius: 10px; -webkit-appearance: none;">
                                <option value="">-- Pilih Kategori (Opsional) --</option>
                                <option value="Teknologi" {{ old('category') == 'Teknologi' ? 'selected' : '' }}>Teknologi / Komputer</option>
                                <option value="Ekonomi" {{ old('category') == 'Ekonomi' ? 'selected' : '' }}>Ekonomi / Bisnis</option>
                                <option value="Sastra" {{ old('category') == 'Sastra' ? 'selected' : '' }}>Sastra / Fiksi</option>
                                <option value="Hukum" {{ old('category') == 'Hukum' ? 'selected' : '' }}>Hukum</option>
                                <option value="Kesehatan" {{ old('category') == 'Kesehatan' ? 'selected' : '' }}>Kesehatan</option>
                                <option value="Lainnya" {{ old('category') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                        </div>

                        {{-- Alasan --}}
                        <div class="form-group">
                            <label class="font-weight-bold text-dark mb-2">
                                <i class="fas fa-comment-alt mr-1 text-primary"></i> Alasan Pengajuan <span class="text-danger">*</span>
                            </label>
                            <textarea name="reason"
                                class="form-control bg-light border-0 @error('reason') is-invalid @enderror"
                                rows="4"
                                placeholder="Jelaskan urgensi buku ini..." style="border-radius: 10px;" required>{{ old('reason') }}</textarea>
                            @error('reason')
                            <div class="invalid-feedback d-block pl-1">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-between align-items-center">
                            <button type="reset" class="btn btn-light text-muted font-weight-bold rounded-pill px-4" id="btnReset">
                                <i class="fas fa-undo mr-1"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 font-weight-bold shadow-lg" style="background: linear-gradient(to right, #4e73df, #224abe); border: none;">
                                <i class="fas fa-paper-plane mr-2"></i> Kirim Usulan
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Logic untuk Preview Gambar
        $('#imageUpload').on('change', function() {
            // 1. Ganti nama label file
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);

            // 2. Baca file dan tampilkan preview
            const file = this.files[0];
            if (file) {
                let reader = new FileReader();
                reader.onload = function(event) {
                    $('#imagePreview').attr('src', event.target.result);
                    $('#previewContainer').removeClass('d-none'); // Munculkan container
                    $('#previewContainer').addClass('animate-fade-in'); // Efek animasi (opsional)
                }
                reader.readAsDataURL(file);
            } else {
                // Jika user cancel pilih file
                $('#previewContainer').addClass('d-none');
            }
        });

        // Logic untuk Tombol Reset (agar preview hilang juga)
        $('#btnReset').on('click', function() {
            $('#previewContainer').addClass('d-none'); // Sembunyikan preview
            $('#imageUpload').next('.custom-file-label').html('Pilih file gambar...'); // Reset label
        });
    });
</script>
@endpush

@endsection