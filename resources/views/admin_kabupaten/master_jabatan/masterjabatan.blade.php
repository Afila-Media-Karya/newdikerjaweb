@php
    $role = hasRole();
@endphp
@section('title', 'Master Jabatan')
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

                            @if($role['guard'] == 'administrator' && $role['role'] == '2')
                                <div style="position:absolute">
                                    <div class="btn-group" style="position: relative;left: 26rem;top: 12px;width:38rem;">
                                        <select name="tenaga_kesehatan_id" class="form-c" id="satuan_kerja_filter" data-control="select2" data-placeholder="Filter Satuan Kerja" class="form-control form-control-sm form-control-solid">
                                            <option selected value="0">Filter by Perangkat Daerah</option>
                                            @foreach($satuan_kerja as $val)
                                                <option value="{{$val->value}}">{{$val->text}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif

                                <table id="kt_table_data" class="table table-row-dashed table-row-gray-300 gy-7">
                                    <thead class="text-center">
                                        <tr class="fw-bolder fs-6 text-gray-800">
                                            <th>No</th>
                                            <th>Jabatan</th>
                                            <th>Jenis Jabatan</th>
                                            <th>Unit Kerja</th>
                                            <th>Kelas Jabatan</th>
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
                        <label class="form-label">Nama Struktur Jabatan</label>
                        <input type="text" id="nama_struktur" class="form-control" name="nama_struktur" placeholder="Masukkan Struktur Jabatan">
                        <small class="text-danger nama_struktur_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Nama Jabatan</label>
                        <input type="text" id="nama_jabatan" class="form-control" name="nama_jabatan" placeholder="Masukkan Jabatan">
                        <small class="text-danger nama_jabatan_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Nama Jenis Jabatan</label>
                        <select class="form-select form-control" id="jenis_jabatan" name="jenis_jabatan" data-control="select2" data-placeholder="Pilih Jenis Jabatan">
                            <option></option>
                            @foreach($jenis_jabatan as $val)
                                <option value="{{$val->value}}" data-id="{{$val->id}}" data-level="{{ $val->level }}" data-kelas="{{ $val->kelas }}">{{$val->text}} - Kelas {{$val->kelas}}</option>
                            @endforeach
                        </select>
                        <small class="text-danger jenis_jabatan_error"></small>
                    </div>

                    <input type="hidden" name="kelas_jabatan" id="kelas_jabatan">
                    <input type="hidden" name="level_jabatan" id="level_jabatan">

                    <div class="mb-10">
                        <label class="form-label">Pagu TPP</label>
                        <input type="text" id="pagu_tpp" class="form-control pagu_tpp" value="0" name="pagu_tpp" data-inputmask="'alias': 'currency', 'radixPoint': ',', 'groupSeparator': '.', 'numericInput': true, 'autoUnmask': true, 'rightAlign': false">
                        <small class="text-danger pagu_tpp_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Target Waktu</label>
                        <input type="number" id="target_waktu" class="form-control" name="target_waktu" placeholder="Masukkan Targer Waktu">
                        <small class="text-danger target_waktu_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Satuan Kerja</label>
                        <select class="form-select form-control" id="id_satuan_kerja" name="id_satuan_kerja" data-control="select2" data-placeholder="Pilih Satuan Kerja">
                            <option value="0"selected> Semua</option>
                            @foreach($satuan_kerja as $val)
                                <option value="{{$val->value}}">{{$val->text}}</option>
                            @endforeach
                        </select>
                        <small class="text-danger id_satuan_kerja_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Kelompok Jabatan</label>
                        <select class="form-select form-control" id="kelompok_jabatan" name="id_kelompok_jabatan" data-control="select2" data-placeholder="Pilih Kelompok Jabatan">
                            <option></option>
                        </select>
                        <small class="text-danger id_kelompok_jabatan_error"></small>
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
        let url_main = '/master-jabatan/master-jabatan';
        let _jenis_jabatan = '';

        $(document).on('click', '#button-side-form', function() {
            control.overlay_form('Tambah', 'Master Jabatan');
        })

        $(document).on('submit', ".form-data", function(e) {
            e.preventDefault();
            let type = $(this).attr('data-type');
            if (type == 'add') {
                control.submitFormMultipart(`${url_main}/store`, 'Tambah', 'Master Jabatan','POST');
            } else {
                let uuid = $("input[name='uuid']").val();
                control.submitFormMultipart(`${url_main}/update/` + uuid, 'Update','Master Jabatan', 'POST');
            }
        });

        $(document).on('click', '.button-update', function(e) {
            e.preventDefault();
            let url = `${url_main}/show/` + $(this).attr('data-uuid');
            control.overlay_form('Update', 'Master Jabatan', url);
        })

        $(document).on('click', '.button-delete', function(e) {
            e.preventDefault();
            let url = `${url_main}/delete/` + $(this).attr('data-uuid');
            let label = $(this).attr('data-label');
            control.ajaxDelete(url, label)
        })

        $(document).on('change','#jenis_jabatan', function () {
            var selectedDataId = $(this).find('option:selected').data('id');
            var selectedDataLevel = $(this).find('option:selected').data('level');
            var selectedDataKelas = $(this).find('option:selected').data('kelas');

            _jenis_jabatan = selectedDataId;
            $('#kelas_jabatan').val(selectedDataKelas);
            $('#level_jabatan').val(selectedDataLevel);

            control.push_select(`/master-jabatan/master-jabatan/option-kelompok-jabatan?jenis_jabatan=${selectedDataId}&level=${selectedDataLevel}`,'#kelompok_jabatan');
        })

        $(document).on('change','#satuan_kerja_filter', function () {
            datatable($(this).val());
        })

        datatable = (satuan_kerja) =>{
            let columns = [{
                data: null,
                className : 'text-center',
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            }, {
                data: 'nama_jabatan',
                className : 'text-right',
            }, {
                data: 'jenis_jabatan',
                className : 'text-right',
            }, {
                data: 'nama_satuan_kerja',
                className : 'text-right',
            }, {
                data: 'kelas_jabatan',
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
                            <a href="javascript:;" type="button" data-uuid="${data}" data-kt-drawer-show="true" data-kt-drawer-target="#side_form" class="btn btn-primary button-update btn-icon btn-sm"> 
                                <img src="{{ asset('admin/assets/media/icons/edit.svg')}}" alt="" srcset="">
                            </a>

                            <a href="javascript:;" type="button" data-uuid="${data}" data-label="${full.nama_jabatan}" class="btn btn-danger button-delete btn-icon btn-sm"> 
                                <img src="{{ asset('admin/assets/media/icons/trash.svg')}}" alt="" srcset="">
                            </a>
                            `;
                    },
            }];
            control.initDatatable(`${url_main}/datatable?satuan_kerja=${satuan_kerja}`, columns, columnDefs);
        }

        $(function() {
             Inputmask("Rp 999.999.999", {
            radixPoint: ",",
            groupSeparator: ".",
            numericInput: true
            }).mask("#pagu_tpp");
            // datatable();
            datatable($('#satuan_kerja_filter').val())
        })
    </script>
@endsection