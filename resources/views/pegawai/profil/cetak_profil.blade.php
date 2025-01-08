<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="{{public_path('admin/assets/css/bootstrap.min.css')}}">
    <style>
        .box-header {
            border-radius: 8px 8px 0px 0px;
            background: #0D47A1;
            padding: 16px;
            color: #fff;
            font-size: 14px;
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
            font-size:14px;
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
        }

        .table-group tbody tr:last-child td {
            border-bottom: 1px solid #dee2e6;
        }

    </style>
</head>
<body>
    <div class="box-header">
        <img src="{{ public_path('admin/assets/media/icons/profil/data_pribadi.png') }}" alt="Data Pribadi">
        <span>Data Pribadi</span>
    </div>

<table id="data_pribadi" style="width:100%;border-collapse: collapse;">
    <tr style="line-height: 3;">
        <td style="padding-left:20px; padding-right:20px">
          @if($data_pribadi->foto !== null && $data_pribadi->foto !== "")
                    <img src="{{ $foto_profil }}" style="width:160px;height:160px; position:absolute;left:80px;top:100px;" alt="image">
                    <!-- <img src="https://smbd.id/wp-content/uploads/2023/10/West-Ham-United.-primetimesng.com_-e1698700480386.jpeg" style="width:160px;height:160px; position:absolute;left:80px;top:100px;" alt="image">  -->
                @else
                    <img src="{{ public_path('/admin/assets/media/avatars/blank.png') }}" style="width:160px;height:160px; position:absolute;left:80px;top:100px;" alt="image">
                @endif
        </td>
        <td style="padding-left:20px; padding-right:20px">
            <label class="form-label">Nama</label>
            <input type="text" class="form-control form-control-sm" value="{{ $data_pribadi->nama }}" disabled>
        </td>
    </tr>
    <tr style="line-height: 3;">
        <td style="padding-left:20px; padding-right:20px">
          
        </td>
        <td style="padding-left:20px; padding-right:20px">
            <label class="form-label">NIP</label>
            <input type="text" class="form-control form-control-sm" value="{{ $data_pribadi->nip }}" disabled>
        </td>
    </tr>
    <tr style="line-height: 3;">
        <td style="padding-left:20px; padding-right:20px">
            <label class="form-label">Tempat Lahir</label>
            <input type="text" class="form-control form-control-sm" value="{{ $data_pribadi->tempat_lahir }}" disabled>
        </td>
        <td style="padding-left:20px; padding-right:20px">
            <label class="form-label">Tanggal Lahir</label>
            <input type="text" class="form-control form-control-sm" value="{{ \Carbon\Carbon::parse($data_pribadi->tanggal_lahir)->format('j F Y') }}" disabled>
        </td>
    </tr>
    <tr style="line-height: 3;">
        <td style="padding-left:20px; padding-right:20px">
        @php
            $jk = $data_pribadi->jenis_kelamin == 'L' ? 'Laki Laki' : 'Perempuan'
        @endphp
            <label class="form-label">Jenis Kelamin</label>
            <input type="text" class="form-control form-control-sm" value="{{ $jk }}" disabled>
        </td>
        <td style="padding-left:20px; padding-right:20px">
            <label class="form-label">Tanggal Lahir</label>
            <input type="text" class="form-control form-control-sm" value="{{ $data_pribadi->agama }}" disabled>
        </td>
    </tr>
    <tr style="line-height: 3;">
        <td style="padding-left:20px; padding-right:20px">
            <label class="form-label">Status Perkawinan</label>
            <input type="text" class="form-control form-control-sm" value="{{ $data_pribadi->status_perkawinan }}" disabled>
        </td>
        <td style="padding-left:20px; padding-right:20px">
            <label class="form-label">TMT Pegawai</label>
            <input type="text" class="form-control form-control-sm" value="{{ $data_pribadi->tmt_pegawai && $data_pribadi->tmt_pegawai !== 'NULL' ? \Carbon\Carbon::parse($data_pribadi->tmt_pegawai)->format('j F Y') : '' }}" disabled>
        </td>
    </tr>
    <tr style="line-height: 3;">
        <td style="padding-left:20px; padding-right:20px">
            <label class="form-label">Golongan</label>
            <input type="text" class="form-control form-control-sm" value="{{ $data_pribadi->golongan }}" disabled>
        </td>
        <td style="padding-left:20px; padding-right:20px">
            <label class="form-label">TMT Golongan</label>
            <input type="text" class="form-control form-control-sm" value="{{ $data_pribadi->tmt_golongan && $data_pribadi->tmt_golongan !== 'NULL' ? \Carbon\Carbon::parse($data_pribadi->tmt_golongan)->format('j F Y') : '' }}" disabled>
        </td>
    </tr>
    <tr style="line-height: 3;">
        <td style="padding-left:20px; padding-right:20px">
            <label class="form-label">Pendidikan</label>
            <input type="text" class="form-control form-control-sm" value="{{ $data_pribadi->pendidikan }}" disabled>
        </td>
        <td style="padding-left:20px; padding-right:20px">
           <label class="form-label">Pendidikan Lulus</label>
            <input type="text" class="form-control form-control-sm" value="{{ $data_pribadi->pendidikan_lulus && $data_pribadi->pendidikan_lulus !== 'NULL' ? \Carbon\Carbon::parse($data_pribadi->pendidikan_lulus)->format('j F Y') : '' }}" disabled>
        </td>
    </tr>
    <tr style="line-height: 3;">
        <td style="padding-left:20px; padding-right:20px">
            <label class="form-label">Pendidikan Struktural</label>
            <input type="text" class="form-control form-control-sm" value="{{ $data_pribadi->pendidikan_struktural }}" disabled>
        </td>
        <td style="padding-left:20px; padding-right:20px">
           <label class="form-label">Pendidikan Struktural Lulus</label>
            <input type="text" class="form-control form-control-sm" value="{{ $data_pribadi->pendidikan_struktural_lulus && $data_pribadi->pendidikan_struktural_lulus !== 'NULL' ? \Carbon\Carbon::parse($data_pribadi->pendidikan_struktural_lulus)->format('j F Y') : '' }}" disabled>
        </td>
    </tr>
