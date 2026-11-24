@extends('layouts.app')
@section('title', 'Kelola Pengaduan')
@section('content')
<h4 class="mb-3">Daftar Pengaduan Penghuni</h4>

<form method="GET" class="row g-2 mb-3">
    <div class="col-md-3">
        <select name="status" class="form-select">
            <option value="">-- Semua Status --</option>
            @foreach(['diajukan', 'diproses', 'selesai', 'ditolak'] as $st)
                <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <select name="kategori" class="form-select">
            <option value="">-- Semua Kategori --</option>
            @foreach(['listrik', 'air', 'wi-fi', 'kamar mandi', 'lainnya'] as $kat)
                <option value="{{ $kat }}" {{ request('kategori') == $kat ? 'selected' : '' }}>{{ ucfirst($kat) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <button type="submit" class="btn btn-secondary w-100">Filter</button>
    </div>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-bordered table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Kamar / Penghuni</th>
                    <th>Judul & Keluhan</th>
                    <th>Kategori</th>
                    <th>Foto</th>
                    <th>Status</th>
                    <th>Tanggapan & Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengaduans as $p)
                <tr>
                    <td>
                        <strong>Kamar {{ $p->kamar->nomor_kamar }}</strong><br>
                        <small>{{ $p->penghuni->user->name }}</small>
                    </td>
                    <td>
                        <strong>{{ $p->judul }}</strong><br>
                        <small class="text-muted">{{ $p->deskripsi }}</small>
                    </td>
                    <td>{{ ucfirst($p->kategori) }}</td>
                    <td>
                        @if($p->foto)
                            <a href="{{ asset('storage/' . $p->foto) }}" target="_blank" class="btn btn-sm btn-outline-secondary">Foto</a>
                        @else - @endif
                    </td>
                    <td>
                        @php $warna = ['diajukan' => 'warning', 'diproses' => 'primary', 'selesai' => 'success', 'ditolak' => 'danger'][$p->status]; @endphp
                        <span class="badge bg-{{ $warna }}">{{ ucfirst($p->status) }}</span>
                    </td>
                    <td style="min-width: 260px;">
                        <form action="{{ route('admin.pengaduan.update', $p->id) }}" method="POST">
                            @csrf @method('PUT')
                            <select name="status" class="form-select form-select-sm mb-1">
                                @foreach(['diajukan', 'diproses', 'selesai', 'ditolak'] as $st)
                                    <option value="{{ $st }}" {{ $p->status == $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                                @endforeach
                            </select>
                            <textarea name="tanggapan" rows="2" class="form-control form-control-sm mb-1" placeholder="Tulis tanggapan...">{{ $p->tanggapan }}</textarea>
                            <button type="submit" class="btn btn-primary btn-sm w-100">Simpan Update</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center">Tidak ada data pengaduan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $pengaduans->withQueryString()->links() }}</div>
@endsection
