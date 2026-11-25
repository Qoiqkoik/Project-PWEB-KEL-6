@extends('layouts.app')
@section('title', 'Tagihan Saya')
@section('content')
<h4 class="mb-3">Tagihan & Riwayat Pembayaran Saya</h4>
<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-bordered table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Periode Tagihan</th>
                    <th>Jumlah</th>
                    <th>Jatuh Tempo</th>
                    <th>Status</th>
                    <th>Metode / Tanggal Bayar</th>
                    <th>Bukti & Pencatatan Pembayaran</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pembayarans as $b)
                @php($badgeColor = ['belum_bayar' => 'warning', 'lunas' => 'success', 'terlambat' => 'danger'][$b->status])
                <tr>
                    <td>{{ $b->periode_bulan }}/{{ $b->periode_tahun }}</td>
                    <td>Rp {{ number_format($b->jumlah, 0, ',', '.') }}</td>
                    <td>
                        {{ $b->jatuh_tempo->format('d/m/Y') }}
                        @if($b->status === 'terlambat')
                            <br><small class="text-danger fw-semibold">Tagihan melewati jatuh tempo</small>
                        @endif
                    </td>
                    <td><span class="badge bg-{{ $badgeColor }}">{{ strtoupper(str_replace('_', ' ', $b->status)) }}</span></td>
                    <td>
                        {{ $b->metode ? ucfirst($b->metode) : '-' }}<br>
                        <small>{{ $b->tanggal_bayar?->format('d/m/Y') ?? 'Belum tercatat' }}</small>
                    </td>
                    <td style="min-width: 290px;">
                        @if($b->bukti_bayar)
                            <a href="{{ route('penghuni.pembayaran.bukti', $b) }}" class="btn btn-sm btn-outline-info mb-2">Unduh Bukti</a>
                        @endif

                        @if($b->status !== 'lunas')
                            <form action="{{ route('penghuni.pembayaran.upload', $b) }}" method="POST" enctype="multipart/form-data" class="payment-form">
                                @csrf
                                <label class="form-label small mb-1">Metode pembayaran</label>
                                <select name="metode" class="form-select form-select-sm mb-2 payment-method" required>
                                    <option value="transfer" @selected($b->metode === 'transfer')>Transfer</option>
                                    <option value="tunai" @selected($b->metode === 'tunai')>Tunai</option>
                                </select>
                                <label class="form-label small mb-1">Tanggal pembayaran</label>
                                <input type="date" name="tanggal_bayar" value="{{ old('tanggal_bayar', $b->tanggal_bayar?->toDateString()) }}" max="{{ today()->toDateString() }}" class="form-control form-control-sm mb-2" required>
                                <label class="form-label small mb-1">Bukti transfer (wajib untuk transfer)</label>
                                <input type="file" name="bukti_bayar" class="form-control form-control-sm payment-proof" accept=".jpg,.jpeg,.png,.webp">
                                <small class="text-muted d-block mt-1">Gambar JPG, PNG, atau WEBP, maksimal 2 MB. Menunggu verifikasi admin setelah dikirim.</small>
                                @error('metode') <div class="text-danger small">{{ $message }}</div> @enderror
                                @error('tanggal_bayar') <div class="text-danger small">{{ $message }}</div> @enderror
                                @error('bukti_bayar') <div class="text-danger small">{{ $message }}</div> @enderror
                                <button type="submit" class="btn btn-sm btn-primary mt-2">Kirim Pembayaran</button>
                            </form>
                        @else
                            <span class="text-success fw-bold">Pembayaran terverifikasi lunas.</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center">Belum ada tagihan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $pembayarans->links() }}</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.payment-form').forEach((form) => {
        const method = form.querySelector('.payment-method');
        const proof = form.querySelector('.payment-proof');
        const updateProofRequirement = () => {
            proof.required = method.value === 'transfer';
        };

        method.addEventListener('change', updateProofRequirement);
        updateProofRequirement();
    });
</script>
@endpush
