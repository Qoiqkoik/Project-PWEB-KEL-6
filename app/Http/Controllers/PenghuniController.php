<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kamar;
use App\Models\Penghuni;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'no_hp' => $validated['no_hp'],
            'password' => Hash::make($validated['password']),
            'role' => 'penghuni',
        ]);

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

        if (!empty($validated['kamar_id'])) {
            Kamar::where('id', $validated['kamar_id'])->update(['status' => 'terisi']);
        }

        return redirect()->route('admin.penghuni.index')->with('success', 'Data penghuni dan akun berhasil dibuat.');
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

        $penghuni->user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'no_hp' => $validated['no_hp'],
        ]);

        // Kosongkan kamar lama jika pindah atau nonaktif
        if ($penghuni->kamar_id && ($penghuni->kamar_id != $validated['kamar_id'] || $validated['status'] === 'nonaktif')) {
            Kamar::where('id', $penghuni->kamar_id)->update(['status' => 'kosong']);
        }

        $kamarBaru = $validated['status'] === 'aktif' ? ($validated['kamar_id'] ?? null) : null;
        if ($kamarBaru) {
            Kamar::where('id', $kamarBaru)->update(['status' => 'terisi']);
        }

        $penghuni->update([
            'kamar_id' => $kamarBaru,
            'nik' => $validated['nik'],
            'alamat_asal' => $validated['alamat_asal'],
            'pekerjaan' => $validated['pekerjaan'],
            'nama_kontak_darurat' => $validated['nama_kontak_darurat'],
            'no_kontak_darurat' => $validated['no_kontak_darurat'],
            'status' => $validated['status'],
            'tanggal_keluar' => $validated['status'] === 'nonaktif' ? now() : null,
        ]);

        return redirect()->route('admin.penghuni.index')->with('success', 'Data penghuni berhasil diperbarui.');
    }

    public function destroy(Penghuni $penghuni)
    {
        if ($penghuni->kamar_id) {
            Kamar::where('id', $penghuni->kamar_id)->update(['status' => 'kosong']);
        }
        $penghuni->user->delete();
        return redirect()->route('admin.penghuni.index')->with('success', 'Data penghuni berhasil dihapus.');
    }
}
