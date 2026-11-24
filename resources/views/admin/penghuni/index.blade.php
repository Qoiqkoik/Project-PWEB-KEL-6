@extends('layouts.app')
@section('title', 'Data Penghuni')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Manajemen Data Penghuni</h4>
    <a href="{{ route('admin.penghuni.create') }}" class="btn btn-primary">+ Tambah Penghuni</a>
</div>
<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-bordered table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nama & Email</th>
                    <th>No. HP</th>
                    <th>Kamar</th>
                    <th>NIK & Pekerjaan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($penghunis as $p)
                <tr>
                    <td><strong>{{ $p->user->name }}</strong><br><small class="text-muted">{{ $p->user->email }}</small></td>
                    <td>{{ $p->user->no_hp }}</td>
                    <td>{{ $p->kamar->nomor_kamar ?? '-' }}</td>
                    <td>{{ $p->nik }}<br><small class="text-muted">{{ $p->pekerjaan }}</small></td>
                    <td><span class="badge bg-{{ $p->status == 'aktif' ? 'success' : 'secondary' }}">{{ ucfirst($p->status) }}</span></td>
                    <td>
                        <a href="{{ route('admin.penghuni.edit', $p->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('admin.penghuni.destroy', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus penghuni beserta akunnya?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center">Belum ada data penghuni.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $penghunis->links() }}</div>
@endsection
