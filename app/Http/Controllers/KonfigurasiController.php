<?php

namespace App\Http\Controllers;

use App\Models\Setjamkerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use PhpParser\Node\Stmt\TryCatch;
use Carbon\Carbon;

use Illuminate\Support\Facades\Auth;
use App\Helper\LogHelper;

class KonfigurasiController extends Controller
{
    public function lokasikantor(){
        $lok_kantor = DB::table('konfigurasi_lokasi') -> where('id', 1) -> first();
        return view('konfigurasi.lokasikantor', compact('lok_kantor'));
    }

    public function updatelokasikantor(Request $request){
        $lokasi_kantor = $request -> lokasi_kantor;
        $radius = $request -> radius;

        $update = DB::table('konfigurasi_lokasi') -> where('id', 1) -> update([
            'lokasi_kantor' => $lokasi_kantor,
            'radius' => $radius
        ]);

        if($update){
            return Redirect::back() -> with(['success' => 'Data Berhasil Diupdate']);
        }else{
            return Redirect::back() -> with(['warning' => 'Data Gagal Diupdate']);
        }
    }

    //-------------------------------------------------------------------------------------------------------------------

    public function jamkerja(){
        $jam_kerja = DB::table('jam_kerja')->orderBy('kode_jam_kerja')->get();
        return view('konfigurasi.jamkerja', compact('jam_kerja'));
    }

