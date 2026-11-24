<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Kamar;
use App\Models\Penghuni;
use App\Models\Pengaduan;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Admin
        $admin = User::create([
            'name' => 'Admin FauzanKos',
            'email' => 'admin@fauzankos.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'no_hp' => '081234567890',
        ]);

        // 2. Data Kamar Awal
        $kamar1 = Kamar::create([
            'nomor_kamar' => 'A-01',
            'tipe' => 'VIP AC',
            'harga_per_bulan' => 850000,
            'kapasitas' => 1,
            'fasilitas' => 'Kasur, Lemari, Meja Belajar, AC, Kamar Mandi Dalam',
            'status' => 'terisi',
        ]);

        // 3. Akun Penghuni
        $userPenghuni = User::create([
            'name' => 'Firdhan Andhika',
            'email' => 'firdhan@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'penghuni',
            'no_hp' => '089876543210',
        ]);

        $penghuni = Penghuni::create([
            'user_id' => $userPenghuni->id,
            'kamar_id' => $kamar1->id,
            'nik' => '3509123456780001',
            'alamat_asal' => 'Jl. Kalimantan No. 10, Jember',
            'pekerjaan' => 'Mahasiswa',
            'nama_kontak_darurat' => 'Budi Santoso',
            'no_kontak_darurat' => '081222333444',
            'tanggal_masuk' => now()->subMonths(2),
            'status' => 'aktif',
        ]);

        // 4. Contoh Data Pengaduan Awal
        Pengaduan::create([
            'penghuni_id' => $penghuni->id,
            'kamar_id' => $kamar1->id,
            'judul' => 'Kran Air Kamar Mandi Bocor',
            'kategori' => 'air',
            'deskripsi' => 'Kran air di wastafel kamar mandi terus menetes meskipun sudah ditutup rapat.',
            'status' => 'diajukan',
        ]);

        // 5. Tambah Kamar Kosong & Data Pembayaran Awal
        \App\Models\Kamar::create([
            'nomor_kamar' => 'A-02',
            'tipe' => 'Reguler Non-AC',
            'harga_per_bulan' => 550000,
            'kapasitas' => 1,
            'fasilitas' => 'Kasur, Lemari, Meja Belajar, Kamar Mandi Luar',
            'status' => 'kosong',
        ]);

        \App\Models\Pembayaran::create([
         'penghuni_id' => $penghuni->id,
         'periode_bulan' => now()->month,
         'periode_tahun' => now()->year,
         'jumlah' => 850000,
         'jatuh_tempo' => now()->addDays(5),
         'status' => 'belum_bayar',
         'keterangan' => 'Tagihan kos bulan berjalan',
        ]);
    }
}
