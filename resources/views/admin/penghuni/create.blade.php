
@extends('layouts.app')

@section('title', 'Tambah Penghuni')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header fw-bold">
                Registrasi Penghuni Baru
            </div>

            <div class="card-body">

                {{-- Menampilkan pesan kesalahan --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Periksa kembali data yang kamu masukkan:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.penghuni.store') }}" method="POST">
                    @csrf

                    <div class="row g-3 mb-3">

                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="name"
                                value="{{ old('name') }}"
                                class="form-control @error('name') is-invalid @enderror"
                                required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email Login</label>
                            <input type="email" name="email"
                                value="{{ old('email') }}"
                                class="form-control @error('email') is-invalid @enderror"
                                required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">No. HP</label>
                            <input type="text" name="no_hp"
                                value="{{ old('no_hp') }}"
                                class="form-control @error('no_hp') is-invalid @enderror"
                                required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Password Akun</label>
                            <input type="password" name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Pilih Kamar Kosong</label>
                            <select name="kamar_id"
                                class="form-select @error('kamar_id') is-invalid @enderror">
                                <option value="">-- Belum Pilih Kamar --</option>
                                @foreach($kamars as $k)
                                    <option value="{{ $k->id }}"
                                        {{ old('kamar_id') == $k->id ? 'selected' : '' }}>
                                        {{ $k->nomor_kamar }} - {{ $k->tipe }}
                                        (Rp {{ number_format($k->harga_per_bulan, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Tanggal Masuk</label>
                            <input type="date" name="tanggal_masuk"
                                value="{{ old('tanggal_masuk', date('Y-m-d')) }}"
                                class="form-control @error('tanggal_masuk') is-invalid @enderror"
                                required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">NIK</label>
                            <input type="text" name="nik"
                                value="{{ old('nik') }}"
                                class="form-control @error('nik') is-invalid @enderror"
                                required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Pekerjaan</label>
                            <input type="text" name="pekerjaan"
                                value="{{ old('pekerjaan') }}"
                                class="form-control @error('pekerjaan') is-invalid @enderror"
                                required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Alamat Asal</label>
                            <textarea name="alamat_asal" rows="2"
                                class="form-control @error('alamat_asal') is-invalid @enderror"
                                required>{{ old('alamat_asal') }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nama Kontak Darurat</label>
                            <input type="text" name="nama_kontak_darurat"
                                value="{{ old('nama_kontak_darurat') }}"
                                class="form-control @error('nama_kontak_darurat') is-invalid @enderror"
                                required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">No. Kontak Darurat</label>
                            <input type="text" name="no_kontak_darurat"
                                value="{{ old('no_kontak_darurat') }}"
                                class="form-control @error('no_kontak_darurat') is-invalid @enderror"
                                required>
                        </div>

                    </div>

                    <button type="submit" class="btn btn-primary">
                        Simpan Penghuni
                    </button>

                    <a href="{{ route('admin.penghuni.index') }}"
                        class="btn btn-secondary">
                        Batal
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
