@section('title', 'Profil Daerah')
@extends('layouts.layout')
@section('content')
<div class="post d-flex flex-column-fluid" id="kt_post">
        <!--begin::Container-->
        <div id="kt_content_container" class="container">
            <div class="row">

                <div class="card">
                    <div class="card-header pt-5">
                        <!--begin::Title-->
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bolder text-dark">Profil Kepala Derah</span>
                        </h3>
                        <!--end::Title-->
                    </div>
                    <div class="card-body p-0">

                        <div class="container">
                            <div class="py-5">
                                <div class="row mb-10">
                                    <div class="col-lg-6">
                                        <label class="form-label">Nama Daerah</label>
                                        <input type="text" class="form-control form-control-sm" value="{{ $data->nama_daerah }}" disabled>
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="form-label">Pimpinan Daerah</label>
                                        <input type="text" class="form-control form-control-sm" value="{{ $data->pimpinan_daerah }}" disabled>
                                    </div>
                                </div>
                                <div class="row mb-10">
                                    <div class="col-lg-6">
                                        <label class="form-label">E-mail</label>
                                        <input type="text" class="form-control form-control-sm" value="{{ $data->email }}" disabled>
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="form-label">No telpon</label>
                                        <input type="text" class="form-control form-control-sm" value="{{ $data->no_telp }}" disabled>
                                    </div>
                                </div>
                                <div class="row mb-10">
                                    <div class="col-lg-6">
                                        <label class="form-label">Alamat</label>
                                        <textarea class="form-control form-control-sm" rows="3" disabled>{{$data->alamat}}</textarea>
                                    </div>
                                </div>
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

@endsection