<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PresensiKuliahSubuh;
use Illuminate\Http\Request;
use App\Models\RekapPresensi;
use Illuminate\Support\Facades\DB;

class RekapKehadiranController extends Controller
{
    public function showPersentase($nik)
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
    public function showKuliahSubuh($nik, $bulan, $tahun)
    {
        $tanggal_awal = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '-01';
        $tanggal_akhir = date("Y-m-t", strtotime($tanggal_awal));

        $kehadiran = DB::table('presensi_kuliah_subuh')
            ->where('nik', $nik)
            ->whereBetween('tgl_kuliah_subuh', [$tanggal_awal, $tanggal_akhir])
            ->count();

        if ($kehadiran > 0) {
            return response()->json([
                'status' => 'sudah_hadir',
                'pesan' => 'Sudah hadir pada bulan ini'
            ]);
        } else {
            return response()->json([
                'status' => 'belum_hadir',
                'pesan' => 'Belum hadir pada bulan ini'
            ]);
        }
    }
}
