<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Cetak Laporan Karyawan</title>

  <link rel="icon" type="image/png" href="{{asset('assets/img/favicon.png')}}" sizes="32x32">

  <!-- Normalize or reset CSS with your favorite library -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/7.0.0/normalize.min.css">

  <!-- Load paper.css for happy printing -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.4.1/paper.css">

  <!-- Set page size here: A5, A4 or A3 -->
  <!-- Set also "landscape" if you need -->
    <style>
        @page { size: legal}

        #title{
            font-family: Arial, Helvetica, sans-serif;
            font-size: 18px;
            font-weight: bold;
        }
        .alamat{
            font-style: italic;
        }

        .tabeldatakaryawan{
            margin-top: 40px;
        }

        .tabeldatakaryawan td{
            padding: 5px;
        }

        .tabelpresensi{
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .tabelpresensi th{
            border: 1px solid;
            padding: 8px;
        }
        .tabelpresensi td{
            font-size: 11px;
            border: 1px solid;
            padding: 8px;
        }
        .foto{
            width: 64px;
            height: 64px;
        }
        .sheet {
            overflow: visible;
            height: auto !important;
}
    </style>
</head>

<!-- Set "A5", "A4" or "A3" for class name -->
<!-- Set also "landscape" if you need -->
<body class="legal">


  <!-- Each sheet element should have the class "sheet" -->
  <!-- "padding-**mm" is optional: you can set 10, 15, 20 or 25 -->
  <section class="sheet padding-10mm">

    <!-- Write HTML just like a web page -->
    <table style="width: 100%" >
        <tr>
            <td style="width: 40" >
                <img src="{{ asset('assets/img/logo_laporan.png') }}" width="70" height="70" alt="">
            </td>
            <td>
                <span id="title">
                    LAPORAN PRESENSI KARYAWAN <br>
                    PERIODE {{ strtoupper($namabulan[(string)$bulan]) }} {{ $tahun }} <br>
                    RUMAH SAKIT IBU DAN ANAK SITTI KHADIJAH <br>
                </span>
                <span class="alamat">Jl. Nani Wartabone No. 101 Telp. (0435) 821253-824410 Email : rsia_gtlo@gmail.co.id</span>
            </td>
        </tr>
    </table>
        <table class="tabeldatakaryawan">
            <tr>
                <td rowspan="5">
                    @php
                    $path = Storage::url('upload/karyawan/' . $karyawan -> foto);
                    @endphp
                    <img src="{{ url($path) }}" alt="" width="100px" height="100px">
                </td> 
            </tr>
            <tr>
                <td>Nik</td>
                <td>:</td>
                <td>{{ $karyawan -> nik }}</td>
            </tr>
            <tr>
                <td>Nama Karyawan</td>
                <td>:</td>
                <td>{{ $karyawan -> nama_lengkap }}</td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>:</td>
                <td>{{ $karyawan -> jabatan }}</td>
            </tr>
            <tr>
                <td>Ruangan</td>
                <td>:</td>
                <td>{{ $karyawan -> nama_ruangan }}</td>
            </tr>
        </table>

        <table class="tabelpresensi">
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Dinas</th>
                <th>Jam Masuk</th>
                <th>Foto</th>
                <th>Jam Pulang</th>
                <th>Foto</th>
                <th>Status</th>
                <th>Jenis</th>
                <th>Keterangan</th>
                {{-- <th>Jam Kerja</th> --}}
            </tr>
                @foreach ($presensi as $d)
                @if ($d->status == "h")
                    @php
                    $path_foto_in = Storage::url('upload/absensi/' . $d -> foto_in);
                    $path_foto_out = Storage::url('upload/absensi/' . $d -> foto_out);
                    $jam_terlambat = hitungjamkerja($d->jam_masuk, $d -> jam_in);
                    @endphp
                    <tr>
                        <td>{{ $loop -> iteration }}</td>
                        <td>{{ date("d-m-Y", strtotime($d -> tgl_presensi)) }}</td>
                        <td>{{ $d->nama_jam_kerja}}</td>
                        <td>{{ $d -> jam_in }}</td>
                        <td><img src="{{ url($path_foto_in) }}" class="foto" alt=""></td>
                        <td>{{ $d -> jam_out != null ? $d -> jam_out : 'Belum Absen' }}</td>
                        <td>
                            @if ($d -> jam_out != null)
                                <img src="{{ url($path_foto_out) }}" class="foto" alt="">
                            @else
                                <img src="{{ asset('assets/img/sample/avatar/avatar1.jpg') }}" class="foto" alt="">
                            @endif
                        </td>
                        <td style="text-align: center">
                            {{ $d->status }}
                            @if ($d -> jam_in > $d->jam_masuk)
                                Terlambat {{ $jam_terlambat }}
                            @else
                                Tepat Waktu
                            @endif
                        </td>
                        {{-- <td>
                            @if ($d -> jam_out != null)
                                @php
                                    $tgl_masuk = $d->tgl_presensi;
                                    $tgl_pulang = $d->lintashari == 1 ? date('Y-m-d', strtotime('+ 1 days', strtotime($tgl_masuk))) : $tgl_masuk ;
                                    $jam_masuk = $tgl_masuk . ' ' . $d->jam_in;
                                    $jam_pulang = $tgl_pulang . ' ' . $d->jam_out;

                                    $jmljamkerja = hitungjamkerja($jam_masuk, $jam_pulang);
                                @endphp
                            @else
                                @php
                                    $jmljamkerja = 0;
                                @endphp
                            @endif
                            {{ $jmljamkerja }}
                        </td> --}}
                        <td>
                            {{ $d->jenis_presensi }}
                        </td>
                        <td>
                            {{ $d->keterangan_luar }}
                        </td>
                    </tr>
                @else
                    <tr>
                        <td>{{ $loop -> iteration }}</td>
                        <td>{{ date("d-m-Y", strtotime($d -> tgl_presensi)) }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td style="text-align: center">{{ $d->status }}</td>
                        <td>
                            {{ $d->keterangan }}
                        </td>
                        <td></td>
                    </tr>
                @endif
                @endforeach
        </table>

        <table width="100%" style="margin-top: 150px">
            <tr>
                <td></td>
                <td style="text-align: center">Gorontalo, {{ date('d-m-Y') }}</td>
            </tr>
            <tr>
                <td style="text-align: center; vertical-align: bottom" height="150px">
                    <u>Agil Dwi Sulistyo</u><br>
                    <i>Administrator</i>
                </td>
                <td style="text-align: center; vertical-align: bottom">
                    <u>Rusli A. Katili</u><br>
                    <i>Direktur</i>
                </td>
            </tr>
        </table>

  </section>

</body>

</html>