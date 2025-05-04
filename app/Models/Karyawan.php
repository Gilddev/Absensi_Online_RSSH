<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Sanctum\HasApiTokens;

class Karyawan extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = "karyawan";
    protected $primaryKey = "nik";
    protected $fillable = [
        'nik',
        'nama_lengkap',
        'jabatan',
        'no_hp',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'kode_ruangan', 'kode_ruangan');
    }

    public function rekapPresensi(){
        return $this->hasMany(RekapPresensi::class, 'nik', 'nik');
    }
}
