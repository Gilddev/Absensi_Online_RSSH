@extends('layouts.presensi')
@section('header')
    <!-- App Header -->
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="javascript:;" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Absensi Oncall</div>
        <div class="right"></div>
    </div>
    <!-- * App Header -->

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
    crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endsection

@section('content')

<div class="row mt-5">
    <div class="col">
        <div class="container">
            <form action="/presensi/storeoncall" method="POST">
                @csrf

                <!-- Tanggal Presensi -->
                <div class="mb-3 mt-3">
                    <label for="tgl_pengajuan" class="form-label">Tanggal</label>
                    <input type="text" class="form-control" value="{{ date('Y-m-d') }}" disabled>
                    <input type="hidden" name="tgl_pengajuan" value="{{ date('Y-m-d') }}">
                </div>

                <!-- Status Oncall -->
                <div class="mb-3">
                    <label for="status" class="form-label">Status Oncall</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="">-- Pilih Status --</option>
                        @if($kuota_oncall > 0)
                            <option value="pribadi">Oncall Pribadi</option>
                        @endif
                        <option value="kantor">Oncall Kantor</option>
                    </select>                    
                </div>

                <!-- Karyawan Pengganti -->
                <div class="mb-3">
                    <label for="pengganti" class="form-label">Karyawan Pengganti</label>
                    <input type="text" name="karyawan_pengganti" id="karyawan_pengganti" class="form-control" required>
                </div>

                <!-- Keterangan -->
                <div class="mb-3">
                    <label for="keterangan" class="form-label">Keterangan</label>
                    <textarea name="keterangan" id="keterangan" rows="4" class="form-control" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Simpan Pengajuan Oncall</button>
            </form>
        </div>   
    </div>
</div>
@endsection