@extends('layouts.app')
@section('title', 'Kelola Pembayaran')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Manajemen Tagihan & Pembayaran</h4>
    <a href="{{ route('admin.pembayaran.create') }}" class="btn btn-primary">+ Buat Tagihan</a>
</div>

<form method="GET" action="{{ route('admin.pembayaran.index') }}" class="card card-body shadow-sm mb-3">
    <div class="row g-2 align-items-end">
        <div class="col-md-3">
            <label for="status" class="form-label">Status</label>
            <select id="status" name="status" class="form-select">
                <option value="">Semua status</option>
                <option value="belum_bayar" @selected(($filters['status'] ?? '') === 'belum_bayar')>Belum bayar</option>
                <option value="lunas" @selected(($filters['status'] ?? '') === 'lunas')>Lunas</option>
                <option value="terlambat" @selected(($filters['status'] ?? '') === 'terlambat')>Terlambat</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="periode_bulan" class="form-label">Bulan tagihan</label>
            <select id="periode_bulan" name="periode_bulan" class="form-select">
                <option value="">Semua bulan</option>
                @foreach([1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'] as $nomorBulan => $namaBulan)
                    <option value="{{ $nomorBulan }}" @selected((string) ($filters['periode_bulan'] ?? '') === (string) $nomorBulan)>{{ $namaBulan }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label for="periode_tahun" class="form-label">Tahun</label>
            <input id="periode_tahun" type="number" name="periode_tahun" min="2020" max="{{ now()->year + 5 }}" value="{{ $filters['periode_tahun'] ?? '' }}" class="form-control">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-outline-primary">Filter</button>
            <a href="{{ route('admin.pembayaran.index') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </div>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-bordered table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Penghuni / Kamar</th>
                    <th>Periode</th>
                    <th>Jumlah</th>
                    <th>Jatuh Tempo</th>
                    <th>Pembayaran</th>
                    <th>Bukti</th>
                    <th>Status</th>
                    <th>Verifikasi</th>
                    <th>Aksi Tagihan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pembayarans as $b)
                @php($badgeColor = ['belum_bayar' => 'warning', 'lunas' => 'success', 'terlambat' => 'danger'][$b->status])
                <tr>
                    <td>
                        <strong>{{ $b->penghuni->user->name }}</strong><br>
                        <small>Kamar {{ $b->penghuni->kamar->nomor_kamar ?? '-' }}</small>
                    </td>
                    <td>{{ $b->periode_bulan }}/{{ $b->periode_tahun }}</td>
                    <td>Rp {{ number_format($b->jumlah, 0, ',', '.') }}</td>
                    <td>
                        {{ $b->jatuh_tempo->format('d/m/Y') }}
                        @if($b->status === 'terlambat')
                            <br><small class="text-danger fw-semibold">Lewat jatuh tempo</small>
                        @endif
                    </td>
                    <td>
                        {{ $b->metode ? ucfirst($b->metode) : '-' }}<br>
                        <small>{{ $b->tanggal_bayar?->format('d/m/Y') ?? 'Belum tercatat' }}</small>
                    </td>
                    <td>
                        @if($b->bukti_bayar)
                            <a href="{{ route('admin.pembayaran.bukti', $b) }}" class="btn btn-sm btn-outline-info">Unduh</a>
                        @else
                            -
                        @endif
                    </td>
                    <td><span class="badge bg-{{ $badgeColor }}">{{ strtoupper(str_replace('_', ' ', $b->status)) }}</span></td>
                    <td style="min-width: 220px;">
                        <form action="{{ route('admin.pembayaran.konfirmasi', $b) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <select name="status" class="form-select form-select-sm mb-1">
                                <option value="belum_bayar" @selected($b->status !== 'lunas')>Belum bayar</option>
                                <option value="lunas" @selected($b->status === 'lunas')>Lunas</option>
                            </select>
                            <select name="metode" class="form-select form-select-sm mb-1">
                                <option value="">Pilih metode</option>
                                <option value="transfer" @selected(old('metode', $b->metode) === 'transfer')>Transfer</option>
                                <option value="tunai" @selected(old('metode', $b->metode) === 'tunai')>Tunai</option>
                            </select>
                            <input type="date" name="tanggal_bayar" value="{{ old('tanggal_bayar', $b->tanggal_bayar?->toDateString()) }}" max="{{ today()->toDateString() }}" class="form-control form-control-sm mb-1">
                            <input type="text" name="keterangan" value="{{ old('keterangan', $b->keterangan) }}" placeholder="Keterangan" class="form-control form-control-sm mb-1">
                            <button class="btn btn-sm btn-success w-100">Simpan Verifikasi</button>
                        </form>
                    </td>
                    <td class="text-nowrap">
                        <a href="{{ route('admin.pembayaran.edit', $b) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form action="{{ route('admin.pembayaran.destroy', $b) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus tagihan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center">Belum ada data tagihan untuk filter ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $pembayarans->links() }}</div>
@endsection
