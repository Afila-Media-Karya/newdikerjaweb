@php
    $role = hasRole();
@endphp
@section('title', 'Jabatan Kosong')
@extends('layouts.layout')
@section('button')

    <div id="kt_toolbar_container" class="container-fluid d-flex flex-end">
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            @if ($role['guard'] == 'administrator')
                <a href="{{ route('kabupaten.Jabatan.jabatan_kosong.cetak') }}?type=excel"
                    class="btn btn-success btn-sm export-laporan">
                    <img src="{{ asset('admin/assets/media/icons/excel.svg') }}" style="position: relative; bottom: 1px;"
                        alt="" srcset=""> Export Excel
                </a>
                <a href="{{ route('kabupaten.Jabatan.jabatan_kosong.cetak') }}?type=pdf"
                    class="btn btn-danger btn-sm export-laporan">
                    <img src="{{ asset('admin/assets/media/icons/pdf.svg') }}" style="position: relative; bottom: 1px;"
                        alt="" srcset=""> Export PDF
                </a>
            @else
                <a href="{{ route('opd.Jabatan.jabatan_kosong.cetak', ['satuan_kerja' => $satuan_kerja_user]) }}?type=excel"
                    class="btn btn-primary btn-sm export-laporan">
                    <img src="{{ asset('admin/assets/media/icons/excel.svg') }}" style="position: relative; bottom: 1px;"
                        alt="" srcset=""> Export Excel
                </a>
                <a href="{{ route('opd.Jabatan.jabatan_kosong.cetak', ['satuan_kerja' => $satuan_kerja_user]) }}?type=pdf"
                    class="btn btn-danger btn-sm export-laporan">
                    <img src="{{ asset('admin/assets/media/icons/pdf.svg') }}" style="position: relative; bottom: 1px;"
                        alt="" srcset=""> Export PDF
                </a>
            @endif
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

                            @if($role['guard'] == 'administrator' && $role['role'] == '2')
                                    <div style="position:absolute">
                                    <div class="btn-group" style="position: relative;left: 26rem;top: 12px;width:38rem;">
                                        <select name="tenaga_kesehatan_id" id="satuan_kerja_filter" data-control="select2" data-placeholder="Filter Satuan Kerja" class="form-control form-control-sm form-control-solid">
                                            <option selected value="0">Filter by Unit Kerja</option>
                                            @foreach($unit_kerja as $val)
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
                                            <th>Atasan Langsung</th>
                                            <th>Satuan Kerja</th>
                                            @if ($role['guard'] !== 'web')
                                                <th>Aksi</th>
                                            @endif
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
                                    <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1" />
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
                        <label class="form-label">Jabatan</label>
                        <input type="text" id="jabatan" class="form-control" name="jabatan">
                        <small class="text-danger jabatan_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Pilih Satuan Kerja</label>
                        <select class="form-select form-control" id="id_satuan_kerja" name="id_satuan_kerja"
                            data-control="select2" data-placeholder="Pilih Satuan Kerja">
                            <option></option>
                            @foreach ($satuan_kerja as $val)
                                <option value="{{ $val->value }}">{{ $val->text }}</option>
                            @endforeach
                        </select>
                        <small class="text-danger id_satuan_kerja_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Unit Kerja</label>
                        <select class="form-select form-control" name="id_unit_kerja" id="id_unit_kerja"
                            data-control="select2" data-placeholder="Pilih Unit Kerja">
                            <option></option>
                        </select>
                        <small class="text-danger id_unit_kerja_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Pilih Pegawai</label>
                        <select class="form-select form-control" id="id_pegawai" name="id_pegawai"
                            data-control="select2" data-placeholder="Pilih Pegawai">
                            <option></option>
                        </select>
                        <small class="text-danger id_pegawai_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Status</label>
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" name="status_jabatan" type="radio"
                                        value="definitif" id="radio1">
                                    <label class="form-check-label" for="radio1">
                                        Definitif
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" name="status_jabatan" type="radio" value="plt"
                                        id="radio2">
                                    <label class="form-check-label" for="radio2">
                                        PLT
                                    </label>
                                </div>
                            </div>
                        </div>
                        <small class="text-danger status_jabatan_error"></small>
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
        let satuan_kerja_user = {!! json_encode($satuan_kerja_user) !!};
        let role = {!! json_encode($role) !!};
        let url_main = '';
        role.guard !== 'web' ? url_main = '/jabatan/jabatan-kosong' : url_main = '/jabatan-opd/jabatan-kosong';

        $(document).on('click', '#button-side-form', function() {
            control.overlay_form('Tambah', 'Jabatan Kosong');
        })

        $(document).on('submit', ".form-data", function(e) {
            e.preventDefault();
            let type = $(this).attr('data-type');
            if (type == 'add') {
                control.submitFormMultipart(`${url_main}/store`, 'Tambah', 'Jabatan Kosong', 'POST');
            } else {
                let uuid = $("input[name='uuid']").val();
                control.submitFormMultipart(`${url_main}/update/` + uuid, 'Update', 'Jabatan Kosong', 'POST');
            }
        });

        $(document).on('click', '.button-update', function(e) {
            e.preventDefault();
            let url = `${url_main}/show/` + $(this).attr('data-uuid');
            control.overlay_form('Update', 'Jabatan Kosong', url);
        })

        $(document).on('click', '.button-delete', function(e) {
            e.preventDefault();
            let url = `${url_main}/delete/` + $(this).attr('data-uuid');
            let label = $(this).attr('data-label');
            control.ajaxDelete(url, label)
        })

        $(document).on('change', '#id_satuan_kerja', function(e) {
            e.preventDefault();
            control.push_select(`/perangkat-daerah/unit-kerja/option?satuan_kerja=${$(this).val()}`,
                '#id_unit_kerja');
        })

        $(document).on('change', '#id_unit_kerja', function(e) {
            e.preventDefault();
            role.guard !== 'web' ? control.push_select(
                `/pegawai/list-pegawai/option?satuan_kerja=${$('#id_satuan_kerja').val()}&unit_kerja=${$(this).val()}`,
                '#id_pegawai') : control.push_select(
                `/pegawai-opd/list-pegawai-opd/option?satuan_kerja=${$('#id_satuan_kerja').val()}&unit_kerja=${$(this).val()}`,
                '#id_pegawai');
        })

        $('.export-laporan').click(function(e) {
            e.preventDefault();
            let type = $(this).attr('data-type');
            let params = $('#satuan_kerja_filter').val() ? $('#satuan_kerja_filter').val() : '';
            let url_main = $(this).attr('href');
            window.open(`${url_main}&satuan_kerja=${params}`, "_blank");
        })

        $(document).on('change','#satuan_kerja_filter', function () {
            datatable($(this).val());
        })

        datatable = (satuan_kerja) =>{
            let columns = [{
                data: null,
                className: 'text-center',
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            }, {
                data: 'jabatan',
                className: 'text-right',
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
                data: 'atasan_langsung',
                className: 'text-right',
            }, {
                data: 'nama_satuan_kerja',
                className: 'text-right',
            }, {
                data: 'uuid',
                className: 'text-center',
            }];

            if (role.guard === 'web') {
                columns = [{
                    data: null,
                    className: 'text-center',
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }, {
                    data: 'jabatan',
                    className: 'text-right',
                }, {
                    data: 'atasan_langsung',
                    className: 'text-right',
                }, {
                    data: 'nama_satuan_kerja',
                    className: 'text-right',
                }];
            }
            let columnDefs = [];

            if (role.guard !== 'web') {
                columnDefs = [{
                    targets: -1,
                    title: 'Aksi',
                    width: '9rem',
                    orderable: false,
                    render: function(data, type, full, meta) {
                        return `
                            <a href="javascript:;" type="button" data-uuid="${data}" data-kt-drawer-show="true" data-kt-drawer-target="#side_form" class="btn btn-primary button-update btn-icon btn-sm"> 
                                <img src="{{ asset('admin/assets/media/icons/edit.svg') }}" alt="" srcset="">
                            </a>
                            `;
                    },
                }];
            }

            control.initDatatable(`${url_main}/datatable?satuan_kerja=${satuan_kerja}`, columns, columnDefs);
        }

        $(function() {
            // datatable();
            role.guard == 'web' ? datatable(satuan_kerja_user) : datatable(0);
        })
    </script>
@endsection
