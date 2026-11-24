<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kamar extends Model
{
    protected $fillable = [
        'nomor_kamar',
        'tipe',
        'harga_per_bulan',
        'kapasitas',
        'fasilitas',
        'status',
        'foto',
    ];

    public function penghunis()
    {
        return $this->hasMany(Penghuni::class);
    }

    public function pengaduans()
    {
        return $this->hasMany(Pengaduan::class);
    }
}
