@extends('layouts.app')
@section('title', 'Edit Kamar')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header fw-bold">Edit Kamar {{ $kamar->nomor_kamar }}</div>
            <div class="card-body">
                <form action="{{ route('admin.kamar.update', $kamar->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Nomor Kamar</label>
                        <input type="text" name="nomor_kamar" value="{{ $kamar->nomor_kamar }}" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipe Kamar</label>
                        <input type="text" name="tipe" value="{{ $kamar->tipe }}" class="form-control" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Harga per Bulan (Rp)</label>
                            <input type="number" name="harga_per_bulan" value="{{ $kamar->harga_per_bulan }}" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kapasitas (Orang)</label>
                            <input type="number" name="kapasitas" value="{{ $kamar->kapasitas }}" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fasilitas</label>
                        <textarea name="fasilitas" rows="2" class="form-control">{{ $kamar->fasilitas }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            @foreach(['kosong', 'terisi', 'perbaikan'] as $st)
                                <option value="{{ $st }}" {{ $kamar->status == $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('admin.kamar.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
