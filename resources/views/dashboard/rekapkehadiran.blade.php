@extends('layouts.admin.tabler')

@section('content')
<div class="container">
    <h4 class="mb-4">Rekap Kehadiran Karyawan</h4>

    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>No</th>
                <th>Nama Lengkap</th>
                <th>Nama Ruangan</th>
                <th>Persentase Kehadiran</th>
                <th>Persentase Keterlambatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rekap as $index => $data)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $data->nama_lengkap }}</td>
                    <td>{{ $data->nama_ruangan ?? '-' }}</td>
                    <td>{{ $data->persentase_kehadiran }}%</td>
                    <td>{{ $data->persentase_keterlambatan }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Data tidak ditemukan</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
