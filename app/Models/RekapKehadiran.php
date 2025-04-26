<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekapKehadiran extends Model
{
    use HasFactory;

    protected $table = 'rekap_kehadiran';
    protected $primaryKey = 'id';
}
