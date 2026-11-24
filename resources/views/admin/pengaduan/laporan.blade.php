@extends('layouts.app')
@section('title', 'Laporan Pengaduan')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Laporan Pengaduan Fasilitas Kos</h4>
    <button onclick="window.print()" class="btn btn-outline-primary">Cetak Laporan</button>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-header fw-bold">Jumlah per Status</div>
            <ul class="list-group list-group-flush">
                @foreach(['diajukan', 'diproses', 'selesai', 'ditolak'] as $st)
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ ucfirst($st) }}</span>
                        <span class="badge bg-primary rounded-pill">{{ $perStatus[$st] ?? 0 }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-header fw-bold">Jumlah per Kategori</div>
            <ul class="list-group list-group-flush">
                @foreach(['listrik', 'air', 'wi-fi', 'kamar mandi', 'lainnya'] as $kat)
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ ucfirst($kat) }}</span>
                        <span class="badge bg-info text-dark rounded-pill">{{ $perKategori[$kat] ?? 0 }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm h-100 border-success">
            <div class="card-header fw-bold bg-success text-white">Rata-Rata Waktu Penyelesaian</div>
            <div class="card-body d-flex flex-column justify-content-center text-center">
                <h2>{{ $rataRataJam ? round($rataRataJam, 1) . ' Jam' : 'Belum Ada' }}</h2>
                <small class="text-muted">Dihitung dari pengaduan yang telah selesai/ditolak</small>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header fw-bold">Riwayat Pengaduan Selesai & Durasi Penanganan</div>
    <table class="table table-bordered mb-0">
        <thead>
            <tr>
                <th>Kamar</th>
                <th>Judul</th>
                <th>Kategori</th>
                <th>Tgl Diajukan</th>
                <th>Tgl Selesai</th>
                <th>Durasi</th>
                <th>Ditangani Oleh</th>
            </tr>
        </thead>
        <tbody>
            @forelse($selesaiList as $s)
            <tr>
                <td>{{ $s->kamar->nomor_kamar }}</td>
                <td>{{ $s->judul }}</td>
                <td>{{ ucfirst($s->kategori) }}</td>
                <td>{{ $s->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $s->tanggal_selesai->format('d/m/Y H:i') }}</td>
                <td>{{ $s->created_at->diffForHumans($s->tanggal_selesai, true) }}</td>
                <td>{{ $s->adminPenangan->name ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center">Belum ada pengaduan yang selesai.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