</table>

<div class="box-header">
    <img src="{{ public_path('admin/assets/media/icons/profil/pendidikan_formal.png') }}" alt="Data Pribadi">
    <span>Riwayat Pendidikan Formal</span>
</div>

<table class="table-group table-row-gray-300 gy-7">
    <thead class="text-center">
        <tr class="fw-bolder fs-6 text-gray-800">
            <th rowspan="2">No</th>
            <th rowspan="2">Tingkat Pendidikan</th>
            <th rowspan="2">Fakultas</th>
            <th colspan="3">Sekolah / Universitas</th>
            <th colspan="2">STTB/Ijazah</th>
        </tr>
        <tr>
            <th>Nomor</th>
            <th>Tanggal</th>
            <th>Nama Sekolah / Rektor</th>
            <th>Nama</th>
            <th>Lokasi (Kab/Kota)</th>
        </tr>
    </thead>
    <tbody>
        @if(count($riwayat_pendidikan_formal) > 0)
            @foreach($riwayat_pendidikan_formal as $a => $b)
                <tr class="text-center">
                    <td>{{$a+1}}</td>
                    <td>{{$b->pendidikan}}</td>
                    <td>{{$b->fakultas}}</td>
                    <td>{{$b->nomor_ijazah}}</td>
                    <td>{{$b->tanggal}}</td>
                    <td>{{$b->pimpinan}}</td>
                    <td>{{$b->nama_sekolah}}</td>
                    <td>{{$b->alamat}}</td>            
                </tr>
            @endforeach
          @else
          <tr class="text-center">
            <td colspan="8">Belum ada data</td>
        </tr>  
        @endif
    </tbody>
</table>

<div class="box-header">
    <img src="{{ public_path('admin/assets/media/icons/profil/pendidikan_non_formal.png') }}" alt="Data Pribadi">
    <span>Riwayat Pendidikan Non Formal</span>
