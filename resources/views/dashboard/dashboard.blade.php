@extends('layouts.presensi')
@section('content')

<style>
    .logout{
        position: absolute;
        color: seagreen;
        font-size: 30px;
        text-decoration: none;
        right: 8px;
    }
    .logout:hover{
        color: white;
    }
    .bgheader {
        height: 180px;
        background-image: url('tabler/static/illustrations/BG.png');
        background-position: center;
        padding: 20px;
    }
    .image-container {
        box-shadow: 0px 5px 20px rgba(0, 0, 0, 0.5);
    }
</style>

<div class="bgheader" id="">
    <a href="/proseslogout" class="logout">
        <ion-icon name="log-out-outline"></ion-icon>
    </a>
    <div id="user-detail">
        <div class="avatar">
            @if (!empty(Auth::guard('karyawan') -> user() -> foto))
                @php
                    $path = Storage::url('upload/karyawan/'.Auth::guard('karyawan') -> user() -> foto);
                @endphp
                <div class="image-container">
                    <img src="{{url($path)}}" alt="avatar" class="center-cropped-img">
                </div>
                    
                <!-- <img src="{{url($path)}}" alt="avatar" class="imaged w64 rounded"> -->
            @else
                <img src="assets/img/sample/avatar/avatar1.jpg" alt="avatar" class="imaged w64 rounded" style="box-shadow: 3px 3px 10px 0px rgba(0, 0, 0, 0.75); border: 2px solid white;">
            @endif
        </div>
        <div class="mt-1" id="user-info">
            <h3 id="user-name">{{Auth::guard('karyawan') -> user() -> nama_lengkap}}</h3>
            <small id="user-role">{{Auth::guard('karyawan') -> user() -> jabatan}}</small>
        </div>
    </div>
