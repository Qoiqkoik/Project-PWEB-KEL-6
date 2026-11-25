@extends('layouts.app')
@php($isEditing = isset($pembayaran))
@section('title', $isEditing ? 'Edit Tagihan' : 'Buat Tagihan')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header fw-bold">{{ $isEditing ? 'Edit Tagihan Sewa Kos' : 'Buat Tagihan Sewa Kos' }}</div>
            <div class="card-body">
                <form action="{{ $isEditing ? route('admin.pembayaran.update', $pembayaran) : route('admin.pembayaran.store') }}" method="POST">
                    @csrf
                    @if($isEditing)
                        @method('PUT')
                    @endif
                    <div class="mb-3">
                        <label class="form-label">Penghuni</label>
                        <select name="penghuni_id" class="form-select @error('penghuni_id') is-invalid @enderror" required>
                            @foreach($penghunis as $p)
                                <option value="{{ $p->id }}" @selected(old('penghuni_id', $pembayaran->penghuni_id ?? '') == $p->id)>
                                    {{ $p->user->name }} (Kamar {{ $p->kamar->nomor_kamar ?? '-' }} - Rp {{ number_format($p->kamar->harga_per_bulan ?? 0,0,',','.') }})
                                </option>
                            @endforeach
                        </select>
                        @error('penghuni_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Bulan Tagihan</label>
                            <select name="periode_bulan" class="form-select @error('periode_bulan') is-invalid @enderror" required>
                                @foreach([1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'] as $nomorBulan => $namaBulan)
                                    <option value="{{ $nomorBulan }}" @selected(old('periode_bulan', $pembayaran->periode_bulan ?? now()->month) == $nomorBulan)>{{ $namaBulan }}</option>
                                @endforeach
                            </select>
                            @error('periode_bulan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tahun</label>
                            <input type="number" name="periode_tahun" value="{{ old('periode_tahun', $pembayaran->periode_tahun ?? now()->year) }}" min="2020" max="{{ now()->year + 5 }}" class="form-control @error('periode_tahun') is-invalid @enderror" required>
                            @error('periode_tahun') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jumlah Tagihan (Rp)</label>
                        <input type="number" name="jumlah" value="{{ old('jumlah', $pembayaran->jumlah ?? '') }}" min="1" class="form-control @error('jumlah') is-invalid @enderror" required>
                        @error('jumlah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jatuh Tempo</label>
                        <input type="date" name="jatuh_tempo" value="{{ old('jatuh_tempo', isset($pembayaran) ? $pembayaran->jatuh_tempo->toDateString() : '') }}" class="form-control @error('jatuh_tempo') is-invalid @enderror" required>
                        @error('jatuh_tempo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="2">{{ old('keterangan', $pembayaran->keterangan ?? '') }}</textarea>
                        @error('keterangan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    @if($penghunis->isEmpty())
                        <div class="alert alert-warning">Belum ada penghuni yang dapat dipilih.</div>
                    @endif
                    <button type="submit" class="btn btn-primary" @disabled($penghunis->isEmpty())>
                        {{ $isEditing ? 'Simpan Perubahan' : 'Terbitkan Tagihan' }}
                    </button>
                    <a href="{{ route('admin.pembayaran.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
