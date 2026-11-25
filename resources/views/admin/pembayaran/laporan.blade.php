@extends('layouts.app')
@section('title', 'Laporan Pembayaran')
@section('content')
<style>
    @media print {
        .navbar, .no-print, .alert { display: none !important; }
        body, .card { background: #fff !important; box-shadow: none !important; }
        .card { border: 0 !important; }
        table { page-break-inside: auto; }
        tr { page-break-inside: avoid; page-break-after: auto; }
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-1">Laporan Pembayaran FauzanKos</h4>
        <div class="text-muted">Periode tagihan {{ sprintf('%02d', $periodeBulan) }}/{{ $periodeTahun }}</div>
    </div>
    <button onclick="window.print()" class="btn btn-outline-primary no-print">Cetak / Simpan PDF</button>
</div>

<form method="GET" action="{{ route('admin.pembayaran.laporan') }}" class="card card-body shadow-sm mb-4 no-print">
    <div class="row g-2 align-items-end">
        <div class="col-md-4">
            <label for="periode_bulan" class="form-label">Bulan tagihan</label>
            <select id="periode_bulan" name="periode_bulan" class="form-select">
                @foreach([1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'] as $nomorBulan => $namaBulan)
                    <option value="{{ $nomorBulan }}" @selected($periodeBulan === $nomorBulan)>{{ $namaBulan }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label for="periode_tahun" class="form-label">Tahun</label>
            <input id="periode_tahun" type="number" name="periode_tahun" min="2020" max="{{ now()->year + 5 }}" value="{{ $periodeTahun }}" class="form-control" required>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Tampilkan Laporan</button>
        </div>
    </div>
</form>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card border-success shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-muted">Total Pemasukan Terverifikasi</h6>
                <h3 class="text-success">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-danger shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-muted">Total Tagihan Belum Lunas</h6>
                <h3 class="text-danger">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header fw-bold">Riwayat Pembayaran Lunas</div>
    <div class="table-responsive">
        <table class="table table-bordered mb-0">
            <thead class="table-light">
                <tr><th>Penghuni</th><th>Kamar</th><th>Periode</th><th>Jumlah</th><th>Metode</th><th>Tanggal Bayar</th></tr>
            </thead>
            <tbody>
                @forelse($lunasList as $pembayaran)
                <tr>
                    <td>{{ $pembayaran->penghuni->user->name }}</td>
                    <td>{{ $pembayaran->penghuni->kamar->nomor_kamar ?? '-' }}</td>
                    <td>{{ $pembayaran->periode_bulan }}/{{ $pembayaran->periode_tahun }}</td>
                    <td>Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}</td>
                    <td>{{ ucfirst($pembayaran->metode ?? '-') }}</td>
                    <td>{{ $pembayaran->tanggal_bayar?->format('d/m/Y') ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center">Belum ada pembayaran lunas pada periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header fw-bold text-danger">Daftar Penghuni Menunggak</div>
    <div class="table-responsive">
        <table class="table table-bordered mb-0">
            <thead class="table-light">
                <tr><th>Penghuni</th><th>Kamar</th><th>Periode</th><th>Jumlah Tunggakan</th><th>Jatuh Tempo</th></tr>
            </thead>
            <tbody>
                @forelse($menunggakList as $pembayaran)
                <tr>
                    <td>{{ $pembayaran->penghuni->user->name }}</td>
                    <td>{{ $pembayaran->penghuni->kamar->nomor_kamar ?? '-' }}</td>
                    <td>{{ $pembayaran->periode_bulan }}/{{ $pembayaran->periode_tahun }}</td>
                    <td>Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}</td>
                    <td>{{ $pembayaran->jatuh_tempo->format('d/m/Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center">Tidak ada penghuni menunggak pada periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
