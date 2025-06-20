<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RekapKehadiranController extends Controller
{
    public function sync()
    {
        Artisan::call('hitung:persentase-kehadiran');

        return redirect()->back()->with('success', 'Data kehadiran berhasil disinkronisasi.');
    }

    public function monitoringPersentase(Request $request){
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", 
        "Oktober", "November", "Desember"];
        
        // Ambil data bulan dan tahun dari request
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

        $bulanini = request()->input('bulan') ?? Carbon::now()->month;
        $tahunini = request()->input('tahun') ??Carbon::now()->year;

        // Query dasar join ke tabel karyawan
        $query = DB::table('rekap_presensi')
                    ->join('karyawan', 'rekap_presensi.nik', '=', 'karyawan.nik')
                    ->select('rekap_presensi.*', 'karyawan.nama_lengkap', 'karyawan.kode_ruangan')
                    ->where('rekap_presensi.bulan', $bulanini)
                    ->where('rekap_presensi.tahun', $tahunini)
                    ->orderBy('karyawan.kode_ruangan');

        // Jika user memilih bulan dan tahun, lakukan filter
        if (!empty($bulan)) {
            $query->where('rekap_presensi.bulan', $bulan);
        }

        if (!empty($tahun)) {
            $query->where('rekap_presensi.tahun', $tahun);
        }

        // Eksekusi query
        $rekapPresensi = $query->get();

        // Untuk dropdown bulan dan tahun di view tetap tersedia
        $listBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $tahunmulai = 2024;
        $tahunsekarang = date('Y');
        $listTahun = range($tahunmulai, $tahunsekarang);

        // Kirim ke view
        return view('presensi.monitoringpersentase', compact('rekapPresensi', 'listBulan', 'listTahun', 'bulan', 'tahun', 'namabulan'));
    }

    public function rekapPersentase(){
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", 
        "Oktober", "November", "Desember"];
        $ruangan = DB::table('ruangan')->get();
        return view('presensi.rekappersentase', compact('namabulan', 'ruangan'));
    }

    public function cetakRekapPersentase(Request $request){
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", 
        "Oktober", "November", "Desember"];
        
        // Ambil data bulan, tahun dan ruangan dari request
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $kode_ruangan = $request->kode_ruangan;

        $bulanini = request()->input('bulan') ?? Carbon::now()->month;
        $tahunini = request()->input('tahun') ??Carbon::now()->year;

        // Query dasar join ke tabel karyawan
        $query = DB::table('rekap_presensi')
                    ->join('karyawan', 'rekap_presensi.nik', '=', 'karyawan.nik')
                    ->select('rekap_presensi.*', 'karyawan.nama_lengkap', 'karyawan.kode_ruangan')
                    ->where('rekap_presensi.bulan', $bulanini)
                    ->where('rekap_presensi.tahun', $tahunini)
                    ->orderBy('karyawan.kode_ruangan');

        // Jika user memilih bulan dan tahun, lakukan filter
        if (!empty($bulan)) {
            $query->where('rekap_presensi.bulan', $bulan);
        }

        if (!empty($tahun)) {
            $query->where('rekap_presensi.tahun', $tahun);
        }

        if (!empty($kode_ruangan)) {
            $query->where('karyawan.kode_ruangan', $kode_ruangan);
        }

        // Eksekusi query
        $rekappersentase = $query->get();

        // Kirim ke view
        return view('presensi.cetakrekappersentase', compact('rekappersentase','bulan', 'tahun', 'namabulan'));
    }
}
