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
@section('title', 'Profil')
@section('side-form')
    @include('pegawai.profil.data_pribadi.form')
    @include('pegawai.profil.pendidikan_formal.form')
    @include('pegawai.profil.pendidikan_non_formal.form')
    @include('pegawai.profil.riwayat_kepangkatan.form')
    @include('pegawai.profil.riwayat_jabatan.form')
    @include('pegawai.profil.catatan_hukuman_dinas.form')
    @include('pegawai.profil.diklat_struktural.form')
    @include('pegawai.profil.diklat_fungsional.form')
    @include('pegawai.profil.diklat_teknis.form')
    @include('pegawai.profil.riwayat_penghargaan.form')
    @include('pegawai.profil.riwayat_istri.form')
    @include('pegawai.profil.riwayat_anak.form')
    @include('pegawai.profil.riwayat_orang_tua.form')
    @include('pegawai.profil.riwayat_saudara.form')
    @include('pegawai.profil.riwayat_tambahan.form')
    @include('pegawai.profil.file_pegawai.form')
@endsection
@section('button')
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-end">
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            <a href="{{ route('pegawai.profil.cetak') }}" class="btn btn-primary btn-sm" target="_blank">
                <img src="{{ asset('admin/assets/media/icons/printer.svg') }}" alt="" srcset="">
                Cetak
            </a>
        </div>
    </div>
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
                                <!--begin::Accordion-->
                                <div class="accordion" id="kt_accordion_1">

                                    <div class="accordion-item">
                                        <h4 class="accordion-header" id="kt_accordion_1_header_0">
                                            <div class="row align-items-center">
                                                <img src="{{ asset('admin/assets/media/icons/profil/data_pribadi.svg') }}" style="width:62px; height: 100%;position:relative;left:11px;">
                                                <div class="col">
                                                    <button class="accordion-button fs-4 fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#kt_accordion_1_body_0" aria-expanded="true" aria-controls="kt_accordion_1_body_0">
                                                        Data Pribadi
                                                    </button>
                                                </div>
                                                <div class="col-auto">
                                                    <button class="btn btn-success btn-sm" data-kt-drawer-show="true" data-kt-drawer-target="#side_form" style="position:relative;right:11px;">
                                                        <img src="{{'admin/assets/media/icons/edit.svg'}}" alt="" srcset="">
                                                        Edit data
                                                    </button>
                                                </div>
                                            </div>
                                        </h4>
                                        <div id="kt_accordion_1_body_0" class="accordion-collapse collapse" aria-labelledby="kt_accordion_1_header_0" data-bs-parent="#kt_accordion_1">
                                            <div class="accordion-body">
                                                
                                                @include('pegawai.profil.data_pribadi.konten')

                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item">
                                        <h4 class="accordion-header" id="kt_accordion_1_header_1">
                                            <div class="row align-items-center">
                                                <img src="{{ asset('admin/assets/media/icons/profil/pendidikan_formal.svg') }}" style="width:62px; height: 100%;position:relative;left:11px;">
                                                <div class="col">
                                                    <button class="accordion-button fs-4 fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#kt_accordion_1_body_1" aria-expanded="true" aria-controls="kt_accordion_1_body_1">
                                                        Riwayat Pendidikan Formal
                                                    </button>
                                                </div>
                                                <div class="col-auto">
                                                     <button id="btn-pendidikan-formal" class="btn btn-success btn-sm btn-form" data-kt-drawer-show="true" data-kt-drawer-target="#side_form_pendidikan_formal" style="position:relative;right:11px;">
                                                     <img src="{{'admin/assets/media/icons/add.svg'}}" alt="" srcset=""> 
                                                     Tambah data 
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
                                                <div class="col-auto">
                                                     <button id="btn-pendidikan-non-formal" class="btn btn-success btn-sm btn-form" data-kt-drawer-show="true" data-kt-drawer-target="#side_form_pendidikan_non_formal" style="position:relative;right:11px;">
                                                     <img src="{{'admin/assets/media/icons/add.svg'}}" alt="" srcset=""> 
                                                     Tambah data 
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
                                                <div class="col-auto">
                                                     <button id="btn-riwayat-kepangkatan" class="btn btn-success btn-sm btn-form" data-kt-drawer-show="true" data-kt-drawer-target="#side_form_riwayat_kepangkatan" style="position:relative;right:11px;">
                                                     <img src="{{'admin/assets/media/icons/add.svg'}}" alt="" srcset=""> 
                                                     Tambah data 
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
                                                <div class="col-auto">
                                                     <button id="btn-riwayat-jabatan" class="btn btn-success btn-sm btn-form" data-kt-drawer-show="true" data-kt-drawer-target="#side_form_riwayat_jabatan" style="position:relative;right:11px;">
                                                     <img src="{{'admin/assets/media/icons/add.svg'}}" alt="" srcset=""> 
                                                     Tambah data 
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
                                                <div class="col-auto">
                                                     <button id="btn-catatan-hukuman-dinas" class="btn btn-success btn-sm btn-form" data-kt-drawer-show="true" data-kt-drawer-target="#side_form_catatan_hukuman_dinas" style="position:relative;right:11px;">
                                                     <img src="{{'admin/assets/media/icons/add.svg'}}" alt="" srcset=""> 
                                                     Tambah data 
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
                                                <div class="col-auto">
                                                     <button id="btn-diklat-struktural" class="btn btn-success btn-sm btn-form" data-kt-drawer-show="true" data-kt-drawer-target="#side_form_diklat_struktural" style="position:relative;right:11px;">
                                                     <img src="{{'admin/assets/media/icons/add.svg'}}" alt="" srcset=""> 
                                                     Tambah data 
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
                                                <div class="col-auto">
                                                     <button id="btn-diklat-fungsional" class="btn btn-success btn-sm btn-form" data-kt-drawer-show="true" data-kt-drawer-target="#side_form_diklat_fungsional" style="position:relative;right:11px;">
                                                     <img src="{{'admin/assets/media/icons/add.svg'}}" alt="" srcset=""> 
                                                     Tambah data 
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
                                                <div class="col-auto">
                                                     <button id="btn-diklat-teknis" class="btn btn-success btn-sm btn-form" data-kt-drawer-show="true" data-kt-drawer-target="#side_form_diklat_teknis" style="position:relative;right:11px;">
                                                     <img src="{{'admin/assets/media/icons/add.svg'}}" alt="" srcset=""> 
                                                     Tambah data 
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
                                                <div class="col-auto">
                                                     <button id="btn-riwayat-penghargaan" class="btn btn-success btn-sm btn-form" data-kt-drawer-show="true" data-kt-drawer-target="#side_form_riwayat_penghargaan" style="position:relative;right:11px;">
                                                     <img src="{{'admin/assets/media/icons/add.svg'}}" alt="" srcset=""> 
                                                     Tambah data 
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
                                                <div class="col-auto">
                                                     <button id="btn-riwayat-istri" class="btn btn-success btn-sm btn-form" data-kt-drawer-show="true" data-kt-drawer-target="#side_form_riwayat_istri" style="position:relative;right:11px;">
                                                     <img src="{{'admin/assets/media/icons/add.svg'}}" alt="" srcset=""> 
                                                     Tambah data 
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
                                                <div class="col-auto">
                                                     <button id="btn-riwayat-anak" class="btn btn-success btn-sm btn-form" data-kt-drawer-show="true" data-kt-drawer-target="#side_form_riwayat_anak" style="position:relative;right:11px;">
                                                     <img src="{{'admin/assets/media/icons/add.svg'}}" alt="" srcset=""> 
                                                     Tambah data 
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
                                                <div class="col-auto">
                                                     <button id="btn-riwayat-orang-tua" class="btn btn-success btn-sm btn-form" data-kt-drawer-show="true" data-kt-drawer-target="#side_form_riwayat_orang_tua" style="position:relative;right:11px;">
                                                     <img src="{{'admin/assets/media/icons/add.svg'}}" alt="" srcset=""> 
                                                     Tambah data 
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
                                                <div class="col-auto">
                                                     <button id="btn-riwayat-saudara" class="btn btn-success btn-sm btn-form" data-kt-drawer-show="true" data-kt-drawer-target="#side_form_riwayat_saudara" style="position:relative;right:11px;">
                                                     <img src="{{'admin/assets/media/icons/add.svg'}}" alt="" srcset=""> 
                                                     Tambah data 
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
                                                <div class="col-auto">
                                                     <button id="btn-riwayat-tambahan" class="btn btn-success btn-sm btn-form" data-kt-drawer-show="true" data-kt-drawer-target="#side_form_riwayat_tambahan" style="position:relative;right:11px;">
                                                     <img src="{{'admin/assets/media/icons/add.svg'}}" alt="" srcset=""> 
                                                     Tambah data 
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
                                                <div class="col-auto">
                                                     <button id="btn-file-pegawai" class="btn btn-success btn-sm btn-form" data-kt-drawer-show="true" data-kt-drawer-target="#side_form_file_pegawai" style="position:relative;right:11px;">
                                                     <img src="{{'admin/assets/media/icons/add.svg'}}" alt="" srcset=""> 
                                                     Tambah data 
                                                    </button>
                                                </div>
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
        let control = new Control();
        let show_collapse = {!! json_encode($show_collapse) !!};

        $(document).on('submit','.form-data', function (e) {
            e.preventDefault();
            let modul = '';
            let url = '';
            let role_data = '';
            let type = $(this).attr('data-type');
            if ($(this).attr('id') == 'data-pribadi') {
                modul = 'Data Pribadi';
                role_data = 'Tambah';
                url = `/profil/data-pribadi/update/${$("input[name='uuid']").val()}`;   
                submit(modul, url, role_data,$(this));
            }else if($(this).attr('id') == 'pendidikan-formal'){
                
                if (type == 'add') {
                    modul = 'Riwayat Pendidikan Formal'
                    url = '/profil/riwayat-pendidikan-formal/store';
                    role_data = 'Tambah';
                } else {
                    let uuid = $("input[name='uuid']").val();
                    modul = 'Riwayat Pendidikan Formal';
                    url = `/profil/riwayat-pendidikan-formal/update/${uuid}`;
                    role_data = 'Update';
                }  
                submit(modul, url, role_data,$(this)); 
            }else if($(this).attr('id') == 'pendidikan-non-formal'){
                if (type == 'add') {
                    modul = 'Riwayat Pendidikan Non Formal'
                    url = '/profil/riwayat-pendidikan-non-formal/store';
                    role_data = 'Tambah';
                } else {
                    let uuid = $("input[name='uuid']").val();
                    modul = 'Riwayat Pendidikan Non Formal';
                    url = `/profil/riwayat-pendidikan-non-formal/update/${uuid}`;
                    role_data = 'Update';
                }  
                submit(modul, url, role_data,$(this)); 
            }else if($(this).attr('id') == 'riwayat-kepangkatan'){
                if (type == 'add') {
                    modul = 'Riwayat Kepangkatan';
                    url = '/profil/riwayat-kepangkatan/store';
                    role_data = 'Tambah';
                } else {
                    let uuid = $("input[name='uuid']").val();
                    modul = 'Riwayat Kepangkatan';
                    url = `/profil/riwayat-kepangkatan/update/${uuid}`;
                    role_data = 'Update';
                }  
                submit(modul, url, role_data,$(this)); 
            }else if($(this).attr('id') == 'riwayat-jabatan'){
                if (type == 'add') {
                    modul = 'Riwayat Jabatan';
                    url = '/profil/riwayat-jabatan/store';
                    role_data = 'Tambah';
                } else {
                    let uuid = $("input[name='uuid']").val();
                    modul = 'Riwayat Jabatan';
                    url = `/profil/riwayat-jabatan/update/${uuid}`;
                    role_data = 'Update';
                }  
                submit(modul, url, role_data,$(this)); 
            }else if($(this).attr('id') == 'catatan-hukuman-dinas'){
                if (type == 'add') {
                    modul = 'Catatan Hukuman Dinas';
                    url = '/profil/catatan-hukuman-dinas/store';
                    role_data = 'Tambah';
                } else {
                    let uuid = $("input[name='uuid']").val();
                    modul = 'Catatan Hukuman Dinas';
                    url = `/profil/catatan-hukuman-dinas/update/${uuid}`;
                    role_data = 'Update';
                }  
                submit(modul, url, role_data,$(this)); 
            }else if($(this).attr('id') == 'diklat-struktural'){
                if (type == 'add') {
                    modul = 'Riwayat Diklat Struktural';
                    url = '/profil/diklat-struktural/store';
                    role_data = 'Tambah';
                } else {
                    let uuid = $("input[name='uuid']").val();
                    modul = 'Riwayat Diklat Struktural';
                    url = `/profil/diklat-struktural/update/${uuid}`;
                    role_data = 'Update';
                }  
                submit(modul, url, role_data,$(this)); 
            }else if($(this).attr('id') == 'diklat-fungsional'){
                if (type == 'add') {
                    modul = 'Riwayat Diklat Fungsional';
                    url = '/profil/diklat-fungsional/store';
                    role_data = 'Tambah';
                } else {
                    let uuid = $("input[name='uuid']").val();
                    modul = 'Riwayat Diklat Fungsional';
                    url = `/profil/diklat-fungsional/update/${uuid}`;
                    role_data = 'Update';
                }  
                submit(modul, url, role_data,$(this)); 
            }else if($(this).attr('id') == 'diklat-teknis'){
                if (type == 'add') {
                    modul = 'Riwayat Diklat Teknis';
                    url = '/profil/diklat-teknis/store';
                    role_data = 'Tambah';
                } else {
                    let uuid = $("input[name='uuid']").val();
                    modul = 'Riwayat Diklat Teknis';
                    url = `/profil/diklat-teknis/update/${uuid}`;
                    role_data = 'Update';
                }  
                submit(modul, url, role_data,$(this)); 
            }else if($(this).attr('id') == 'diklat-teknis'){
                if (type == 'add') {
                    modul = 'Riwayat Diklat Teknis';
                    url = '/profil/diklat-teknis/store';
                    role_data = 'Tambah';
                } else {
                    let uuid = $("input[name='uuid']").val();
                    modul = 'Riwayat Diklat Teknis';
                    url = `/profil/diklat-teknis/update/${uuid}`;
                    role_data = 'Update';
                }  
                submit(modul, url, role_data,$(this)); 
            }else if($(this).attr('id') == 'riwayat-penghargaan'){
                if (type == 'add') {
                    modul = 'Riwayat Penghargaan';
                    url = '/profil/riwayat-penghargaan/store';
                    role_data = 'Tambah';
                } else {
                    let uuid = $("input[name='uuid']").val();
                    modul = 'Riwayat Penghargaan';
                    url = `/profil/riwayat-penghargaan/update/${uuid}`;
                    role_data = 'Update';
                }  
                submit(modul, url, role_data,$(this)); 
            }else if($(this).attr('id') == 'riwayat-istri'){
                if (type == 'add') {
                    modul = 'Riwayat Istri';
                    url = '/profil/riwayat-istri/store';
                    role_data = 'Tambah';
                } else {
                    let uuid = $("input[name='uuid']").val();
                    modul = 'Riwayat Istri';
                    url = `/profil/riwayat-istri/update/${uuid}`;
                    role_data = 'Update';
                }  
                submit(modul, url, role_data,$(this)); 
            }else if($(this).attr('id') == 'riwayat-anak'){
                if (type == 'add') {
                    modul = 'Riwayat Anak';
                    url = '/profil/riwayat-anak/store';
                    role_data = 'Tambah';
                } else {
                    let uuid = $("input[name='uuid']").val();
                    modul = 'Riwayat Anak';
                    url = `/profil/riwayat-anak/update/${uuid}`;
                    role_data = 'Update';
                }  
                submit(modul, url, role_data,$(this)); 
            }else if($(this).attr('id') == 'riwayat-orang-tua'){
                if (type == 'add') {
                    modul = 'Riwayat Orang Tua';
                    url = '/profil/riwayat-orang-tua/store';
                    role_data = 'Tambah';
                } else {
                    let uuid = $("input[name='uuid']").val();
                    modul = 'Riwayat Orang Tua';
                    url = `/profil/riwayat-orang-tua/update/${uuid}`;
                    role_data = 'Update';
                }  
                submit(modul, url, role_data,$(this)); 
            }else if($(this).attr('id') == 'riwayat-saudara'){
                if (type == 'add') {
                    modul = 'Riwayat Saudara';
                    url = '/profil/riwayat-saudara/store';
                    role_data = 'Tambah';
                } else {
                    let uuid = $("input[name='uuid']").val();
                    modul = 'Riwayat Saudara';
                    url = `/profil/riwayat-saudara/update/${uuid}`;
                    role_data = 'Update';
                }  
                submit(modul, url, role_data,$(this)); 
            }else if($(this).attr('id') == 'riwayat-tambahan'){
                if (type == 'add') {
                    modul = 'Riwayat Tambahan';
                    url = '/profil/riwayat-tambahan/store';
                    role_data = 'Tambah';
                } else {
                    let uuid = $("input[name='uuid']").val();
                    modul = 'Riwayat Tambahan';
                    url = `/profil/riwayat-tambahan/update/${uuid}`;
                    role_data = 'Update';
                }  
                submit(modul, url, role_data,$(this)); 
            }else if($(this).attr('id') == 'file-pegawai'){
                if (type == 'add') {
                    modul = 'File Pegawai';
                    url = '/profil/file-pegawai/store';
                    role_data = 'Tambah';
                } else {
                    let uuid = $("input[name='uuid']").val();
                    modul = 'File Pegawai';
                    url = `/profil/file-pegawai/update/${uuid}`;
                    role_data = 'Update';
                }  
                submit(modul, url, role_data,$(this)); 
            }

            
        })

        $(document).on('click', '.button-update', function(e) {
            e.preventDefault();
            let url_main = '';
            let modul = '';
            let url = '';
            if ($(this).attr('data-modul') == 'riwayat_pendidikan_formal') {
                url_main = '/profil/riwayat-pendidikan-formal';
                modul = 'Riwayat Pendidikan Formal';
                url = `${url_main}/show/` + $(this).attr('data-uuid');
            }else if($(this).attr('data-modul') == 'riwayat_pendidikan_non_formal'){
                url_main = '/profil/riwayat-pendidikan-non-formal';
                modul = 'Riwayat Pendidikan Non Formal';
                url = `${url_main}/show/` + $(this).attr('data-uuid');
            }else if($(this).attr('data-modul') == 'riwayat_kepangkatan'){
                url_main = '/profil/riwayat-kepangkatan';
                modul = 'Riwayat Kepangkatan';
                url = `${url_main}/show/` + $(this).attr('data-uuid');
            }else if($(this).attr('data-modul') == 'riwayat_jabatan'){
                url_main = '/profil/riwayat-jabatan';
                modul = 'Riwayat Jabatan';
                url = `${url_main}/show/` + $(this).attr('data-uuid');
            }else if($(this).attr('data-modul') == 'catatan_hukuman_dinas'){
                url_main = '/profil/catatan-hukuman-dinas';
                modul = 'Catatan Hukuman Dinas';
                url = `${url_main}/show/` + $(this).attr('data-uuid');
            }else if($(this).attr('data-modul') == 'diklat_struktural'){
                url_main = '/profil/diklat-struktural';
                modul = 'Riwayat Diklat Struktural';
                url = `${url_main}/show/` + $(this).attr('data-uuid');
            }else if($(this).attr('data-modul') == 'diklat_fungsional'){
                url_main = '/profil/diklat-fungsional';
                modul = 'Riwayat Diklat Fungsional';
                url = `${url_main}/show/` + $(this).attr('data-uuid');
            }else if($(this).attr('data-modul') == 'diklat_teknis'){
                url_main = '/profil/diklat-teknis';
                modul = 'Riwayat Diklat Teknis';
                url = `${url_main}/show/` + $(this).attr('data-uuid');
            }else if($(this).attr('data-modul') == 'riwayat_penghargaan'){
                url_main = '/profil/riwayat-penghargaan';
                modul = 'Riwayat Penghargaan';
                url = `${url_main}/show/` + $(this).attr('data-uuid');
            }else if($(this).attr('data-modul') == 'riwayat_istri'){
                url_main = '/profil/riwayat-istri';
                modul = 'Riwayat Istri';
                url = `${url_main}/show/` + $(this).attr('data-uuid');
            }else if($(this).attr('data-modul') == 'riwayat_anak'){
                url_main = '/profil/riwayat-anak';
                modul = 'Riwayat Anak';
                url = `${url_main}/show/` + $(this).attr('data-uuid');
            }else if($(this).attr('data-modul') == 'riwayat_orang_tua'){
                url_main = '/profil/riwayat-orang-tua';
                modul = 'Riwayat Orang Tua';
                url = `${url_main}/show/` + $(this).attr('data-uuid');
            }else if($(this).attr('data-modul') == 'riwayat_saudara'){
                url_main = '/profil/riwayat-saudara';
                modul = 'Riwayat Saudara';
                url = `${url_main}/show/` + $(this).attr('data-uuid');
            }else if($(this).attr('data-modul') == 'riwayat_tambahan'){
                // alert($(this).attr('data-type'));
                url_main = '/profil/riwayat-tambahan';
                modul = 'Riwayat Tambahan';

                if ($(this).attr('data-type') === 'keahlian') {
                    url = `${url_main}/show/${$(this).attr('data-uuid')}?jenis=keahlian`;    
                }else{
                    url = `${url_main}/show/${$(this).attr('data-uuid')}?jenis=bahasa`;    
                }   
            }else if($(this).attr('data-modul') == 'file_pegawai'){
                url_main = '/profil/file-pegawai';
                modul = 'File Pegawai';
                url = `${url_main}/show/` + $(this).attr('data-uuid');
            }

            control.overlay_form('Update', modul, url);
        })

        $(document).on('click', '.button-delete', function(e) {
            e.preventDefault();
            let url = '';
            let label = '';
            let module = '';
            if ($(this).attr('data-modul') == 'riwayat_pendidikan_formal') {
                url = `/profil/riwayat-pendidikan-formal/delete/${$(this).attr('data-uuid')}`;
                label = $(this).attr('data-label');
                module = 'Riwayat Pendidikan Formal';
            }else if($(this).attr('data-modul') == 'riwayat_pendidikan_non_formal'){
                url = `/profil/riwayat-pendidikan-non-formal/delete/${$(this).attr('data-uuid')}`;
                label = $(this).attr('data-label');
                module = 'Riwayat Pendidikan Non Formal';
            }else if($(this).attr('data-modul') == 'riwayat_kepangkatan'){
                url = `/profil/riwayat-kepangkatan/delete/${$(this).attr('data-uuid')}`;
                label = $(this).attr('data-label');
                module = 'Riwayat Kepangkatan';
            }else if($(this).attr('data-modul') == 'riwayat_jabatan'){
                url = `/profil/riwayat-jabatan/delete/${$(this).attr('data-uuid')}`;
                label = $(this).attr('data-label');
                module = 'Riwayat Jabatan';
            }else if($(this).attr('data-modul') == 'catatan_hukuman_dinas'){
                url = `/profil/catatan-hukuman-dinas/delete/${$(this).attr('data-uuid')}`;
                label = $(this).attr('data-label');
                module = 'Catatan Hukuman Dinas';
            }else if($(this).attr('data-modul') == 'diklat_struktural'){
                url = `/profil/diklat-struktural/delete/${$(this).attr('data-uuid')}`;
                label = $(this).attr('data-label');
                module = 'Riwayat Diklat Struktural';
            }else if($(this).attr('data-modul') == 'diklat_fungsional'){
                url = `/profil/diklat-fungsional/delete/${$(this).attr('data-uuid')}`;
                label = $(this).attr('data-label');
                module = 'Riwayat Diklat Fungsional';
            }else if($(this).attr('data-modul') == 'diklat_teknis'){
                url = `/profil/diklat-teknis/delete/${$(this).attr('data-uuid')}`;
                label = $(this).attr('data-label');
                module = 'Riwayat Diklat Teknis';
            }else if($(this).attr('data-modul') == 'diklat_penghargaan'){
                url = `/profil/diklat-penghargaan/delete/${$(this).attr('data-uuid')}`;
                label = $(this).attr('data-label');
                module = 'Riwayat Penghargaan';
            }else if($(this).attr('data-modul') == 'riwayat_istri'){
                url = `/profil/riwayat-istri/delete/${$(this).attr('data-uuid')}`;
                label = $(this).attr('data-label');
                module = 'Riwayat Istri';
            }else if($(this).attr('data-modul') == 'riwayat_anak'){
                url = `/profil/riwayat-anak/delete/${$(this).attr('data-uuid')}`;
                label = $(this).attr('data-label');
                module = 'Riwayat Anak';
            }else if($(this).attr('data-modul') == 'riwayat_orang_tua'){
                url = `/profil/riwayat-orang-tua/delete/${$(this).attr('data-uuid')}`;
                label = $(this).attr('data-label');
                module = 'Riwayat Orang Tua';
            }else if($(this).attr('data-modul') == 'riwayat_saudara'){
                url = `/profil/riwayat-saudara/delete/${$(this).attr('data-uuid')}`;
                label = $(this).attr('data-label');
                module = 'Riwayat Saudara';
            }else if($(this).attr('data-modul') == 'riwayat_tambahan'){

                if ($(this).attr('data-type') === 'keahlian') {
                    url = `/profil/riwayat-saudara/delete/${$(this).attr('data-uuid')}?jenis=keahlian`;    
                }else{
                    url = `/profil/riwayat-saudara/delete/${$(this).attr('data-uuid')}?jenis=bahasa`;    
                }

                label = $(this).attr('data-label');
                module = 'Riwayat Tambahan';
            }else if($(this).attr('data-modul') == 'file_pegawai'){
                url = `/profil/file-pegawai/delete/${$(this).attr('data-uuid')}`;
                label = $(this).attr('data-label');
                module = 'File Pegawai';
            }
            ajaxDelete(url, label, module)
        });

        $(document).on('click', '.btn-form', function() {
            if ($(this).attr('id') == 'btn-pendidikan-formal') {
                control.overlay_form('Tambah', 'Riwayat Pendidikan Formal');
                $('#kt_accordion_1_body_1').addClass('show')
            }else if($(this).attr('id') == 'btn-pendidikan-non-formal'){
                 $(".form-data")[0].reset();
                control.overlay_form('Tambah', 'Riwayat Pendidikan Non Formal');
                $('#kt_accordion_1_body_2').addClass('show')
            }else if($(this).attr('id') == 'btn-riwayat-kepangkatan'){
                 $(".form-data")[0].reset();
                control.overlay_form('Tambah', 'Riwayat Kepangkatan');
                $('#kt_accordion_1_body_3').addClass('show')
            }else if($(this).attr('id') == 'btn-riwayat-jabatan'){
                 $(".form-data")[0].reset();
                control.overlay_form('Tambah', 'Riwayat Jabatan');
                $('#kt_accordion_1_body_4').addClass('show')
            }else if($(this).attr('id') == 'btn-catatan-hukuman-dinas'){
                control.overlay_form('Tambah', 'Catatan Hukuman Dinas');
                $('#kt_accordion_1_body_5').addClass('show')
            }else if($(this).attr('id') == 'btn-diklat-struktural'){
                control.overlay_form('Tambah', 'Riwayat Diklat Struktural');
                $('#kt_accordion_1_body_6').addClass('show')
            }else if($(this).attr('id') == 'btn-diklat-fungsional'){
                control.overlay_form('Tambah', 'Riwayat Diklat Fungsional');
                $('#kt_accordion_1_body_7').addClass('show')
            }else if($(this).attr('id') == 'btn-diklat-teknis'){
                control.overlay_form('Tambah', 'Riwayat Diklat Teknis');
                $('#kt_accordion_1_body_8').addClass('show')
            }else if($(this).attr('id') == 'btn-riwayat-penghargaan'){
                control.overlay_form('Tambah', 'Riwayat Penghargaan');
                $('#kt_accordion_1_body_9').addClass('show')
            }else if($(this).attr('id') == 'btn-riwayat-istri'){
                control.overlay_form('Tambah', 'Riwayat Istri');
                $('#kt_accordion_1_body_10').addClass('show')
            }else if($(this).attr('id') == 'btn-riwayat-anak'){
                control.overlay_form('Tambah', 'Riwayat Anak');
                $('#kt_accordion_1_body_11').addClass('show')
            }else if($(this).attr('id') == 'btn-riwayat-orang-tua'){
                control.overlay_form('Tambah', 'Riwayat Orang Tua');
                $('#kt_accordion_1_body_12').addClass('show')
            }else if($(this).attr('id') == 'btn-riwayat-saudara'){
                control.overlay_form('Tambah', 'Riwayat Saudara');
                $('#kt_accordion_1_body_13').addClass('show')
            }else if($(this).attr('id') == 'btn-riwayat-tambahan'){
                control.overlay_form('Tambah', 'Riwayat Tambahan');
                $('#kt_accordion_1_body_14').addClass('show')
            }else if($(this).attr('id') == 'btn-file-pegawai'){
                control.overlay_form('Tambah', 'File Pegawai');
                $('#kt_accordion_1_body_15').addClass('show')
            }
            $('.form-control').val('');
        })

        $(document).on('change','#jenis_riwayat_tambahan', function () {
            
            let form = '';
            if ($(this).val() === 'Keahlian') {
                form = `
                    <div class="row mb-10">
                        <label class="form-label">Nama Keahlian</label>
                        <input type="text" id="nama_keahlian" class="form-control"  name="nama_keahlian" placeholder="Masukkan Nama Keahlian">
                        <small class="text-danger nama_keahlian_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Level Keahlian</label>
                        <select class="form-control" name="level_keahlian">
                            <option selected disabled> Pilih Level Keahlian </option>
                            <option value="Pemula">Pemula</option>
                            <option value="Menengah">Menengah</option>
                            <option value="Mahir">Mahir</option>
                            <option value="Ahli">Ahli</option>
                            <option value="Praktisi">Praktisi</option>
                            <option value="Pemimpin">Pemimpin</option>
                        </select>
                        <small class="text-danger pendidikan_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Tanggal Mulai Keahlian</label>
                        <input type="date" id="tanggal" class="form-control"  name="tanggal">
                        <small class="text-danger tanggal_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Pelatihan</label>
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" name="pelatihan" type="radio" value="Formal" id="Formal"/>
                                    <label class="form-check-label" for="Formal">
                                        Formal
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-8">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" name="pelatihan" type="radio" value="Non Formal" id="Non_Formal"/>
                                    <label class="form-check-label" for="Non_Formal">
                                        Non Formal
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-8">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" name="pelatihan" type="radio" value="Diklat" id="Diklat"/>
                                    <label class="form-check-label" for="Diklat">
                                        Diklat
                                    </label>
                                </div>
                            </div>
                        </div>
                        <small class="text-danger pelatihan_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Pilih Predikat</label>
                        <select class="form-control" name="predikat">
                            <option selected disabled> Pilih Level Keahlian </option>
                            <option value="Pemula">Pemula</option>
                            <option value="Layak">Menengah</option>
                            <option value="Berpengelaman">Berpengelaman</option>
                            <option value="Kompeten">Kompeten</option>
                            <option value="Ahli">Ahli</option>
                            <option value="Master">Master</option>
                            <option value="Pakar">Pakar</option>
                        </select>
                        <small class="text-danger level_keahlian_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Sertifikat</label>
                        <input type="file" id="sertifikat" class="form-control"  name="sertifikat">
                        <small class="text-danger sertifikat_error"></small>
                    </div>                
                `;
            } else {
                form = `
                <div class="row mb-10">
                        <label class="form-label">Nama Bahasa</label>
                        <input type="text" id="nama_bahasa" class="form-control"  name="nama_bahasa" placeholder="Masukkan Nama Bahasa">
                        <small class="text-danger nama_bahasa_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Level Keahlian Membaca</label>
                        <select class="form-control" name="level_keahlian_membaca">
                            <option selected disabled> Pilih Level Keahlian </option>
                            <option value="Pemula">Pemula</option>
                            <option value="Dasar">Dasar</option>
                            <option value="Menengah">Menengah</option>
                            <option value="Lanjutan">Lanjutan</option>
                            <option value="Profesional">Profesional</option>
                            <option value="Expert">Expert</option>
                        </select>
                        <small class="text-danger level_keahlian_membaca_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Level Keahlian Mendengarkan</label>
                        <select class="form-control" name="level_keahlian_mendengarkan">
                            <option selected disabled> Pilih Level Keahlian </option>
                            <option value="Pemula">Pemula</option>
                            <option value="Dasar">Dasar</option>
                            <option value="Menengah">Menengah</option>
                            <option value="Lanjutan">Lanjutan</option>
                            <option value="Profesional">Profesional</option>
                            <option value="Expert">Expert</option>
                        </select>
                        <small class="text-danger level_keahlian_membaca_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Level Keahlian Menulis</label>
                        <select class="form-control" name="level_keahlian_menulis">
                            <option selected disabled> Pilih Level Keahlian </option>
                            <option value="Pemula">Pemula</option>
                            <option value="Dasar">Dasar</option>
                            <option value="Menengah">Menengah</option>
                            <option value="Lanjutan">Lanjutan</option>
                            <option value="Profesional">Profesional</option>
                            <option value="Expert">Expert</option>
                        </select>
                        <small class="text-danger level_keahlian_membaca_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Level Keahlian Berbicara</label>
                        <select class="form-control" name="level_keahlian_berbicara">
                            <option selected disabled> Pilih Level Keahlian </option>
                            <option value="Pemula">Pemula</option>
                            <option value="Dasar">Dasar</option>
                            <option value="Menengah">Menengah</option>
                            <option value="Lanjutan">Lanjutan</option>
                            <option value="Profesional">Profesional</option>
                            <option value="Expert">Expert</option>
                        </select>
                        <small class="text-danger level_keahlian_membaca_error"></small>
                    </div>
                    
                    <div class="mb-10">
                        <label class="form-label">Tanggal Mulai Keahlian</label>
                        <input type="date" id="tanggal" class="form-control"  name="tanggal">
                        <small class="text-danger tanggal_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Pelatihan</label>
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" name="pelatihan" type="radio" value="Formal" id="Formal"/>
                                    <label class="form-check-label" for="Formal">
                                        Formal
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-8">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" name="pelatihan" type="radio" value="Non Formal" id="Non_Formal"/>
                                    <label class="form-check-label" for="Non_Formal">
                                        Non Formal
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-8">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" name="pelatihan" type="radio" value="Diklat" id="Diklat"/>
                                    <label class="form-check-label" for="Diklat">
                                        Diklat
                                    </label>
                                </div>
                            </div>
                        </div>
                        <small class="text-danger pelatihan_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Pilih Predikat</label>
                        <select class="form-control" name="predikat">
                            <option selected disabled> Pilih Level Keahlian </option>
                            <option value="Pemula">Pemula</option>
                            <option value="Layak">Menengah</option>
                            <option value="Berpengelaman">Berpengelaman</option>
                            <option value="Kompeten">Kompeten</option>
                            <option value="Ahli">Ahli</option>
                            <option value="Master">Master</option>
                            <option value="Pakar">Pakar</option>
                        </select>
                        <small class="text-danger predikat_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Sertifikat Bahasa</label>
                        <input type="file" id="sertifikat" class="form-control"  name="sertifikat">
                        <small class="text-danger sertifikat_error"></small>
                    </div>                
                `;
            }

            $('#form-konten-tambahan').html(form);
        })

        submit = (module,url,role_data, forElement) => {
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
            });

            $.ajax({
            type: 'POST',
            url: url,
            data: new FormData(forElement[0]),
            contentType: false,
            processData: false,
            success: function (response) {

                $(".text-danger").html("");
                if (response.success == true) {
                swal
                    .fire({
                    text: `${module} berhasil di ${role_data}`,
                    icon: "success",
                    showConfirmButton: false,
                    timer: 1500,
                    })
                    .then(function () {
                        window.location.href = `/profil?collapse=${response.data.show_collapse}`;
                    });
                } else {
                $("form")[0].reset();
                $("#from_select").val(null).trigger("change");
                Swal.fire("Gagal Memproses data!", `${response.message}`, "warning");
                }
            },
            error: function (xhr) {
                console.log(xhr);
                $(".text-danger").html("");
                $.each(xhr.responseJSON["errors"], function (key, value) {
                $(`.${key}_error`).html(value);
                });
            },
            });
        }

        ajaxDelete = (url, label, module) => {
            let token = $("meta[name='csrf-token']").attr("content");
            Swal.fire({
            title: `Apakah anda yakin akan menghapus data ${label} ?`,
            text: "Anda tidak akan dapat mengembalikan ini!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, hapus itu!",
            }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                url: url,
                type: "DELETE",
                data: {
                    id: $(this).attr("data-id"),
                    _token: token,
                },
                success: function (res) {
                    swal.fire({
                    title: "Menghapus!",
                    text: "Data Anda telah dihapus.",
                    icon: "success",
                    showConfirmButton: false,
                    timer: 1500,
                    });
                    setTimeout(() => {
                       window.location.href = '/profil'; 
                    }, 1000);
                },
                error: function (xhr) {
                    if (xhr.statusText == "Unprocessable Content") {
                    Swal.fire(
                        `${xhr.responseJSON.data}`,
                        `${xhr.responseJSON.message}`,
                        "warning"
                    );
                    }
                },
                });
            }
            });
        }

        $(function (s) {
            $('.table-group').DataTable();
            $(`#${show_collapse}`).addClass('show');
        })
    </script>
@endsection
