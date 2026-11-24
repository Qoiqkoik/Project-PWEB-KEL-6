<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Penghuni;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    // ================= ADMIN =================
    public function indexAdmin(Request $request)
    {
        $query = Pembayaran::with(['penghuni.user', 'penghuni.kamar']);
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $pembayarans = $query->latest()->paginate(10);
        return view('admin.pembayaran.index', compact('pembayarans'));
    }

    public function create()
    {
        $penghunis = Penghuni::with(['user', 'kamar'])->where('status', 'aktif')->get();
        return view('admin.pembayaran.create', compact('penghunis'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'penghuni_id' => 'required|exists:penghunis,id',
            'periode_bulan' => 'required|integer|min:1|max:12',
            'periode_tahun' => 'required|integer|min:2020',
            'jumlah' => 'required|integer|min:0',
            'jatuh_tempo' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        Pembayaran::create([
            ...$validated,
            'status' => 'belum_bayar',
        ]);

        return redirect()->route('admin.pembayaran.index')->with('success', 'Tagihan bulanan berhasil dibuat.');
    }

    public function konfirmasi(Request $request, Pembayaran $pembayaran)
    {
        $validated = $request->validate([
            'status' => 'required|in:belum_bayar,lunas,terlambat',
            'metode' => 'nullable|in:tunai,transfer',
            'keterangan' => 'nullable|string',
        ]);

        $pembayaran->update([
            'status' => $validated['status'],
            'metode' => $validated['metode'] ?? $pembayaran->metode ?? 'tunai',
            'tanggal_bayar' => $validated['status'] === 'lunas' ? ($pembayaran->tanggal_bayar ?? now()) : null,
            'keterangan' => $validated['keterangan'],
        ]);

        return back()->with('success', 'Status pembayaran berhasil diperbarui.');
    }

    public function laporan()
    {
        $lunasList = Pembayaran::with(['penghuni.user', 'penghuni.kamar'])
            ->where('status', 'lunas')
            ->latest('tanggal_bayar')
            ->get();

        $totalPemasukan = $lunasList->sum('jumlah');
        $totalTunggakan = Pembayaran::whereIn('status', ['belum_bayar', 'terlambat'])->sum('jumlah');

        return view('admin.pembayaran.laporan', compact('lunasList', 'totalPemasukan', 'totalTunggakan'));
    }

    // ================= PENGHUNI =================
    public function indexPenghuni()
    {
        $penghuni = auth()->user()->penghuni;
        $pembayarans = $penghuni
            ? Pembayaran::where('penghuni_id', $penghuni->id)->latest()->paginate(10)
            : collect();

        return view('penghuni.pembayaran.index', compact('pembayarans', 'penghuni'));
    }

    public function uploadBukti(Request $request, Pembayaran $pembayaran)
    {
        $validated = $request->validate([
            'metode' => 'required|in:tunai,transfer',
            'bukti_bayar' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $path = $request->file('bukti_bayar')->store('bukti_bayar', 'public');

        $pembayaran->update([
            'metode' => $validated['metode'],
            'bukti_bayar' => $path,
            'tanggal_bayar' => now(),
            'keterangan' => 'Bukti pembayaran telah diunggah penghuni, menunggu verifikasi admin.',
        ]);

        return back()->with('success', 'Bukti pembayaran berhasil diunggah. Menunggu konfirmasi admin.');
    }
}
