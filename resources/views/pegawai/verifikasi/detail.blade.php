@php
    $role = hasRole();
    $path = explode('/', request()->path());
@endphp
@extends('layouts.layout')
@section('style')
    <style>

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
@endsection
@section('content')
<div class="post d-flex flex-column-fluid" id="kt_post">
        <!--begin::Container-->
        <div id="kt_content_container" class="container">
            <div class="row">

                <div class="card">
                    <div class="card-body p-0">

                        <div class="container">
                            <div class="py-5">

                                <div class="row mb-10">
                                    <div class="col-lg-4">
                                        <label class="form-label">Status</label><br>
                                        @php
                                        $status = '';
                                        $color = '';
                                        if ($data->status == '1') {
                                            $status = 'Aktif';
                                            $color = 'success';
                                        }else if($data->status == '2'){
                                             $status = 'Pensiun';
                                             $color = 'danger';
                                        }else{
                                            $status = 'Pindah';
                                            $color = 'warning';
                                        }
                                    @endphp
                                        <span class="badge badge-{{$color}}">{{$status}}</span>
                                    </div>

                                    <div class="col-lg-4">
                                        @php
                                        $verifikasi = '';
                                        $color = '';
                                        if ($data->status_verifikasi == true) {
                                            $verifikasi = 'Terverifikasi';
                                            $color = 'success';
                                        }else{
                                            $verifikasi = 'Belum terverifikasi';
                                            $color = 'danger';
                                        }
                                        @endphp
                                        <label class="form-label">Verifikasi Admin Kabupaten</label><br>
                                        <span class="badge badge-{{$color}}">{{$verifikasi}}</span>
                                    </div>

                                    <div class="col-lg-4">
                                        <label class="form-label">Verifikasi Admin OPD</label><br>
                                        @php
                                        $verifikasi_opd = '';
                                        $color = '';
                                        if ($data->verifikasi_opd == true) {
                                            $verifikasi_opd = 'Terverifikasi';
                                            $color = 'success';
                                        }else{
                                            $verifikasi_opd = 'Belum terverifikasi';
                                            $color = 'danger';
                                        }
                                    @endphp
                                        <span class="badge badge-{{$color}}">{{$verifikasi_opd}}</span>
                                    </div>

                                    
                                </div>

                                <div class="row mb-10">
                                    <div class="col-lg-6">
                                        <label class="form-label">Nama</label>
                                        <input type="text" class="form-control form-control-sm" value="{{ $data->nama }}" disabled>
                                        <small class="text-danger asal_daerah_error"></small>
                                    </div>

                                    <div class="col-lg-6">
                                        <label class="form-label">NIP</label>
                                        <input type="text" class="form-control form-control-sm" value="{{ $data->nip }}" disabled>
                                        <small class="text-danger asal_daerah_error"></small>
                                    </div>
                                </div>
                                <div class="row mb-10">
                                    <div class="col-lg-6">
                                        <label class="form-label">Tempat Lahir</label>
                                        <input type="text" class="form-control form-control-sm" value="{{ $data->tempat_lahir }}" disabled>
                                        <small class="text-danger asal_daerah_error"></small>
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="form-label">Tanggal Lahir</label>
                                        <input type="text" class="form-control form-control-sm" value="{{ \Carbon\Carbon::parse($data->tanggal_lahir)->format('j F Y') }}" disabled>
                                        <small class="text-danger asal_daerah_error"></small>
                                    </div>
                                </div>
                                <div class="row mb-10">
                                    <div class="col-lg-6">
                                        <label class="form-label">Jenis Kelamin</label>
                                        @php
                                            $jk = $data->jenis_kelamin == 'L' ? 'Laki Laki' : 'Perempuan'
                                        @endphp
                                        <input type="text" class="form-control form-control-sm" value="{{ $jk }}" disabled>
                                        <small class="text-danger asal_daerah_error"></small>
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="form-label">Agama</label>
                                        <input type="text" class="form-control form-control-sm" value="{{ $data->agama }}" disabled>
                                        <small class="text-danger asal_daerah_error"></small>
                                    </div>
                                </div>
                                <div class="row mb-10">
                                    <div class="col-lg-6">
                                        <label class="form-label">Status Perkawinan</label>
                                        <input type="text" class="form-control form-control-sm" value="{{ $data->status_perkawinan }}" disabled>
                                        <small class="text-danger asal_daerah_error"></small>
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="form-label">TMT Pegawai</label>
                                        <input type="text" class="form-control form-control-sm" value="{{ \Carbon\Carbon::parse($data->tmt_pegawai)->format('j F Y') }}" disabled>
                                        <small class="text-danger asal_daerah_error"></small>
                                    </div>
                                </div>
                                <div class="row mb-10">
                                    <div class="col-lg-6">
                                        <label class="form-label">Golongan</label>
                                        <input type="text" class="form-control form-control-sm" value="{{ $data->golongan }}" disabled>
                                        <small class="text-danger asal_daerah_error"></small>
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="form-label">TMT Golongan</label>
                                        <input type="text" class="form-control form-control-sm" value="{{ \Carbon\Carbon::parse($data->tmt_golongan)->format('j F Y') }}" disabled>
                                        <small class="text-danger asal_daerah_error"></small>
                                    </div>
                                </div>
                                <div class="row mb-10">
                                    <div class="col-lg-6">
                                        <label class="form-label">Golongan</label>
                                        <input type="text" class="form-control form-control-sm" value="{{ $data->golongan }}" disabled>
                                        <small class="text-danger asal_daerah_error"></small>
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="form-label">TMT Golongan</label>
                                        <input type="text" class="form-control form-control-sm" value="{{ \Carbon\Carbon::parse($data->tmt_golongan)->format('j F Y') }}" disabled>
                                        <small class="text-danger asal_daerah_error"></small>
                                    </div>
                                </div>
                                <div class="row mb-10">
                                    <div class="col-lg-6">
                                        <label class="form-label">Pendidikan</label>
                                        <input type="text" class="form-control form-control-sm" value="{{ $data->pendidikan }}" disabled>
                                        <small class="text-danger asal_daerah_error"></small>
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="form-label">Pendidikan Lulus</label>
                                        <input type="text" class="form-control form-control-sm" value="{{ \Carbon\Carbon::parse($data->pendidikan_lulus)->format('j F Y') }}" disabled>
                                        <small class="text-danger asal_daerah_error"></small>
                                    </div>
                                </div>
                                <div class="row mb-10">
                                    <div class="col-lg-6">
                                        <label class="form-label">Pendidikan Struktural</label>
                                        <input type="text" class="form-control form-control-sm" value="{{ $data->pendidikan_struktural }}" disabled>
                                        <small class="text-danger asal_daerah_error"></small>
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="form-label">Pendidikan Struktural Lulus</label>
                                        <input type="text" class="form-control form-control-sm" value="{{ \Carbon\Carbon::parse($data->pendidikan_struktural_lulus)->format('j F Y') }}" disabled>
                                        <small class="text-danger asal_daerah_error"></small>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-2 mb-10">
                                    <button class="btn btn-danger button-update btn-sm" data-type="0" data-uuid="{{ $params }}">
                                        <img src="{{ asset('admin/assets/media/icons/reject.svg') }}" alt="" srcset="">
                                        Reject
                                    </button>
                                    <button class="btn btn-success button-update btn-sm" data-type="1" data-uuid="{{ $params }}">
                                        <img src="{{ asset('admin/assets/media/icons/accept.svg') }}" alt="" srcset="">
                                        Accept
                                    </button>
                                </div>
                                   

                                <!--begin::Accordion-->
                                <div class="accordion" id="kt_accordion_1">
                                    <div class="accordion-item">
                                        <h4 class="accordion-header" id="kt_accordion_1_header_1">
                                            <div class="row align-items-center">
                                                <img src="{{ asset('admin/assets/media/icons/profil/pendidikan_formal.svg') }}" style="width:62px; height: 100%;position:relative;left:11px;">
                                                <div class="col">
                                                    <button class="accordion-button fs-4 fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#kt_accordion_1_body_1" aria-expanded="true" aria-controls="kt_accordion_1_body_1">
                                                        Riwayat Pendidikan Formal
                                                    </button>
                                                </div>
                                                
                                            </div>
                                        </h4>
                                        <div id="kt_accordion_1_body_1" class="accordion-collapse collapse" aria-labelledby="kt_accordion_1_header_1" data-bs-parent="#kt_accordion_1">
                                            <div class="accordion-body">
                                                     @include('pegawai.profil.pendidikan_formal.konten')  
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item">
                                        <h4 class="accordion-header" id="kt_accordion_1_header_2">
                                            <div class="row align-items-center">
                                                <img src="{{ asset('admin/assets/media/icons/profil/pendidikan_non_formal.svg') }}" style="width:62px; height: 100%;position:relative;left:11px;">
                                                <div class="col">
                                                    <button class="accordion-button fs-4 fw-bold" id="" type="button" data-bs-toggle="collapse" data-bs-target="#kt_accordion_1_body_2" aria-expanded="true" aria-controls="kt_accordion_1_body_2">
                                                        Riwayat Pendidikan Non formal
                                                    </button>
                                                </div>
                                                
                                            </div>
                                        </h4>
                                        <div id="kt_accordion_1_body_2" class="accordion-collapse collapse" aria-labelledby="kt_accordion_1_header_2" data-bs-parent="#kt_accordion_1">
                                            <div class="accordion-body">
                                                @include('pegawai.profil.pendidikan_non_formal.konten')  
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item">
                                        <h4 class="accordion-header" id="kt_accordion_1_header_3">
                                            <div class="row align-items-center">
                                                <img src="{{ asset('admin/assets/media/icons/profil/riwayat_kepangkatan.svg') }}" style="width:62px; height: 100%;position:relative;left:11px;">
                                                <div class="col">
                                                    <button class="accordion-button fs-4 fw-bold" id="" type="button" data-bs-toggle="collapse" data-bs-target="#kt_accordion_1_body_3" aria-expanded="true" aria-controls="kt_accordion_1_body_3">
                                                        Riwayat Kepangkatan
                                                    </button>
                                                </div>
                                                
                                            </div>
                                        </h4>
                                        <div id="kt_accordion_1_body_3" class="accordion-collapse collapse" aria-labelledby="kt_accordion_1_header_3" data-bs-parent="#kt_accordion_1">
                                            <div class="accordion-body">
                                            @include('pegawai.profil.riwayat_kepangkatan.konten')  
                                            </div>
                                        </div>
                                    </div>
                                
                                    <div class="accordion-item">
                                        <h4 class="accordion-header" id="kt_accordion_1_header_4">
                                            <div class="row align-items-center">
                                                <img src="{{ asset('admin/assets/media/icons/profil/riwayat_jabatan.svg') }}" style="width:62px; height: 100%;position:relative;left:11px;">
                                                <div class="col">
                                                    <button class="accordion-button fs-4 fw-bold" id="" type="button" data-bs-toggle="collapse" data-bs-target="#kt_accordion_1_body_4" aria-expanded="true" aria-controls="kt_accordion_1_body_4">
                                                        Riwayat Jabatan
                                                    </button>
                                                </div>
                                                
                                            </div>
                                        </h4>
                                        <div id="kt_accordion_1_body_4" class="accordion-collapse collapse" aria-labelledby="kt_accordion_1_header_4" data-bs-parent="#kt_accordion_1">
                                            <div class="accordion-body">
                                            @include('pegawai.profil.riwayat_jabatan.konten')  
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item">
                                        <h4 class="accordion-header" id="kt_accordion_1_header_5">
                                            <div class="row align-items-center">
                                                <img src="{{ asset('admin/assets/media/icons/profil/hukdis.svg') }}" style="width:62px; height: 100%;position:relative;left:7px;">
                                                <div class="col">
                                                    <button class="accordion-button fs-4 fw-bold" id="" type="button" data-bs-toggle="collapse" data-bs-target="#kt_accordion_1_body_5" aria-expanded="true" aria-controls="kt_accordion_1_body_5">
                                                        Catatan Hukuman Dinas
                                                    </button>
                                                </div>
                                                
                                            </div>
                                        </h4>
                                        <div id="kt_accordion_1_body_5" class="accordion-collapse collapse" aria-labelledby="kt_accordion_1_header_5" data-bs-parent="#kt_accordion_1">
                                            <div class="accordion-body">
                                            @include('pegawai.profil.catatan_hukuman_dinas.konten')  
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item">
                                        <h4 class="accordion-header" id="kt_accordion_1_header_6">
                                            <div class="row align-items-center">
                                                <img src="{{ asset('admin/assets/media/icons/profil/diklat_struktural.svg') }}" style="width:62px; height: 100%;position:relative;left:7px;">
                                                <div class="col">
                                                    <button class="accordion-button fs-4 fw-bold" id="" type="button" data-bs-toggle="collapse" data-bs-target="#kt_accordion_1_body_6" aria-expanded="true" aria-controls="kt_accordion_1_body_6">
                                                        Riwayat Diklat Struktural
                                                    </button>
                                                </div>
                                               
                                            </div>
                                        </h4>
                                        <div id="kt_accordion_1_body_6" class="accordion-collapse collapse" aria-labelledby="kt_accordion_1_header_6" data-bs-parent="#kt_accordion_1">
                                            <div class="accordion-body">
                                            @include('pegawai.profil.diklat_struktural.konten') 
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item">
                                        <h4 class="accordion-header" id="kt_accordion_1_header_7">
                                           <div class="row align-items-center">
                                                <img src="{{ asset('admin/assets/media/icons/profil/diklat_struktural.svg') }}" style="width:62px; height: 100%;position:relative;left:7px;">
                                                <div class="col">
                                                    <button class="accordion-button fs-4 fw-bold" id="" type="button" data-bs-toggle="collapse" data-bs-target="#kt_accordion_1_body_7" aria-expanded="true" aria-controls="kt_accordion_1_body_7">
                                                        Riwayat Diklat Fungsional
                                                    </button>
                                                </div>
                                            </div>
                                        </h4>
                                        <div id="kt_accordion_1_body_7" class="accordion-collapse collapse" aria-labelledby="kt_accordion_1_header_7" data-bs-parent="#kt_accordion_1">
                                            <div class="accordion-body">
                                            @include('pegawai.profil.diklat_fungsional.konten') 
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item">
                                        <h4 class="accordion-header" id="kt_accordion_1_header_8">
                                            <div class="row align-items-center">
                                                <img src="{{ asset('admin/assets/media/icons/profil/diklat_struktural.svg') }}" style="width:62px; height: 100%;position:relative;left:7px;">
                                                <div class="col">
                                                    <button class="accordion-button fs-4 fw-bold" id="" type="button" data-bs-toggle="collapse" data-bs-target="#kt_accordion_1_body_8" aria-expanded="true" aria-controls="kt_accordion_1_body_8">
                                                        Riwayat Diklat Teknis
                                                    </button>
                                                </div>
                                            </div>
                                        </h4>
                                        <div id="kt_accordion_1_body_8" class="accordion-collapse collapse" aria-labelledby="kt_accordion_1_header_8" data-bs-parent="#kt_accordion_1">
                                            <div class="accordion-body">
                                                @include('pegawai.profil.diklat_teknis.konten') 
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item">
                                        <h4 class="accordion-header" id="kt_accordion_1_header_8">
                                            <div class="row align-items-center">
                                                <img src="{{ asset('admin/assets/media/icons/profil/riwayat_penghargaan.svg') }}" style="width:62px; height: 100%;position:relative;left:7px;">
                                                <div class="col">
                                                    <button class="accordion-button fs-4 fw-bold" id="" type="button" data-bs-toggle="collapse" data-bs-target="#kt_accordion_1_body_9" aria-expanded="true" aria-controls="kt_accordion_1_body_9">
                                                        Riwayat Penghargaan
                                                    </button>
                                                </div>
                                            </div>
                                        </h4>
                                        <div id="kt_accordion_1_body_9" class="accordion-collapse collapse" aria-labelledby="kt_accordion_1_header_9" data-bs-parent="#kt_accordion_1">
                                            <div class="accordion-body">
                                             @include('pegawai.profil.riwayat_penghargaan.konten')
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item">
                                        <h4 class="accordion-header" id="kt_accordion_1_header_8">
                                            <div class="row align-items-center">
                                                <img src="{{ asset('admin/assets/media/icons/profil/riwayat_istri.svg') }}" style="width:62px; height: 100%;position:relative;left:7px;">
                                                <div class="col">
                                                    <button class="accordion-button fs-4 fw-bold" id="" type="button" data-bs-toggle="collapse" data-bs-target="#kt_accordion_1_body_10" aria-expanded="true" aria-controls="kt_accordion_1_body_10">
                                                        Riwayat Istri
                                                    </button>
                                                </div>
                                            </div>
                                        </h4>
                                        <div id="kt_accordion_1_body_10" class="accordion-collapse collapse" aria-labelledby="kt_accordion_1_header_10" data-bs-parent="#kt_accordion_1">
                                            <div class="accordion-body">
                                            @include('pegawai.profil.riwayat_istri.konten')
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item">
                                        <h4 class="accordion-header" id="kt_accordion_1_header_8">
                                            <div class="row align-items-center">
                                                <img src="{{ asset('admin/assets/media/icons/profil/riwayat_anak.svg') }}" style="width:62px; height: 100%;position:relative;left:7px;">
                                                <div class="col">
                                                    <button class="accordion-button fs-4 fw-bold" id="" type="button" data-bs-toggle="collapse" data-bs-target="#kt_accordion_1_body_11" aria-expanded="true" aria-controls="kt_accordion_1_body_11">
                                                        Riwayat Anak
                                                    </button>
                                                </div>
                                            </div>
                                        </h4>
                                        <div id="kt_accordion_1_body_11" class="accordion-collapse collapse" aria-labelledby="kt_accordion_1_header_11" data-bs-parent="#kt_accordion_1">
                                            <div class="accordion-body">
                                            @include('pegawai.profil.riwayat_anak.konten')
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item">
                                        <h4 class="accordion-header" id="kt_accordion_1_header_8">
                                            <div class="row align-items-center">
                                                <img src="{{ asset('admin/assets/media/icons/profil/riwayat_orang_tua.svg') }}" style="width:62px; height: 100%;position:relative;left:7px;">
                                                <div class="col">
                                                    <button class="accordion-button fs-4 fw-bold" id="" type="button" data-bs-toggle="collapse" data-bs-target="#kt_accordion_1_body_12" aria-expanded="true" aria-controls="kt_accordion_1_body_12">
                                                        Riwayat Orang Tua
                                                    </button>
                                                </div>
                                            </div>
                                        </h4>
                                        <div id="kt_accordion_1_body_12" class="accordion-collapse collapse" aria-labelledby="kt_accordion_1_header_12" data-bs-parent="#kt_accordion_1">
                                            <div class="accordion-body">
                                            @include('pegawai.profil.riwayat_orang_tua.konten')
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item">
                                        <h4 class="accordion-header" id="kt_accordion_1_header_8">
                                            <div class="row align-items-center">
                                                <img src="{{ asset('admin/assets/media/icons/profil/data_pribadi.svg') }}" style="width:62px; height: 100%;position:relative;left:7px;">
                                                <div class="col">
                                                    <button class="accordion-button fs-4 fw-bold" id="" type="button" data-bs-toggle="collapse" data-bs-target="#kt_accordion_1_body_13" aria-expanded="true" aria-controls="kt_accordion_1_body_13">
                                                        Riwayat Saudara
                                                    </button>
                                                </div>
                                            </div>
                                        </h4>
                                        <div id="kt_accordion_1_body_13" class="accordion-collapse collapse" aria-labelledby="kt_accordion_1_header_13" data-bs-parent="#kt_accordion_1">
                                            <div class="accordion-body">
                                                @include('pegawai.profil.riwayat_saudara.konten')
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item">
                                        <h4 class="accordion-header" id="kt_accordion_1_header_8">
                                            <div class="row align-items-center">
                                                <img src="{{ asset('admin/assets/media/icons/profil/riwayat_tambahan.svg') }}" style="width:62px; height: 100%;position:relative;left:7px;">
                                                <div class="col">
                                                    <button class="accordion-button fs-4 fw-bold" id="" type="button" data-bs-toggle="collapse" data-bs-target="#kt_accordion_1_body_14" aria-expanded="true" aria-controls="kt_accordion_1_body_14">
                                                        Riwayat Tambahan
                                                    </button>
                                                </div>
                                            </div>
                                        </h4>
                                        <div id="kt_accordion_1_body_14" class="accordion-collapse collapse" aria-labelledby="kt_accordion_1_header_14" data-bs-parent="#kt_accordion_1">
                                            <div class="accordion-body">
                                            @include('pegawai.profil.riwayat_tambahan.konten')
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item">
                                        <h4 class="accordion-header" id="kt_accordion_1_header_8">
                                            <div class="row align-items-center">
                                                <img src="{{ asset('admin/assets/media/icons/profil/file_pegawai.svg') }}" style="width:62px; height: 100%;position:relative;left:7px;">
                                                <div class="col">
                                                    <button class="accordion-button fs-4 fw-bold" id="" type="button" data-bs-toggle="collapse" data-bs-target="#kt_accordion_1_body_15" aria-expanded="true" aria-controls="kt_accordion_1_body_15">
                                                        File Pegawai
                                                    </button>
                                            </div>
                                        </h4>
                                        <div id="kt_accordion_1_body_15" class="accordion-collapse collapse" aria-labelledby="kt_accordion_1_header_15" data-bs-parent="#kt_accordion_1">
                                            <div class="accordion-body">
                                            @include('pegawai.profil.file_pegawai.konten')
                                            </div>
                                        </div>
                                    </div> 
                                </div>
                                <!--end::Accordion-->

                                 <a href="javascript:;" id="back_" type="button" class="btn btn-primary button-update btn-sm"> 
                                    Kembali
                                </a>

                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
        <!--end::Container-->
    </div>
