<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Cetak Rekap Persentase Karyawan</title>

  <link rel="icon" type="image/png" href="{{asset('assets/img/favicon.png')}}" sizes="32x32">

  <!-- Normalize or reset CSS with your favorite library -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/7.0.0/normalize.min.css">

  <!-- Load paper.css for happy printing -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.4.1/paper.css">

  <!-- Set page size here: A5, A4 or A3 -->
  <!-- Set also "landscape" if you need -->
    <style>
        @page { size: A3 landscape}

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
            font-size: 12px;
        }
        .tabelpresensi td{
            font-size: 12px;
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
<body class="A3">

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
                    REKAP PERSENTASE KARYAWAN <br>
                    PERIODE {{ strtoupper($namabulan[(string)$bulan]) }} {{ $tahun }} <br>
                    RUMAH SAKIT IBU DAN ANAK SITTI KHADIJAH <br>
                </span>
                <span class="alamat">Jl. Nani Wartabone No. 101 Telp. (0435) 821253-824410 Email : rsia_gtlo@gmail.co.id</span>
            </td>
        </tr>
    </table>
        
    <table class="tabelpresensi">
        <tr>
            <!-- <th rowspan="2">Nik</th> -->
            <th>No</th>
            <th>Nama</th>
            <th>Unit</th>
            <th>Jumlah Hari Kerja</th>
            <th>Jumlah Hadir</th>
            <th>Jumlah Terlambat</th>
            <th>% Keterlambatan</th>
            <th>% Kehadiran</th>
        </tr>
        @foreach ($rekappersentase as $r)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $r->nama_lengkap }}</td>
                <td>{{ $r->kode_ruangan }}</td>
                <td>{{ $r->jumlah_hari_kerja }}</td>
                <td>{{ $r->jumlah_hadir }}</td>
                <td>{{ $r->jumlah_terlambat }}</td>
                <td>{{ $r->persentase_keterlambatan }}</td>
                <td>{{ $r->persentase_kehadiran }}</td>
            </tr>
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