@php
    $role = hasRole();
@endphp
@section('title', 'Pegawai Masuk')
@extends('layouts.layout')
@section('button')
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
        <!--begin::Page title-->
        <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
            data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
            class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
            <!--begin::Title-->
            <button class="btn btn-primary btn-sm " data-kt-drawer-show="true" data-kt-drawer-target="#side_form"
                id="button-side-form"><i class="fa fa-plus-circle" style="color:#ffffff" aria-hidden="true"></i> Tambah
                Data</button>
            <!--end::Title-->
        </div>
        <!--end::Page title-->
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
                                <table id="kt_table_data" class="table table-row-dashed table-row-gray-300 gy-7">
                                    <thead class="text-center">
                                        <tr class="fw-bolder fs-6 text-gray-800">
                                            <th>No</th>
                                            <th>NIP</th>
                                            <th>Nama</th>
                                            <th>Instansi Asal</th>
                                            <th>Instansi Masuk</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
        <!--end::Container-->
    </div>
@endsection
@section('side-form')
    <div id="side_form" class="bg-white" data-kt-drawer="true" data-kt-drawer-activate="true"
        data-kt-drawer-toggle="#side_form_button" data-kt-drawer-close="#side_form_close" data-kt-drawer-width="500px">
        <!--begin::Card-->
        <div class="card w-100">
            <!--begin::Card header-->
            <div class="card-header pe-5">
                <!--begin::Title-->
                <div class="card-title">
                    <!--begin::User-->
                    <div class="d-flex justify-content-center flex-column me-3">
                        <a href="#"
                            class="fs-4 fw-bolder text-gray-900 text-hover-primary me-1 lh-1 title_side_form"></a>
                    </div>
                    <!--end::User-->
                </div>
                <!--end::Title-->
                <!--begin::Card toolbar-->
                <div class="card-toolbar">
                    <!--begin::Close-->
                    <div class="btn btn-sm btn-icon btn-active-light-primary" id="side_form_close">
                        <!--begin::Svg Icon | path: icons/duotone/Navigation/Close.svg-->
                        <span class="svg-icon svg-icon-2">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)"
                                    fill="#000000">
                                    <rect fill="#000000" x="0" y="7" width="16" height="2"
                                        rx="1" />
                                    <rect fill="#000000" opacity="0.5"
                                        transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)"
                                        x="0" y="7" width="16" height="2" rx="1" />
                                </g>
                            </svg>
                        </span>
                        <!--end::Svg Icon-->
                    </div>
                    <!--end::Close-->
                </div>
                <!--end::Card toolbar-->
            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body hover-scroll-overlay-y">
                <form class="form-data">

                    <input type="hidden" name="id">
                    <input type="hidden" name="uuid">

                    <div class="mb-10">
                        <label class="form-label">Instansi Asal</label>
                        <input type="text" id="asal_daerah" class="form-control" name="asal_daerah" placeholder="Masukkan instansi asal">
                        <small class="text-danger asal_daerah_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Satuan Kerja Masuk</label>
                        <select class="form-select form-control" id="id_satuan_kerja" name="id_satuan_kerja" data-control="select2" data-placeholder="Pilih Satuan Kerja">
                            <option></option>
                            @foreach($satuan_kerja as $val)
                                <option value="{{$val->value}}">{{$val->text}}</option>
                            @endforeach
                        </select>
                        <small class="text-danger id_satuan_kerja_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Unit Kerja</label>
                        <select class="form-select form-control" name="id_unit_kerja" id="id_unit_kerja" data-control="select2" data-placeholder="Pilih Unit Kerja">
                            <option></option>
                        </select>
                        <small class="text-danger id_unit_kerja_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Jabatan Masuk</label>
                        <select class="form-select form-control" id="id_jabatan_masuk" name="id_jabatan_masuk" data-control="select2" data-placeholder="Pilih Jabatan Masuk">
                            <option></option>
                        </select>
                        <small class="text-danger id_jabatan_masuk_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">TMT Mutasi Masuk</label>
                        <input type="date" id="tmt" class="form-control" name="tmt">
                        <small class="text-danger tmt_error"></small>
                    </div>

                    <hr>

                    <div class="mb-10">
                        <label class="form-label">NIP</label>
                        <input type="text" id="nip" class="form-control" name="nip" placeholder="Masukkan NIP">
                        <small class="text-danger nip_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Nama</label>
                        <input type="text" id="nama" class="form-control" name="nama" placeholder="Masukkan Nama">
                        <small class="text-danger nama_error"></small>
                    </div>

                    <div class="row mb-10">
                        <div class="col-lg-6">
                            <label class="form-label">Tempat Lahir</label>
                            <input type="text" id="tempat_lahir" class="form-control" name="tempat_lahir" placeholder="Masukkan tempat lahir">
                            <small class="text-danger tempat_lahir_error"></small>
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" id="tanggal_lahir" class="form-control" name="tanggal_lahir">
                            <small class="text-danger tanggal_lahir_error"></small>
                        </div>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Jenis Kelamin</label>
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" name="jenis_kelamin" type="radio" value="L" id="L"/>
                                    <label class="form-check-label" for="L">
                                        Laki Laki
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" name="jenis_kelamin" type="radio" value="P" id="P"/>
                                    <label class="form-check-label" for="P">
                                        Perempuan
                                    </label>
                                </div>
                            </div>
                        </div>
                        <small class="text-danger jenis_kelamin_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Agama</label>
                        <select class="form-select form-control" name="agama" data-control="select2" data-placeholder="Pilih Agama">
                            <option></option>
                            @foreach($agama as $val)
                                <option value="{{$val->value}}">{{$val->text}}</option>
                            @endforeach
                        </select>
                        <small class="text-danger agama_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Status Perkawinan</label>
                        <select name="status_perkawinan" class="form-control">
                            <option selected disabled>Pilih Status Perkawinan</option>
                            <option value="Kawin">Kawin</option>
                            <option value="Belum Kawin">Belum Kawin</option>
                            <option value="Cerai Hidup">Cerai Hidup</option>
                            <option value="Cerai Mati">Cerai Mati</option>
                        </select>
                        <small class="text-danger status_perkawinan_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">TMT Pegawai</label>
                        <input type="date" id="tmt_pegawai" class="form-control" name="tmt_pegawai">
                        <small class="text-danger tmt_pegawai_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Golongan</label>
                        <select class="form-select form-control" name="golongan" data-control="select2" data-placeholder="Pilih Golongan">
                            <option></option>
                            @foreach($golongan as $val)
                                <option value="{{$val->value}}">{{$val->text}}</option>
                            @endforeach
                        </select>
                        <small class="text-danger golongan_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">TMT Jabatan</label>
                        <input type="date" id="tmt_jabatan" class="form-control" name="tmt_jabatan">
                        <small class="text-danger tmt_jabatan_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">TMT Golongan</label>
                        <input type="date" id="tmt_golongan" class="form-control" name="tmt_golongan">
                        <small class="text-danger tmt_golongan_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Pendidikan</label>
                        <select class="form-select form-control" name="pendidikan" data-control="select2" data-placeholder="Pilih Pendidikan">
                            <option></option>
                            @foreach($pendidikan as $val)
                                <option value="{{$val->value}}">{{$val->text}}</option>
                            @endforeach
                        </select>
                        <small class="text-danger pendidikan_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Pendidikan Lulus</label>
                        <input type="date" id="pendidikan_lulus" class="form-control" name="pendidikan_lulus">
                        <small class="text-danger pendidikan_lulus_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Pendidikan Struktural</label>
                        <input type="text" id="pendidikan_struktural" class="form-control" name="pendidikan_struktural">
                        <small class="text-danger pendidikan_struktural_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Pendidikan Struktural Lulus</label>
                        <input type="date" id="pendidikan_struktural_lulus" class="form-control" name="pendidikan_struktural_lulus">
                        <small class="text-danger pendidikan_struktural_lulus_error"></small>
                    </div>

                    <div class="separator separator-dashed mt-8 mb-5"></div>
                    <div class="d-flex gap-5">
                        <button type="submit" class="btn btn-primary btn-sm btn-submit d-flex align-items-center"><i
                                class="bi bi-file-earmark-diff"></i> Simpan</button>
                        <button type="reset" id="side_form_close"
                            class="btn mr-2 btn-light btn-cancel btn-sm d-flex align-items-center"
                            style="background-color: #ea443e65; color: #EA443E"><i class="bi bi-trash-fill"
                                style="color: #EA443E"></i>Batal</button>
                    </div>
                </form>
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card-->
    </div>
