@if ($histori -> isEmpty())
    <div class="alert alert-warning">
        <p>Data Belum Ada</p>
    </div>
@endif

@foreach ($histori as $d)
<!-- <ul class="listview image-listview">
    <li>
    <div class="item">
        @php
            $path = Storage::url('upload/absensi/'. $d -> foto_in);
        @endphp

        <img src="{{url($path)}}" alt="image" class="image">
        <div class="in">
            <div>
                <b>{{date("d-m-Y", strtotime($d -> tgl_presensi))}}</b>
                <br>
                {{--<small class="text-muted">{{$d -> jabatan}}</small>--}}
            </div>
            <span class="badge {{$d -> jam_in < "07:00" ? "bg-success" : "bg-danger"}}">
                {{$d -> jam_in}}
            </span>
            <span class="badge bg-primary">{{$d -> jam_out}}</span>
        </div>
    </div>
    </li>
</ul> -->

    <style>
        .historicontent{
            display: flex;
        }
        .datapresensi h3,
        .datapresensi p{
            margin-left: 10px;
            margin-bottom: 1px; /* Atur jarak sesuai keinginan Anda */
        }
        .card{
            border: 1px solid blue;
        }
    </style>

    @if ($d->status == "h")
        <div class="card" style="margin-bottom: 5px">
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
        <div class="card" style="margin-bottom: 5px">
            <div class="card-body">
                <div class="historicontent">
                    <div class="iconpresensi">
                        <ion-icon name="airplane-outline" style="font-size: 48px;" class="text-success"></ion-icon>
                    </div>
                    <div class="datapresensi">
                        <h3>IZIN</h3>
                        <p>{{ date("d-m-Y", strtotime($d->tgl_presensi)) }} ({{$d->kode_izin}})</p>
                        <p>{{ $d->keterangan }}</p>
                    </div>
                </div>
            </div>
        </div>        
        @elseif ($d->status == "s")
        <div class="card" style="margin-bottom: 5px">
            <div class="card-body">
                <div class="historicontent">
                    <div class="iconpresensi">
                        <ion-icon name="medkit-outline" style="font-size: 48px;" class="text-success"></ion-icon>
                    </div>
                    <div class="datapresensi">
                        <h3>SAKIT</h3>
                        <p>{{ date("d-m-Y", strtotime($d->tgl_presensi)) }} ({{$d->kode_izin}})</p>
                        <p>{{ $d->keterangan }}</p>
                    </div>
                </div>
            </div>
        </div>        
    @endif
@endforeach