@extends('layouts.app')
@section('title', 'Tagihan Saya')
@section('content')
<h4 class="mb-3">Riwayat & Tagihan Sewa Kos Saya</h4>
<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-bordered table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Periode</th>
                    <th>Jumlah</th>
                    <th>Jatuh Tempo</th>
                    <th>Status</th>
                    <th>Upload Bukti Pembayaran</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pembayarans as $b)
                <tr>
                    <td>Bulan {{ $b->periode_bulan }} / {{ $b->periode_tahun }}</td>
                    <td>Rp {{ number_format($b->jumlah, 0, ',', '.') }}</td>
                    <td>{{ \Carbon\Carbon::parse($b->jatuh_tempo)->format('d/m/Y') }}</td>
                    <td>
                        @php $w = ['belum_bayar' => 'warning', 'lunas' => 'success', 'terlambat' => 'danger'][$b->status]; @endphp
                        <span class="badge bg-{{ $w }}">{{ strtoupper(str_replace('_', ' ', $b->status)) }}</span>
                    </td>
                    <td>
                        @if($b->status !== 'lunas')
                            <form action="{{ route('penghuni.pembayaran.upload', $b->id) }}" method="POST" enctype="multipart/form-data" class="d-flex gap-1">
                                @csrf
                                <select name="metode" class="form-select form-select-sm" style="width: 110px;">
                                    <option value="transfer">Transfer</option>
                                    <option value="tunai">Tunai</option>
                                </select>
                                <input type="file" name="bukti_bayar" class="form-control form-control-sm" accept="image/*" required>
                                <button type="submit" class="btn btn-sm btn-primary">Kirim</button>
                            </form>
                        @else
                            <span class="text-success fw-bold">Sudah Lunas ({{ ucfirst($b->metode) }})</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center">Belum ada tagihan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