@endsection
@section('script')
    <script>
        let control = new Control();
        let role = {!! json_encode($role) !!};
        let satuan_kerja_user = {!! json_encode($satuan_kerja_user) !!};
        let url_main = '/pegawai/pegawai-masuk';

        $(document).on('click', '#button-side-form', function() {
            control.overlay_form('Tambah', 'Pegawai Masuk');
        })

        $(document).on('submit', ".form-data", function(e) {
            e.preventDefault();
            let type = $(this).attr('data-type');
            if (type == 'add') {
                control.submitFormMultipart(`${url_main}/store`, 'Tambah', 'Pegawai Masuk','POST');
            } else {
                let uuid = $("input[name='uuid']").val();
                control.submitFormMultipart(`${url_main}/update/` + uuid, 'Update','Pegawai Masuk', 'POST');
            }
        });

        $(document).on('click', '.button-update', function(e) {
            e.preventDefault();
            let url = `${url_main}/show/` + $(this).attr('data-uuid');
            control.overlay_form('Update', 'Pegawai Masuk', url);
        })

        $(document).on('click', '.button-delete', function(e) {
            e.preventDefault();
            let url = `${url_main}/delete/` + $(this).attr('data-uuid');
            let label = $(this).attr('data-label');
            control.ajaxDelete(url, label)
        })

        $(document).on('change','#id_satuan_kerja', function () {
            if ($(this).val() !== '') {
                control.push_select(`/jabatan/jabatan-kosong/option/${$(this).val()}`,'#id_jabatan_masuk');   
                control.push_select(`/perangkat-daerah/unit-kerja/option?satuan_kerja=${$(this).val()}`,'#id_unit_kerja');  
            }  
        })

        $(document).on('change','#satuan_kerja_filter', function () {
            datatable($(this).val());
        })

        datatable = () =>{
            let columns = [{
                data: null,
                className : 'text-center',
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            }, {
                data: 'nip',
                className : 'text-right',
            }, {
                data: 'nama',
                className : 'text-right',
            }, {
                data: 'asal_daerah',
                className : 'text-right',
            }, {
                data: 'nama_satuan_kerja',
                className : 'text-right',
            }, {
                data: 'uuid',
                className : 'text-center',
            }];
            let columnDefs = [{
                targets: -1,
                title: 'Aksi',
                width: '10rem',
                orderable: false,
                render: function(data, type, full, meta) {
                    return `
                            <a href="/pegawai/pegawai-masuk/detail/${data}" type="button" data-uuid="${data}" class="btn btn-warning btn-icon btn-sm"> 
                                <img src="{{ asset('admin/assets/media/icons/eye.svg')}}" alt="" srcset="">
                            </a>
                            `;
                    },
            }];
            control.initDatatable(`${url_main}/datatable`, columns, columnDefs);
        }

        $(function() {
            datatable();
        })
    </script>
@endsection