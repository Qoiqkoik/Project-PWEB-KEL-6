@extends('layouts.app')
@section('title', 'Data Kamar')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Manajemen Data Kamar</h4>
    <a href="{{ route('admin.kamar.create') }}" class="btn btn-primary">+ Tambah Kamar</a>
</div>
<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-bordered table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>No. Kamar</th>
                    <th>Tipe</th>
                    <th>Harga / Bulan</th>
                    <th>Kapasitas</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kamars as $k)
                <tr>
                    <td><strong>{{ $k->nomor_kamar }}</strong></td>
                    <td>{{ $k->tipe }}</td>
                    <td>Rp {{ number_format($k->harga_per_bulan, 0, ',', '.') }}</td>
                    <td>{{ $k->kapasitas }} Orang</td>
                    <td>
                        @php $w = ['kosong' => 'success', 'terisi' => 'primary', 'perbaikan' => 'danger'][$k->status]; @endphp
                        <span class="badge bg-{{ $w }}">{{ ucfirst($k->status) }}</span>
                    </td>
                    <td>
                        <a href="{{ route('admin.kamar.edit', $k->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('admin.kamar.destroy', $k->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus kamar ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center">Belum ada data kamar.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $kamars->links() }}</div>
@endsection
