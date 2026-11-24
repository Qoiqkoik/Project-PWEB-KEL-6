@extends('layouts.app')
@section('title', 'Dashboard Admin')
@section('content')
<h4 class="mb-4">Dashboard Pengelola FauzanKos</h4>
<div class="row g-3">
    <div class="col-md-3">
        <div class="card border-primary shadow-sm">
            <div class="card-body">
                <h6 class="text-muted">Total Kamar</h6>
                <h3>{{ $totalKamar }} <small class="fs-6 text-success">({{ $kamarKosong }} Kosong)</small></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-info shadow-sm">
            <div class="card-body">
                <h6 class="text-muted">Penghuni Aktif</h6>
                <h3>{{ $totalPenghuniAktif }} Orang</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-success shadow-sm">
            <div class="card-body">
                <h6 class="text-muted">Pemasukan Bulan Ini</h6>
                <h4>Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-warning shadow-sm">
            <div class="card-body">
                <h6 class="text-muted">Pengaduan Aktif</h6>
                <h3>{{ $pengaduanAktif }} Laporan</h3>
            </div>
        </div>
    </div>
</div>
@endsection
