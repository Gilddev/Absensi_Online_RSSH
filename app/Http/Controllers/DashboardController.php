<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $hariini = date('Y-m-d');
        $bulanini = date('m') * 1;
        $tahunini = date('Y');
        $nik = Auth::guard('karyawan') -> user() -> nik;
        $presensihariini = DB::table('presensi') -> where('nik', $nik) -> where('tgl_presensi', $hariini) -> first();

        // menampilkan histori pada dashboard
        $historibulanini = DB::table('presensi') -> whereRaw('MONTH(tgl_presensi)="' . $bulanini . '"')
            -> select('presensi.*', 'pengajuan_izin.keterangan as keterangan_pengajuan', 'presensi_oncall.keterangan as keterangan_presensi', 'jam_kerja.*', 'file_surat_izin', 'karyawan_pengganti')
            -> leftJoin('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            -> leftJoin('pengajuan_izin', 'presensi.kode_izin', '=', 'pengajuan_izin.kode_izin')
            -> leftJoin('presensi_oncall', 'presensi.kode_oncall', '=', 'presensi_oncall.kode_oncall')
            -> where('presensi.nik', $nik)
            -> whereRaw('MONTH(tgl_presensi)="' . $bulanini . '"')
            -> whereRaw('YEAR(tgl_presensi)="' . $tahunini . '"') 
            -> orderBy('tgl_presensi', 'desc')
            -> get();

        // menampilkan rekap presensi pada dashboard presensi karyawan
        $rekappresensi = DB::table('presensi') 
            -> selectRaw('
            SUM(IF(status="h",1,0)) as jmlhadir,
            SUM(IF(status="i",1,0)) as jmlizin,
            SUM(IF(status="s",1,0)) as jmlsakit,
            SUM(IF(status="op",1,0)) as jmloncallpribadi,
            SUM(IF(status="ok",1,0)) as jmloncallkantor,
            SUM(IF(jam_in > jam_masuk, 1, 0)) as jmlterlambat
            ') 
            -> leftJoin('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            -> where('nik', $nik)
            -> whereRaw('MONTH(tgl_presensi)="' . $bulanini . '"')
            -> whereRaw('YEAR(tgl_presensi)="' . $tahunini . '"')
            -> first();
        
        // menampilkan leaderboard pada dashboard
        $leaderboard = DB::table('presensi')
            -> join('karyawan', 'presensi.nik', '=', 'karyawan.nik')
            -> where('tgl_presensi', $hariini)
            -> orderBy('jam_in')
            -> get();

        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        
        // menampilkan jumlah data izin atau sakit di dashboard
        // $rekapizin = DB::table('pengajuan_izin')
        //     -> selectRaw('SUM(IF(status = "i", 1, 0)) as jmlizin, SUM(IF(status = "s", 1, 0)) as jmlsakit')
        //     -> where('nik', $nik)
        //     -> whereRaw('MONTH(tgl_izin_dari)="' . $bulanini . '"')
        //     -> whereRaw('YEAR(tgl_izin_dari)="' . $tahunini . '"')
        //     -> where('status_approved', 1)
        //     -> first();

        return view('dashboard.dashboard', compact('presensihariini', 'historibulanini', 'namabulan', 'bulanini', 
        'tahunini', 'rekappresensi', 'leaderboard'));
    }

    public function dashboardadmin(){
        // menampilkan rekap presensi pada dashboard administrator
        $tahunini = date('Y');
        $bulanini = date('m') * 1;
        $hariini = date("Y-m'd");

        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        $rekappresensi = DB::table('presensi') 
            -> selectRaw('
            SUM(IF(status="h",1,0)) as jmlhadir,
            SUM(IF(status="i",1,0)) as jmlizin,
            SUM(IF(status="s",1,0)) as jmlsakit,
            SUM(IF(jam_in > jam_masuk, 1, 0)) as jmlterlambat
            ') 
            -> leftJoin('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            -> where('tgl_presensi', $hariini)
            -> first();

        $rekappresensibulan = DB::table('presensi')
            ->selectRaw('
                SUM(IF(status="h",1,0)) as jmlhadir,
                SUM(IF(status="i",1,0)) as jmlizin,
                SUM(IF(status="s",1,0)) as jmlsakit,
                SUM(IF(jam_in > jam_masuk, 1, 0)) as jmlterlambat
            ')
            ->leftJoin('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->whereYear('tgl_presensi', $tahunini)
            ->whereMonth('tgl_presensi', $bulanini)
            ->first();

        return view('dashboard.dashboardadmin', compact('rekappresensi', 'rekappresensibulan', 'bulanini', 'namabulan'));
    }

    public function dashboardkaru(){
        return view('dashboard.dashboardkaru');
    }
}