</div>

    <table  class="table-group table-row-gray-300 gy-7">
        <thead class="text-center">
            <tr class="fw-bolder fs-6 text-gray-800">
                <th rowspan="2">No</th>
                <th rowspan="2">Nama Kursus/Seminar/Lokarya</th>
                <th colspan="2">Tanggal</th>
                <th colspan="3">Ijazah/Tanda Lulus/Surat Keterangan</th>
                <th rowspan="2">Instansi Penyelenggara</th>
                <th rowspan="2">Tempat</th>

            </tr>
            <tr>
                <th>Mulai</th>
                <th>Selesai</th>
                <th>Nomor</th>
                <th>Tanggal</th>
                <th>Nama Pejabat</th>
            </tr>
        </thead>
        <tbody>
            @if(count($riwayat_pendidikan_non_formal) > 0)
                @foreach($riwayat_pendidikan_non_formal as $a => $b)
                    <tr class="text-center">
                        <td>{{$a+1}}</td>
                        <td>{{$b->nama_kursus}}</td>
                        <td>{{$b->tanggal_mulai}}</td>
                        <td>{{$b->tanggal_selesai}}</td>
                        <td>{{$b->nomor}}</td>
                        <td>{{$b->tanggal}}</td>
                        <td>{{$b->nama_pejabat}}</td>
                        <td>{{$b->penyelenggara}}</td>
                        <td>{{$b->nama_tempat}}</td>
                    </tr>
                @endforeach
                @else
                <tr class="text-center">
                    <td colspan="9">Belum ada data</td>
                </tr>
            @endif
        </tbody>
    </table>

<div class="box-header">
    <img src="{{ public_path('admin/assets/media/icons/profil/kepangkatan.png') }}" alt="Data Pribadi">
    <span>Riwayat Kepangkatan</span>
</div>

    <table class="table-group table-row-gray-300 gy-7">
        <thead class="text-center">
            <tr class="fw-bolder fs-6 text-gray-800">
                <th rowspan="2">No</th>
                <th rowspan="2">Gol. Ruang</th>
                <th colspan="3">Masa Kerja</th>
                <th colspan="3">Surat Keputusan</th>
                <th rowspan="2">TMT</th>
                <th rowspan="2">Unit Kerja</th>
            </tr>
            <tr>
                <th>Tahun</th>
                <th>Bulan</th>
                <th>Gaji Pokok</th>
                <th>Nomor</th>
                <th>Tanggal</th>
                <th>Jabatan Pendantanganan </th>
            </tr>
        </thead>
        <tbody>
           @if(count($riwayat_kepangkatan) > 0)
           @foreach($riwayat_kepangkatan as $a => $b)
                <tr class="text-center">
                    <td>{{$a+1}}</td>
                    <td>{{$b->golongan}}</td>
                    <td>{{$b->tahun}}</td>
                    <td>{{$b->bulan}}</td>
                    <td>Rp. {{number_format($b->gaji_pokok)}}</td>
                    <td>{{$b->nomor}}</td>
                    <td>{{$b->tanggal}}</td>
                    <td>{{$b->pejabat_pendantanganan}}</td>
                    <td>{{$b->tmt}}</td>
                    <td>{{$b->nama_unit_kerja}}</td>
                </tr>
           @endforeach
            @else
            <tr class="text-center">
                    <td colspan="10">Belum ada data</td>
                </tr>
           @endif
        </tbody>
    </table>

<div class="box-header">
    <img src="{{ public_path('admin/assets/media/icons/profil/jabatan.png') }}" alt="Data Pribadi">
    <span>Riwayat Jabatan</span>
</div>

    <table class="table-group table-row-gray-300 gy-7">
        <thead class="text-center">
            <tr class="fw-bolder fs-6 text-gray-800">
                <th rowspan="2">No</th>
                <th rowspan="2">Nama Jabatan</th>
                <th colspan="4">Surat Keputusan</th>
                <th rowspan="2">TMT</th>
                <th rowspan="2">Unit Kerja</th>
            </tr>
            <tr>
                <th>Gol. Ruang</th>
                <th>Nomor</th>
                <th>Tanggal</th>
                <th>Pejabat Pendantanganan </th>
            </tr>
        </thead>
        <tbody>
           @if(count($riwayat_jabatan) > 0)
            @foreach($riwayat_jabatan as $a => $b)
                <tr class="text-center">
                    <td>{{$a+1}}</td>
                    <td>{{$b->nama_jabatan}}</td>
                    <td>{{$b->golongan}}</td>
                    <td>{{$b->nomor}}</td>
                    <td>{{$b->tanggal}}</td>
                    <td>{{$b->pejabat_pendantanganan}}</td>
                    <td>{{$b->tmt}}</td>
                    <td>{{$b->nama_unit_kerja}}</td>
                </tr>
           @endforeach
           @else
            <tr class="text-center">
                    <td colspan="8">Belum ada data</td>
                </tr>
           @endif
        </tbody>
    </table>

