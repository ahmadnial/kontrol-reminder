<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaBroadcastLog extends Model
{
    protected $fillable = [
        'no_surat_kontrol',
        'nama_pasien',
        'nomor_wa',
        'layanan',
        'dokter',
        'tgl_rencana',
        'tgl_expired',
        'rujukan_expired',
        'message',
        'status',
        'response'
    ];
}
