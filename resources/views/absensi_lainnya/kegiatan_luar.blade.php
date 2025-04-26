@extends('layouts.presensi')

@section('content')
<div class="container">
    <h1>Absen Kegiatan Luar</h1>

    <form action="{{ route('absensi.lainnya.kegiatan-luar.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="foto_in" class="form-label">Upload Foto Selfie</label>
            <input type="file" name="foto_in" id="foto_in" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success">Simpan Absensi</button>
    </form>
</div>
@endsection
