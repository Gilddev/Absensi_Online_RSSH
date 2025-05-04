@extends('layouts.admin.tabler')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <!-- Page pre-title -->
                <h2 class="page-title">
                    Persentase Rekap Kehadiran
                </h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row row-deck row-cards">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">

                                @if (Session::get('success'))
                                    <div class="alert alert-success">
                                        {{ Session::get('success') }}
                                    </div>
                                @endif

                                @if (Session::get('warning'))
                                    <div class="alert alert-warning">
                                        {{ Session::get('warning') }}
                                    </div>
                                @endif

                            </div>
                        </div>
                        <!-- Bagian tombol tambah -->
                        <div class="col-12">
                            <div class="row">
                                <div class="col-2">
                                    <div class="form-group">
                                        <a href="{{ route('rekap-kehadiran.sync') }}" class="btn btn-primary" onclick="return confirm('Apakah Anda yakin ingin menghitung ulang persentase kehadiran?')">
                                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-refresh"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" /><path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" /></svg>   
                                        Sync Rekap Data</a>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <a href="{{ route('rekap-kehadiran.sync') }}" class="btn btn-success" onclick="return confirm('Apakah Anda yakin ingin menghitung ulang persentase kehadiran?')">
                                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-file-type-xls"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M5 12v-7a2 2 0 0 1 2 -2h7l5 5v4" /><path d="M4 15l4 6" /><path d="M4 21l4 -6" /><path d="M17 20.25c0 .414 .336 .75 .75 .75h1.25a1 1 0 0 0 1 -1v-1a1 1 0 0 0 -1 -1h-1a1 1 0 0 1 -1 -1v-1a1 1 0 0 1 1 -1h1.25a.75 .75 0 0 1 .75 .75" /><path d="M11 15v6h3" /></svg>   
                                        Export Excel</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <form action="{{ route('cetak.rekap.kehadiran') }}" method="GET">
                                    <div class="row">

                                        <div class="col-5">
                                            <div class="form-group">
                                                <select name="bulan" id="bulan" class="form-select">
                                                    <option value="">Bulan</option>
                                                    @for ($i=1; $i<=12; $i++)
                                                        <option value="{{$i}}" {{ (request('bulan') == $i) ? 'selected' : '' }}>{{$namabulan[$i]}}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-5">
                                            <div class="form-group">
                                                <select name="tahun" id="tahun" class="form-select">
                                                    <option value="">Tahun</option>
                                                    @php
                                                        $tahunmulai = 2024;
                                                        $tahunsekarang = date("Y");
                                                    @endphp
                                                    @for ($tahun = $tahunmulai; $tahun <= $tahunsekarang; $tahun ++)
                                                        <option value="{{$tahun}}" {{ (request('tahun') == $tahun) ? 'selected' : '' }}>{{$tahun}}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-2">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary">
                                                    <svg xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-search"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
                                                Cari
                                                </button>
                                            </div>
                                        </div>

                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-12 table-responsive">
                                <!-- <table class="table table-border"> -->
                                <table class="table card-table table-vcenter text-nowrap datatable">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>Jumlah Hari Kerja</th>
                                            <th>Jumlah Hadir</th>
                                            <th>Jumlah Terlambat</th>
                                            <th>Persentase Keterlambatan</th>
                                            <th>Persentase Kehadiran</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($rekapPresensi as $index => $item)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $item->nama_lengkap }}</td>
                                                <td>{{ $item->jumlah_hari_kerja }}</td>
                                                <td>{{ $item->jumlah_hadir }}</td>
                                                <td>{{ $item->jumlah_terlambat }}</td>
                                                <td>{{ $item->persentase_keterlambatan }} %</td>
                                                <td>{{ $item->persentase_kehadiran }} %</td>
                                                <!-- Tambahkan kolom lain -->
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                {{-- {{ $karyawan -> links('vendor.pagination.bootstrap-5') }} --}}
                            </div>
                        </div>

                    </div>  
                </div>
            </div>
        </div>
    </div>
</div>
@endsection