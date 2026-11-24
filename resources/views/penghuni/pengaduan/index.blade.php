@extends('layouts.app')
@section('title', 'Pengaduan Saya')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Riwayat Pengaduan Saya (Kamar: {{ $penghuni->kamar->nomor_kamar ?? '-' }})</h4>
    <a href="{{ route('penghuni.pengaduan.create') }}" class="btn btn-primary">+ Ajukan Pengaduan</a>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-bordered table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Tanggal</th>
                    <th>Judul & Deskripsi</th>
                    <th>Kategori</th>
                    <th>Foto</th>
                    <th>Status</th>
                    <th>Tanggapan Admin</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengaduans as $p)
                <tr>
                    <td>{{ $p->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <strong>{{ $p->judul }}</strong><br>
                        <small class="text-muted">{{ $p->deskripsi }}</small>
                    </td>
                    <td><span class="badge bg-info text-dark">{{ strtoupper($p->kategori) }}</span></td>
                    <td>
                        @if($p->foto)
                            <a href="{{ asset('storage/' . $p->foto) }}" target="_blank" class="btn btn-sm btn-outline-secondary">Lihat Foto</a>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $warna = ['diajukan' => 'warning', 'diproses' => 'primary', 'selesai' => 'success', 'ditolak' => 'danger'][$p->status];
                        @endphp
                        <span class="badge bg-{{ $warna }}">{{ ucfirst($p->status) }}</span>
                    </td>
                    <td>{{ $p->tanggapan ?? 'Belum ada tanggapan' }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center">Belum ada pengaduan yang diajukan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $pengaduans->links() }}</div>
@endsection