@endsection
@section('script')
    <script>

    @if ($errors->any())
        // Initialize SweetAlert to display errors
        Swal.fire({
            title: 'Peringatan!',
            text: 'Status Verikasi admin opd belum di accept', // Display the first error message
            icon: 'warning',
            confirmButtonText: 'OK'
        });
    @endif

        let role = {!! json_encode($role) !!};
        let url_main = '';
        role.guard !== 'web' ? url_main = '/pegawai/verifikasi' : url_main = '/pegawai-opd/verifikasi-opd';

        $(document).on('click','#back_', function () {
            window.location.href = url_main;
        })

         $(document).on('click','.button-update', function (e) {
            e.preventDefault();
            let value = $(this).attr('data-type');
            let uuid = $(this).attr('data-uuid');
            
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
            });

            $.ajax({
            type: 'POST',
            url: `${url_main}/verifikasi`,
            data: {
                value : value,
                uuid : uuid
            },
            success: function (response) {
                console.log(response);
                // $(".text-danger").html("");
                if (response.success == true) {
                swal
                    .fire({
                    text: `Pegawai dengan NIP ${response.data.nip} berhasil di verifikasi`,
                    icon: "success",
                    showConfirmButton: false,
                    timer: 1500,
                    })
                    .then(function () {
                        window.location.href = `${url_main}`;
                    });
                } else {
                Swal.fire("Gagal Memproses data!", `${response.message}`, "warning");
                }
            },
            error: function (xhr) {
                console.log(xhr);
                Swal.fire(
                    "Gagal Memproses data!",
                    "Silahkan Hubungi Admin",
                    "warning"
                );
            },
            });

        })

         $(function (s) {
            $('.table-group').DataTable();
        })
    </script>
@endsection
