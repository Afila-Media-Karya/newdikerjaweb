@php
    $role = hasRole();
@endphp
@section('title', 'List Jabatan')
@extends('layouts.layout')
@section('button')
<div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      

        <div class="d-flex align-items-center gap-2 gap-lg-3">
                        <a href="#" id="export-struktur" data-type="pdf" class="btn btn-sm btn-dark">
                            <img src="{{asset('admin/assets/media/icons/pdf.svg')}}" style="position: relative; bottom: 1px;" alt="" srcset="">
                            Cetak Struktur
                        </a>
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
                                    <thead class="text-right">
                                        <tr class="fw-bolder fs-6 text-gray-800">
                                            <th>No</th>
                                            <th>Jabatan</th>
                                            <th>Pejabat</th>
                                            <th>Atasan Langsung</th>
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
                            class="fs-4 fw-bolder text-gray-900 text-hover-primary me-1 lh-1 title_side_form">Update Jabatan</a>
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

                    <div class="mb-10" style="display:none">
                        <label class="form-label">Pilih Satuan Kerja</label>
                        <select class="form-select form-control" id="id_satuan_kerja" name="id_satuan_kerja" data-control="select2" data-placeholder="Pilih Satuan Kerja">
                            <option></option>
                            @foreach($satuan_kerja as $val)
                                <option value="{{$val->value}}" @if($val->value == $satuan_kerja_user) selected @endif>{{$val->text}}</option>
                            @endforeach
                        </select>
                        <small class="text-danger id_satuan_kerja_error"></small>
                    </div>

                    <div class="mb-10" style="display:none">
                        <label class="form-label">Unit Kerja</label>
                        <select class="form-select form-control" name="id_unit_kerja" id="id_unit_kerja" data-control="select2" data-placeholder="Pilih Unit Kerja">
                            <option></option>
                        </select>
                        <small class="text-danger id_unit_kerja_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Lokasi Kerja</label>
                        <select class="form-select form-control" id="id_lokasi_kerja" name="id_lokasi_kerja" data-control="select2" data-placeholder="Pilih Kerja">
                            <option></option>
                        </select>
                        <small class="text-danger id_lokasi_kerja_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Lokasi Apel</label>
                        <select class="form-select form-control" id="id_lokasi_apel" name="id_lokasi_apel" data-control="select2" data-placeholder="Pilih Kerja">
                            <option></option>
                        </select>
                        <small class="text-danger id_lokasi_apel_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Jabatan</label>
                        <select class="form-select form-control" id="id_master_jabatan" name="id_master_jabatan" data-control="select2" data-placeholder="Pilih Jabatan">
                            <option></option>
                        </select>
                        <small class="text-danger id_master_jabatan_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Jabatan Atasan Langsung</label>
                        <select class="form-select form-control" id="id_parent" name="id_parent" data-control="select2" data-placeholder="Pilih Jabatan Atasan Langsung">
                            <option></option>
                        </select>
                        <small class="text-danger id_parent_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Pilih Pegawai</label>
                        <select class="form-select form-control" id="id_pegawai" name="id_pegawai" data-control="select2" data-placeholder="Pilih Pegawai">
                            <option></option>
                            @foreach($pegawai as $val)
                                <option value="{{$val->id}}">{{$val->text}}</option>
                            @endforeach
                        </select>
                        <small class="text-danger id_pegawai_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Status</label>
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" name="status" type="radio" value="definitif" id="definitif"/>
                                    <label class="form-check-label" for="L">
                                        Definitif
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" name="status" type="radio" value="plt" id="plt"/>
                                    <label class="form-check-label" for="P">
                                        Pelaksana Tugas
                                    </label>
                                </div>
                            </div>
                        </div>
                        <small class="text-danger status_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Pembayaran</label>
                        <select name="pembayaran" class="form-control">
                            <option selected disabled>Pilih Pembayaran</option>
                            <option value="100">100</option>
                            <option value="75">75</option>
                            <option value="25">25</option>
                            <option value="20">20</option>
                            <option value="0">0</option>
                        </select>
                        <small class="text-danger pembayaran_error"></small>
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
        let unit_kerja = {!! json_encode($unit_kerja) !!};
        let lokasi = {!! json_encode($lokasi) !!};
        let url_main = '/jabatan-opd/list-jabatan';

        $(document).on('click', '#button-side-form', function() {
            $('#id_satuan_kerja, #id_unit_kerja,#id_lokasi_kerja,#id_lokasi_apel,#id_master_jabatan,#id_parent').prop('disabled', false);
            control.overlay_form('Tambah', 'Jabatan');
            $('#id_satuan_kerja').val(satuan_kerja_user);
            $('#id_satuan_kerja').trigger('change');

            setTimeout(() => {
                $('#id_unit_kerja').val(unit_kerja);
                $('#id_unit_kerja').trigger('change');

                $('#id_lokasi_kerja').val(lokasi);
                $('#id_lokasi_kerja').trigger('change');

                $('#id_lokasi_apel').val(lokasi);
                $('#id_lokasi_apel').trigger('change');
            }, 500);
        })


        $('#export-struktur').click(function(e){
            e.preventDefault();

                window.open(`/master-jabatan-opd/master-jabatan/cetak-jabatan?satuan_kerja=${unit_kerja}`, "_blank");

        })

        $(document).on('submit', ".form-data", function(e) {
            e.preventDefault();
            let type = $(this).attr('data-type');
            if (type == 'add') {
                control.submitFormMultipart(`${url_main}/store`, 'Tambah', 'Jabatan','POST');
            } else {
                let uuid = $("input[name='uuid']").val();
                control.submitFormMultipart(`${url_main}/update/` + uuid, 'Update','Jabatan', 'POST');
            }
        });

        master_jabatan_for_atasan_langsung = (params) => {
            $.ajax({
                url: `/master-jabatan-opd/master-jabatan/showId/${params}`,
                method: "GET",
                success: function (res) {
                    let result = res.data;
                    // console.log(result);
                    control.push_select_atasan_langsung(`/master-jabatan-opd/master-jabatan/option-atasan-langsung?level=${result.level}&satuan_kerja=${result.id_satuan_kerja}`,'#id_parent');   

                    if ($('.form-data').attr('data-type') !== 'add') {
                        if (parseInt(result.level) < 7) {
                            $('#id_parent').prop('disabled', true);
                            $('#id_satuan_kerja,#id_unit_kerja,#id_lokasi_kerja,#id_lokasi_apel,#id_master_jabatan').prop('disabled', true);
                        }else{
                           $('#id_unit_kerja,#id_lokasi_kerja,#id_lokasi_apel,#id_master_jabatan,#id_parent').prop('disabled', false);
                            $('#id_parent').prop('disabled', false);
                        }
                    }
                },
                error: function (xhr) {
                alert("gagal");
                },
            });
        }

        $(document).on('click', '.button-update', function(e) {
            e.preventDefault();
            let url = `${url_main}/show/` + $(this).attr('data-uuid');
            control.overlay_form('Update', 'Jabatan', url);
        })

        $(document).on('change','#id_master_jabatan', function () {
            if ($(this).val() !== '') {
                $.ajax({
                url: `/master-jabatan-opd/master-jabatan/showId/${$(this).val()}`,
                method: "GET",
                success: function (res) {
                    let result = res.data;
                    console.log(result);
                    control.push_select_atasan_langsung(`/master-jabatan-opd/master-jabatan/option-atasan-langsung?level=${result.level}&satuan_kerja=${$('#id_satuan_kerja').val()}`,'#id_parent');   

                    if ($('.form-data').attr('data-type') !== 'add') {
                        if (parseInt(result.level) < 7) {
                            $('#id_parent').prop('disabled',true);
                        }else{
                            $('#id_parent').prop('disabled',false);
                        }
                    }
                    
                },
                error: function (xhr) {
                alert("gagal");
                },
            });
            }
        })

        $(document).on('change','#id_satuan_kerja', function () {
            if ($(this).val() !== '') {
                control.push_select(`/perangkat-daerah-opd/unit-kerja/option?satuan_kerja=${$(this).val()}`,'#id_unit_kerja');
                control.push_select(`/master-jabatan-opd/master-jabatan/option?satuan_kerja=${$(this).val()}&type=${$('.form-data').attr('data-type')}`,'#id_master_jabatan');

                control.push_select(`/perangkat-daerah-opd/lokasi/option-lokasi/${$(this).val()}`,'#id_lokasi_kerja');
                control.push_select(`/perangkat-daerah-opd/lokasi/option-lokasi-apel/${$(this).val()}`,'#id_lokasi_apel');
            }
        })

        datatable = (satuan_kerja) =>{
            let columns = [{
                data: null,
                className : 'text-right',
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            }, {
                data: 'jabatan',
                className : 'text-right',
            }, {
                data: 'pejabat',
                className : 'text-right',
            }, {
                data: 'atasan_langsung',
                className : 'text-right'
            }, {
                data: 'status',
                className : 'text-right',
                render: function(data, type, row, meta) {
                    let label = '';
                    let color = '';

                    if (data == 'definitif') {
                        label = 'Definitif';
                        color = 'success';
                    }else{
                        label = 'Pelaksana Tugas';
                        color = 'warning';
                    }

                    if (row.pejabat == null) {
                        label = 'tidak ada';
                        color = 'danger';
                    }
                    return `<span class="badge badge-${color}">${label}</span>`;
                }
            }, {
                data: 'uuid',
                className : 'text-right',
            }
            ];

            let columnDefs = [
                {
                        targets: -1,
                        title: 'Aksi',
                        width: '9rem',
                        orderable: false,
                        render: function(data, type, full, meta) {
                            return `
                                <a href="javascript:;" type="button" data-uuid="${data}" data-kt-drawer-show="true" data-kt-drawer-target="#side_form" class="btn btn-primary button-update btn-icon btn-sm" data-toggle="tooltip" title="edit"> 
                                    <img src="{{ asset('admin/assets/media/icons/edit.svg')}}" alt="" srcset="">
                                </a>
                                <a href="${url_main}/detail/${full.uuid}" type="button" data-uuid="${data}" class="btn btn-warning btn-icon btn-sm"> 
                                    <img src="{{ asset('admin/assets/media/icons/eye.svg')}}" alt="" srcset="">
                                </a>
                            `;
                            },
                    }
            ];

            control.initDatatable(`${url_main}/datatable?satuan_kerja=${satuan_kerja}`, columns, columnDefs);
        }

        $(function() {
            datatable(unit_kerja)
        })
    </script>
@endsection