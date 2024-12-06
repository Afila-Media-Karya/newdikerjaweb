@php
    $role = hasRole();
@endphp
@extends('layouts.layout')
@section('button')
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
        <!--begin::Page title-->
        @if($role['role'] !== '3')
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
            data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
            class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
            <!--begin::Title-->
            <button class="btn btn-primary btn-sm " data-kt-drawer-show="true" data-kt-drawer-target="#side_form"
                id="button-side-form"><i class="fa fa-plus-circle" style="color:#ffffff" aria-hidden="true"></i> Tambah
                Data</button>
            <!--end::Title-->
        </div>
        @endif
        <!--end::Page title-->

        <div class="d-flex align-items-center gap-2 gap-lg-3">
            <a href="#" id="export-excel" data-type="excel" class="btn btn-sm btn-success">
                <img src="{{asset('admin/assets/media/icons/excel.svg')}}" style="position: relative; bottom: 1px;" alt="" srcset="">
                Export Excel
            </a>
            <a href="#" id="export-pdf" data-type="pdf" class="btn btn-sm btn-danger">
                <img src="{{asset('admin/assets/media/icons/pdf.svg')}}" style="position: relative; bottom: 1px;" alt="" srcset="">
                Export PDF
            </a>
        </div>
    </div>
@endsection
@section('title', 'Daftar Pegawai')
@section('content')
<div class="post d-flex flex-column-fluid" id="kt_post">
        <!--begin::Container-->
        <div id="kt_content_container" class="container">
            <div class="row">

                <div class="card">
                    <div class="card-body p-0">

                        <div class="container">
                            <div class="py-5">
                               
                            <div class="d-flex justify-content-end" style="position: relative;right: 222px;top: 12px;">
                                <div style="position:absolute">
                                    <button type="button" class="btn btn-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                        <span class="svg-icon svg-icon-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="black" />
                                            </svg>
                                        </span>
                                        Filter
                                    </button>
                                    <div class="menu menu-sub menu-sub-dropdown w-800px w-md-800px mt-5" data-kt-menu="true" id="kt-toolbar-filter" style="background:#F7F9FC">
                                        <div class="px-5 py-5">
                                            <div class="fs-4 text-dark">Filter</div>
                                        </div>
                                        <div class="px-5 py-5">

                                            <form class="filter-table">
                                                <div class="row mb-5">
                                                <div class="col-lg-4">
                                                    <select class="form-control form-control-sm" name="jenis_kelamin" data-control="select2" data-placeholder="Jenis Kelamin">
                                                        <option></option>
                                                        <option value="semua">Semua</option>
                                                        <option value="L">Laki Laki</option>
                                                        <option value="P">Perempuan</option>
                                                    </select>
                                                </div>
                                                <div class="col-lg-4">
                                                    <select class="form-control form-control-sm" name="agama" data-control="select2" data-placeholder="Agama">
                                                        <option></option>
                                                        <option value="semua">Semua</option>
                                                        @foreach($agama as $val)
                                                            <option value="{{$val->value}}">{{$val->text}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-lg-4">
                                                    <select name="status_perkawinan" class="form-control form-control-sm" data-control="select2" data-placeholder="Status Perkawinan">
                                                        <option></option>
                                                        <option value="semua">Semua</option>
                                                        <option value="Kawin">Kawin</option>
                                                        <option value="Belum Kawin">Belum Kawin</option>
                                                        <option value="Cerai Hidup">Cerai Hidup</option>
                                                        <option value="Cerai Mati">Cerai Mati</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row mb-5">
                                                <div class="col-lg-6">
                                                    <select class="form-control form-control-sm" name="pendidikan" data-control="select2" data-placeholder="Pendidikan">
                                                        <option></option>
                                                        <option value="semua">Semua</option>
                                                        @foreach($pendidikan as $val)
                                                            <option value="{{$val->value}}">{{$val->text}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-lg-6">
                                                    <select class="form-control form-control-sm" name="golongan" data-control="select2" data-placeholder="Golongan">
                                                        <option></option>
                                                        <option value="semua">Semua</option>
                                                        @foreach($golongan as $val)
                                                            <option value="{{$val->value}}">{{$val->text}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row mb-5">
                                                <div class="col-lg-4">
                                                    <select class="form-control form-control-sm" name="jenis_jabatan" data-control="select2" data-placeholder="Jenis Jabatan">
                                                        <option></option>
                                                        <option value="semua">Semua</option>
                                                        @foreach($jenis_jabatan as $val)
                                                            <option value="{{$val->text}}">{{$val->text}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-lg-4">
                                                    <select class="form-control form-control-sm" name="status_kepegawaian" data-control="select2" data-placeholder="Status Kepegawaian">
                                                        <option></option>
                                                        <option value="semua">Semua</option>
                                                        <option value="PNS">PNS</option>
                                                        <option value="PPPK">PPPK</option>
                                                    </select>
                                                </div>
                                                <div class="col-lg-4">
                                                    <select class="form-control form-control-sm" name="tipe_pegawai" data-control="select2" data-placeholder="Tipe Pegawai">
                                                        <option></option>
                                                        <option value="semua">Semua</option>
                                                        <option value="pegawai_administratif">Pegawai Administratif</option>
                                                        <option value="tenaga_pendidik">Tenaga Pendidik</option>
                                                        <option value="tenaga_kesehatan">Tenaga Kesehatan</option>
                                                    </select>
                                                </div>
                                            </div>

                                            @if($role['guard'] !== 'web')
                                                    <div class="row">
                                                        <div class="col-lg-6 mb-5">
                                                            <select class="form-control form-control-sm" id="satuan_kerja_filter" name="satuan_kerja" data-control="select2" data-placeholder="Satuan Kerja">
                                                                <option></option>
                                                                <option value="semua">Semua</option>
                                                                @foreach($satuan_kerja as $val)
                                                                    <option value="{{$val->value}}">{{$val->text}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-lg-6 mb-5">
                                                            <select class="form-control form-control-sm" id="unit_kerja_filter" name="unit_kerja" data-control="select2" data-placeholder="Unit Kerja">
                                                                <option></option>
                                                                
                                                            </select>
                                                        </div>
                                                    </div>
                                                @endif

                                            <div class="d-flex justify-content-end gap-2">
                                                <button type="submit" class="btn btn-primary btn-sm" data-kt-menu-dismiss="true" data-kt-docs-table-filter="filter">Terapkan</button>
                                                <button type="reset" class="btn btn-light btn-cancel btn-sm me-2" data-kt-menu-dismiss="true" data-kt-docs-table-filter="reset" style="background-color: #ea443e65; color: #EA443E">Batal</button>
                                            </div>
                                            </form>

                                        </div>
                                    </div>
                                </div>
                            </div>

                                <table id="kt_table_data" class="table table-row-dashed table-row-gray-300 gy-7">
                                    <thead class="text-center">
                                        <tr class="fw-bolder fs-6 text-gray-800">
                                            <th>No</th>
                                            <th>NIP</th>
                                            <th>Nama</th>
                                            <th>Jabatan</th>
                                            <th>Status</th>
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

                    <!-- <div class="mb-10">
                        <label class="form-label">Eselon</label>
                        <select class="form-select form-control" name="eselon" data-control="select2" data-placeholder="Pilih Eselon">
                            <option></option>
                            @foreach($eselon as $val)
                                <option value="{{$val->value}}">{{$val->text}}</option>
                            @endforeach
                        </select>
                        <small class="text-danger eselon_error"></small>
                    </div> -->

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

                    <div class="mb-10">
                        <label class="form-label">Status Kepegawaian</label>
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" name="status_kepegawaian" type="radio" value="PNS" id="PNS"/>
                                    <label class="form-check-label" for="PNS">
                                        PNS
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" name="status_kepegawaian" type="radio" value="PPPK" id="PPPK"/>
                                    <label class="form-check-label" for="PPPK">
                                        PPPK
                                    </label>
                                </div>
                            </div>
                        </div>
                        <small class="text-danger status_kepegawaian_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Tipe Pegawai</label>
                        <select name="tipe_pegawai" class="form-control">
                            <option selected disabled>Pilih Status Perkawinan</option>
                            <option value="pegawai_administratif">Pegawai Administratif</option>
                            <option value="tenaga_pendidik">Tenaga Pendidik</option>
                            <option value="tenaga_pendidik_non_guru">Tenaga Kependidikan Non Guru</option>
                            <option value="tenaga_kesehatan">Tenaga Kesehatan</option>
                        </select>
                        <small class="text-danger tipe_pegawai_error"></small>
                    </div>

                    @if($role['guard'] == 'administrator' && $role['role'] == '2')
                    <div class="mb-10">
                        <label class="form-label">Satuan Kerja</label>
                        <select class="form-select form-control" name="id_satuan_kerja" id="id_satuan_kerja" data-control="select2" data-placeholder="Pilih Satuan Kerja">
                            <option></option>
                            @foreach($satuan_kerja as $val)
                                <option value="{{$val->value}}">{{$val->text}}</option>
                            @endforeach
                        </select>
                        <small class="text-danger id_satuan_kerja_error"></small>
                    </div>
                    @else
                    <input type="hidden" name="id_satuan_kerja" value="{{$satuan_kerja_user}}">
                    @endif

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
        let url_main = '';
        role.guard !== 'web' ? url_main = '/pegawai/list-pegawai' : url_main = '/pegawai-opd/list-pegawai-opd';

        $(document).on('click', '#button-side-form', function() {
            control.overlay_form('Tambah', 'Daftar Pegawai');
        })

        $(document).on('submit', ".form-data", function(e) {
            e.preventDefault();
            let type = $(this).attr('data-type');
            if (type == 'add') {
                control.submitFormMultipart(`${url_main}/store`, 'Tambah', 'Daftar Pegawai','POST');
            } else {
                let uuid = $("input[name='uuid']").val();
                control.submitFormMultipart(`${url_main}/update/` + uuid, 'Update','Daftar Pegawai', 'POST');
            }
        });

        $(document).on('click', '.button-update', function(e) {
            e.preventDefault();
            let url = `${url_main}/show/` + $(this).attr('data-uuid');
            control.overlay_form('Update', 'Daftar Pegawai', url);
        })

        $(document).on('click', '.button-delete', function(e) {
            e.preventDefault();
            let url = `${url_main}/delete/` + $(this).attr('data-uuid');
            let label = $(this).attr('data-label');
            control.ajaxDelete(url, label)
        });

        $(document).on('click','.button-reset', function (e) {
           e.preventDefault();
           let label = $(this).attr('data-label');
           let uuid = $(this).attr('data-uuid');
           
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
            });

            Swal.fire({
            title: `Apakah anda yakin akan reset biometrik perangkat ${label}?`,
            text: "Aksi ini akan menghapus wajah, dan pegawai harus rekam wajah kembali!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, Reset itu!",
            }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                url: `${url_main}/reset-wajah`,
                type: "POST",
                data: {
                    uuid : uuid,
                },
                success: function () {
                    swal.fire({
                    title: "Reset!",
                    text: "Wajah berhasil di reset.",
                    icon: "success",
                    showConfirmButton: false,
                    timer: 1500,
                    });
                    role.guard == 'web' ? datatable(satuan_kerja_user) : datatable($('#satuan_kerja_filter').val());
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
        })

        $(document).on('change','#satuan_kerja_filter', function () {
            if ($(this).val() !== '') {
                control.push_select_laporan(`/perangkat-daerah/unit-kerja/option?satuan_kerja=${$(this).val()}`,'#unit_kerja_filter');    
            }
        })


        $(document).on('submit','.filter-table', function (e) {
            e.preventDefault();
            datatable($(this).serialize());
        })

        $('#export-excel,#export-pdf,#export-backup').click(function(e){
            e.preventDefault();
             let type = $(this).attr('data-type');
            let params = $('.filter-table').serialize()
            let url_main = '';

            role.guard == 'web' ? datatable(satuan_kerja_user) : datatable($('#satuan_kerja_filter').val());

            // if (validation(parsedData) === true) {
                if (role.guard !== 'web') {
                    url_main = '/laporan/pegawai';
                }else{
                    url_main = '/laporan-opd/pegawai';
                }
                window.open(`${url_main}?${params}&type=${type}`, "_blank");
            // }
        })

        datatable = (serialize) =>{
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
                data: 'nama_jabatan',
                className : 'text-right',
                render: function(data, type, row, meta) {
                    if (row.status_jabatan !== 'definitif' && data !== null) {
                        let status_jabatan = row.status_jabatan;
                        return `${status_jabatan.toUpperCase()} ${data}`;
                    }

                    if (data !== null) {
                        return data;  
                    }
                    
                    return '-';
                }
            }, {
                data: 'status',
                className : 'text-right',
                render: function(data, type, row, meta) {
                    let label = '';
                    if (data == '1') {
                        label = '<span class="badge badge-success">Aktif</span>';
                    }else if(data == '2'){
                        label = '<span class="badge badge-danger">Pensiun</span>';
                    }else{
                        label = '<span class="badge badge-warning">Pindah</span>';
                    }
                    return label;
                }
            }, {
                data: 'uuid',
                className : 'text-center',
            }];
            let columnDefs = [{
                targets: -1,
                title: 'Aksi',
                width: '15rem',
                orderable: false,
                render: function(data, type, full, meta) {

                    let button_more = `<a href="javascript:;" type="button" data-uuid="${data}" data-kt-drawer-show="true" data-kt-drawer-target="#side_form" class="btn btn-primary button-update btn-icon btn-sm" data-toggle="tooltip" title="edit"> 
                                <img src="{{ asset('admin/assets/media/icons/edit.svg')}}" alt="" srcset="">
                            </a>

                            <a href="javascript:;" type="button" data-uuid="${data}" data-label="${full.nip}" class="btn btn-danger button-delete btn-icon btn-sm"> 
                                <img src="{{ asset('admin/assets/media/icons/trash.svg')}}" data-toggle="tooltip" title="hapus">
                            </a>`;

                    if (role.guard === 'web' && role.role === '1' || role.role === '3') {
                        button_more = `
                        <a href="javascript:;" type="button" data-uuid="${data}" data-kt-drawer-show="true" data-kt-drawer-target="#side_form" class="btn btn-primary button-update btn-icon btn-sm" data-toggle="tooltip" title="edit"> 
                                <img src="{{ asset('admin/assets/media/icons/edit.svg')}}" alt="" srcset="">
                            </a>
                        <a href="${url_main}/detail/${data}" type="button" data-uuid="${data}" class="btn btn-warning btn-icon btn-sm"> 
                                <img src="{{ asset('admin/assets/media/icons/eye.svg')}}" alt="" srcset="">
                            </a>`;
                    }        


                    return `
                           ${button_more} 
                            <a href="javascript:;" type="button" data-uuid="${data}" data-label="${full.nama}" class="btn button-reset btn-icon btn-sm" data-toggle="tooltip" title="reset biometrik perangkat" style="background:#8F9BB3"> 
                                <img src="{{ asset('admin/assets/media/icons/device.png')}}" data-toggle="tooltip" title="Biometrik">
                            </a>
                            `;
                    },
            }];
            control.initDatatable(`${url_main}/datatable?${serialize}`, columns, columnDefs);
        }

        $(function() {
            // role.guard == 'web' ? datatable(satuan_kerja_user.id_satuan_kerja) : datatable($('#satuan_kerja_filter').val());
            datatable($('.filter-table').serialize());
        })
    </script>
@endsection