<div class="box-header">
    <img src="{{ public_path('admin/assets/media/icons/profil/hukdis.png') }}" alt="Data Pribadi">
    <span>Catatan Hukuman Dinas</span>
</div>

    <table class="table-group table-row-gray-300 gy-7">
        <thead class="text-center">
            <tr class="fw-bolder fs-6 text-gray-800">
                <th rowspan="2">No</th>
                <th rowspan="2">Kategori Hukum</th>
                <th rowspan="2">Nama Hukuman</th>
                <th colspan="2">SK</th>
                <th colspan="2">Lama</th>
                <th rowspan="2">Keterangan</th>
            </tr>
            <tr>
                <th>Nama SK</th>
                <th>Tanggal SK</th>
                <th>Tanggal Mulai</th>
                <th>Tanggal Selesai</th>
            </tr>
        </thead>
        <tbody>
            @if(count($catatan_hukuman_dinas) > 0)
                @foreach($catatan_hukuman_dinas as $a => $b)
                    <tr class="text-center">
                        <td>{{$a+1}}</td>
                        <td>{{$b->kategori_hukuman}}</td>
                        <td>{{$b->nama_hukuman}}</td>
                        <td>{{$b->nama_sk}}</td>
                        <td>{{$b->tanggal_sk}}</td>
                        <td>{{$b->tanggal_mulai}}</td>
                        <td>{{$b->tanggal_selesai}}</td>
                        <td>{{$b->keterangan_pelanggaran}}</td>
                    </tr>
                @endforeach
            @else
            <tr class="text-center">
                    <td colspan="8">Belum ada data</td>
                </tr>
            @endif
        </tbody>
    </table>

<div class="box-header">
    <img src="{{ public_path('admin/assets/media/icons/profil/diklat.png') }}" alt="Data Pribadi">
    <span>Riwayat Diklat Struktural</span>
</div>

       <table class="table-group table-row-gray-300 gy-7">
        <thead class="text-center">
            <tr class="fw-bolder fs-6 text-gray-800">
                <th rowspan="2">No</th>
                <th colspan="2">Diklat Struktural</th>
                <th colspan="2">Tanggal</th>
                <th rowspan="2">Jumlah Jam</th>
                <th colspan="3">STTB</th>
                <th colspan="2">Instansi Penyelenggara</th>
            </tr>
            <tr>
                <th>Kategori</th>
                <th>Nama</th>
                <th>Mulai</th>
                <th>Selesai</th>
                <th>Nomor</th>
                <th>Tanggal</th>
                <th>Jabatan Pendantanganan</th>
                <th>Instansi</th>
                <th>Lokasi</th>
            </tr>
        </thead>
        <tbody>
            @if(count($diklat_struktral) > 0)
                @foreach($diklat_struktral as $a => $b)
                    <tr class="text-center">
                        <td>{{$a+1}}</td>
                        <td>{{$b->kategori_diklat_struktural}}</td>
                        <td>{{$b->nama_diklat_struktural}}</td>
                        <td>{{$b->tanggal_mulai}}</td>
                        <td>{{$b->tanggal_selesai}}</td>
                        <td>{{$b->jumlah_jam}}</td>
                        <td>{{$b->nomor_sttb}}</td>
                        <td>{{$b->tanggal}}</td>
                        <td>{{$b->pejabat_pendantanganan}}</td>
                        <td>{{$b->nama_instansi}}</td>
                        <td>{{$b->lokasi}}</td>
                    </tr>
                @endforeach
            @else
        <tr class="text-center">
                    <td colspan="11">Belum ada data</td>
                </tr>
            @endif
            
        </tbody>
    </table>

<div class="box-header">
    <img src="{{ public_path('admin/assets/media/icons/profil/diklat.png') }}" alt="Data Pribadi">
    <span>Riwayat Diklat Fungsional</span>
