<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use App\Models\Penghuni;
use App\Models\Pembayaran;
use App\Models\Pengaduan;

class DashboardController extends Controller
{
    public function admin()
    {
        $totalKamar = Kamar::count();
        $kamarKosong = Kamar::where('status', 'kosong')->count();
        $kamarTerisi = Kamar::where('status', 'terisi')->count();
        $totalPenghuniAktif = Penghuni::where('status', 'aktif')->count();
        $tagihanBelumLunas = Pembayaran::whereIn('status', ['belum_bayar', 'terlambat'])->count();
        $pendapatanBulanIni = Pembayaran::where('status', 'lunas')
            ->where('periode_bulan', now()->month)
            ->where('periode_tahun', now()->year)
            ->sum('jumlah');
        $pengaduanAktif = Pengaduan::whereIn('status', ['diajukan', 'diproses'])->count();

        return view('admin.dashboard', compact(
            'totalKamar', 'kamarKosong', 'kamarTerisi',
            'totalPenghuniAktif', 'tagihanBelumLunas', 'pendapatanBulanIni', 'pengaduanAktif'
        ));
    }

    public function penghuni()
    {
        $penghuni = auth()->user()->penghuni()->with(['kamar', 'pembayarans', 'pengaduans'])->first();
        $tagihanAktif = $penghuni ? $penghuni->pembayarans()->where('status', '!=', 'lunas')->get() : collect();
        $pengaduanTerbaru = $penghuni ? $penghuni->pengaduans()->latest()->take(3)->get() : collect();

        return view('penghuni.dashboard', compact('penghuni', 'tagihanAktif', 'pengaduanTerbaru'));
    }
}
