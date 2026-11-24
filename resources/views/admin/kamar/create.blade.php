@extends('layouts.app')
@section('title', 'Tambah Kamar')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header fw-bold">Tambah Kamar Baru</div>
            <div class="card-body">
                <form action="{{ route('admin.kamar.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nomor Kamar</label>
                        <input type="text" name="nomor_kamar" class="form-control" placeholder="Contoh: B-01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipe Kamar</label>
                        <input type="text" name="tipe" class="form-control" placeholder="Contoh: VIP AC" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Harga per Bulan (Rp)</label>
                            <input type="number" name="harga_per_bulan" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kapasitas (Orang)</label>
                            <input type="number" name="kapasitas" value="1" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fasilitas</label>
                        <textarea name="fasilitas" rows="2" class="form-control"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="kosong">Kosong</option>
                            <option value="terisi">Terisi</option>
                            <option value="perbaikan">Perbaikan</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('admin.kamar.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
