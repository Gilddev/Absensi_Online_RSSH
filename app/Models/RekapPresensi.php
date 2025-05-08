<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekapPresensi extends Model
{
    use HasFactory;

    protected $table = 'rekap_presensi';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nik',
        'bulan',
        'tahun',
        'jumlah_hari_kerja',
        'jumlah_hadir',
        'jumlah_terlambat',
        'persentase_kehadiran',
        'persentase_keterlambatan',
    ];
}
