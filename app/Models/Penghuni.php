<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penghuni extends Model
{
    protected $fillable = [
        'user_id',
        'kamar_id',
        'nik',
        'alamat_asal',
        'pekerjaan',
        'nama_kontak_darurat',
        'no_kontak_darurat',
        'tanggal_masuk',
        'tanggal_keluar',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kamar()
    {
        return $this->belongsTo(Kamar::class);
    }

    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class);
    }

    public function pengaduans()
    {
        return $this->hasMany(Pengaduan::class);
    }
}
