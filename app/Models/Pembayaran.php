<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $fillable = [
        'penghuni_id',
        'periode_bulan',
        'periode_tahun',
        'jumlah',
        'jatuh_tempo',
        'tanggal_bayar',
        'metode',
        'bukti_bayar',
        'status',
        'keterangan',
    ];

    public function penghuni()
    {
        return $this->belongsTo(Penghuni::class);
    }
}