</div>    

    <table id="kt_table_data" class="table-group table-row-gray-300 gy-7">
        <thead class="text-center">
            <tr class="fw-bolder fs-6 text-gray-800">
                <th rowspan="2">No</th>
                <th rowspan="2">Diklat Fungsional</th>
                <th colspan="2">Tanggal</th>
                <th rowspan="2">Jumlah Jam</th>
                <th colspan="3">STTB</th>
                <th colspan="2">Instansi Penyelenggara</th>
            </tr>
            <tr>
                <th>Mulai</th>
                <th>Selesai</th>
                <th>Nomor</th>
                <th>Tanggal</th>
                <th>Jabatan Pendantanganan</th>
                <th>Instansi</th>
                <th>Lokasi</th>
            </tr>
        </thead>
        <tbody>
            @if(count($diklat_fungsional) > 0)
                @foreach($diklat_fungsional as $a => $b)
                    <tr class="text-center">
                        <td>{{$a+1}}</td>
                        <td>{{$b->nama_diklat_fungsional}}</td>
                        <td>{{$b->tanggal_mulai}}</td>
                        <td>{{$b->tanggal_selesai}}</td>
                        <td>{{$b->jumlah_jam}}</td>
                        <td>{{$b->nomor_sttb}}</td>
                        <td>{{$b->tanggal}}</td>
                        <td>{{$b->pejabat_pendantanganan}}</td>
                        <td>{{$b->nama_instansi}}</td>
                        <td>{{$b->lokasi}}</td>
                    </tr>
                @endforeach
            @else
                <tr class="text-center">
                    <td colspan="10">Belum ada data</td>
                </tr>
            @endif
            
        </tbody>
    </table>

<div class="box-header">
    <img src="{{ public_path('admin/assets/media/icons/profil/diklat.png') }}" alt="Data Pribadi">
    <span>Riwayat Diklat Teknis</span>
</div> 

    <table class="table-group table-row-gray-300 gy-7">
        <thead class="text-center">
            <tr class="fw-bolder fs-6 text-gray-800">
                <th rowspan="2">No</th>
                <th rowspan="2">Diklat Fungsional</th>
                <th colspan="2">Tanggal</th>
                <th rowspan="2">Jumlah Jam</th>
                <th colspan="3">STTB</th>
                <th colspan="2">Instansi Penyelenggara</th>
            </tr>
            <tr>
                <th>Mulai</th>
                <th>Selesai</th>
                <th>Nomor</th>
                <th>Tanggal</th>
                <th>Jabatan Pendantanganan</th>
                <th>Instansi</th>
                <th>Lokasi</th>
            </tr>
        </thead>
        <tbody>
            @if(count($diklat_teknis) > 0)
                @foreach($diklat_teknis as $a => $b)
                    <tr class="text-center">
                        <td>{{$a+1}}</td>
                        <td>{{$b->nama_diklat_teknis}}</td>
                        <td>{{$b->tanggal_mulai}}</td>
                        <td>{{$b->tanggal_selesai}}</td>
                        <td>{{$b->jumlah_jam}}</td>
                        <td>{{$b->nomor_sttb}}</td>
                        <td>{{$b->tanggal}}</td>
                        <td>{{$b->pejabat_pendantanganan}}</td>
                        <td>{{$b->nama_instansi}}</td>
                        <td>{{$b->lokasi}}</td>
                    </tr>
                @endforeach
            @else
                <tr class="text-center">
                    <td colspan="10">Belum ada data</td>
                </tr>
            @endif
            
        </tbody>
    </table>

<div class="box-header">
    <img src="{{ public_path('admin/assets/media/icons/profil/penghargaan.png') }}" alt="Data Penghargaan">
    <span>Riwayat Penghargaan</span>
</div> 

    <table id="kt_table_data" class="table-group table-row-gray-300 gy-7">
        <thead class="text-center">
            <tr class="fw-bolder fs-6 text-gray-800">
                <th rowspan="2">No</th>
                <th rowspan="2">Nama Penghargaan</th>
                <th colspan="3">Surat Keputusan</th>
                <th colspan="2">Instansi Penyelenggara</th>
            </tr>
            <tr>
                <th>Nomor</th>
                <th>Tanggal</th>
                <th>Jabatan Pendantanganan</th>
                <th>Instansi</th>
                <th>Lokasi</th>
            </tr>
        </thead>
        <tbody>
           @if(count($riwayat_penghargaan) > 0)
                @foreach($riwayat_penghargaan as $a => $b)
                    <tr class="text-center">
                        <td>{{$a+1}}</td>
                        <td>{{$b->nama_penghargaan}}</td>
                        <td>{{$b->nomor_surat_keputusan}}</td>
                        <td>{{$b->tanggal}}</td>
                        <td>{{$b->pejabat_pendantanganan}}</td>
                        <td>{{$b->nama_instansi}}</td>
                        <td>{{$b->lokasi}}</td>
                    </tr>
            @endforeach
            @else
                <tr class="text-center">
                    <td colspan="7">Belum ada data</td>
                </tr>
           @endif
        </tbody>
    </table>

