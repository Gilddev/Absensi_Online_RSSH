<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RekapPresensi;

class RekapKehadiranController extends Controller
{
    public function show($nik)
    {
        $data = RekapPresensi::where('nik', $nik)
            ->select('nik', 'bulan', 'tahun', 'persentase_kehadiran')
            ->get();

        if ($data->isEmpty()) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json([
            'nik' => $nik,
            'rekap' => $data
        ]);
    }
}
