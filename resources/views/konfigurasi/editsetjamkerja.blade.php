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
                <h2 class="page-title">Edit Jam Kerja</h2>
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

        <form action="/konfigurasi/updatesetjamkerja" method="POST">
            @csrf
            <input type="hidden" name="nik" value="{{ $karyawan->nik }}">

            <div class="accordion" id="accordionJadwal">
                @php
                    use Carbon\Carbon;
                    $tahun = Carbon::now()->format('Y'); // Tahun saat ini
                    $bulanList = [
                        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                    ];
                @endphp

                @foreach ($bulanList as $index => $bulan)
                    @php
                        $bulanIndex = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
                        $totalHari = Carbon::createFromDate($tahun, $bulanIndex, 1)->daysInMonth; // Ambil jumlah hari dalam bulan tersebut
                    @endphp

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading{{ $bulanIndex }}">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                data-bs-target="#collapse{{ $bulanIndex }}" aria-expanded="false" 
                                aria-controls="collapse{{ $bulanIndex }}">
                                {{ $bulan }}
                            </button>
                        </h2>
                        <div id="collapse{{ $bulanIndex }}" class="accordion-collapse collapse" 
                            aria-labelledby="heading{{ $bulanIndex }}" data-bs-parent="#accordionJadwal">
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
                                            <th>Tanggal</th>
                                            <th>Jam Kerja</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @for ($i = 1; $i <= $totalHari; $i++)
                                            @php
                                                $tanggalFull = "$tahun-$bulanIndex-" . str_pad($i, 2, '0', STR_PAD_LEFT);
                                                $jamKerjaTerpilih = optional($jadwal->where('tanggal_kerja', $tanggalFull)->first())->kode_jam_kerja;
                                            @endphp
                                            <tr>
                                                <td>
                                                    Tanggal {{ $i }}
                                                    <input type="hidden" name="tanggal[]" value="{{ $tanggalFull }}">
                                                </td>
                                                <td>
                                                    <select name="kode_jam_kerja[]" class="form-select">
                                                        <option value="">Pilih Jam Kerja</option>
                                                        @foreach ($jam_kerja as $d)
                                                            <option value="{{ $d->kode_jam_kerja }}" 
                                                                {{ $jamKerjaTerpilih == $d->kode_jam_kerja ? 'selected' : '' }}>
                                                                {{ $d->nama_jam_kerja }}
                                                            </option>
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
            <button class="btn btn-primary w-100 mt-3" type="submit">Update Jam Kerja</button>
        </form>

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
                  Update Jam Kerja
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
                        <td>{{ $karyawan->kode_ruangan }}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <form action="/konfigurasi/updatesetjamkerja" method="POST">
                    @csrf
                    <input type="hidden" name="nik" value="{{ $karyawan->nik }}">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jam Kerja</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($set_jam_kerja as $s)
                            <tr>
                                <td>
                                    {{ $s->tanggal_kerja }}
                                    <input type="hidden" name="tanggal[]" value="{{ $s->tanggal_kerja }}">
                                </td>
                                <td>
                                    <select name="kode_jam_kerja[]" id="kode_jam_kerja" class="form-select">
                                        <option value="">Pilih Jam Kerja</option>
                                        @foreach ($jam_kerja as $d)
                                            <option {{ $d->kode_jam_kerja == $s->kode_jam_kerja ? 'selected' : ''}} value="{{ $d->kode_jam_kerja}}">{{ $d->nama_jam_kerja }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button class="btn btn-primary w-100" type="submit">Update</button>
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
