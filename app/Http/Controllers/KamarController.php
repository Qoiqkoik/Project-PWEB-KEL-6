<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use Illuminate\Http\Request;

class KamarController extends Controller
{
    public function index(Request $request)
    {
        $query = Kamar::query();
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $kamars = $query->orderBy('nomor_kamar')->paginate(10);
        return view('admin.kamar.index', compact('kamars'));
    }

    public function create()
    {
        return view('admin.kamar.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_kamar' => 'required|string|unique:kamars,nomor_kamar',
            'tipe' => 'required|string|max:100',
            'harga_per_bulan' => 'required|integer|min:0',
            'kapasitas' => 'required|integer|min:1',
            'fasilitas' => 'nullable|string',
            'status' => 'required|in:kosong,terisi,perbaikan',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('kamar', 'public');
        }

        Kamar::create($validated);
        return redirect()->route('admin.kamar.index')->with('success', 'Data kamar berhasil ditambahkan.');
    }

    public function edit(Kamar $kamar)
    {
        return view('admin.kamar.edit', compact('kamar'));
    }

    public function update(Request $request, Kamar $kamar)
    {
        $validated = $request->validate([
            'nomor_kamar' => 'required|string|unique:kamars,nomor_kamar,' . $kamar->id,
            'tipe' => 'required|string|max:100',
            'harga_per_bulan' => 'required|integer|min:0',
            'kapasitas' => 'required|integer|min:1',
            'fasilitas' => 'nullable|string',
            'status' => 'required|in:kosong,terisi,perbaikan',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('kamar', 'public');
        }

        $kamar->update($validated);
        return redirect()->route('admin.kamar.index')->with('success', 'Data kamar berhasil diperbarui.');
    }

    public function destroy(Kamar $kamar)
    {
        $kamar->delete();
        return redirect()->route('admin.kamar.index')->with('success', 'Data kamar berhasil dihapus.');
    }
}
