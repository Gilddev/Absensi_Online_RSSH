<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;
use App\Models\PresensiKuliahSubuh;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PresensiKuliahSubuhController extends Controller
{
    public function createKuliahSubuh(){
        //untuk mengirim data rugangan ke view 
        $ruangan = DB::table('ruangan')->get();
        return view('presensi.create_kuliahsubuh', compact('ruangan'));
    }

    public function storeKuliahSubuh(Request $request){
        $request->validate([
            'foto_kegiatan' => 'required|image|mimes:jpeg,png,jpg'
            // 'foto_kegiatan' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);
    
        $nik = auth()->user()->nik;
        $tgl = date('Y-m-d');

        // Ambil data karyawan untuk dapatkan kode_ruangan dan nama_ruangan
        $karyawan = DB::table('karyawan')
            ->join('ruangan', 'karyawan.kode_ruangan', '=', 'ruangan.kode_ruangan')
            ->where('nik', $nik)
            ->select('karyawan.kode_ruangan', 'ruangan.nama_ruangan')
            ->first();

        $tanggal = date('Y-m-d');
        $waktu = date('H-i');
        $namaRuangan = str_replace(' ', '_', strtoupper($karyawan->nama_ruangan)); // hilangkan spasi dan huruf besar
    
        // Proses upload foto
        if ($request->hasFile('foto_kegiatan')) {
            $file = $request->file('foto_kegiatan');
            $filename = $namaRuangan . '-' . $nik . '-' . $tanggal . '-' . $waktu . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/upload/kuliahsubuh/', $filename);
        }
    
        // Simpan ke tabel presensi_kuliah_subuh
        DB::table('presensi_kuliah_subuh')->insert([
            'nik' => $nik,
            'tgl_kuliah_subuh' => $tgl,
            'kode_ruangan' => $karyawan->kode_ruangan,
            'foto_kegiatan' => $filename,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    
        return redirect()->route('dashboard')->with('success', 'Absensi kuliah subuh berhasil disimpan.');
    }

    public function rekapKuliahSubuh(){
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", 
        "Oktober", "November", "Desember"];
        $ruangan = DB::table('ruangan')->get();
        return view('presensi.rekapkuliahsubuh', compact('namabulan', 'ruangan'));
    }

    public function cetakrekapKuliahSubuh(Request $request){
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $kode_ruangan = $request->kode_ruangan;

        $dari =  $tahun . "-" . $bulan . "-01";
        $sampai = date("Y-m-t", strtotime($dari));

        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        // Buat list tanggal dalam bulan
        // $rangetanggal = [];
        // $select_date = "";
        // $field_date = "";
        $select_date = "";
        $field_date = "";

        $i = 1;
        while (strtotime($dari) <= strtotime($sampai)) {
            $rangetanggal[] = $dari;
            $select_date .= "MAX(IF(tgl_kuliah_subuh = '$dari', CONCAT(
                IFNULL(tgl_kuliah_subuh, 'NA'), '|',
                IFNULL(kode_ruangan, 'NA'), '|'
            ), NULL)) as tgl_" . $i . ",";

            $field_date .= "tgl_" . $i . ",";
            $i++;
            $dari = date("Y-m-d", strtotime("+1 day", strtotime($dari)));
        }

        $jmlhari = count($rangetanggal);
        $lastrange = $jmlhari - 1;
        $sampai = $rangetanggal[$lastrange];

        if ($jmlhari == 30) {
            array_push($rangetanggal, NULL);
        } elseif ($jmlhari == 29) {
            array_push($rangetanggal, NULL, NULL);
        } elseif ($jmlhari == 28) {
            array_push($rangetanggal, NULL, NULL, NULL);
        }

        $query = Karyawan::query();
        $query->selectRaw("$field_date karyawan.nik, nama_lengkap, jabatan, kode_ruangan");

        $query->leftJoin(
            DB::raw("(
                SELECT 
                    $select_date 
                    presensi_kuliah_subuh.nik 
                FROM presensi_kuliah_subuh
                WHERE tgl_kuliah_subuh BETWEEN '$rangetanggal[0]' AND '$sampai'
                GROUP BY nik
            ) presensi_kuliah_subuh"),
            function($join){
                $join->on('karyawan.nik', '=', 'presensi_kuliah_subuh.nik');
            }
        );

        if (!empty($kode_ruangan)){
            $query->where('karyawan.kode_ruangan', $kode_ruangan);
        }

        $query->orderBy('karyawan.kode_ruangan');
        $rekap = $query->get();
        // dd($rekap);
        // dd($query->toSql(), $query->getBindings());

        return view('presensi.cetakrekapkuliahsubuh', compact('bulan', 'tahun', 'kode_ruangan', 'rekap', 'namabulan', 'rangetanggal', 'jmlhari'));
    }
}
