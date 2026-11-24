@extends('layouts.app')
@section('title', 'Tambah Penghuni')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header fw-bold">Registrasi Penghuni Baru</div>
            <div class="card-body">
                <form action="{{ route('admin.penghuni.store') }}" method="POST">
                    @csrf
                    <div class="row g-3 mb-3">
                        <div class="col-md-6"><label class="form-label">Nama Lengkap</label><input type="text" name="name" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">Email Login</label><input type="email" name="email" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">No. HP</label><input type="text" name="no_hp" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">Password Akun</label><input type="password" name="password" class="form-control" required></div>
                        <div class="col-md-6">
                            <label class="form-label">Pilih Kamar Kosong</label>
                            <select name="kamar_id" class="form-select">
                                <option value="">-- Belum Pilih Kamar --</option>
                                @foreach($kamars as $k)
                                    <option value="{{ $k->id }}">{{ $k->nomor_kamar }} - {{ $k->tipe }} (Rp {{ number_format($k->harga_per_bulan,0,',','.') }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6"><label class="form-label">Tanggal Masuk</label><input type="date" name="tanggal_masuk" value="{{ date('Y-m-d') }}" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">NIK</label><input type="text" name="nik" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">Pekerjaan</label><input type="text" name="pekerjaan" class="form-control" required></div>
                        <div class="col-12"><label class="form-label">Alamat Asal</label><textarea name="alamat_asal" rows="2" class="form-control" required></textarea></div>
                        <div class="col-md-6"><label class="form-label">Nama Kontak Darurat</label><input type="text" name="nama_kontak_darurat" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">No. Kontak Darurat</label><input type="text" name="no_kontak_darurat" class="form-control" required></div>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan Penghuni</button>
                    <a href="{{ route('admin.penghuni.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
