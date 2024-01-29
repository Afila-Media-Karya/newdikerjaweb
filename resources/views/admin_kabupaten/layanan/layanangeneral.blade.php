@section('title', 'Layanan General')
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
                                            <th>Satuan Kerja</th>
                                            <th>Pegawai</th>
                                            <th>Jenis Layanan</th>
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
                        <label class="form-label">Pilih Satuan Kerja</label>
                        <select class="form-select form-control" id="id_satuan_kerja" name="id_satuan_kerja" data-control="select2" data-placeholder="Pilih Satuan Kerja">
                            <option></option>
                            @foreach($satuan_kerja as $val)
                                <option value="{{$val->value}}">{{$val->text}}</option>
                            @endforeach
                        </select>
                        <small class="text-danger id_satuan_kerja_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Pilih Pegawai</label>
                        <select class="form-select form-control" id="id_pegawai" name="id_pegawai" data-control="select2" data-placeholder="Pilih Pegawai">
                            <option></option>
                        </select>
                        <small class="text-danger id_pegawai_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Jenis Layanan</label>
                        <select class="form-select form-control" name="jenis_layanan" data-control="select2" data-placeholder="Pilih Jenis Layanan">
                            <option></option>
                            @foreach($jenis_layanan as $val)
                                <option value="{{$val->value}}">{{$val->text}}</option>
                            @endforeach
                        </select>
                        <small class="text-danger agama_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Keterangan</label>
                        <input type="text" id="keterangan" class="form-control" name="keterangan" placeholder="Masukkan Keterangan">
                        <small class="text-danger keterangan_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Dokumen</label>
                        <input type="file" class="form-control" name="dokumen">
                        <small class="text-danger dokumen_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Dokumen Pendukung</label>
                        <input type="file" class="form-control" name="dokumen_pendukung">
                        <small class="text-danger dokumen_pendukung_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Pilih Status</label>
                        <select name="status_general" class="form-control">
                            <option selected disabled>Pilih Status</option>
                            <option value="Permohonan">Permohonan</option>
                            <option value="Perubahan">Perubahan</option>
                            <option value="Proses Atasan Langsung">Proses Atasan Langsung</option>
                            <option value="Proses SKPD">Proses SKPD</option>
                            <option value="Proses BKPSDM">Proses BKPSDM</option>
                            <option value="Proses SETDA">Proses SETDA</option>
                            <option value="Tidak Disetujui">Tidak Disetujui</option>
                            <option value="Selesai">Selesai</option>
                        </select>
                        <small class="text-danger status_error"></small>
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

        $(document).on('click', '#button-side-form', function() {
            control.overlay_form('Tambah', 'Layanan General');
        })

        $(document).on('submit', ".form-data", function(e) {
            e.preventDefault();
            let type = $(this).attr('data-type');
            if (type == 'add') {
                control.submitFormMultipartData('/layanan/layanan-general/store', 'Tambah', 'Layanan General','POST');
            } else {
                let uuid = $("input[name='uuid']").val();
                control.submitFormMultipartData('/layanan/layanan-general/update/' + uuid, 'Update','Layanan General', 'POST');
            }
        });


        $(document).on('click', '.button-update', function(e) {
            e.preventDefault();
            let url = '/layanan/layanan-general/show/' + $(this).attr('data-uuid');
            control.overlay_form('Update', 'Layanan', url);
        })

        $(document).on('click', '.button-delete', function(e) {
            e.preventDefault();
            let url = '/layanan/layanan-general/delete/' + $(this).attr('data-uuid');
            let label = $(this).attr('data-label');
            control.ajaxDelete(url, label)
        })

        $(document).on('change','#id_satuan_kerja', function (e) {
             e.preventDefault();
            if ($(this).val !== '') {
                control.push_select(`/perangkat-daerah/unit-kerja/option?satuan_kerja=${$(this).val()}`,'#id_unit_kerja'); 
                control.push_select(`/pegawai/list-pegawai/option?satuan_kerja=${$(this).val()}`,'#id_pegawai')
            }
        })


        datatable = () =>{
            let columns = [{
                data: null,
                className : 'text-center',
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            }, {
                data: 'nama_satuan_kerja',
                className : 'text-right',
            }, {
                data: 'nama_pegawai',
                className : 'text-right',
            }, {
                data: 'jenis_layanan',
                className : 'text-right',
            }, {
                data: 'uuid',
                className : 'text-center',
            }];
            let columnDefs = [{
                targets: -1,
                title: 'Aksi',
                width: '9rem',
                orderable: false,
                render: function(data, type, full, meta) {
                    return `
                            <a href="javascript:;" type="button" data-uuid="${data}" data-kt-drawer-show="true" data-kt-drawer-target="#side_form" class="btn btn-primary button-update btn-icon btn-sm"> 
                                <img src="{{ asset('admin/assets/media/icons/edit.svg')}}" alt="" srcset="">
                            </a>

                            <a href="javascript:;" type="button" data-uuid="${data}" data-label="${full.nama}" class="btn btn-danger button-delete btn-icon btn-sm"> 
                                <img src="{{ asset('admin/assets/media/icons/trash.svg')}}" alt="" srcset="">
                            </a>
                            `;
                    },
            }];
            control.initDatatable('/layanan/layanan-general/datatable', columns, columnDefs);
        }

        $(function() {
            control.dragDrop();
            datatable();
        })
    </script>
@endsection