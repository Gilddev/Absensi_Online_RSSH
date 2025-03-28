<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Cetak Rekap Presensi</title>

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
            font-size: 8px;
        }
        .tabelpresensi td{
            font-size: 8px;
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

        body.A3.landscape.sheet {
            width: 420mm !important;
            height: auto !important;
        }
    </style>
</head>

<!-- Set "A5", "A4" or "A3" for class name -->
<!-- Set also "landscape" if you need -->
<body class="A3 landscape">

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
                    REKAP PRESENSI KARYAWAN <br>
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
            <th rowspan="2">Nama Karyawan</th>
            <th rowspan="2">Unit</th>
            <th colspan="{{ $jmlhari }}">Bulan {{ $namabulan[(string)$bulan] }} {{ $tahun }}</th>
            <th rowspan="2">H</th>
            <th rowspan="2">I</th>
            <th rowspan="2">S</th>
            {{-- <th rowspan="2">TH</th> --}}
            <th rowspan="2">WD</th>
        </tr>
        <tr>
            @foreach ($rangetanggal as $d)
            @if ($d != NULL)
                <th>{{ date("d", strtotime($d)) }}</th>
            @endif
            @endforeach
        </tr>
        @foreach ($rekap as $r)
            <tr>
                <td>{{ $r->nama_lengkap }}</td>
                <td>{{ $r->kode_ruangan }}</td>
                <?php
                    $jml_hadir = 0;
                    $jml_izin = 0;
                    $jml_sakit = 0;
                    $jml_tidakhadir = 0;
                    $jml_waktu_dinas = 0;
                    
                    for($i = 1; $i <= $jmlhari; $i++){
                        $tgl = 'tgl_' . $i;
                        $datapresensi = explode("|", $r->$tgl);
                        
                        if ($r->$tgl != NULL) {
                            $status = $datapresensi[2];  // Status (H, I, S, dll.)
                            $status2 = $datapresensi[3]; // Shift (Pagi, Siang, Malam) sesuai nama jam dinas
                            $status3 = $datapresensi[0]; // Jam Masuk
                            $status4 = $datapresensi[1]; // Jam Pulang
                        } else {
                            $status = "";
                            $status2 = "";
                            $status3 = "";
                            $status4 = "";
                        }
                    
                        // Hitung jumlah kehadiran
                        if ($status == "h") {
                            $jml_hadir += 1;
                        }
                        if ($status == "i") {
                            $jml_izin += 1;
                        }
                        if ($status == "s") {
                            $jml_sakit += 1;
                        }
                        // if (empty($status)) {
                        //     $jml_tidakhadir += 1;
                        // }
                    ?>
                    <td style="font-size:8px;">
                        {{$status}}
                        {{$status2}}
                        <span style="color: {{ ($status2 == "PAGI" && $status3 > "08:10:00") || ($status2 == "SIANG" && $status3 > "14:10:00") || ($status2 == "MALAM" && $status3 > "21:10:00") ? "red" : ""}}">
                            {{$status3}}
                        </span>
                        {{$status4}}
                    </td>
                    <?php      
                        }
                    ?>
                    <td>{{ !empty($jml_hadir) ? $jml_hadir : "" }}</td>
                    <td>{{ !empty($jml_izin) ? $jml_izin : "" }}</td>
                    <td>{{ !empty($jml_sakit) ? $jml_sakit : "" }}</td>
                    {{-- <td>{{ !empty($jml_tidakhadir) ? $jml_tidakhadir : "" }}</td> --}}
                    {{-- <td>{{ !empty($jml_waktu_dinas) ? $jml_waktu_dinas : "" }}</td> --}}
                    <td>{{ $r->jumlah_wd }}</td>
            </tr>
        @endforeach
    </table>
    <p>Note : Untuk sekuriti dan driver, waktu dinas hanya 2 dan jam pertukaran dinas 09:00:00</p>
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