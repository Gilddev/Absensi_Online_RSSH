@extends('layouts.karu.tabler')
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <!-- Page pre-title -->
                <div class="page-pretitle">
                  Overview
                </div>
                <h2 class="page-title mt-3 mb-2">
                  Halaman Dashboard Kepala Ruangan
                </h2>
                <small>
                  {{ date("d-m-Y",strtotime(date('Y-m-d')))}}
                </small>
            </div>
        </div>
    </div>
</div>
@endsection