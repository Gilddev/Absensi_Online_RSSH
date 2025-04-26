@extends('layouts.presensi')
@section('header')
    <!-- App Header -->
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="javascript:;" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Absensi Kuliah Subuh</div>
        <div class="right"></div>
    </div>
    <!-- * App Header -->

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
    crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        .webcam-capture, .webcam-capture video{
            display: block;
            width: 100% !important;
            height: 100% !important;
            margin: auto;
            border-radius: 15px;
            top: 50px;
        }
        #map { 
            height: 230px; 
        }
    </style>

@endsection

@section('content')
    <div class="container">
        <h4 class="mb-5">Form Absensi Kuliah Subuh</h4>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Terjadi kesalahan:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="/presensi/storekuliahsubuh" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Tanggal otomatis (readonly) --}}
            <div class="mb-3">
                <label for="tgl" class="form-label">Tanggal</label>
                <input type="text" class="form-control" id="tgl" value="{{ date('Y-m-d') }}" readonly>
            </div>

            @php
                $nik = auth()->user()->nik;
                $karyawan = DB::table('karyawan')
                    ->join('ruangan', 'karyawan.kode_ruangan', '=', 'ruangan.kode_ruangan')
                    ->where('nik', $nik)
                    ->select('nama_lengkap', 'ruangan.nama_ruangan')
                    ->first();
            @endphp

            <div class="mb-3">
                <label for="nama_lengkap" class="form-label">Nama Karyawan</label>
                <input type="text" class="form-control" value="{{ $karyawan->nama_lengkap ?? '-' }}" readonly>
            </div>

            <div class="mb-3">
                <label for="nama_ruangan" class="form-label">Ruangan</label>
                <input type="text" class="form-control" value="{{ $karyawan->nama_ruangan ?? '-' }}" readonly>
            </div>

            {{-- Upload Foto Kegiatan --}}
            <div class="mb-3">
                <label for="foto_kegiatan" class="form-label">Foto Kegiatan</label>
                <input type="file" class="form-control" name="foto_kegiatan" accept="image/*" required>
            </div>

            {{-- Tombol Submit --}}
            <button type="submit" class="btn btn-primary">Simpan Absensi</button>
        </form>
    </div>
@endsection