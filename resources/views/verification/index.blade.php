@extends('layout')

@section('title', 'Verifikasi Akun')

@section('content')
<div class="container main-content mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 font-weight-bold text-primary"><i class="fas fa-id-card mr-2"></i> Verifikasi Identitas</h5>
                </div>
                <div class="card-body p-4">

                    @if($student->verification_status == 'verified')
                    <div class="text-center py-4">
                        <div style="width: 80px; height: 80px; background: #d4edda; color: #155724; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; margin: 0 auto 20px;">
                            <i class="fas fa-check"></i>
                        </div>
                        <h3 class="font-weight-bold text-success">Akun Terverifikasi</h3>
                        <p class="text-muted">Anda sudah dapat meminjam buku di perpustakaan.</p>
                        <img src="{{ asset('storage/' . $student->ktm_image) }}" class="img-thumbnail mt-3" style="max-height: 200px;">
                    </div>

                    @elseif($student->verification_status == 'pending')
                    <div class="text-center py-4">
                        <div style="width: 80px; height: 80px; background: #fff3cd; color: #856404; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; margin: 0 auto 20px;">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h4 class="font-weight-bold text-warning">Menunggu Verifikasi Admin</h4>
                        <p class="text-muted">Kami sedang mengecek data KTM Anda. Proses ini biasanya memakan waktu 1x24 jam.</p>
                    </div>

                    @else
                    @if($student->verification_status == 'rejected')
                    <div class="alert alert-danger mb-4">
                        <strong><i class="fas fa-times-circle"></i> Verifikasi Ditolak!</strong>
                        <p class="mb-0">{{ $student->rejection_reason }}</p>
                    </div>
                    @endif

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-1"></i> Untuk meminjam buku, Anda wajib mengunggah foto <strong>Kartu Tanda Mahasiswa (KTM)</strong> yang masih berlaku.
                    </div>

                    <form action="{{ route('verification.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label class="font-weight-bold">Upload Foto KTM</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="ktmFile" name="ktm_image" required accept="image/*" onchange="previewImage()">
                                <label class="custom-file-label" for="ktmFile">Pilih file foto...</label>
                            </div>
                            <small class="text-muted">Format: JPG, PNG. Maks: 2MB.</small>
                        </div>

                        <div class="mt-3 text-center d-none" id="previewContainer">
                            <img id="imgPreview" src="#" alt="Preview KTM" class="img-thumbnail" style="max-height: 250px;">
                        </div>

                        <button type="submit" class="btn btn-primary btn-block mt-4 py-2 font-weight-bold" style="background: #2c3e50; border: none;">
                            <i class="fas fa-upload mr-2"></i> Unggah KTM
                        </button>
                    </form>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function previewImage() {
        const file = document.getElementById('ktmFile').files[0];
        const previewContainer = document.getElementById('previewContainer');
        const imgPreview = document.getElementById('imgPreview');
        const label = document.querySelector('.custom-file-label');

        if (file) {
            label.textContent = file.name;
            const reader = new FileReader();
            reader.onload = function(e) {
                imgPreview.src = e.target.result;
                previewContainer.classList.remove('d-none');
            }
            reader.readAsDataURL(file);
        }
    }
</script>
@endsection