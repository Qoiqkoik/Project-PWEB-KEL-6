@extends('layouts.app')
@section('title', 'Kelola Pembayaran')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Manajemen Tagihan & Pembayaran</h4>
    <a href="{{ route('admin.pembayaran.create') }}" class="btn btn-primary">+ Buat Tagihan Bulanan</a>
</div>
<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-bordered table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Penghuni / Kamar</th>
                    <th>Periode</th>
                    <th>Jumlah</th>
                    <th>Jatuh Tempo</th>
                    <th>Bukti</th>
                    <th>Status</th>
                    <th>Konfirmasi Admin</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pembayarans as $b)
                <tr>
                    <td><strong>{{ $b->penghuni->user->name }}</strong><br><small>Kamar {{ $b->penghuni->kamar->nomor_kamar ?? '-' }}</small></td>
                    <td>Bulan {{ $b->periode_bulan }} / {{ $b->periode_tahun }}</td>
                    <td>Rp {{ number_format($b->jumlah, 0, ',', '.') }}</td>
                    <td>{{ \Carbon\Carbon::parse($b->jatuh_tempo)->format('d/m/Y') }}</td>
                    <td>
                        @if($b->bukti_bayar)
                            <a href="{{ asset('storage/' . $b->bukti_bayar) }}" target="_blank" class="btn btn-sm btn-outline-info">Lihat Bukti</a>
                        @else - @endif
                    </td>
                    <td>
                        @php $w = ['belum_bayar' => 'warning', 'lunas' => 'success', 'terlambat' => 'danger'][$b->status]; @endphp
                        <span class="badge bg-{{ $w }}">{{ strtoupper(str_replace('_', ' ', $b->status)) }}</span>
                    </td>
                    <td style="min-width: 220px;">
                        <form action="{{ route('admin.pembayaran.konfirmasi', $b->id) }}" method="POST">
                            @csrf @method('PUT')
                            <select name="status" class="form-select form-select-sm mb-1">
                                <option value="belum_bayar" {{ $b->status == 'belum_bayar' ? 'selected' : '' }}>Belum Bayar</option>
                                <option value="lunas" {{ $b->status == 'lunas' ? 'selected' : '' }}>Lunas</option>
                                <option value="terlambat" {{ $b->status == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                            </select>
                            <select name="metode" class="form-select form-select-sm mb-1">
                                <option value="transfer" {{ $b->metode == 'transfer' ? 'selected' : '' }}>Transfer</option>
                                <option value="tunai" {{ $b->metode == 'tunai' ? 'selected' : '' }}>Tunai</option>
                            </select>
                            <button class="btn btn-sm btn-success w-100">Simpan</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center">Belum ada data tagihan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $pembayarans->links() }}</div>
@endsection
