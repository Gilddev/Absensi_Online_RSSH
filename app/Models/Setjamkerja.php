<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setjamkerja extends Model
{
    use HasFactory;
    protected $table = "konfigurasi_jam_kerja";
    protected $fillable = ['nik', 'tanggal_kerja', 'kode_jam_kerja'];
    public $timestamps = false;
}
