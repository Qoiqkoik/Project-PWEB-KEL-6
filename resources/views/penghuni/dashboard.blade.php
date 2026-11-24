@extends('layouts.app')
@section('title', 'Dashboard Penghuni')
@section('content')
<h4 class="mb-3">Selamat Datang, {{ auth()->user()->name }}</h4>
<div class="row g-3">
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-primary text-white fw-bold">Info Kamar Anda</div>
            <div class="card-body">
                @if($penghuni && $penghuni->kamar)
                    <h5>Kamar {{ $penghuni->kamar->nomor_kamar }} ({{ $penghuni->kamar->tipe }})</h5>
                    <p class="mb-1">Tarif: <strong>Rp {{ number_format($penghuni->kamar->harga_per_bulan, 0, ',', '.') }}/bulan</strong></p>
                    <small class="text-muted">Fasilitas: {{ $penghuni->kamar->fasilitas }}</small>
                @else
                    <p class="text-muted mb-0">Anda belum menempati kamar.</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-warning fw-bold">Tagihan Belum Lunas</div>
            <div class="card-body">
                <h3>{{ $tagihanAktif->count() }} Tagihan</h3>
                <a href="{{ route('penghuni.pembayaran.index') }}" class="btn btn-sm btn-outline-dark mt-2">Lihat Tagihan</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-info text-white fw-bold">Pengaduan Fasilitas</div>
            <div class="card-body">
                <p class="mb-2">Ada kendala listrik, air, atau Wi-Fi?</p>
                <a href="{{ route('penghuni.pengaduan.create') }}" class="btn btn-sm btn-primary">+ Ajukan Pengaduan</a>
            </div>
        </div>
    </div>
</div>
@endsection
