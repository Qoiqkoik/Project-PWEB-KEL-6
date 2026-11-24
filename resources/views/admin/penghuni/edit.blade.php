@extends('layouts.app')
@section('title', 'Edit Penghuni')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header fw-bold">Edit Data Penghuni</div>
            <div class="card-body">
                <form action="{{ route('admin.penghuni.update', $penghuni->id) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="row g-3 mb-3">
                        <div class="col-md-6"><label class="form-label">Nama Lengkap</label><input type="text" name="name" value="{{ $penghuni->user->name }}" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" value="{{ $penghuni->user->email }}" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">No. HP</label><input type="text" name="no_hp" value="{{ $penghuni->user->no_hp }}" class="form-control" required></div>
                        <div class="col-md-6">
                            <label class="form-label">Kamar</label>
                            <select name="kamar_id" class="form-select">
                                <option value="">-- Tanpa Kamar --</option>
                                @foreach($kamars as $k)
                                    <option value="{{ $k->id }}" {{ $penghuni->kamar_id == $k->id ? 'selected' : '' }}>{{ $k->nomor_kamar }} - {{ $k->tipe }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4"><label class="form-label">NIK</label><input type="text" name="nik" value="{{ $penghuni->nik }}" class="form-control" required></div>
                        <div class="col-md-4"><label class="form-label">Pekerjaan</label><input type="text" name="pekerjaan" value="{{ $penghuni->pekerjaan }}" class="form-control" required></div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="aktif" {{ $penghuni->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ $penghuni->status == 'nonaktif' ? 'selected' : '' }}>Nonaktif (Keluar)</option>
                            </select>
                        </div>
                        <div class="col-12"><label class="form-label">Alamat Asal</label><textarea name="alamat_asal" rows="2" class="form-control" required>{{ $penghuni->alamat_asal }}</textarea></div>
                        <div class="col-md-6"><label class="form-label">Nama Kontak Darurat</label><input type="text" name="nama_kontak_darurat" value="{{ $penghuni->nama_kontak_darurat }}" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">No. Kontak Darurat</label><input type="text" name="no_kontak_darurat" value="{{ $penghuni->no_kontak_darurat }}" class="form-control" required></div>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Data</button>
                    <a href="{{ route('admin.penghuni.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
