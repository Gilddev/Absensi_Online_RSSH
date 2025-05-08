<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Presensi;
use App\Models\Oncall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;

use App\Models\Pengajuanizin;
use App\Models\Setjamkerja;
use App\Models\RekapPresensi;

class PresensiController extends Controller
{
    public function gettanggal($tanggal){
        return (int)$tanggal;
    }

    public function create(){
        $nik = Auth::guard('karyawan')->user()->nik;
        $hariini = date("Y-m-d");

        $jamsekarang = date("H:i");
        $tgl_sebelumnya = date("Y-m-d", strtotime("-1 days", strtotime($hariini)));
        $cek_presensi_sebelumnya = DB::table('presensi')
            ->join('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->where('tgl_presensi', $tgl_sebelumnya)
            ->where('nik', $nik)
            ->first();
        
        $cek_lintas_hari_presensi = $cek_presensi_sebelumnya != null ? $cek_presensi_sebelumnya->lintashari : 0;

        if ($cek_lintas_hari_presensi == 1){
            if ($jamsekarang < "09:00"){
                $hariini = $tgl_sebelumnya;
            }
        }

        $namatanggal = date('Y-m-d', strtotime($hariini));

        $cek = DB::table('presensi')->where('tgl_presensi', $hariini)->where('nik', $nik)->count();
        $cekkondisi = DB::table('presensi')->select('nik', 'jam_in', 'jam_out')->where('tgl_presensi', $hariini)->where('nik', $nik)->get();
        $lok_kantor = DB::table('konfigurasi_lokasi')->where('id', 1)->first();
        
        $jamkerja = DB::table('konfigurasi_jam_kerja')
            ->join('jam_kerja', 'konfigurasi_jam_kerja.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->where('nik', $nik)->where('tanggal_kerja', $namatanggal)->first();
        
        if ($jamkerja == null) {
            return view('presensi.notifjadwal');
        } else {
            return view('presensi.create', compact('cek', 'lok_kantor', 'cekkondisi', 'jamkerja', 'hariini'));
        }
    }

    public function createLuar()
    {
        $nik = Auth::guard('karyawan')->user()->nik;
        $hariini = date("Y-m-d");

        $jamsekarang = date("H:i");
        $tgl_sebelumnya = date("Y-m-d", strtotime("-1 days", strtotime($hariini)));
        $cek_presensi_sebelumnya = DB::table('presensi')
            ->join('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->where('tgl_presensi', $tgl_sebelumnya)
            ->where('nik', $nik)
            ->first();
        
        $cek_lintas_hari_presensi = $cek_presensi_sebelumnya != null ? $cek_presensi_sebelumnya->lintashari : 0;

        if ($cek_lintas_hari_presensi == 1){
            if ($jamsekarang < "09:00"){
                $hariini = $tgl_sebelumnya;
            }
        }

        $namatanggal = date('Y-m-d', strtotime($hariini));

        $cek = DB::table('presensi')->where('tgl_presensi', $hariini)->where('nik', $nik)->count();
        $cekkondisi = DB::table('presensi')->select('nik', 'jam_in', 'jam_out')->where('tgl_presensi', $hariini)->where('nik', $nik)->get();
        $lok_kantor = DB::table('konfigurasi_lokasi')->where('id', 2)->first();
        
        $jamkerja = DB::table('konfigurasi_jam_kerja')
            ->join('jam_kerja', 'konfigurasi_jam_kerja.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->where('nik', $nik)->where('tanggal_kerja', $namatanggal)->first();
        
        if ($jamkerja == null) {
            return view('presensi.notifjadwal');
        } else {
            return view('presensi.create_luar', compact('cek', 'lok_kantor', 'cekkondisi', 'jamkerja', 'hariini'));
        }
    }

    public function createOncall(){
        // ambil data user login
        $nik = auth()->user()->nik;

        // cek kuota oncall pribadi
        $karyawan = DB::table('karyawan')->where('nik', $nik)->first();
        $kuota_oncall = $karyawan->kuota_oncall ?? 0;

        return view('presensi.create_oncall', compact('kuota_oncall'));
    }

    public function storeOncall(Request $request)
    {
        $nik = auth()->user()->nik;

        $tgl_presensi = date("Y-m-d");
        $jamkerja = DB::table('konfigurasi_jam_kerja')
            ->join('jam_kerja', 'konfigurasi_jam_kerja.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->where('nik', $nik)
            ->whereDate('tanggal_kerja', $tgl_presensi)  // Pastikan tanggal_kerja sesuai dengan tanggal presensi
            ->first();

        // Validasi inputan
        $request->validate([
            'tgl_pengajuan' => 'required|date',
            'status' => 'required|in:pribadi,kantor',
            'karyawan_pengganti' => 'required|string|max:255',
            'keterangan' => 'required|string',
        ]);

        // Cek kuota kalau statusnya oncall pribadi
        if ($request->status == 'pribadi') {
            $karyawan = DB::table('karyawan')->where('nik', $nik)->first();
            if ($karyawan->kuota_oncall <= 0) {
                return back()->with('error', 'Kuota oncall pribadi Anda sudah habis.');
            }
        }
        // dd($jamkerja);

        // Simpan data ke tabel presensi_oncall
        // fungsi insertGetId berfungsi untuk langsung mengambil id dari baris data yang baru di tabel
        $kode_oncall = DB::table('presensi_oncall')->insertGetId([
            'nik' => $nik,
            'tgl_pengajuan' => $request->tgl_pengajuan,
            'status' => $request->status,
            'kode_jam_kerja' => $jamkerja->kode_jam_kerja,
            'karyawan_pengganti' => $request->karyawan_pengganti,
            'keterangan' => $request->keterangan,
        ]);

        // Kurangi kuota kalau oncall pribadi
        if ($request->status == 'pribadi') {
            DB::table('karyawan')->where('nik', $nik)->decrement('kuota_oncall');

            DB::table('presensi')->insert([
                'nik' => $nik,
                'tgl_presensi' => $request->tgl_pengajuan,
                'kode_jam_kerja' => $jamkerja->kode_jam_kerja,
                'status' => "op",
                'kode_oncall' => $kode_oncall
            ]);
        } else {
            DB::table('presensi')->insert([
                'nik' => $nik,
                'tgl_presensi' => $request->tgl_pengajuan,
                'kode_jam_kerja' => $jamkerja->kode_jam_kerja,
                'status' => "ok",
                'kode_oncall' => $kode_oncall
            ]);
        }
        // Redirect ke dashboard dengan pesan sukses
        return redirect()->route('dashboard');
    }

    public function store(Request $request){
        $nik = Auth::guard('karyawan')->user()->nik;
        $hariini = date("Y-m-d");
        $jamsekarang = date("H:i");

        $tgl_sebelumnya = date("Y-m-d", strtotime("-1 days", strtotime($hariini)));
        $cek_presensi_sebelumnya = DB::table('presensi')
            ->join('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->where('tgl_presensi', $tgl_sebelumnya)
            ->where('nik', $nik)
            ->first();

        $cek_lintas_hari_presensi = $cek_presensi_sebelumnya != null ? $cek_presensi_sebelumnya->lintashari : 0;
        $tgl_presensi = $cek_lintas_hari_presensi == 1 && $jamsekarang < "09:00" ? $tgl_sebelumnya : date("Y-m-d");

        $jam = date("H:i:s");
        $lok_kantor = DB::table('konfigurasi_lokasi')->where('id', 1)->first();
        $lok = explode(",", $lok_kantor->lokasi_kantor);
        $latitudekantor = $lok[0];
        $longitudekantor = $lok[1];
        $lokasi = $request->lokasi;
        $lokasiuser = explode(",", $lokasi);
        $latitudeuser = $lokasiuser[0];
        $longitudeuser = $lokasiuser[1];

        $jarak = $this->distance($latitudekantor, $longitudekantor, $latitudeuser, $longitudeuser);
        $radius = round($jarak["meters"]);

        $jamkerja = DB::table('konfigurasi_jam_kerja')
            ->join('jam_kerja', 'konfigurasi_jam_kerja.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->where('nik', $nik)
            ->whereDate('tanggal_kerja', $tgl_presensi)  // Pastikan tanggal_kerja sesuai dengan tanggal presensi
            ->first();

        $presensi = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('nik', $nik);
        $cek = $presensi->count();
        $datapresensi = $presensi->first();

        if ($cek > 0) {
            $ket = "out";
        } else {
            $ket = "in";
        }

        $image = $request->image;
        $folderPath = "public/upload/absensi/";
        $formatName = $nik . "-" . $tgl_presensi . "-" . $ket;
        $image_parts = explode(";base64", $image);
        $image_base64 = base64_decode($image_parts[1]);
        $fileName = $formatName . ".png";
        $file = $folderPath . $fileName;
        
        $tgl_pulang = $jamkerja->lintashari == 1 ? date('Y-m-d', strtotime("+1 days", strtotime($tgl_presensi))) : $tgl_presensi;
        
        $jam_pulang = $hariini . " " . $jam;
        $jam_kerja_pulang = $tgl_pulang . " " . $jamkerja->jam_pulang;

        if ($radius >= $lok_kantor->radius) {
            echo("error|Maaf anda di luar radius, jarak anda " . $radius . " meter dari kantor|radius");
        } else {
            if ($cek > 0) {
                if ($jam_pulang < $jam_kerja_pulang) {
                    echo ("error|Maaf Belum Waktunya Pulang|out");
                } else if (!empty($datapresensi->jam_out)){
                    echo "error|Anda Sudah Melakukan Absen Sebelumnya ! |out";
                } else {
                    $data_pulang = [
                        'jam_out' => $jam,
                        'foto_out' => $fileName,
                        'lokasi_out' => $lokasi
                    ];
                    $update = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('nik', $nik)->update($data_pulang);
                    if ($update) {
                        echo ("success|Terimakasih, Hati-hati Di Jalan|out");
                        Storage::put($file, $image_base64);
                    } else {
                        echo ("error|Gagal Absen, Hubungi Staff IT|out");
                    }
                }
            } else {
                if ($jam < $jamkerja->awal_jam_masuk) {
                    echo ("error|Maaf, Belum Waktunya Melakukan Absensi|in");
                } else if ($jam > $jamkerja->akhir_jam_masuk) {
                    echo ("error|Maaf, Waktu Melakukan Absensi Sudah Habis|in");
                } else {
                    
                    $data = [
                        'nik' => $nik,
                        'tgl_presensi' => $tgl_presensi,
                        'jam_in' => $jam,
                        'foto_in' => $fileName,
                        'lokasi_in' => $lokasi,
                        'kode_jam_kerja' => $jamkerja->kode_jam_kerja,
                        'status' => 'h',
                        'jenis_presensi' => "Absensi Regular"
                    ];
                    $simpan = DB::table('presensi')->insert($data);
                    if ($simpan) {
                        echo ("success|Terimakasih, Selamat Bekerja|in");
                        Storage::put($file, $image_base64);
                    } else {
                        echo ("error|Gagal Absen, Hubungi Staff IT|in");
                    }
                }
            }
        }
    }

    public function storeLuar(Request $request){
        $nik = Auth::guard('karyawan')->user()->nik;
        $hariini = date("Y-m-d");
        $jamsekarang = date("H:i");

        $tgl_sebelumnya = date("Y-m-d", strtotime("-1 days", strtotime($hariini)));
        $cek_presensi_sebelumnya = DB::table('presensi')
            ->join('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->where('tgl_presensi', $tgl_sebelumnya)
            ->where('nik', $nik)
            ->first();

        $cek_lintas_hari_presensi = $cek_presensi_sebelumnya != null ? $cek_presensi_sebelumnya->lintashari : 0;
        $tgl_presensi = $cek_lintas_hari_presensi == 1 && $jamsekarang < "09:00" ? $tgl_sebelumnya : date("Y-m-d");

        $jam = date("H:i:s");
        $lok_kantor = DB::table('konfigurasi_lokasi')->where('id', 2)->first();
        $lok = explode(",", $lok_kantor->lokasi_kantor);
        $latitudekantor = $lok[0];
        $longitudekantor = $lok[1];
        $lokasi = $request->lokasi;
        $lokasiuser = explode(",", $lokasi);
        $latitudeuser = $lokasiuser[0];
        $longitudeuser = $lokasiuser[1];
        $keterangan = $request->keterangan;

        $jarak = $this->distance($latitudekantor, $longitudekantor, $latitudeuser, $longitudeuser);
        $radius = round($jarak["meters"]);

        $jamkerja = DB::table('konfigurasi_jam_kerja')
            ->join('jam_kerja', 'konfigurasi_jam_kerja.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->where('nik', $nik)
            ->whereDate('tanggal_kerja', $tgl_presensi)  // Pastikan tanggal_kerja sesuai dengan tanggal presensi
            ->first();

        $presensi = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('nik', $nik);
        $cek = $presensi->count();
        $datapresensi = $presensi->first();

        if ($cek > 0) {
            $ket = "out";
        } else {
            $ket = "in";
        }

        $image = $request->image;
        $folderPath = "public/upload/absensi/";
        $formatName = $nik . "-" . $tgl_presensi . "-" . $ket;
        $image_parts = explode(";base64", $image);
        $image_base64 = base64_decode($image_parts[1]);
        $fileName = $formatName . ".png";
        $file = $folderPath . $fileName;
        
        $tgl_pulang = $jamkerja->lintashari == 1 ? date('Y-m-d', strtotime("+1 days", strtotime($tgl_presensi))) : $tgl_presensi;
        
        $jam_pulang = $hariini . " " . $jam;
        $jam_kerja_pulang = $tgl_pulang . " " . $jamkerja->jam_pulang;

        if ($radius >= $lok_kantor->radius) {
            echo("error|Maaf anda di luar radius, jarak anda " . $radius . " meter dari kantor|radius");
        } else {
            if ($cek > 0) {
                if ($jam_pulang < $jam_kerja_pulang) {
                    echo ("error|Maaf Belum Waktunya Pulang|out");
                } else if (!empty($datapresensi->jam_out)){
                    echo "error|Anda Sudah Melakukan Absen Sebelumnya ! |out";
                } else {
                    $data_pulang = [
                        'jam_out' => $jam,
                        'foto_out' => $fileName,
                        'lokasi_out' => $lokasi
                    ];
                    $update = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('nik', $nik)->update($data_pulang);
                    if ($update) {
                        echo ("success|Terimakasih, Hati-hati Di Jalan|out");
                        Storage::put($file, $image_base64);
                    } else {
                        echo ("error|Gagal Absen, Hubungi Staff IT|out");
                    }
                }
            } else {
                if ($jam < $jamkerja->awal_jam_masuk) {
                    echo ("error|Maaf, Belum Waktunya Melakukan Absensi|in");
                } else if ($jam > $jamkerja->akhir_jam_masuk) {
                    echo ("error|Maaf, Waktu Melakukan Absensi Sudah Habis|in");
                } else {
                    $data = [
                        'nik' => $nik,
                        'tgl_presensi' => $tgl_presensi,
                        'jam_in' => $jam,
                        'foto_in' => $fileName,
                        'lokasi_in' => $lokasi,
                        'kode_jam_kerja' => $jamkerja->kode_jam_kerja,
                        'status' => 'h',
                        'jenis_presensi' => "Absensi Luar",
                        'keterangan_luar' => $keterangan
                    ];
                    $simpan = DB::table('presensi')->insert($data);
                    if ($simpan) {
                        echo ("success|Terimakasih, Selamat Bekerja|in");
                        Storage::put($file, $image_base64);
                    } else {
                        echo ("error|Gagal Absen, Hubungi Staff IT|in");
                    }
                }
            }
        }
    }

    public function hapusAbsensiDatang($id)
    {
        // Ambil data presensi berdasarkan ID dan tanggal hari ini
        $presensi = Presensi::where('id', $id)
                            ->whereDate('tgl_presensi', now()->toDateString()) // Hanya untuk hari ini
                            ->first();
        
        // Hapus file foto_out jika ada
        if ($presensi->foto_in) {
            $filePath = public_path('storage/upload/absensi/' . $presensi->foto_in);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // Hapus file foto_out jika ada
        if ($presensi->foto_out) {
            $filePath = public_path('storage/upload/absensi/' . $presensi->foto_out);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
    
        // Jika data tidak ditemukan atau absensi pulang belum dilakukan
        if (!$presensi || !$presensi->jam_in) {
            return redirect()->back()->with('error', 'Tidak ada absensi datang untuk dihapus.');
        }
    
        // Hapus data presensi dari database
        $presensi->delete();
    
        return redirect()->back()->with('success', 'Absensi pulang berhasil dihapus.');
    }

    public function hapusAbsensiPulang($id)
    {
        // Ambil data presensi berdasarkan ID dan tanggal hari ini
        $presensi = Presensi::where('id', $id)
                            ->whereDate('tgl_presensi', now()->toDateString()) // Hanya untuk hari ini
                            ->first();
        
        // Hapus file foto_out jika ada
        if ($presensi->foto_out) {
            $filePath = public_path('storage/upload/absensi/' . $presensi->foto_out);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
    
        // Jika data tidak ditemukan atau absensi pulang belum dilakukan
        if (!$presensi || !$presensi->jam_out) {
            return redirect()->back()->with('error', 'Tidak ada absensi pulang untuk dihapus.');
        }
    
        // Update field jam_out, foto_out, lokasi_out menjadi null
        $presensi->update([
            'jam_out' => null,
            'foto_out' => null,
            'lokasi_out' => null
        ]);
    
        return redirect()->back()->with('success', 'Absensi pulang berhasil dihapus.');
    }

    // menghitung jarak user dari radius lokasi kantor
    function distance($lat1, $lon1, $lat2, $lon2){
        $theta = $lon1 - $lon2;
        $miles = (sin(deg2rad($lat1)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
        $miles = acos($miles);
        $miles = rad2deg($miles);
        $miles = $miles * 60 * 1.1515;
        $feet = $miles * 5280;
        $yards = $feet / 3;
        $kilometers = $miles * 1.609344;
        $meters = $kilometers * 1000;
        return compact('meters');
    }
    
    public function editprofile(){
        $nik = Auth::guard('karyawan') -> user() -> nik;
        $karyawan = DB::table('karyawan') -> where('nik', $nik) -> first();
        //dd($karyawan);
        return view('presensi.editprofile', compact('karyawan'));
    }

    public function updateprofile(Request $request){
        $nik = Auth::guard('karyawan') -> user() -> nik;
        $nama_lengkap = $request -> nama_lengkap;
        $jabatan = $request -> jabatan;
        $no_hp = $request -> no_hp;
        $password = Hash::make($request -> password);
        $karyawan = DB::table('karyawan') -> where('nik', $nik) -> first();
        $request->validate([
            'foto' => 'image|mimes:png,jpg|max:3000'
        ]);
        if($request -> hasFile('foto')){
            $foto = $nik.".".$request -> file('foto') -> getClientOriginalExtension();
        } else{
            $foto = $karyawan -> foto;
        }
        if(empty($request -> password)){
            $data = [
                'nama_lengkap' => $nama_lengkap,
                'jabatan' => $jabatan,
                'no_hp' => $no_hp,
                'foto' => $foto
            ];
        } else{
            $data = [
                'nama_lengkap' => $nama_lengkap,
                'jabatan' => $jabatan,
                'no_hp' => $no_hp,
                'foto' => $foto,
                'password' => $password
            ];
        }
        $update = DB::table('karyawan') -> where('nik', $nik) -> update($data);
        if($update){
            if($request -> hasFile('foto')){
                $folderPath = "public/upload/karyawan/";
                $request -> file('foto') -> storeAs($folderPath, $foto);
            }
            return Redirect::back() -> with(['success' => 'Data Berhasil Di Update']);
        } else{
            return Redirect::back() -> with(['error' => 'Data Gagal Di Update']);
        }
    }

    public function histori(){
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", 
        "Oktober", "November", "Desember"];
        return view('presensi.histori', compact('namabulan'));
    }

    public function gethistori(Request $request){
        $bulan = $request -> bulan;
        $tahun = $request -> tahun;
        $nik = Auth::guard('karyawan') -> user() -> nik;

        $histori = DB::table('presensi') -> whereRaw('MONTH(tgl_presensi)="' . $bulan . '"')
            -> select('presensi.*', 'keterangan', 'jam_kerja.*', 'file_surat_izin')
            -> leftJoin('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            -> leftJoin('pengajuan_izin', 'presensi.kode_izin', '=', 'pengajuan_izin.kode_izin')
            -> where('presensi.nik', $nik)
            -> whereRaw('MONTH(tgl_presensi)="' . $bulan . '"')
            -> whereRaw('YEAR(tgl_presensi)="' . $tahun . '"') 
            -> orderBy('tgl_presensi')
            -> get();

            //dd($histori);
        return view('presensi.gethistori', compact('histori'));
    }

    public function izin(Request $request){
        $nik = Auth::guard('karyawan') -> user() -> nik;

        if (!empty($request->bulan) && !empty($request->tahun)){
            $dataizin = DB::table('pengajuan_izin')
            -> orderBy('tgl_izin_dari', 'desc')
            -> where ('nik', $nik) 
            ->whereRaw('MONTH(tgl_izin_dari)="' . $request->bulan . '"')
            ->whereRaw('YEAR(tgl_izin_dari)="' . $request->tahun . '"')
            -> get();
        }else{
            $dataizin = DB::table('pengajuan_izin')
                -> orderBy('tgl_izin_dari', 'desc')
                -> where ('nik', $nik)
                -> limit(5)
                -> orderBy('tgl_izin_dari', 'desc')
                ->get();
        }

        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        return view('presensi.izin', compact('dataizin', 'namabulan'));
    }

    public function buatizin(){
        
        return view('presensi.buatizin');
    }

    public function storeizin(Request $request){
        $nik = Auth::guard('karyawan') -> user() -> nik;
        $tgl_izin = $request -> tgl_izin;
        $status = $request -> status;
        $keterangan = $request -> keterangan;

        $data = [
            'nik' => $nik,
            'tgl_izin' => $tgl_izin,
            'status' => $status,
            'keterangan' => $keterangan
        ];

        $simpan = DB::table('pengajuan_izin') -> insert($data);

        if($simpan){
            return redirect('/presensi/izin') -> with(['success' => 'Data Berhasil Disimpan']);
        } else{
            return redirect('/presensi/izin') -> with(['error' => 'Data Gagal Disimpan']);
        }
    }

    public function monitoring(){
        return view('presensi.monitoring');
    }

    public function getJadwalKerjaHariIni()
    {
        $tanggalHariIni = Carbon::now()->toDateString(); // Format: 'YYYY-MM-DD'

        $tampiljadwalkerja = DB::table('konfigurasi_jam_kerja')
            ->join('karyawan', 'konfigurasi_jam_kerja.nik', '=', 'karyawan.nik')
            ->join('ruangan', 'karyawan.kode_ruangan', '=', 'ruangan.kode_ruangan')
            ->join('jam_kerja', 'konfigurasi_jam_kerja.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->leftJoin('presensi', function($join) use ($tanggalHariIni) {
                $join->on('karyawan.nik', '=', 'presensi.nik')
                    ->whereDate('presensi.tgl_presensi', $tanggalHariIni);
            })
            ->where('konfigurasi_jam_kerja.tanggal_kerja', Carbon::now()->toDateString()) // Tanggal kerja hari ini
            ->whereNotNull('konfigurasi_jam_kerja.kode_jam_kerja')
            ->select('konfigurasi_jam_kerja.*', 'karyawan.nama_lengkap', 'karyawan.jabatan', 'ruangan.nama_ruangan', 'jam_kerja.nama_jam_kerja', 'presensi.jam_in')
            ->orderBy('ruangan.nama_ruangan')
            ->orderBy(DB::raw("
                    CASE 
                        WHEN jam_kerja.nama_jam_kerja = 'pagi' THEN 1
                        WHEN jam_kerja.nama_jam_kerja = 'siang' THEN 2
                        WHEN jam_kerja.nama_jam_kerja = 'malam' THEN 3
                        ELSE 4
                    END
                "))
            ->get();

        // dd($tampiljadwalkerja);
      
        // menampilkan data berdasatkan nama ruangan
        $tampiljadwalkerjaGrouped = $tampiljadwalkerja->groupBy('nama_ruangan');

        return view('presensi.monitoringjadwal', compact('tampiljadwalkerjaGrouped', 'tanggalHariIni'));
    }

    public function getpresensi(Request $request){
        $tanggal = $request -> tanggal;
        $presensi = DB::table('presensi') 
            -> select('presensi.*', 'karyawan.nama_lengkap', 'ruangan.nama_ruangan', 'jam_kerja.jam_masuk', 'jam_kerja.nama_jam_kerja', 'pengajuan_izin.keterangan as keterangan_pengajuan', 'presensi_oncall.keterangan as keterangan_presensi')
            -> leftJoin('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            -> leftJoin('pengajuan_izin', 'presensi.kode_izin', '=', 'pengajuan_izin.kode_izin')
            -> leftJoin('presensi_oncall', 'presensi.kode_oncall', '=', 'presensi_oncall.kode_oncall')
            -> join('karyawan', 'presensi.nik', '=', 'karyawan.nik')
            -> join('ruangan', 'karyawan.kode_ruangan', '=', 'ruangan.kode_ruangan')
            -> where('tgl_presensi', $tanggal)
            -> orderBy('ruangan.nama_ruangan')
            -> get();
        
        return view('presensi.getpresensi', compact('presensi'));
    }

    public function tampilkanpeta(Request $request){
        $id = $request -> id;
        $presensi = DB::table('presensi') -> where('id', $id) 
            -> join('karyawan', 'presensi.nik', '=', 'karyawan.nik')
            -> first();
        return view('presensi.showmap', compact('presensi'));
    }

    public function laporan(){
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", 
        "Oktober", "November", "Desember"];
        $karyawan = DB::table('karyawan') -> orderBy('nama_lengkap') -> get();
        return view('presensi.laporan', compact('namabulan', 'karyawan'));
    }

    public function cetaklaporan(Request $request){
        $nik = $request -> nik;
        $bulan = $request -> bulan;
        $tahun = $request -> tahun;
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", 
        "Oktober", "November", "Desember"];
        $karyawan = DB::table("karyawan") -> where('nik', $nik) 
            -> join('ruangan', 'karyawan.kode_ruangan', '=', 'ruangan.kode_ruangan')
            -> first();

        $presensi = DB::table('presensi')
            -> select('presensi.*', 'keterangan', 'jam_kerja.*')
            -> leftJoin('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            -> leftJoin('pengajuan_izin', 'presensi.kode_izin', '=', 'pengajuan_izin.kode_izin')
            -> where('presensi.nik', $nik)
            -> whereRaw('MONTH(tgl_presensi)="' . $bulan . '"')
            -> whereRaw('YEAR(tgl_presensi)="' . $tahun . '"')
            -> orderBy('tgl_presensi')
            -> get();

        return view('presensi.cetaklaporan', compact('bulan', 'tahun', 'namabulan', 'karyawan', 'presensi'));
    }

    public function rekap(){
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", 
        "Oktober", "November", "Desember"];
        $ruangan = DB::table('ruangan')->get();
        return view('presensi.rekap', compact('namabulan', 'ruangan'));
    }

    public function cetakrekap(Request $request){
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $kode_ruangan = $request->kode_ruangan;
        $dari =  $tahun . "-" . $bulan . "-01";
        $sampai = date("Y-m-t", strtotime($dari));
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", 
        "Oktober", "November", "Desember"];
    
        $select_date = "";
        $field_date = "";
        $i = 1;
        while (strtotime($dari) <= strtotime($sampai)) {
            $rangetanggal[] = $dari;
            $select_date .= "MAX(IF(tgl_presensi = '$dari', CONCAT(
                IFNULL(jam_in, 'NA'), '|',
                IFNULL(jam_out, 'NA'), '|',
                IFNULL(presensi.STATUS, 'NA'), '|',
                IFNULL(nama_jam_kerja, 'NA'), '|',
                IFNULL(jam_masuk, 'NA'), '|',
                IFNULL(jam_pulang, 'NA'), '|',
                IFNULL(presensi.kode_izin, 'NA'), '|',
                IFNULL(keterangan, 'NA'), '|'
            ),NULL)) as tgl_" . $i . ",";
    
            $field_date .= "tgl_" . $i . ",";
            $i++;
            $dari = date("Y-m-d", strtotime("+1 day", strtotime($dari)));
        }
        
        $jmlhari = count($rangetanggal);
        $lastrange = $jmlhari - 1;
        $sampai =  $rangetanggal[$lastrange];
        if ($jmlhari == 30) {
            array_push($rangetanggal, NULL);
        } elseif ($jmlhari == 29) {
            array_push($rangetanggal, NULL, NULL);
        } elseif ($jmlhari == 28) {
            array_push($rangetanggal, NULL, NULL, NULL);
        }
    
        $query = Karyawan::query();
        $query->selectRaw("$field_date karyawan.nik, nama_lengkap, jabatan, kode_ruangan, IFNULL(jumlah_wd, 0) as jumlah_wd");
    
        // JOIN dengan data presensi
        $query->leftJoin(
            DB::raw("(
                SELECT     
                    $select_date
                    presensi.nik   
                FROM presensi
                LEFT JOIN jam_kerja ON presensi.kode_jam_kerja = jam_kerja.kode_jam_kerja
                LEFT JOIN pengajuan_izin ON presensi.kode_izin = pengajuan_izin.kode_izin
                WHERE tgl_presensi BETWEEN '$rangetanggal[0]' AND '$sampai'
                GROUP BY nik
            ) presensi"),
            function($join){
                $join->on('karyawan.nik', '=', 'presensi.nik');
            }
        );
    
        // JOIN dengan konfigurasi_jam_kerja untuk menghitung jumlah WD (hari kerja)
        $query->leftJoin(
            DB::raw("(SELECT nik, COUNT(kode_jam_kerja) as jumlah_wd 
                FROM konfigurasi_jam_kerja 
                WHERE kode_jam_kerja IS NOT NULL 
                AND MONTH(tanggal_kerja) = '$bulan' 
                AND YEAR(tanggal_kerja) = '$tahun'
                GROUP BY nik) wd"),
            function($join){
                $join->on('karyawan.nik', '=', 'wd.nik');
            }
        );
    
        // Filter berdasarkan kode ruangan jika dipilih
        if (!empty($kode_ruangan)){
            $query->where('kode_ruangan', $kode_ruangan);
        }
        $query->orderBy('nama_lengkap');
        $rekap = $query->get();
        // dd($rekap);
    
        return view('presensi.cetakrekap', compact('bulan', 'tahun', 'rekap', 'namabulan', 'rangetanggal', 'jmlhari'));
    }

    public function rekapOncall(){
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", 
        "Oktober", "November", "Desember"];
        $ruangan = DB::table('ruangan')->get();
        return view('presensi.rekaponcall', compact('namabulan', 'ruangan'));
    }
    
    public function cetakRekapOncall(Request $request){
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $kode_ruangan = $request->kode_ruangan;

        $dari =  $tahun . "-" . $bulan . "-01";
        $sampai = date("Y-m-t", strtotime($dari));

        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    
        $select_date = "";
        $field_date = "";

        $i = 1;
        while (strtotime($dari) <= strtotime($sampai)) {
            $rangetanggal[] = $dari;
            $select_date .= "MAX(IF(tgl_pengajuan = '$dari', CONCAT(
                IFNULL(status, 'NA'), '|',
                IFNULL(nama_jam_kerja, 'NA'), '|',
                IFNULL(karyawan_pengganti, 'NA'), '|'
            ),NULL)) as tgl_" . $i . ",";
    
            $field_date .= "tgl_" . $i . ",";
            $i++;
            $dari = date("Y-m-d", strtotime("+1 day", strtotime($dari)));
        }
        
        $jmlhari = count($rangetanggal);
        $lastrange = $jmlhari - 1;
        $sampai =  $rangetanggal[$lastrange];

        if ($jmlhari == 30) {
            array_push($rangetanggal, NULL);
        } elseif ($jmlhari == 29) {
            array_push($rangetanggal, NULL, NULL);
        } elseif ($jmlhari == 28) {
            array_push($rangetanggal, NULL, NULL, NULL);
        }
    
        $query = Karyawan::query();
        $query->selectRaw("$field_date karyawan.nik, nama_lengkap, jabatan, kode_ruangan");
    
        // JOIN dengan data presensi
        $query->leftJoin(
            DB::raw("(
                SELECT     
                    $select_date
                    presensi_oncall.nik   
                FROM presensi_oncall
                LEFT JOIN jam_kerja ON presensi_oncall.kode_jam_kerja = jam_kerja.kode_jam_kerja
                WHERE tgl_pengajuan BETWEEN '$rangetanggal[0]' AND '$sampai'
                GROUP BY nik
            ) presensi_oncall"),
            function($join){
                $join->on('karyawan.nik', '=', 'presensi_oncall.nik');
            }
        );

        if (!empty($kode_ruangan)){
            $query->where('karyawan.kode_ruangan', $kode_ruangan);
        }

        $query->orderBy('karyawan.kode_ruangan');
        $rekap = $query->get();
        // dd($rekap);

        return view('presensi.cetakrekaponcall', compact('bulan', 'rekap', 'tahun', 'kode_ruangan', 'namabulan', 'rangetanggal', 'jmlhari'));
    }

    public function izinsakit(Request $request){
        $query = Pengajuanizin::query();
        $query -> select('kode_izin', 'tgl_izin_dari', 'tgl_izin_sampai', 'pengajuan_izin.nik', 'nama_lengkap', 'kode_ruangan', 'status', 'status_approved', 'keterangan', 'file_surat_izin');
        $query -> join('karyawan', 'pengajuan_izin.nik', '=', 'karyawan.nik');
        if(!empty($request -> dari) && !empty($request -> sampai)){
            $query -> whereBetween('tgl_izin_dari', [$request -> dari, $request -> sampai]);
        }

        if(!empty($request -> nik)){
            $query -> where('pengajuan_izin.nik', $request -> nik);
        }

        if(!empty($request -> nama_lengkap)){
            $query -> where('nama_lengkap', 'like', '%' . $request -> nama_lengkap . '%');
        }

        if($request -> status_approved === '0' || $request -> status_approved === '1' || $request -> status_approved === '2'){
            $query -> where('status_approved', $request -> status_approved);
        }

        $query -> orderBy('tgl_izin_dari', 'desc');
        $izinsakit = $query -> paginate(10);
        $izinsakit -> appends($request -> all());
        return view('presensi.izinsakit', compact('izinsakit'));
    }

    public function approveizinsakit(Request $request){
        $status_approved = $request -> status_approved;
        $kode_izin = $request -> kode_izin_form;
        $dataizin = DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->first();
        $nik = $dataizin->nik;
        $status = $dataizin->status;
        //dd($dataizin);
        $tgl_dari = $dataizin->tgl_izin_dari;
        $tgl_sampai = $dataizin->tgl_izin_sampai;
        DB::beginTransaction();
        try {
            if ($status_approved == 1) {
                while (strtotime($tgl_dari) <= strtotime($tgl_sampai)) {
                    DB::table('presensi')->insert([
                        'nik' => $nik,
                        'tgl_presensi' => $tgl_dari,
                        'status' => $status,
                        'kode_izin' => $kode_izin
                    ]);
                    $tgl_dari = date("Y-m-d", strtotime("+ 1 days", strtotime($tgl_dari)));
                }
            }
            DB::table('pengajuan_izin') -> where('kode_izin', $kode_izin) -> update(['status_approved' => $status_approved]);
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Diproses']);
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(['warning' => 'Data Gagal Diproses']);
        }

        // $update = DB::table('pengajuan_izin') -> where('id', $kode_izin) -> update([
        //     'status_approved' => $status_approve
        // ]);
        // if($update){
        //     return Redirect::back() -> with(['success' => 'Data Berhasil Di Update']);
        // }else{
        //     return Redirect::back() -> with(['warning' => 'Data Gagal Di Update']);
        // }
    }

    public function batalkanizinsakit($kode_izin){
        DB::beginTransaction();
        try {
            DB::table('pengajuan_izin') -> where('kode_izin', $kode_izin) -> update([
                'status_approved' => 0
            ]);
            DB::table('presensi')->where('kode_izin', $kode_izin)->delete();
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Di Batalkan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(['warning' => 'Data Gagal Di Batalkan']);
        }
        // $update = DB::table('pengajuan_izin') -> where('kode_izin', $kode_izin) -> update([
        //     'status_approved' => 0
        // ]);
        if($update){
            return Redirect::back() -> with(['success' => 'Data Berhasil Di Update']);
        }else{
            return Redirect::back() -> with(['warning' => 'Data Gagal Di Update']);
        }
    }

    public function cekpengajuanizin(Request $request){
        $tgl_izin = $request -> tgl_izin;
        $nik = Auth::guard('karyawan') -> user() -> nik;
        $cek = DB::table('pengajuan_izin') -> where('nik', $nik) -> where('tgl_izin', $tgl_izin) -> count();
        return $cek;
    }

    public function showact($kode_izin){
        $dataizin = DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->first(); 
        return view('presensi.showact', compact('dataizin'));
    }

    public function deleteizin($kode_izin){
        $cekdataizin = DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->first();
        $file_surat_izin = $cekdataizin->file_surat_izin;

        try {
            DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->delete();
            if ($file_surat_izin != null){
                Storage::delete('/public/upload/sid/' . $file_surat_izin);
            }
            return redirect('/presensi/izin')->with(['success' => 'Data Berhasil Di Hapus']);
        } catch (\Exception $e) {
            return redirect('/presensi/izin')->with(['success' => 'Data Gagal Di Hapus']);
        }
    } 

    public function indexEditDataPresensi($id){
        $presensi = Presensi::findOrFail($id);
        return view('presensi.editdatapresensi', compact('presensi'));
    }

    public function updateEditDataPresensi(Request $request, $id){
        $presensi = Presensi::findOrFail($id);

        $request->validate([
            'jam_in' => 'required',
            'jam_out' => 'nullable',
        ]);

        $data = [];

        if($request->filled('jam_in')){
            $data['jam_in'] = $request->jam_in;
        }

        if($request->filled('jam_out')){
            $data['jam_out'] = $request->jam_out;
        }

        $presensi->update($data);

        return redirect()->route('admin.monitoring')->with('success', 'Data Presensi Berhasil diupdate');
        // return redirect()->with('success', 'Data Berhasil di Update');
    }

    public function deleteDataPresensi($id){
        $presensi = Presensi::findOrFail($id);
        $presensi->delete();

        return redirect()->route('admin.monitoring')->with('success', 'Data Presensi Berhasil dihapus');
    }
}
