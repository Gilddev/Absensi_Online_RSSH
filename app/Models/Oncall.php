<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Oncall extends Model
{
    use HasFactory;

    protected $table = 'presensi_oncall';

    protected $primaryKey = 'kode_oncall';

    protected $fillabel = [
        'nik',
        'tgl_pengajuan',
        'status',
        'kode_jam_kerja',
        'karyawan_pengganti',
        'keterangan'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'nik', 'nik');
    }

}
