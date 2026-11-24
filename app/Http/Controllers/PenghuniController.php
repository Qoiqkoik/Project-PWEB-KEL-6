<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kamar;
use App\Models\Penghuni;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PenghuniController extends Controller
{
    public function index()
    {
        $penghunis = Penghuni::with(['user', 'kamar'])->latest()->paginate(10);
        return view('admin.penghuni.index', compact('penghunis'));
    }

    public function create()
    {
        $kamars = Kamar::where('status', 'kosong')->orderBy('nomor_kamar')->get();
        return view('admin.penghuni.create', compact('kamars'));
    }

public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'no_hp' => 'required|string|max:20',
        'password' => 'required|min:6',
        'kamar_id' => 'nullable|exists:kamars,id',
        'nik' => 'required|string|unique:penghunis,nik',
        'alamat_asal' => 'required|string',
        'pekerjaan' => 'required|string|max:100',
        'nama_kontak_darurat' => 'required|string|max:255',
        'no_kontak_darurat' => 'required|string|max:20',
        'tanggal_masuk' => 'required|date',
    ]);

    DB::transaction(function () use ($validated) {

        // Membuat akun pengguna
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'no_hp' => $validated['no_hp'],
            'password' => Hash::make($validated['password']),
            'role' => 'penghuni',
        ]);

        // Membuat profil penghuni
        Penghuni::create([
            'user_id' => $user->id,
            'kamar_id' => $validated['kamar_id'] ?? null,
            'nik' => $validated['nik'],
            'alamat_asal' => $validated['alamat_asal'],
            'pekerjaan' => $validated['pekerjaan'],
            'nama_kontak_darurat' => $validated['nama_kontak_darurat'],
            'no_kontak_darurat' => $validated['no_kontak_darurat'],
            'tanggal_masuk' => $validated['tanggal_masuk'],
            'status' => 'aktif',
        ]);

        // Mengubah status kamar
        if (!empty($validated['kamar_id'])) {
            Kamar::where('id', $validated['kamar_id'])
                ->update(['status' => 'terisi']);
        }
    });

    return redirect()
        ->route('admin.penghuni.index')
        ->with('success', 'Data penghuni dan akun berhasil dibuat.');
}

    public function edit(Penghuni $penghuni)
    {
        $kamars = Kamar::where('status', 'kosong')
            ->orWhere('id', $penghuni->kamar_id)
            ->orderBy('nomor_kamar')
            ->get();
        return view('admin.penghuni.edit', compact('penghuni', 'kamars'));
    }

    
public function update(Request $request, Penghuni $penghuni)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $penghuni->user_id,
        'no_hp' => 'required|string|max:20',
        'kamar_id' => 'nullable|exists:kamars,id',
        'nik' => 'required|string|unique:penghunis,nik,' . $penghuni->id,
        'alamat_asal' => 'required|string',
        'pekerjaan' => 'required|string|max:100',
        'nama_kontak_darurat' => 'required|string|max:255',
        'no_kontak_darurat' => 'required|string|max:20',
        'status' => 'required|in:aktif,nonaktif',
    ]);

    DB::transaction(function () use ($validated, $penghuni) {

        $kamarLama = $penghuni->kamar_id;
        $kamarBaru = $validated['status'] === 'aktif'
            ? ($validated['kamar_id'] ?? null)
            : null;

        // Periksa kamar baru jika penghuni aktif
        if ($kamarBaru) {
            $kamar = Kamar::where('id', $kamarBaru)
                ->lockForUpdate()
                ->first();

            if (!$kamar) {
                throw ValidationException::withMessages([
                    'kamar_id' => 'Kamar tidak ditemukan.',
                ]);
            }

            // Cek apakah kamar ditempati penghuni aktif lain
            $sudahDitempati = Penghuni::where('kamar_id', $kamarBaru)
                ->where('status', 'aktif')
                ->where('id', '!=', $penghuni->id)
                ->exists();

            if ($kamarLama != $kamarBaru &&
                ($kamar->status !== 'kosong' || $sudahDitempati)) {
                throw ValidationException::withMessages([
                    'kamar_id' => 'Kamar tersebut sudah ditempati.',
                ]);
            }
        }

        // Perbarui akun pengguna
        $penghuni->user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'no_hp' => $validated['no_hp'],
        ]);

        // Kosongkan kamar lama jika pindah atau nonaktif
        if ($kamarLama && $kamarLama != $kamarBaru) {
            Kamar::where('id', $kamarLama)
                ->update(['status' => 'kosong']);
        }

        // Tandai kamar baru sebagai terisi
        if ($kamarBaru) {
            Kamar::where('id', $kamarBaru)
                ->update(['status' => 'terisi']);
        }

        // Perbarui profil penghuni
        $penghuni->update([
            'kamar_id' => $kamarBaru,
            'nik' => $validated['nik'],
            'alamat_asal' => $validated['alamat_asal'],
            'pekerjaan' => $validated['pekerjaan'],
            'nama_kontak_darurat' => $validated['nama_kontak_darurat'],
            'no_kontak_darurat' => $validated['no_kontak_darurat'],
            'status' => $validated['status'],
            'tanggal_keluar' => $validated['status'] === 'nonaktif'
                ? now()
                : null,
        ]);
    });

    return redirect()
        ->route('admin.penghuni.index')
        ->with('success', 'Data penghuni berhasil diperbarui.');
}

   
public function destroy(Penghuni $penghuni)
{
    // Periksa apakah penghuni memiliki riwayat pembayaran
    $adaPembayaran = $penghuni->pembayarans()->exists();

    // Periksa apakah penghuni memiliki riwayat pengaduan
    $adaPengaduan = $penghuni->pengaduans()->exists();

    if ($adaPembayaran || $adaPengaduan) {
        return redirect()
            ->route('admin.penghuni.index')
            ->with('error', 'Penghuni memiliki riwayat pembayaran atau pengaduan. Ubah status menjadi nonaktif agar riwayat tetap tersimpan.');
    }

    DB::transaction(function () use ($penghuni) {

        // Kosongkan kamar yang ditempati
        if ($penghuni->kamar_id) {
            Kamar::where('id', $penghuni->kamar_id)
                ->update(['status' => 'kosong']);
        }

        // Hapus akun pengguna beserta profil penghuninya
        $penghuni->user->delete();
    });

    return redirect()
        ->route('admin.penghuni.index')
        ->with('success', 'Data penghuni berhasil dihapus.');
}
}
