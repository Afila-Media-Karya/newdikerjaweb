<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STRUKTUR JABATAN</title>
    <link rel="stylesheet" href="{{public_path('admin/assets/css/bootstrap.min.css')}}">
    <style>
        .box-header {
            border-radius: 8px 8px 0px 0px;
            background: #442292;
            padding: 16px;
            color: #fff;
            font-size: 18px;
            font-style: normal;
            font-weight: 700;
            display: flex; /* Membuat konten sejajar */
            align-items: center; /* Pusatkan elemen secara vertikal */
            margin-top:20px;
        }

        .box-header img {
            margin-right: 8px; /* Jarakkan gambar dari teks */
            width: 25px; /* Sesuaikan lebar gambar sesuai kebutuhan */
            height: 25px; /* Sesuaikan tinggi gambar sesuai kebutuhan */
        }

        .box-header span{
            position: relative;
            bottom : 6px;
        }

        .table-group{
            margin-top:20px;
            font-size:18px;
        }

        .table-group thead th {
            border: 1px solid #dee2e6;
        }

        .table-group tfoot th {
            border: 1px solid #dee2e6;
        }

        /* Tambahkan padding pada sel */
        .table-group td, .table-group th {
            border: 1px solid #dee2e6;
            padding: 0.5rem; /* Sesuaikan sesuai kebutuhan Anda */
            font-size:20px;
        }

        .table-group tbody tr:last-child td {
            border-bottom: 1px solid #dee2e6;
        }

    </style>
