@php
function selisih($jam_masuk, $jam_keluar)
        {
            list($h, $m, $s) = explode(":", $jam_masuk);
            $dtAwal = mktime($h, $m, $s, "1", "1", "1");
            list($h, $m, $s) = explode(":", $jam_keluar);
            $dtAkhir = mktime($h, $m, $s, "1", "1", "1");
            $dtSelisih = $dtAkhir - $dtAwal;
            $totalmenit = $dtSelisih / 60;
            $jam = explode(".", $totalmenit / 60);
            $sisamenit = ($totalmenit / 60) - $jam[0];
            $sisamenit2 = $sisamenit * 60;
            $jml_jam = $jam[0];
            return $jml_jam . ":" . round($sisamenit2);
        }
@endphp

@foreach ($presensi as $d)
@php
    $pathFotoIn = Storage::url('upload/absensi/' . $d -> foto_in);
    $pathFotoOut = Storage::url('upload/absensi/' . $d -> foto_out);
@endphp
@if ($d->status == "h")
    <tr>
        <td>{{ $loop -> iteration }}</td>
        <!-- <td>{{ $d -> nik }}</td> -->
        <td>{{ $d -> nama_lengkap }}</td>
        <td>{{ $d -> nama_ruangan }}</td>
        <td>{{ $d -> nama_jam_kerja }}</td>
        <td>{{ $d -> jam_in }}</td>
        <td><img src="{{ url($pathFotoIn) }}" class="avatar" alt=""></td>
        <td>{!! $d -> jam_out != null ? $d -> jam_out : '<span class="badge bg-warning" style="color: white">Belum Absen</span>' !!}</td>
        <td>
            @if ($d -> jam_out != null)
            <img src="{{ url($pathFotoOut) }}" class="avatar" alt="">
            @else
            <img src="{{ asset('assets/img/sample/avatar/avatar1.jpg') }}" class="avatar" alt="">
            @endif
        </td>
        <td>
            @if ($d->status == "h")
            <span class="badge bg-success" style="color:white">Hadir</span>
            @endif
        </td>
        <td>{{ $d->jenis_presensi }}</td>
        <td>
            @if ($d->jenis_presensi == "Absensi Regular")
                @if ($d->jam_in >= $d->jam_masuk)
                @php
                $jam_terlambat = selisih($d->jam_masuk, $d->jam_in);
                @endphp
                <span>Terlambat {{ $jam_terlambat }}</span>
                @else
                <span>Tepat Waktu</span>
                @endif
            @elseif ($d->jenis_presensi == "Absensi Luar")
                {{ $d->keterangan_luar }}
            @endif
        </td>
        <td>
            <div class="btn-group">
                {{-- tombol detail absensi --}}
                <a href="javascript:void(0)" class="tampilkanpeta" id="{{ $d -> id }}" >
                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-clipboard-text"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" /><path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" /><path d="M9 12h6" /><path d="M9 16h6" /></svg>
                </a>

                {{-- tombol untuk mengedit jam_in dan jam_out --}}
                <a href="{{ route('admin.indexEditDataPresensi', $d->id) }}" class="edit" style="margin-left: 5px">
                    <svg xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-edit"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" /></svg>
                </a>
    
                {{-- tombol untuk menghapus absensi --}}
                <form action="{{ route('admin.deleteDataPresensi', $d->id) }}" method="POST" style="margin-left: 5px">
                    @csrf
                    @method('DELETE')
                    <a href="#" class="delete-confirm">
                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                    </a>
                    {{-- <button type="submit" class="btn btn-danger">Hapus</button> --}}
                </form>
            </div>
        </td>
    </tr>
@elseif ($d->status == "s" || $d->status == "i")
    <tr>
        <td>{{ $loop -> iteration }}</td>
        <td>{{ $d -> nama_lengkap }}</td>
        <td>{{ $d -> nama_ruangan }}</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td>
            @if ($d->status == "i")
                <span class="badge bg-warning" style="color:white">Izin</span>
                @elseif ($d->status == "s")
                <span class="badge bg-danger" style="color:white">Sakit</span>
            @endif
        </td>
        <td></td>
        <td>{{ $d->keterangan_pengajuan }}</td>
        <td></td>
    </tr>
@elseif ($d->status == "op" || $d->status == "ok")
    <tr>
        <td>{{ $loop -> iteration }}</td>
        <td>{{ $d -> nama_lengkap }}</td>
        <td>{{ $d -> nama_ruangan }}</td>
        <td>{{ $d -> nama_jam_kerja }}</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td>
            @if ($d->status == "op")
                <span class="badge bg-warning" style="color:white">OC Pribadi</span>
                @elseif ($d->status == "s")
                <span class="badge bg-danger" style="color:white">OC Kantor</span>
            @endif
        </td>
        <td></td>
        <td>{{ $d->keterangan_presensi }}</td>
        <td></td>
    </tr>
@endif

@endforeach

<script>
    $(function(){
        $(".tampilkanpeta").click(function(e){
            var id = $(this).attr("id");
            $.ajax({
                type: 'POST',
                url: '/tampilkanpeta',
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id
                },
                cache: false,
                success: function(respond){
                    $("#loadmap").html(respond);
                }
            });
            $("#modal-tampilkanpeta").modal("show");
        });
    });

    $(".delete-confirm").click(function(e){
            var form = $(this).closest('form');
            e.preventDefault();
            //alert('haha');
            Swal.fire({
                title: "Hapus data karyawan ?",
                text: "Data akan terhapus permanen!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Hapus data"
                }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                    Swal.fire({
                    title: "Terhapus!",
                    text: "Data berhasil dihapus.",
                    icon: "success"
                    });
                }
            });
        });
</script>