</div>

        <div class="section" id="menu-section">
            <div class="card">
                <div class="card-body text-center">
                    <h3>Persentase Presensi</h3>
                    <div class="list-menu">

                        <div class="row">
                            <div class="item-menu text-center mx-3">
                                <p>Kehadiran</p>
                                @if ($rekap)
                                    <h3>{{ $rekap->persentase_kehadiran }}%</h3>
                                @else
                                    <h3>0.00%</h3>
                                @endif
                            </div>
                            <div class="item-menu text-center mx-3">
                                <p>Keterlambatan</p>
                                @if ($rekap)
                                    <h3>{{ $rekap->persentase_keterlambatan }}%</h3>
                                @else
                                    <h3>0.00%</h3>
                                @endif
                            </div>
                        </div>

                        {{-- <div class="item-menu text-center">
                            <div class="menu-icon">
                                <a href="/editprofile" class="green" style="font-size: 40px;">
                                    <ion-icon name="person-sharp"></ion-icon>
                                </a>
                            </div>
                            <div class="menu-name">
                                <span class="text-center">Profil</span>
                            </div>
                        </div>

                        <div class="item-menu text-center">
                            <div class="menu-icon">
                                <!-- <a href="/presensi/histori" class="warning" style="font-size: 40px;"> -->
                                <a href="/presensi/histori" class="warning" style="font-size: 40px;">
                                    <ion-icon name="document-text"></ion-icon>
                                </a>
                            </div>
                            <div class="menu-name">
                                <span class="text-center">Histori</span>
                            </div>
                        </div>

                        <div class="item-menu text-center">
                            <div class="menu-icon">
                                <a href="/presensi/create" class="orange" style="font-size: 40px;">
                                    <ion-icon name="location"></ion-icon>
                                </a>
                            </div>
                            <div class="menu-name">
                                Lokasi
                            </div>
                        </div> --}}

                    </div>
                </div>
            </div>
        </div>
        <div class="section mt-5" id="presence-section">
            <div class="todaypresence">
                <div class="row">
                    
                    <!-- Fungsi menampilkan info absensi masuk -->
                    <div class="col-6">
                        @php
                            if ($presensihariini && $presensihariini->foto_in) {
                                $path = Storage::url('upload/absensi/' . $presensihariini->foto_in);
                            } else {
                                // URL gambar default jika gambar tidak ada di database
                                $path = 'assets/img/sample/avatar/default.jpg';
                            }
                        @endphp
                        <script>
                            function infoabsenmasuk(event) {
                                Swal.fire({
                                    text: "Absen Masuk",
                                    imageUrl: "{{ url($path) }}", // Pastikan URL dimasukkan sebagai string yang valid
                                    imageWidth: 400,
                                    imageHeight: 300,
                                    imageAlt: "Custom image"
                                });
                            }
                        </script>
                        <a href="#" onclick="infoabsenmasuk(event)">
                            <div class="card gradasigreen">
                                <div class="card-body">
                                    <div class="presencecontent">
                                        <div class="iconpresence">
                                            @if ($presensihariini != null)
                                                @php
                                                    $path = Storage::url('upload/absensi/' . $presensihariini->foto_in);
                                                @endphp
                                                <img src="{{ url($path) }}" alt="" class="imaged w64">
                                            @else
                                                <ion-icon name="camera"></ion-icon>
                                            @endif
                                        </div>
                                        <div class="presencedetail">
                                            <h4 class="presencetitle">Masuk</h4>
                                            <span>{{ $presensihariini ? $presensihariini->jam_in : 'Belum Absen' }}</span>
                                            {{--<span>{{$presensihariini != null ? $presensihariini->jam_in : 'Belum Absen'}}</span>--}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Fungsi menampilkan info absensi pulang -->
                    <div class="col-6">
                        @php
                            if ($presensihariini && $presensihariini->foto_out) {
                                $path = Storage::url('upload/absensi/' . $presensihariini->foto_out);
                            } else {
                                // URL gambar default jika gambar tidak ada di database
                                $path = 'assets/img/sample/avatar/default.jpg';
                            }
                        @endphp
                        <script>
                            function infoabsenpulang(event) {
                                Swal.fire({
                                    text: "Absen Pulang",
                                    imageUrl: "{{ url($path) }}", // Pastikan URL dimasukkan sebagai string yang valid
                                    imageWidth: 400,
                                    imageHeight: 300,
                                    imageAlt: "Custom image"
                                });
                            }
                        </script>
                        <a href="#" onclick="infoabsenpulang(event)">
                            <div class="card gradasired">
                                <div class="card-body">
                                    <div class="presencecontent">
                                        <div class="iconpresence">
                                        @if ($presensihariini != null && $presensihariini -> jam_out != null)
                                                @php
                                                    $path = Storage::url('upload/absensi/' . $presensihariini -> foto_out);
                                                @endphp
                                                <img src="{{url($path)}}" alt="" class="imaged w64">
                                            @else
                                                <ion-icon name="camera"></ion-icon>
                                            @endif
                                        </div>
                                        <div class="presencedetail">
                                            <h4 class="presencetitle">Pulang</h4>
                                            <span>{{$presensihariini && $presensihariini->jam_out != null ? $presensihariini-> jam_out : 'Belum Absen'}}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>  
                        </a>
                    </div>

                </div>
            </div>

            <!-- Fungsi menghapus absensi datang -->
            <div class="mb-2">
                @if($presensihariini && $presensihariini->jam_in)
                    <form action="{{ route('presensi.hapusDatang', $presensihariini->id) }}" method="POST"
                        onsubmit="return confirm('Yakin ingin menghapus absensi datang?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-success btn-block">Hapus Absensi Datang</button>
                    </form>
                @endif
            </div>

            <!-- Fungsi menghapus absensi pulang -->
            <div class="mb-2">
                @if($presensihariini && $presensihariini->jam_out)
                    <form action="{{ route('presensi.hapusPulang', $presensihariini->id) }}" method="POST"
                        onsubmit="return confirm('Yakin ingin menghapus absensi pulang?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block">Hapus Absensi Pulang</button>
                    </form>
                @endif
            </div>

            <!-- Fungsi menghapus absensi pulang -->
            {{-- <div class="row mb-2">
                <div class="col-12">
                    <script>
                        function hapusabsenpulang(event) {
                            Swal.fire({
                                title: "Apakah anda ingin menghapus absen pulang?",
                                showDenyButton: true,
                                confirmButtonText: "Iya",
                                denyButtonText: "Tidak"
                                }).then((result) => {
                                /* Read more about isConfirmed, isDenied below */
                                if (result.isConfirmed) {
                                    // fungsi jika jawabannya iya
                                    Swal.fire("Terhapus!", "", "success");
                                } else if (result.isDenied) {
                                    // fungsi jika jawabannya tidak
                                }
                                });
                            }
                    </script>
                        <a href="#" onclick="hapusabsenpulang(event)">
                            <div class="card gradasired">
                                <div class="card-body">
                                    <div class="presencecontent">
                                        Hapus Absensi Pulang
                                    </div>
                                </div>
                            </div>  
                        </a>
                </div>
            </div> --}}

            <div id="rekappresensi">
                <h3>Rekap Presensi Bulan {{$namabulan[(int)$bulanini]}} {{$tahunini}}</h3>
                <div class="row">
                    <div class="col-4">
                        <div class="card">
                            <div class="card-body text-center" style="padding: 12px 12px !important">
                                @if ($rekappresensi->jmlhadir > 0)
                                <span class="badge bg-danger" style="position: absolute; top: 3px; right: 8px; font-size: 0.6rem; z-index:999">
                                    {{$rekappresensi->jmlhadir}}
                                </span>
                                @endif
                                <ion-icon name="accessibility-outline" style="font-size: 1.6rem" class="text-primary mb-1"></ion-icon>
                                <br>
                                <span style="font-size: 0.8rem font-weight: 500">Hadir</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="card">
                            <div class="card-body text-center" style="padding: 12px 12px !important">
                                @if ($rekappresensi->jmlizin > 0)
                                <span class="badge bg-danger" style="position: absolute; top: 3px; right: 8px; font-size: 0.6rem; z-index:999">
                                    {{$rekappresensi->jmlizin}}
                                </span>
                                @endif
                                <ion-icon name="document-outline" style="font-size: 1.6rem" class="text-success mb-1"></ion-icon>
                                <br>
                                <span style="font-size: 0.8rem font-weight: 500">Izin</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="card">
                            <div class="card-body text-center" style="padding: 12px 12px !important">
                                @if ($rekappresensi->jmlsakit > 0)
                                <span class="badge bg-danger" style="position: absolute; top: 3px; right: 8px; font-size: 0.6rem; z-index:999">
                                    {{$rekappresensi->jmlsakit}}
                                </span>
                                @endif
                                <ion-icon name="medkit-outline" style="font-size: 1.6rem" class="text-warning mb-1"></ion-icon>
                                <br>
                                <span style="font-size: 0.8rem font-weight: 500">Sakit</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-4 mt-1">
                        <div class="card">
                            <div class="card-body text-center" style="padding: 12px 12px !important">
                                @if ($rekappresensi->jmloncallpribadi > 0)
                                <span class="badge bg-danger" style="position: absolute; top: 3px; right: 8px; font-size: 0.6rem; z-index:999">
                                    {{$rekappresensi->jmloncallpribadi}}
                                </span>
                                @endif
                                <ion-icon name="call-outline" style="font-size: 1.6rem" class="text-warning mb-1"></ion-icon>
                                <br>
                                <span style="font-size: 0.8rem font-weight: 500">OC Pribadi</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-4 mt-1">
                        <div class="card">
                            <div class="card-body text-center" style="padding: 12px 12px !important">
                                @if ($rekappresensi->jmloncallkantor > 0)
                                <span class="badge bg-danger" style="position: absolute; top: 3px; right: 8px; font-size: 0.6rem; z-index:999">
                                    {{$rekappresensi->jmloncallkantor}}
                                </span>
                                @endif
                                <ion-icon name="call-outline" style="font-size: 1.6rem" class="text-warning mb-1"></ion-icon>
                                <br>
                                <span style="font-size: 0.8rem font-weight: 500">OC Kantor</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-4 mt-1">
                        <div class="card">
                            <div class="card-body text-center" style="padding: 12px 12px !important">
                                @if ($rekappresensi->jmlterlambat > 0)
                                <span class="badge bg-danger" style="position: absolute; top: 3px; right: 8px; font-size: 0.6rem; z-index:999">
                                    {{$rekappresensi->jmlterlambat}}
                                </span>
                                @endif
                                <ion-icon name="timer-outline" style="font-size: 1.6rem" class="text-danger mb-1"></ion-icon>
                                <br>
                                <span style="font-size: 0.8rem font-weight: 500">Telat</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="presencetab mt-2">
                <div class="tab-pane fade show active" id="pilled" role="tabpanel">
                    <ul class="nav nav-tabs style1" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#home" role="tab">
                                Bulan Ini
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="tab-content mt-2" style="margin-bottom:100px;">
                    <div class="tab-pane fade show active" id="home" role="tabpanel">
                        <!-- <ul class="listview image-listview">
                            @foreach ($historibulanini as $d)
                            @php
                                $path =  Storage::url('upload/absensi/' . $d -> foto_in);
                            @endphp
                            <li>
                                <div class="item">
                                    <div class="icon-box bg-primary">
                                        <ion-icon name="clipboard-outline"></ion-icon>
                                    </div>
                                    <div class="in">
                                        <div>{{date("d-m-Y", strtotime($d -> tgl_presensi))}}</div>
                                        
                                        <span class="badge badge-success">{{ $d -> jam_in}}</span>
                                        <span class="badge badge-danger">{{$d->jam_out != null ? $d-> jam_out : 'Belum Absen'}}</span>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul> -->

                        <style>
                            .historicontent{
                                display: flex;
                            }
                            .datapresensi h3,
                            .datapresensi p{
                                margin-left: 10px;
                                margin-top: 0.5px;
                                margin-bottom: 0.5px; /* Atur jarak sesuai keinginan Anda */
                            }
                        </style>

                        @foreach ($historibulanini as $d)
                        @if ($d->status == "h")
                            <div class="card" style="margin-bottom: 5px; border: 1px solid green">
                                <div class="card-body">
                                    <div class="historicontent">
                                        <div class="iconpresensi">

                                        @php
                                            // jam ketika karyawan absen
                                            $jam_in = date("H:i", strtotime($d->jam_in));

                                            // jam jadwal masuk 
                                            $jam_masuk = date("H:i", strtotime($d->jam_masuk));

                                            $jadwal_jam_masuk = $d->tgl_presensi . " " . $jam_masuk;
                                            $jam_presesi = $d->tgl_presensi . " " . $jam_in;
                                        @endphp

                                            @if ($jam_in > $jam_masuk )
                                            <ion-icon name="finger-print-outline" style="font-size: 48px;" class="text-danger"></ion-icon>
                                            @else
                                            <ion-icon name="finger-print-outline" style="font-size: 48px;" class="text-success"></ion-icon>
                                            @endif
                                        </div>
                                        <div class="datapresensi">
                                            <h3>{{ $d->nama_jam_kerja }}</h3>
                                            <p>{{ $d->jenis_presensi }}</p>
                                            <p>{{ date("d-m-Y", strtotime($d->tgl_presensi)) }}</p>
                                            <p>
                                                {!! $d->jam_in != null ? date("H:i", strtotime($d->jam_in)) : '<span class="text-danger">Belum Absen Masuk</span>' !!} - 
                                                {!! $d->jam_out != null ? date("H:i", strtotime($d->jam_out)) : '<span class="text-danger">Belum Absen Pulang</span>' !!} 
                                            </p>
                                            <div id="keterangan">
                                                @if ($jam_in > $jam_masuk)
                                                @php
                                                    $jml_jam_terlambat = hitungjamterlambat($jadwal_jam_masuk, $jam_presesi)
                                                @endphp
                                                    <p class="danger">Terlambat {{ $jml_jam_terlambat }}</p>
                                                @else
                                                    <p style="color:green">Tepat Waktu</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif ($d->status == "i")
                            <div class="card" style="margin-bottom: 5px; border: 1px solid green">
                                <div class="card-body">
                                    <div class="historicontent">
                                        <div class="iconpresensi">
                                            <ion-icon name="airplane-outline" style="font-size: 48px;" class="text-success"></ion-icon>
                                        </div>
                                        <div class="datapresensi">
                                            <h3>IZIN</h3>
                                            <p>{{ date("d-m-Y", strtotime($d->tgl_presensi)) }} ({{$d->kode_izin}})</p>
                                            <p>{{ $d->keterangan_pengajuan }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>        
                        @elseif ($d->status == "s")
                            <div class="card" style="margin-bottom: 5px; border: 1px solid green">
                                <div class="card-body">
                                    <div class="historicontent">
                                        <div class="iconpresensi">
                                            <ion-icon name="medkit-outline" style="font-size: 48px;" class="text-success"></ion-icon>
                                        </div>
                                        <div class="datapresensi">
                                            <h3>SAKIT</h3>
                                            <p>{{ date("d-m-Y", strtotime($d->tgl_presensi)) }} ({{$d->kode_izin}})</p>
                                            <p>{{ $d->keterangan_pengajuan }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif ($d->status == "op")
                            <div class="card" style="margin-bottom: 5px; border: 1px solid green">
                                <div class="card-body">
                                    <div class="historicontent">
                                        <div class="iconpresensi">
                                            <ion-icon name="call-outline" style="font-size: 48px;" class="text-success"></ion-icon>
                                        </div>
                                        <div class="datapresensi">
                                            <h3>Oncall Pribadi</h3>
                                            <p>{{ date("d-m-Y", strtotime($d->tgl_presensi)) }} ({{$d->karyawan_pengganti}})</p>
                                            <p>{{ $d->keterangan_presensi }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>        
                        @elseif ($d->status == "ok")
                            <div class="card" style="margin-bottom: 5px; border: 1px solid green">
                                <div class="card-body">
                                    <div class="historicontent">
                                        <div class="iconpresensi">
                                            <ion-icon name="call-outline" style="font-size: 48px;" class="text-success"></ion-icon>
                                        </div>
                                        <div class="datapresensi">
                                            <h3>Oncall Kantor</h3>
                                            <p>{{ date("d-m-Y", strtotime($d->tgl_presensi)) }} ({{$d->kode_oncall}})</p>
                                            <p>{{ $d->keterangan_presensi }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>        
                        @endif
                        @endforeach

                    </div>
                    <div class="tab-pane fade" id="profile" role="tabpanel">
                        <ul class="listview image-listview">
                            @foreach ($leaderboard as $d)
                            <li>
                                <div class="item">
                                    <img src="assets/img/sample/avatar/avatar1.jpg" alt="image" class="image">
                                    <div class="in">
                                        <div style="margin-left:5px">
                                            <b>{{$d -> nama_lengkap}}</b>
                                            <br>
                                            <small class="text-muted">{{$d -> jabatan}}</small>
                                        </div>
                                        <span class="badge {{$d -> jam_in < "07:00" ? "bg-success" : "bg-danger"}}">
                                            {{$d -> jam_in}}
                                        </span>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>

                </div>
            </div>
        </div>
@endsection

{{-- @push('myscript')
<script>
    let isNavigatingBack = false;

    // Deteksi tombol back
    window.addEventListener('popstate', function (event) {
        if (!isNavigatingBack) {
            event.preventDefault();

            if (confirm("Apakah Anda ingin logout?")) {
                window.location.href = "{{ route('logout.karyawan') }}"; // sesuaikan dengan rute logout kamu
            } else {
                // Push kembali ke halaman saat ini agar tidak benar-benar mundur
                history.pushState(null, null, location.href);
            }
        }
    });

    // Mencegah back button langsung keluar dari halaman
    window.onload = function () {
        history.pushState(null, null, location.href);
    };
</script>
@endpush --}}