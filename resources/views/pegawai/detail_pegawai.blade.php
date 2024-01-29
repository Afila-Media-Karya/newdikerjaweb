@php
    $role = hasRole();
@endphp
@extends('layouts.layout')
@section('content')
<div class="post d-flex flex-column-fluid" id="kt_post">
        <!--begin::Container-->
        <div id="kt_content_container" class="container">
            <div class="row">

                <div class="card">
                    <div class="card-body p-0">

                        <div class="container">
                            <div class="py-5">
                                <hr>

                                <div class="row mb-10">
                                    <div class="col-lg-6">
                                        <label class="form-label">NIP</label>
                                        <input type="text" class="form-control form-control-sm" value="{{ $data->nip }}" disabled>
                                        <small class="text-danger asal_daerah_error"></small>
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="form-label">Nama</label>
                                        <input type="text" class="form-control form-control-sm" value="{{ $data->nama }}" disabled>
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

                                 <a href="/pegawai/pegawai-pensiun" type="button" class="btn btn-primary button-update btn-sm"> 
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
