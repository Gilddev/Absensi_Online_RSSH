<?php

namespace App\Console\Commands;

use App\Models\JamKerja;
use App\Models\Karyawan;
use App\Models\Presensi;
use App\Models\RekapPresensi;
use App\Models\Setjamkerja;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class HitungPersentaseKehadiran extends Command
{
    protected $signature = 'hitung:persentase-kehadiran';
    protected $description = 'Menghitung persentase kehadiran dan keterlambatan karyawan';

    public function handle()
    {
        $this->info('Proses perhitungan persentase kehadiran dimulai...');

        $rekapData = Setjamkerja::select('nik',
            DB::raw('MONTH(tanggal_kerja) as bulan'),
            DB::raw('YEAR(tanggal_kerja) as tahun')
        )
        ->groupBy('nik', 'bulan', 'tahun')
        ->get();

        foreach ($rekapData as $data) {
            $nik = $data->nik;
            $bulan = $data->bulan;
            $tahun = $data->tahun;

            // Hitung jumlah hari kerja dari setjamkerja
            $jumlahHariKerja = Setjamkerja::where('nik', $nik)
                ->whereMonth('tanggal_kerja', $bulan)
                ->whereYear('tanggal_kerja', $tahun)
                ->whereNotNull('kode_jam_kerja')
                ->count();

            // Ambil semua presensi untuk nik, bulan, dan tahun
            $presensi = Presensi::where('nik', $nik)
                ->whereMonth('tgl_presensi', $bulan)
                ->whereYear('tgl_presensi', $tahun)
                ->get();

            $jumlahHadir = $presensi->whereNotNull('jam_in')->count();

            $jumlahTerlambat = 0;

            foreach ($presensi as $p) {
                if (!$p->jam_in || !$p->kode_jam_kerja) {
                    continue;
                }

                $jamKerja = JamKerja::where('kode_jam_kerja', $p->kode_jam_kerja)->first();

                if ($jamKerja) {
                    $waktuMasuk = Carbon::parse($jamKerja->jam_masuk);
                    $waktuPresensi = Carbon::parse($p->jam_in);

                    if ($waktuPresensi->gt($waktuMasuk)) {
                        $jumlahTerlambat++;
                    }
                }
            }

            $persentaseKehadiran = $jumlahHariKerja > 0 ? round(($jumlahHadir / $jumlahHariKerja) * 100, 2) : 0;
            $persentaseKeterlambatan = $jumlahHadir > 0 ? round(($jumlahTerlambat / $jumlahHadir) * 100, 2) : 0;

            RekapPresensi::updateOrCreate(
                [
                    'nik' => $nik,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                ],
                [
                    'jumlah_hari_kerja' => $jumlahHariKerja,
                    'jumlah_hadir' => $jumlahHadir,
                    'jumlah_terlambat' => $jumlahTerlambat,
                    'persentase_kehadiran' => $persentaseKehadiran,
                    'persentase_keterlambatan' => $persentaseKeterlambatan,
                ]
            );
        }
        
        $this->info('Perhitungan persentase kehadiran selesai.');
    }
}