    public function storejamkerja(Request $request){
            $kode_jam_kerja = $request->kode_jam_kerja;
            $nama_jam_kerja = $request->nama_jam_kerja;
            $awal_jam_masuk = $request->awal_jam_masuk;
            $jam_masuk = $request->jam_masuk;
            $akhir_jam_masuk = $request->akhir_jam_masuk;
            $jam_pulang = $request->jam_pulang;
            $lintashari = $request->lintashari;
            
            $data = [
                'kode_jam_kerja' => $kode_jam_kerja,
                'nama_jam_kerja' => $nama_jam_kerja,
                'awal_jam_masuk' => $awal_jam_masuk,
                'jam_masuk' => $jam_masuk,
                'akhir_jam_masuk' => $akhir_jam_masuk,
                'jam_pulang' => $jam_pulang,
                'lintashari' => $lintashari
            ];
            try {
                DB::table('jam_kerja')->insert($data);
                return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
            } catch  (\Exception $e) {
                dd($e);
                return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);
            }
    }

    // public function storejamkerja(Request $request)
    // {
    //     $nik = $request->nik;
    //     $tanggal = $request->tanggal;
    //     $kode_jam_kerja = $request->kode_jam_kerja;

    //     // Pastikan input tidak kosong
    //     if (!$nik || empty($tanggal) || empty($kode_jam_kerja)) {
    //         return Redirect::back()->with(['warning' => 'Harap isi semua data!']);
    //     }

    //     // Ambil bulan dan tahun saat ini
    //     $bulan = date('m');
    //     $tahun = date('Y');

    //     // Siapkan data untuk insert batch
    //     $data = [];
    //     foreach ($tanggal as $key => $tgl) {
    //         if (!empty($kode_jam_kerja[$key])) { // Hanya simpan jika ada kode jam kerja
    //             $tanggal_kerja = Carbon::createFromFormat('Y-m-d', "$tahun-$bulan-$tgl")->format('Y-m-d');
    //             $data[] = [
    //                 'nik' => $nik,
    //                 'tanggal_kerja' => $tanggal_kerja,
    //                 'kode_jam_kerja' => $kode_jam_kerja[$key]
    //             ];
    //         }
    //     }

    //     if (!empty($data)) {
    //         try {
    //             DB::table('konfigurasi_jam_kerja')->insert($data);
    //             return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
    //         } catch (\Exception $e) {
    //             return Redirect::back()->with(['warning' => 'Data Gagal Disimpan!']);
    //         }
    //     } else {
    //         return Redirect::back()->with(['warning' => 'Tidak ada data yang disimpan!']);
    //     }
    // }

    public function editjamkerja(Request $request){
        $kode_jam_kerja = $request->kode_jam_kerja;
        $jam_kerja = DB::table('jam_kerja')->where('kode_jam_kerja', $kode_jam_kerja)->first();
        return view('konfigurasi.editjamkerja', compact('jam_kerja'));
    }

    public function updatejamkerja(Request $request){
        $kode_jam_kerja = $request->kode_jam_kerja;
        $nama_jam_kerja = $request->nama_jam_kerja;
        $awal_jam_masuk = $request->awal_jam_masuk;
        $jam_masuk = $request->jam_masuk;
        $akhir_jam_masuk = $request->akhir_jam_masuk;
        $jam_pulang = $request->jam_pulang;
        $lintashari = $request->lintashari;
        
        $data = [
            'nama_jam_kerja' => $nama_jam_kerja,
            'awal_jam_masuk' => $awal_jam_masuk,
            'jam_masuk' => $jam_masuk,
            'akhir_jam_masuk' => $akhir_jam_masuk,
            'jam_pulang' => $jam_pulang,
            'lintashari' => $lintashari
        ];
        try {
            DB::table('jam_kerja')->where('kode_jam_kerja', $kode_jam_kerja)->update($data);
            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } catch  (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Diupdate']);
        }
    }

    public function deletejamkerja($kode_jam_kerja){
        $hapus = DB::table('jam_kerja')->where('kode_jam_kerja', $kode_jam_kerja)->delete();
        if  ($hapus){
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        }else{
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);
        }
    }

    //-------------------------------------------------------------------------------------------------------------------

    public function setjamkerja($nik){
        $karyawan = DB::table('karyawan')
            -> join('ruangan', 'karyawan.kode_ruangan', '=', 'ruangan.kode_ruangan')
            ->where('nik', $nik)
            ->first();
        $jam_kerja = DB::table('jam_kerja')->orderBy('kode_jam_kerja')->get();
        $cek_jam_kerja = DB::table('konfigurasi_jam_kerja')->where('nik', $nik)->count();
        // dd($cek_jam_kerja);
        if($cek_jam_kerja > 0){
            $set_jam_kerja = DB::table('konfigurasi_jam_kerja')->where('nik', $nik)->get();
            return view('konfigurasi.editsetjamkerja', compact('karyawan', 'jam_kerja', 'set_jam_kerja'));
        }else{
            return view('konfigurasi.setjamkerja', compact('karyawan', 'jam_kerja'));
        }
    }

    public function storesetjamkerja(Request $request){
        $nik = $request->nik;
        $tanggal = $request->tanggal;
        $kode_jam_kerja = $request->kode_jam_kerja;
        // dd($tanggal);

        // if (!$nik || empty($tanggal) || empty($kode_jam_kerja) || count($tanggal) !== count($kode_jam_kerja)) {
        //     LogHelper::log("Gagal Menyimpan Jam Kerja", "Data tidak valid untuk NIK: $nik.");
        //     return Redirect('/karyawan')->with(['warning' => 'Data tidak valid!']);
        // }

        for($i=0; $i < count($tanggal); $i++){
            $data[] = [
                'nik' => $nik,
                'tanggal_kerja' => $tanggal[$i],
                'kode_jam_kerja' => $kode_jam_kerja[$i]
            ];
        }
        // dd($data);
        try {
            Setjamkerja::insert($data);

             // Catat log aktivitas jika sukses
            // LogHelper::log("Tambah Jam Kerja", "Berhasil menyimpan jam kerja untuk NIK: $nik, jumlah hari: " . count($data));

            return Redirect('/karyawan')->with(['success' => 'Jam Kerja Berhasil Di Setting']);
        } catch (\Throwable $e) {
            // dd($e);

            // Catat log aktivitas jika gagal
            // LogHelper::log("Gagal Menyimpan Jam Kerja", "Error: " . $e->getMessage());

            return Redirect('/karyawan')->with(['warning' => 'Jam Kerja Gagal Di Setting']);
        } 
    }

    public function updatesetjamkerja(Request $request){
        $nik = $request->nik;
        $tanggal = $request->tanggal;
        $kode_jam_kerja = $request->kode_jam_kerja;
        
        for($i=0; $i < count($tanggal); $i++){
            $data[] = [
                'nik' => $nik,
                'tanggal_kerja' => $tanggal[$i],
                'kode_jam_kerja' => $kode_jam_kerja[$i]
            ];
        }
        //dd($data);
        DB::beginTransaction();
        try {
            DB::table('konfigurasi_jam_kerja')->where('nik', $nik)->delete();
            Setjamkerja::insert($data);
            DB::commit();
            return Redirect('/karyawan')->with(['success' => 'Jam Kerja Berhasil Di Update']);
        } catch (\Throwable $e) {
            //dd($e);
            DB::rollBack();
            return Redirect('/karyawan')->with(['warning' => 'Jam Kerja Gagal Di Update']);
        }
    }
}


    // public function storejamkerja(Request $request){
    //     $kode_jam_kerja = $request->kode_jam_kerja;
    //     $nama_jam_kerja = $request->nama_jam_kerja;
    //     $awal_jam_masuk = $request->awal_jam_masuk;
    //     $jam_masuk = $request->jam_masuk;
    //     $akhir_jam_masuk = $request->akhir_jam_masuk;
    //     $jam_pulang = $request->jam_pulang;
    //     $lintashari = $request->lintashari;
        
    //     $data = [
    //         'kode_jam_kerja' => $kode_jam_kerja,
    //         'nama_jam_kerja' => $nama_jam_kerja,
    //         'awal_jam_masuk' => $awal_jam_masuk,
    //         'jam_masuk' => $jam_masuk,
    //         'akhir_jam_masuk' => $akhir_jam_masuk,
    //         'jam_pulang' => $jam_pulang,
    //         'lintashari' => $lintashari
    //     ];
    //     try {
    //         DB::table('jam_kerja')->insert($data);
    //         return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
    //     } catch  (\Exception $e) {
    //         return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);
    //     }
    // }