<div class="box-header">
    <img src="{{ public_path('admin/assets/media/icons/profil/istri.png') }}" alt="Data Istri">
    <span>Riwayat Istri</span>
</div> 

    <table class="table-group table-row-gray-300 gy-7">
        <thead class="text-center">
            <tr class="fw-bolder fs-6 text-gray-800">
                <th>No</th>
                <th>Nama Istri</th>
                <th>Tempat Tanggal Lahir</th>
                <th>Status Pernikahan</th>
                <th>Memperoleh Tunjangan</th>
                <th>Pendidikan</th>
                <th>Pekerjaan</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
           @if(count($riwayat_istri) > 0)
                @foreach($riwayat_istri as $a => $b)
                    <tr class="text-center">
                        <td>{{$a+1}}</td>
                        <td>{{$b->nama_istri}}</td>
                        <td>{{$b->tempat_lahir}}, {{$b->tanggal_lahir}}</td>
                        <td>{{$b->status_perkawinan}}</td>
                        <td>{{$b->memperoleh_tunjangan}}</td>
                        <td>{{$b->pendidikan}}</td>
                        <td>{{$b->pekerjaan}}</td>
                        <td>{{$b->keterangan}}</td>
                    </tr>
            @endforeach
            @else
                <tr class="text-center">
                    <td colspan="8">Belum ada data</td>
                </tr>
           @endif
        </tbody>
    </table>

<div class="box-header">
    <img src="{{ public_path('admin/assets/media/icons/profil/anak.png') }}" alt="Data Istri">
    <span>Riwayat Anak</span>
</div> 

    <table class="table-group table-row-gray-300 gy-7">
        <thead class="text-center">
            <tr class="fw-bolder fs-6 text-gray-800">
                <th>No</th>
                <th>Nama Anak</th>
                <th>Jenis Kelamin</th>
                <th>Tempat Tanggal Lahir</th>
                <th>Status Pernikahan</th>
                <th>Memperoleh Tunjangan</th>
                <th>Pendidikan</th>
                <th>Pekerjaan</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
           @if(count($riwayat_anak) > 0)
                @foreach($riwayat_anak as $a => $b)
                    <tr class="text-center">
                        <td>{{$a+1}}</td>
                        <td>{{$b->nama_anak}}</td>
                        <td>{{$b->jk}}</td>
                        <td>{{$b->tempat_lahir}}, {{$b->tanggal_lahir}}</td>
                        <td>{{$b->status_perkawinan}}</td>
                        <td>{{$b->memperoleh_tunjangan}}</td>
                        <td>{{$b->pendidikan}}</td>
                        <td>{{$b->pekerjaan}}</td>
                        <td>{{$b->keterangan}}</td>
                    </tr>
            @endforeach
            @else
                 <tr class="text-center">
                    <td colspan="9">Belum ada data</td>
                </tr>
           @endif
        </tbody>
    </table>

<div class="box-header">
    <img src="{{ public_path('admin/assets/media/icons/profil/riwayat_orangtua.png') }}" alt="Data Istri">
    <span>Riwayat Orang Tua</span>
