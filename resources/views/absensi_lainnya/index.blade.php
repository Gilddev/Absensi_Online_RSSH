@extends('layouts.presensi')

@section('header')
    <!-- App Header -->
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="javascript:;" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Absensi Lainnya</div>
        <div class="right"></div>
    </div>
    <!-- * App Header -->

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
    crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

@endsection

@section('content')
<div class="container">
    <h1>Absensi Lainnya</h1>
    <div class="mt-4">
        <a href="/presensi/create_luar" class="btn btn-primary btn-block">Absensi Masuk Kegiatan Luar</a>
        <small>Diisi hanya sekali</small>
        <p class="mt-1">Absensi masuk ini diisi oleh karyawan jika sedang mengikuti kegiatan diluar kantor pada saat jam dinasnya dan akan kembali ke kantor setelah kegiatan selesai sebelum jam pulang.</p>

        <a href="/presensi/create_oncall" class="btn btn-primary btn-block">Absensi Oncall</a>
        <small>Diisi hanya sekali</small>
        <p class="mt-1">Absensi ini diisi oleh karyawan yang hendak melakukan oncall pribadi maupun oncall kantor.</p>

        <a href="/presensi/create_kuliahsubuh" class="btn btn-primary btn-block">Absensi Kuliah Subuh</a>
        <small>Diisi hanya sekali</small>
        <p class="mt-1">Absensi ini diisi ketika mengikuti kegiatan kuliah subuh.</p>

        <!-- nanti tinggal tambah tombol lagi untuk Kuliah Subuh, Oncall, dll -->
    </div>
</div>
@endsection