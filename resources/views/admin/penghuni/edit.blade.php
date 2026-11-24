
@extends('layouts.app')
@section('title', 'Edit Penghuni')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header fw-bold">Edit Data Penghuni</div>
            <div class="card-body">

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Periksa kembali data yang dimasukkan:</strong>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.penghuni.update', $penghuni->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="name"
                                value="{{ old('name', $penghuni->user->name) }}"
                                class="form-control @error('name') is-invalid @enderror" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email"
                                value="{{ old('email', $penghuni->user->email) }}"
                                class="form-control @error('email') is-invalid @enderror" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">No. HP</label>
                            <input type="text" name="no_hp"
                                value="{{ old('no_hp', $penghuni->user->no_hp) }}"
                                class="form-control @error('no_hp') is-invalid @enderror" required>
                            @error('no_hp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Kamar</label>
                            <select name="kamar_id"
                                class="form-select @error('kamar_id') is-invalid @enderror">
                                <option value="">-- Tanpa Kamar --</option>
                                @foreach($kamars as $k)
                                    <option value="{{ $k->id }}"
                                        {{ old('kamar_id', $penghuni->kamar_id) == $k->id ? 'selected' : '' }}>
                                        {{ $k->nomor_kamar }} - {{ $k->tipe }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kamar_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">NIK</label>
                            <input type="text" name="nik"
                                value="{{ old('nik', $penghuni->nik) }}"
                                class="form-control @error('nik') is-invalid @enderror" required>
                            @error('nik') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Pekerjaan</label>
                            <input type="text" name="pekerjaan"
                                value="{{ old('pekerjaan', $penghuni->pekerjaan) }}"
                                class="form-control @error('pekerjaan') is-invalid @enderror" required>
                            @error('pekerjaan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select name="status"
                                class="form-select @error('status') is-invalid @enderror">
                                <option value="aktif" {{ old('status', $penghuni->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ old('status', $penghuni->status) == 'nonaktif' ? 'selected' : '' }}>Nonaktif (Keluar)</option>
                            </select>
                            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Alamat Asal</label>
                            <textarea name="alamat_asal" rows="2"
                                class="form-control @error('alamat_asal') is-invalid @enderror" required>{{ old('alamat_asal', $penghuni->alamat_asal) }}</textarea>
                            @error('alamat_asal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nama Kontak Darurat</label>
                            <input type="text" name="nama_kontak_darurat"
                                value="{{ old('nama_kontak_darurat', $penghuni->nama_kontak_darurat) }}"
                                class="form-control @error('nama_kontak_darurat') is-invalid @enderror" required>
                            @error('nama_kontak_darurat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">No. Kontak Darurat</label>
                            <input type="text" name="no_kontak_darurat"
                                value="{{ old('no_kontak_darurat', $penghuni->no_kontak_darurat) }}"
                                class="form-control @error('no_kontak_darurat') is-invalid @enderror" required>
                            @error('no_kontak_darurat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Data</button>
                    <a href="{{ route('admin.penghuni.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
