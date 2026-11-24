<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaduan extends Model
{
    protected $fillable = [
        'penghuni_id',
        'kamar_id',
        'judul',
        'kategori',
        'deskripsi',
        'foto',
        'status',
        'tanggapan',
        'ditangani_oleh',
        'tanggal_selesai',
    ];

    protected $casts = [
        'tanggal_selesai' => 'datetime',
    ];

    public function penghuni()
    {
        return $this->belongsTo(Penghuni::class);
    }

    public function kamar()
    {
        return $this->belongsTo(Kamar::class);
    }

    public function adminPenangan()
    {
        return $this->belongsTo(User::class, 'ditangani_oleh');
    }
}
