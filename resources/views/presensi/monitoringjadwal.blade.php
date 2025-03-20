@extends('layouts.admin.tabler')
@section('content')

<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <!-- Page pre-title -->
                <h2 class="page-title mb-2">
                  Monitoring Jadwal
                </h2>
                <h2>
                    {{ $tanggalHariIni }}
                </h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
    
        @foreach($tampiljadwalkerjaGrouped as $nama_ruangan => $jadwal)
        <div class="row row-deck row-cards mb-2">
            <div class="col-12">
                <div class="card">  
                        <h2 style="padding-left: 20px; padding-top: 10px;">{{ $nama_ruangan }}</h2>
                        <div class="table-responsive">
                            <table class="table card-table table-vcenter text-nowrap datatable">
                                <thead>
                                    <tr>
                                        <th style="width: 40%;">Nama Karyawan</th>
                                        <th style="width: 20%;">Nama Jabatan</th>
                                        <th style="width: 20%;">Waktu Dinas</th>
                                        <th style="width: 20%;">Status Kehadiran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($jadwal as $item)
                                        <tr>
                                            <td>{{ $item->nama_lengkap }}</td>
                                            <td>{{ $item->jabatan }}</td>
                                            <td>{{ $item->nama_jam_kerja }}</td>
                                            <td>
                                                @if($item->jam_in !== null)
                                                    <span class="badge bg-success" style="color:white">Sudah Hadir</span>
                                                @else
                                                <span class="badge bg-danger" style="color:white">Belum Hadir</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        
    </div>
</div>

@endsection