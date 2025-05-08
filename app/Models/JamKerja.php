<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JamKerja extends Model
{
    use HasFactory;

    protected $table = "jam_kerja";
    protected $fillable = [
        'kode_jam_kerja',
        'nama_jam_kerja',
        'awal_jam_kerja',
        'jam_masuk',
        'akhir_jam_masuk',
        'jam_pulang',
        'lintashari',
    ];
}
