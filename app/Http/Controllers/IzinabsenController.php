<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class IzinabsenController extends Controller
{
    public function create(){
        return view('izin.create');
    }

    public function store(Request $request){
        $nik = Auth::guard('karyawan') -> user() -> nik;
        $tgl_izin_dari = $request -> tgl_izin_dari;
        $tgl_izin_sampai = $request -> tgl_izin_sampai;
        $status = "i";
        $keterangan = $request -> keterangan;

        $bulan = date("m", strtotime($tgl_izin_dari));
        $tahun = date("Y", strtotime($tgl_izin_dari));
        //variabel $thn hanya akan di ambil 2 digit terakhir
        $thn = substr($tahun, 2, 2);
        $izinterakhir = DB::table('pengajuan_izin')
            ->whereRaw('MONTH(tgl_izin_dari)="' . $bulan . '"')
            ->whereRaw('YEAR(tgl_izin_dari)="' . $tahun . '"')
            ->orderBy('kode_izin', 'desc')
            ->first();

        $kodeizinterakhir = $izinterakhir != null ? $izinterakhir->kode_izin : "";
        $format = "IZ" . $bulan . $thn;
        $kode_izin = buatkode($kodeizinterakhir, $format, 4);
        
        if($request->hasFile('sid')){
            $sid = $kode_izin . "." . $request->file('sid')->getClientOriginalExtension();
        }else{
            $sid = null;
        }
    
        $data = [
            'kode_izin' => $kode_izin,
            'nik' => $nik,
            'tgl_izin_dari' => $tgl_izin_dari,
            'tgl_izin_sampai' => $tgl_izin_sampai,
            'status' => $status,
            'keterangan' => $keterangan,
            'file_surat_izin' => $sid
        ];

        //cek sudah absen atau belum
        $cekpresensi = DB::table('presensi')
            -> whereBetween('tgl_presensi', [$tgl_izin_dari, $tgl_izin_sampai])
            -> where('nik', $nik)
            -> count();
        
        //cek sudah ada pengajuan pada tanggal yang dipilih
        $cekpengajuan = DB::table('pengajuan_izin')
            ->whereRaw('"' . $tgl_izin_dari . '" BETWEEN tgl_izin_dari AND tgl_izin_sampai');

        // dd($cekpengajuan -> count());

        if ($cekpresensi > 0) {
            return redirect('/presensi/izin') -> with(['error' => 'Gagal Mengajukan. Ada Tanggal Yang Sudah Digunakan / Sudah Melakukan Presensi. Silahkan Ganti Periode Tanggal Pengajuan']);
        } else {
            $simpan = DB::table('pengajuan_izin') -> insert($data);
    
            if($simpan){
                //sid = surat izin dinas / surat keterangan dinas
                if($request->hasFile('sid')){
                    $sid = $kode_izin . "." . $request->file('sid')->getClientOriginalExtension();
                    $folderPath = "public/upload/sid/";
                    $request->file('sid')->storeAs($folderPath, $sid);
                }
                return redirect('/presensi/izin') -> with(['success' => 'Data Berhasil Disimpan']);
            } else{
                return redirect('/presensi/izin') -> with(['error' => 'Data Gagal Disimpan']);
            }
        }
    }

    public function edit($kode_izin){
        $dataizin = DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->first(); 
        return view('izin.edit', compact('dataizin'));
    }

    public function update($kode_izin, Request $request){
        $tgl_izin_dari = $request -> tgl_izin_dari;
        $tgl_izin_sampai = $request -> tgl_izin_sampai;
        $keterangan = $request->keterangan;

        if($request->hasFile('sid')){
            $sid = $kode_izin . "." . $request->file('sid')->getClientOriginalExtension();
        }else{
            $sid = null;
        }

        try {
            $data = [
                'tgl_izin_dari' => $tgl_izin_dari,
                'tgl_izin_sampai' => $tgl_izin_sampai,
                'file_surat_izin' => $sid,
                'keterangan' => $keterangan
            ];
            DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update($data);

            if($request->hasFile('sid')){
                $sid = $kode_izin . "." . $request->file('sid')->getClientOriginalExtension();
                $folderPath = "public/upload/sid/";
                $request->file('sid')->storeAs($folderPath, $sid);
            }

            return redirect('/presensi/izin') -> with(['success' => 'Data Berhasil Diupdate']);
        } catch (\Throwable $e) {
            return redirect('/presensi/izin') -> with(['error' => 'Data Gagal Diupdate']);
        }
    }

    // public function downloadFile($kode_izin)
    // {
    //     // Tentukan path file
    //     $filePath = storage_path('public/upload/sid/' . $kode_izin);

    //     // Cek apakah file ada
    //     if (file_exists($filePath)) {
    //         return response()->download($filePath);
    //     } else {
    //         return response()->json(['message' => 'File not found.'], 404);
    //     }
    // }
}
