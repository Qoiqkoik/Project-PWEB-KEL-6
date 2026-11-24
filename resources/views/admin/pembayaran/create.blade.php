@extends('layouts.app')
@section('title', 'Buat Tagihan')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header fw-bold">Buat Tagihan Sewa Kos</div>
            <div class="card-body">
                <form action="{{ route('admin.pembayaran.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Penghuni</label>
                        <select name="penghuni_id" class="form-select" required>
                            @foreach($penghunis as $p)
                                <option value="{{ $p->id }}">{{ $p->user->name }} (Kamar {{ $p->kamar->nomor_kamar ?? '-' }} - Rp {{ number_format($p->kamar->harga_per_bulan ?? 0,0,',','.') }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6"><label class="form-label">Bulan (1-12)</label><input type="number" name="periode_bulan" value="{{ date('n') }}" min="1" max="12" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">Tahun</label><input type="number" name="periode_tahun" value="{{ date('Y') }}" class="form-control" required></div>
                    </div>
                    <div class="mb-3"><label class="form-label">Jumlah Tagihan (Rp)</label><input type="number" name="jumlah" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Jatuh Tempo</label><input type="date" name="jatuh_tempo" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Keterangan</label><input type="text" name="keterangan" class="form-control"></div>
                    <button type="submit" class="btn btn-primary">Terbitkan Tagihan</button>
                    <a href="{{ route('admin.pembayaran.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
