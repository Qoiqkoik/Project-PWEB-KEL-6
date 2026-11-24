<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengaduanController extends Controller
{
    // ================= BAGIAN PENGHUNI =================
    public function indexPenghuni()
    {
        $penghuni = auth()->user()->penghuni;
        $pengaduans = Pengaduan::with('kamar')
            ->where('penghuni_id', $penghuni->id)
            ->latest()
            ->paginate(10);

        return view('penghuni.pengaduan.index', compact('pengaduans', 'penghuni'));
    }

    public function create()
    {
        $penghuni = auth()->user()->penghuni()->with('kamar')->first();
        if (!$penghuni || !$penghuni->kamar_id) {
            return back()->with('error', 'Anda belum memiliki kamar aktif untuk mengajukan pengaduan.');
        }
        return view('penghuni.pengaduan.create', compact('penghuni'));
    }

    public function store(Request $request)
    {
        $penghuni = auth()->user()->penghuni;
        if (!$penghuni || !$penghuni->kamar_id) {
            return back()->with('error', 'Anda belum menempati kamar.');
        }

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'required|in:listrik,air,wi-fi,kamar mandi,lainnya',
            'deskripsi' => 'required|string',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('pengaduan', 'public');
        }

        Pengaduan::create([
            'penghuni_id' => $penghuni->id,
            'kamar_id' => $penghuni->kamar_id,
            'judul' => $validated['judul'],
            'kategori' => $validated['kategori'],
            'deskripsi' => $validated['deskripsi'],
            'foto' => $fotoPath,
            'status' => 'diajukan',
        ]);

        return redirect()->route('penghuni.pengaduan.index')->with('success', 'Pengaduan berhasil diajukan.');
    }

    // ================= BAGIAN ADMIN =================
    public function indexAdmin(Request $request)
    {
        $query = Pengaduan::with(['penghuni.user', 'kamar', 'adminPenangan']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $pengaduans = $query->latest()->paginate(10);
        return view('admin.pengaduan.index', compact('pengaduans'));
    }

    public function updateStatus(Request $request, Pengaduan $pengaduan)
    {
        $validated = $request->validate([
            'status' => 'required|in:diajukan,diproses,selesai,ditolak',
            'tanggapan' => 'nullable|string',
        ]);

        $pengaduan->update([
            'status' => $validated['status'],
            'tanggapan' => $validated['tanggapan'],
            'ditangani_oleh' => auth()->id(),
            'tanggal_selesai' => in_array($validated['status'], ['selesai', 'ditolak']) ? now() : null,
        ]);

        return back()->with('success', 'Status dan tanggapan pengaduan berhasil diperbarui.');
    }

    // ================= LAPORAN PENGADUAN =================
    public function laporan()
    {
        $perStatus = Pengaduan::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $perKategori = Pengaduan::select('kategori', DB::raw('count(*) as total'))
            ->groupBy('kategori')
            ->pluck('total', 'kategori');

        $selesaiList = Pengaduan::with(['penghuni.user', 'kamar', 'adminPenangan'])
            ->whereNotNull('tanggal_selesai')
            ->latest('tanggal_selesai')
            ->get();

        // Hitung rata-rata waktu penyelesaian (dalam jam)
        $rataRataJam = $selesaiList->avg(function ($item) {
            return $item->created_at->diffInHours($item->tanggal_selesai);
        });

        return view('admin.pengaduan.laporan', compact('perStatus', 'perKategori', 'selesaiList', 'rataRataJam'));
    }
}
