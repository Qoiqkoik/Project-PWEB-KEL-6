@extends('layouts.app')
@section('title', 'Laporan Keuangan')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Laporan Keuangan FauzanKos</h4>
    <button onclick="window.print()" class="btn btn-outline-primary">Cetak Laporan</button>
</div>
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card border-success shadow-sm">
            <div class="card-body">
                <h6 class="text-muted">Total Pemasukan (Lunas)</h6>
                <h3 class="text-success">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-danger shadow-sm">
            <div class="card-body">
                <h6 class="text-muted">Total Tunggakan / Belum Bayar</h6>
                <h3 class="text-danger">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
</div>
<div class="card shadow-sm">
    <div class="card-header fw-bold">Riwayat Pembayaran Lunas</div>
    <table class="table table-bordered mb-0">
        <thead>
            <tr><th>Penghuni</th><th>Kamar</th><th>Periode</th><th>Jumlah</th><th>Metode</th><th>Tgl Bayar</th></tr>
        </thead>
        <tbody>
            @forelse($lunasList as $l)
            <tr>
                <td>{{ $l->penghuni->user->name }}</td>
                <td>{{ $l->penghuni->kamar->nomor_kamar ?? '-' }}</td>
                <td>{{ $l->periode_bulan }}/{{ $l->periode_tahun }}</td>
                <td>Rp {{ number_format($l->jumlah, 0, ',', '.') }}</td>
                <td>{{ ucfirst($l->metode) }}</td>
                <td>{{ \Carbon\Carbon::parse($l->tanggal_bayar)->format('d/m/Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center">Belum ada transaksi lunas.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
