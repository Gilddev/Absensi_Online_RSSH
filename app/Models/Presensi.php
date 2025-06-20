<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory;

    protected $table = "presensi";
    protected $primaryKey = "id";
    protected $fillable = [
        'id',
        'nik',
        'tgl_presensi',
        'jam_in',
        'jam_out',
        'foto_in',
        'foto_out',
        'lokasi_in',
        'lokasi_out',
        'kode_jam_kerja',
        'status',
        'kode_izin',
        'jenis_presensi',
    ];

    public function jamKerja(){
        return $this->belongsTo(Setjamkerja::class, 'kode_jam_kerja', 'kode_jam_kerja');
    }

    public function karyawan(){
        return $this->belongsTo(Karyawan::class, 'nik', 'nik');
    }
}
