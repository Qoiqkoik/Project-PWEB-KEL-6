@extends('layouts.app')
@section('title', 'Ajukan Pengaduan')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-header fw-bold">Form Pengajuan Pengaduan (Kamar {{ $penghuni->kamar->nomor_kamar }})</div>
            <div class="card-body">
                <form action="{{ route('penghuni.pengaduan.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Judul Pengaduan</label>
                        <input type="text" name="judul" value="{{ old('judul') }}" class="form-control @error('judul') is-invalid @enderror" placeholder="Contoh: Lampu kamar mati">
                        @error('judul') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <select name="kategori" class="form-select @error('kategori') is-invalid @enderror">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach(['listrik', 'air', 'wi-fi', 'kamar mandi', 'lainnya'] as $kat)
                                <option value="{{ $kat }}" {{ old('kategori') == $kat ? 'selected' : '' }}>{{ ucfirst($kat) }}</option>
                            @endforeach
                        </select>
                        @error('kategori') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi Keluhan</label>
                        <textarea name="deskripsi" rows="3" class="form-control @error('deskripsi') is-invalid @enderror">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Foto Bukti (Opsional, Maks 2MB)</label>
                        <input type="file" name="foto" id="inputFoto" class="form-control @error('foto') is-invalid @enderror" accept="image/*">
                        @error('foto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <img id="previewFoto" src="#" class="img-thumbnail mt-2 d-none" style="max-height: 180px;">
                    </div>
                    <button type="submit" class="btn btn-primary">Kirim Pengaduan</button>
                    <a href="{{ route('penghuni.pengaduan.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // jQuery preview gambar sebelum diupload
    $('#inputFoto').on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#previewFoto').attr('src', e.target.result).removeClass('d-none');
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
