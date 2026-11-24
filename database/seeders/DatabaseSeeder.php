<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Kamar;
use App\Models\Penghuni;

use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Membuat akun Admin
        User::create([
            'name' => 'Admin FauzanKos',
            'email' => 'admin@fauzankos.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'no_hp' => '081234567890',
        ]);

        // 2. Membuat kamar
        $kamar = Kamar::create([
            'nomor_kamar' => 'A-01',
            'tipe' => 'VIP AC',
            'harga_per_bulan' => 850000,
            'kapasitas' => 1,
            'fasilitas' => 'Kasur, Lemari, Meja Belajar, AC',
            'status' => 'terisi',
        ]);

        // 3. Membuat akun Penghuni
        $userPenghuni = User::create([
            'name' => 'Penghuni Demo',
            'email' => 'penghuni@fauzankos.com',
            'password' => Hash::make('penghuni123'),
            'role' => 'penghuni',
            'no_hp' => '089876543210',
        ]);

        // 4. Membuat data profil penghuni
        Penghuni::create([
            'user_id' => $userPenghuni->id,
            'kamar_id' => $kamar->id,
            'nik' => '3509123456780001',
            'alamat_asal' => 'Jember',
            'pekerjaan' => 'Mahasiswa',
            'nama_kontak_darurat' => 'Budi Santoso',
            'no_kontak_darurat' => '081222333444',
            'tanggal_masuk' => now(),
            'status' => 'aktif',
        ]);

        // 5. Membuat kamar kosong
        Kamar::create([
            'nomor_kamar' => 'A-02',
            'tipe' => 'Reguler Non-AC',
            'harga_per_bulan' => 550000,
            'kapasitas' => 1,
            'fasilitas' => 'Kasur, Lemari, Meja Belajar',
            'status' => 'kosong',
        ]);
    }
}
