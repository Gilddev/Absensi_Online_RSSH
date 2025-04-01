@php
    if (Auth::guard('karu')->check()) {
        $layout = 'layouts.karu.tabler';
    } elseif (Auth::guard('user')->check()){ 
        $layout = 'layouts.admin.tabler';
    }
@endphp

@extends($layout)
@section('content')

<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                  Set Jam Kerja
                </h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row">
            <div class="col-12">
                <table class="table">
                    <tr>
                        <th>NIK</th>
                        <td>{{ $karyawan->nik }}</td>
                    </tr>
                    <tr>
                        <th>Nama</th>
                        <td>{{ $karyawan->nama_lengkap }}</td>
                    </tr>
                    <tr>
                        <th>Ruangan</th>
                        <td>{{ $karyawan->nama_ruangan }}</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <form action="/konfigurasi/storesetjamkerja" method="POST">
                    @csrf
                    <input type="hidden" name="nik" value="{{ $karyawan->nik }}">

                    <div class="accordion" id="jadwalAccordion">
                        @php
                            use Carbon\Carbon;
                            $tahun = Carbon::now()->year;
                            $bulanList = [
                                '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                                '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                                '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                            ];
                        @endphp

                        @foreach ($bulanList as $bulan => $namaBulan)
                            @php
                                $jumlahHari = Carbon::create($tahun, $bulan, 1)->daysInMonth;
                            @endphp

                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading{{ $bulan }}">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $bulan }}">
                                        {{ $namaBulan }}
                                    </button>
                                </h2>
                                <div id="collapse{{ $bulan }}" class="accordion-collapse collapse" data-bs-parent="#jadwalAccordion">
                                    <div class="accordion-body">
                                        <div class="">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th colspan="6">
                                                            Master Jam Kerja
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <th>Kode</th>
                                                        <th>Nama</th>
                                                        <th>Awal Masuk</th>
                                                        <th>Jam Masuk</th>
                                                        <th>Akhir Masuk</th>
                                                        <th>Jam Pulang</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($jam_kerja as $d)
                                                        <tr>
                                                            <td>{{ $d->kode_jam_kerja }}</td>
                                                            <td>{{ $d->nama_jam_kerja }}</td>
                                                            <td>{{ $d->awal_jam_masuk }}</td>
                                                            <td>{{ $d->jam_masuk }}</td>
                                                            <td>{{ $d->akhir_jam_masuk }}</td>
                                                            <td>{{ $d->jam_pulang }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Hari</th>
                                                    <th>Jam Kerja</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @for ($i = 1; $i <= $jumlahHari; $i++)
                                                    <tr>
                                                        <td>
                                                            Tanggal {{ $i }}
                                                            <input type="hidden" name="tanggal[]" value="{{ $tahun }}-{{ $bulan }}-{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">
                                                        </td>
                                                        <td>
                                                            <select name="kode_jam_kerja[]" class="form-select">
                                                                <option value="">Pilih Jam Kerja</option>
                                                                @foreach ($jam_kerja as $d)
                                                                    <option value="{{ $d->kode_jam_kerja }}">{{ $d->nama_jam_kerja }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                    </tr>
                                                @endfor
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <input type="hidden" name="page" value="{{ request()->page }}">
                    <button class="btn btn-primary w-100 mt-3" type="submit">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection


{{-- @php
    if (Auth::guard('karu')->check()) {
        $layout = 'layouts.karu.tabler';
    } elseif (Auth::guard('user')->check()){ 
        $layout = 'layouts.admin.tabler';
    }
@endphp
@extends($layout)
@section('content')

<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <!-- Page pre-title -->
                <h2 class="page-title">
                  Set Jam Kerja
                </h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row">
            <div class="col-12">
                <table class="table">
                    <tr>
                        <th>NIK</th>
                        <td>{{ $karyawan->nik }}</td>
                    </tr>
                    <tr>
                        <th>Nama</th>
                        <td>{{ $karyawan->nama_lengkap }}</td>
                    </tr>
                    <tr>
                        <th>Ruangan</th>
                        <td>{{ $karyawan->nama_ruangan }}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <form action="/konfigurasi/storesetjamkerja" method="POST">
                    @csrf
                    <input type="hidden" name="nik" value="{{ $karyawan->nik }}">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Hari</th>
                                <th>Jam Kerja</th>
                            </tr>
                        </thead>
                        <tbody>

                            @php
                                use Carbon\Carbon;
                                $tahunBulan = Carbon::now()->format('Y-m'); // Ambil tahun dan bulan saat ini
                            @endphp

                            @for ($i = 1; $i <= 31; $i++)
                                <tr>
                                    <td>
                                        Tanggal {{ $i }}
                                        <input type="hidden" name="tanggal[]" value="{{ $tahunBulan }}-{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">
                                    </td>
                                    <td>
                                        <select name="kode_jam_kerja[]" class="form-select">
                                            <option value="">Pilih Jam Kerja</option>
                                            @foreach ($jam_kerja as $d)
                                                <option value="{{ $d->kode_jam_kerja }}">{{ $d->nama_jam_kerja }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                        
                    </table>
                    <button class="btn btn-primary w-100" type="submit">Simpan</button>
                </form>
            </div>
            <div class="col-6">
                <table class="table">
                    <thead>
                        <tr>
                            <th colspan="6">
                                Master Jam Kerja
                            </th>
                        </tr>
                        <tr>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Awal Masuk</th>
                            <th>Jam Masuk</th>
                            <th>Akhir Masuk</th>
                            <th>Jam Pulang</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($jam_kerja as $d)
                            <tr>
                                <td>{{ $d->kode_jam_kerja }}</td>
                                <td>{{ $d->nama_jam_kerja }}</td>
                                <td>{{ $d->awal_jam_masuk }}</td>
                                <td>{{ $d->jam_masuk }}</td>
                                <td>{{ $d->akhir_jam_masuk }}</td>
                                <td>{{ $d->jam_pulang }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection --}}