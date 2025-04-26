<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresensiKuliahSubuh extends Model
{
    use HasFactory;

    protected $table = "presensi_kuliah_subuh";
    protected $primaryKey = "id";
    protected $fillable = [
        'nik',
        'tgl_kuliah_subuh',
        'kode_runagan',
        'foto_kegiatan',
        'created_at',
        'updated_at',
    ];

}