</head>
<body>

    <h5 class="text-center">STRUKTUR JABATAN</h5>
    <table class="table-group table-bordered gy-7" style="width:100%; font-size:18px !important;">
        <thead class="text-center">
            <tr style="background:#E1F5FE; font-weight:bold;">
                <td>NO</td>
                <td>NAMA</td>
                <td>STRUKTUR</td>
                <td>NAMA JABATAN</td>
                <td>ESELON</td>
                <td>KELAS JABATAN</td>
                <td style="width:200px">PAGU TPP</td>
                <td>LOKASI ABSEN</td>
                <td>LOKASI APEL</td>
            </tr>
        </thead>
        <tbody>

            @php
                $no = 1;
            @endphp
           @foreach($result as $key => $value)
                <tr>
                    <td class="text-center">{{$no++}}</td>
                    <td>{{!is_null($value->nama_pegawai) ? $value->nama_pegawai : '-'}} <br> {{$value->nip}} </td>
                    <td>{{$value->nama_struktur}}</td>
                    <td>{{$value->nama_jabatan}}</td>
                    @if($value->level_jabatan < 7 )
                        <td class="text-center">{{$value->jenis_jabatan}}</td>
                    @else
                        <td>-</td>
                    @endif
                    <td class="text-center">{{$value->kelas_jabatan}}</td>
                    <td class="text-center">Rp {{number_format($value->pagu_tpp)}}</td>
                    <td>{{$value->nama_lokasi}}</td>
                    <td>{{$value->lokasi_apel}}</td>
                </tr>

                @if (count($value->bawahan) > 0)
                    @foreach($value->bawahan as $k => $v)
                        <tr>
                            <td class="text-center">{{ $no++ }}</td>
                            <td>{{!is_null($v->nama_pegawai) ? $v->nama_pegawai : '-'}} <br> {{$v->nip}} </td>
                            <td style="padding-left: 25px; width: 400px;"> <span>{{$v->nama_struktur }}</span> </td>
                            <td>{{ $v->nama_jabatan }}</td>
                            @if($v->level_jabatan < 7 )
                                <td class="text-center">{{$v->jenis_jabatan}}</td>
                            @else
                                <td>-</td>
                            @endif
                            <td class="text-center">{{ $v->kelas_jabatan }}</td>
                            <td class="text-center">Rp {{number_format($v->pagu_tpp)}}</td>
                            <td>{{ $v->nama_lokasi }}</td>
                            <td>{{$v->lokasi_apel}}</td>
                        </tr>

                        @if (count($v->bawahan) > 0)
                            @foreach($v->bawahan as $i => $n)
                                <tr>
                                    <td class="text-center">{{ $no++ }}</td>
                                    <td>{{!is_null($n->nama_pegawai) ? $n->nama_pegawai : '-'}} <br> {{$n->nip}} </td>
                                    <td style="padding-left: 50px; width: 400px;"> <span>{{$n->nama_struktur }}</span> </td>
                                    <td>{{ $n->nama_jabatan }}</td>
                                    @if($n->level_jabatan < 7 )
                                        <td class="text-center">{{$n->jenis_jabatan}}</td>
                                    @else
                                        <td>-</td>
                                    @endif
                                    <td class="text-center">{{ $n->kelas_jabatan }}</td>
                                    <td class="text-center">Rp {{number_format($n->pagu_tpp)}}</td>
                                    <td>{{ $n->nama_lokasi }}</td>
                                    <td>{{$n->lokasi_apel}}</td>
                                </tr>

                                @if (count($n->bawahan) > 0)
                                    @foreach($n->bawahan as $l => $o)
                                        <tr>
                                            <td class="text-center">{{ $no++ }}</td>
                                            <td>{{!is_null($o->nama_pegawai) ? $o->nama_pegawai : '-'}} <br> {{$o->nip}} </td>
                                            <td style="padding-left: 75px; width: 400px;"> <span>{{$o->nama_struktur }}</span> </td>
                                            <td>{{ $o->nama_jabatan }}</td>
                                            @if($o->level_jabatan < 7 )
                                                <td class="text-center">{{$o->jenis_jabatan}}</td>
                                            @else
                                                <td>-</td>
                                            @endif
                                            <td class="text-center">{{ $o->kelas_jabatan }}</td>
                                            <td class="text-center">Rp {{number_format($o->pagu_tpp)}}</td>
                                            <td>{{ $o->nama_lokasi }}</td>
                                            <td>{{$o->lokasi_apel}}</td>
                                        </tr>

                                        @if (count($o->bawahan) > 0)
                                            @foreach($o->bawahan as $l => $q)
                                                <tr>
                                                    <td class="text-center">{{ $no++ }}</td>
                                                    <td>{{!is_null($q->nama_pegawai) ? $q->nama_pegawai : '-'}} <br> {{$q->nip}} </td>
                                                    <td style="padding-left: 100px; width: 400px;"> <span>{{$q->nama_struktur }}</span> </td>
                                                    <td>{{ $q->nama_jabatan }}</td>
                                                    @if($q->level_jabatan < 7 )
                                                        <td class="text-center">{{$q->jenis_jabatan}}</td>
                                                    @else
                                                        <td>-</td>
                                                    @endif
                                                    <td class="text-center">{{ $q->kelas_jabatan }}</td>
                                                    <td class="text-center">Rp {{number_format($q->pagu_tpp)}}</td>
                                                    <td>{{ $q->nama_lokasi }}</td>
                                                    <td>{{$q->lokasi_apel}}</td>
                                                </tr>

                                                @if (count($q->bawahan) > 0)
                                                    @foreach($q->bawahan as $t => $b)
                                                        <tr>
                                                            <td class="text-center">{{ $no++ }}</td>
                                                            <td>{{!is_null($b->nama_pegawai) ? $b->nama_pegawai : '-'}} <br> {{$b->nip}} </td>
                                                            <td style="padding-left: 100px; width: 400px;"> <span>{{$b->nama_struktur }}</span> </td>
                                                            <td>{{ $b->nama_jabatan }}</td>
                                                            @if($b->level_jabatan < 7 )
                                                                <td class="text-center">{{$b->jenis_jabatan}}</td>
                                                            @else
                                                                <td>-</td>
                                                            @endif
                                                            <td class="text-center">{{ $b->kelas_jabatan }}</td>
                                                            <td class="text-center">Rp {{number_format($b->pagu_tpp)}}</td>
                                                            <td>{{ $b->nama_lokasi }}</td>
                                                            <td>{{$b->lokasi_apel}}</td>
                                                        </tr>

                                                        @if (count($b->bawahan) > 0)
                                                            @foreach($b->bawahan as $t => $e)
                                                                <tr>
                                                                    <td class="text-center">{{ $no++ }}</td>
                                                                    <td>{{!is_null($e->nama_pegawai) ? $e->nama_pegawai : '-'}} <br> {{$e->nip}} </td>
                                                                    <td style="padding-left: 100px; width: 400px;"> <span>{{$e->nama_struktur }}</span> </td>
                                                                    <td>{{ $e->nama_jabatan }}</td>
                                                                    @if($e->level_jabatan < 7 )
                                                                        <td class="text-center">{{$e->jenis_jabatan}}</td>
                                                                    @else
                                                                        <td>-</td>
                                                                    @endif
                                                                    <td class="text-center">{{ $e->kelas_jabatan }}</td>
                                                                    <td class="text-center">Rp {{number_format($e->pagu_tpp)}}</td>
                                                                    <td>{{ $e->nama_lokasi }}</td>
                                                                    <td>{{$e->lokasi_apel}}</td>
                                                                </tr>
                                                            @endforeach
                                                        @endif

                                                    @endforeach
                                                @endif

                                            @endforeach
                                        @endif

                                    @endforeach
                                @endif

                            @endforeach
                        @endif

                    @endforeach
                @endif

           @endforeach
        </tbody>
    </table>

</body>
</html>