</div> 

    <table id="kt_table_data" class="table-group table-row-gray-300 gy-7">
        <thead class="text-center">
            <tr class="fw-bolder fs-6 text-gray-800">
                <th>No</th>
                <th>Nama Orang Tua</th>
                <th>Jenis Kelamin</th>
                <th>Tempat Tanggal Lahir</th>
                <th>Pendidikan</th>
                <th>Pekerjaan</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
           @if(count($riwayat_orang_tua) > 0)
                @foreach($riwayat_orang_tua as $a => $b)
                        <tr class="text-center">
                            <td>{{$a+1}}</td>
                            <td>{{$b->nama_orang_tua}}</td>
                            <td>{{$b->jk}}</td>
                            <td>{{$b->tempat_lahir}}, {{$b->tanggal_lahir}}</td>
                            <td>{{$b->pendidikan}}</td>
                            <td>{{$b->pekerjaan}}</td>
                            <td>{{$b->keterangan}}</td>
                        </tr>
                @endforeach
            @else
            <tr class="text-center">
                    <td colspan="7">Belum ada data</td>
                </tr>
           @endif
        </tbody>
    </table>

    <div class="box-header">
        <img src="{{ public_path('admin/assets/media/icons/profil/saudara.png') }}" alt="Data Istri">
        <span>Riwayat Saudara</span>
    </div> 


        <table class="table-group table-row-gray-300 gy-7">
        <thead class="text-center">
            <tr class="fw-bolder fs-6 text-gray-800">
                <th>No</th>
                <th>Nama Saudara</th>
                <th>Jenis Kelamin</th>
                <th>Tempat Tanggal Lahir</th>
                <th>Pendidikan</th>
                <th>Pekerjaan</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
           @if(count($riwayat_saudara) > 0)
                @foreach($riwayat_saudara as $a => $b)
                    <tr class="text-center">
                        <td>{{$a+1}}</td>
                        <td>{{$b->nama_saudara}}</td>
                        <td>{{$b->jk}}</td>
                        <td>{{$b->tempat_lahir}}, {{$b->tanggal_lahir}}</td>
                        <td>{{$b->pendidikan}}</td>
                        <td>{{$b->pekerjaan}}</td>
                        <td>{{$b->keterangan}}</td>
                    </tr>
            @endforeach
           @else
           
           <tr class="text-center">
                    <td colspan="7">Belum ada data</td>
             </tr>
           
           @endif
        </tbody>
    </table>

    <div class="box-header">
        <img src="{{ public_path('admin/assets/media/icons/profil/tambahan.png') }}" alt="Data Istri">
        <span>Riwayat Tambahan</span>
    </div> 

        <h5 class="text-center">Riwayat Keahlian</h5>
    <table id="kt_table_data" class="table-group table-row-gray-300 gy-7">
        <thead class="text-center">
            <tr class="fw-bolder fs-6 text-gray-800">
                <th>No</th>
                <th>Nama Keahlian</th>
                <th>Level Keahlian</th>
                <th>Tanggal Mulai Keahlian</th>
                <th>Pelatihan</th>
                <th>Predikat</th>
            </tr>
        </thead>
        <tbody>
           @if(count($riwayat_keahlian) > 0)
           @foreach($riwayat_keahlian as $a => $b)
                <tr class="text-center">
                    <td>{{$a+1}}</td>
                    <td>{{$b->nama_keahlian}}</td>
                    <td>{{$b->level_keahlian}}</td>
                    <td>{{$b->tanggal}}</td>
                    <td>{{$b->pelatihan}}</td>
                    <td>{{$b->predikat}}</td>
                </tr>
           @endforeach
           @else

           <tr class="text-center">
                    <td colspan="6">Belum ada data</td>
             </tr>

           @endif
        </tbody>
    </table>

    <h5 class="text-center">Riwayat Kemampuan Bahasa</h5>
    <table class="table-group table-row-gray-300 gy-7">
        <thead class="text-center">
            <tr class="fw-bolder fs-6 text-gray-800">
                <th rowspan="2">No</th>
                <th rowspan="2">Bahasa</th>
                <th colspan="4">Level Keahlian</th>
                <th rowspan="2">Tanggal Mulai Keahlian</th>
                <th rowspan="2">Pelatihan</th>
                <th rowspan="2">Predikat</th>
            </tr>
            <tr>
                <th>Membaca</th>
                <th>Mendengarkan</th>
                <th>Menulis</th>
                <th>Berbicara</th>
            </tr>
        </thead>
        <tbody>
           @if(count($riwayat_bahasa) > 0)

           @foreach($riwayat_bahasa as $a => $b)
                <tr class="text-center">
                    <td>{{$a+1}}</td>
                    <td>{{$b->nama_bahasa}}</td>
                    <td>{{$b->level_keahlian_membaca}}</td>
                    <td>{{$b->level_keahlian_mendengarkan}}</td>
                    <td>{{$b->level_keahlian_menulis}}</td>
                    <td>{{$b->level_keahlian_berbicara}}</td>
                    <td>{{$b->tanggal}}</td>
                    <td>{{$b->pelatihan}}</td>
                    <td>{{$b->predikat}}</td>
                </tr>
           @endforeach

           @else

           <tr class="text-center">
                    <td colspan="9">Belum ada data</td>
             </tr>

           @endif
        </tbody>
    </table>

</body>
</html>
