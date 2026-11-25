<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    protected function casts(): array
    {
        return [
            'jatuh_tempo' => 'date',
            'tanggal_bayar' => 'date',
        ];
    }

    public function penghuni(): BelongsTo
    {
        return $this->belongsTo(Penghuni::class);
    }
}
