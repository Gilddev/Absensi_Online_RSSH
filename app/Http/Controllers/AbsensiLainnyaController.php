<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Presensi;

class AbsensiLainnyaController extends Controller
{
    // Halaman utama absensi lainnya
    public function index()
    {
        return view('absensi_lainnya.index');
    }

    // Halaman form absen kegiatan luar
    public function kegiatanLuar()
    {
        return view('absensi_lainnya.kegiatan_luar');
    }

    // Simpan data absen kegiatan luar
    public function storeKegiatanLuar(Request $request)
    {
        $request->validate([
            'foto_in' => 'required|image|max:2048', // maksimal 2MB
        ]);

        // Simpan foto
        $fotoPath = $request->file('foto_in')->store('absensi/foto_in', 'public');

        // Simpan presensi
        Presensi::create([
            'nik' => auth()->user()->nik,
            'tgl_presensi' => now()->toDateString(),
            'jam_in' => now()->toTimeString(),
            'foto_in' => $fotoPath,
            'jenis_presensi' => 'kegiatan_luar',
            'status' => 'h',
        ]);

        return redirect()->route('absensi.lainnya.index')->with('success', 'Absensi Kegiatan Luar berhasil disimpan!');
    }
}
