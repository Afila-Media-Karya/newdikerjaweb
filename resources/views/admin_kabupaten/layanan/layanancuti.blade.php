@php
    $role = hasRole();
@endphp
@section('title', 'Layanan Cuti')
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
                                <table id="kt_table_data" class="table table-row-dashed table-row-gray-300 gy-7">
                                    <thead class="text-center">
                                        <tr class="fw-bolder fs-6 text-gray-800">
                                            <th>No</th>
                                            <th>NIP</th>
                                            <th>Nama</th>
                                            <th>Jenis Cuti</th>
                                            <th>Progress</th>
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
                        <label class="form-label">Nama Layanan</label>
                        <input type="text" id="jenis_layanan" class="form-control" name="jenis_layanan" placeholder="Layanan" disabled>
                        <small class="text-danger jenis_layanan_error"></small>
                    </div>

                    <div class="row mb-10">
                        <div class="col-lg-6">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" id="tanggal_mulai" class="form-control" name="tanggal_mulai" disabled>
                            <small class="text-danger tanggal_mulai_error"></small>
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" id="tanggal_akhir" class="form-control" name="tanggal_akhir" disabled>
                            <small class="text-danger tanggal_akhir_error"></small>
                        </div>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Alamat Selama Cuti</label>
                        <textarea id="alamat" class="form-control" name="alamat" rows="5" disabled></textarea>
                        <small class="text-danger alamat_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Alasan</label>
                        <textarea id="alasan" class="form-control" name="alasan" rows="5" disabled></textarea>
                        <small class="text-danger alasan_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Keterangan</label>
                        <textarea id="keterangan" class="form-control" name="keterangan" rows="5"></textarea>
                        <small class="text-danger keterangan_error"></small>
                    </div>


                    <div class="mb-10">
                        <label class="form-label">Status Progress</label>
                        <select name="status" id="status_progress" class="form-control">
                            <option selected disabled>Pilih Status</option>
                            @if($role['guard'] == 'web')
                            <option value="1">Permohonan</option>
                            <option value="2">Perubahan</option>
                            <option value="3">Proses atasan langsung</option>
                            <option value="4">Proses SKPD</option>
                            @elseif($role['guard'] === 'administrator')
                                <option value="5">Proses BKPSDM</option>
                                <option value="6">Proses SETDA</option>
                                <option value="7">Tidak Disetujui</option>
                                <option value="8">Selesai</option>
                            @endif
                        </select>
                        <small class="text-danger status_error"></small>
                    </div>

                    <div class="mb-10" id="dokumen-cuti-konten">
                        <label class="form-label">File Dokumen Cuti</label>
                        <input type="file" id="dokumen_cuti" class="form-control" name="dokumen_cuti">
                        <small class="text-danger dokumen_cuti_error"></small>
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
        let url_main = '';
        role.guard !== 'web' ? url_main = '/layanan/layanan-cuti' : url_main = '/layanan-opd/layanan-cuti';

        $(document).on('click', '#button-side-form', function() {
            control.overlay_form('Tambah', 'Layanan Cuti');
            $('#dokumen-cuti-konten').hide();
        })

        $(document).on('submit', ".form-data", function(e) {
            e.preventDefault();
            let type = $(this).attr('data-type');
            if (type == 'add') {
                control.submitFormMultipartData(`${url_main}/store`, 'Tambah', 'Layanan Cuti','POST');
            } else {
                let uuid = $("input[name='uuid']").val();
                control.submitFormMultipartData(`${url_main}/update/` + uuid, 'Update','Layanan Cuti', 'POST');
            }
        });

        $(document).on('click', '.button-update', function(e) {
            e.preventDefault();
            let url = `${url_main}/show/` + $(this).attr('data-uuid');
            control.overlay_form('Update', 'Layanan Cuti', url);
        })

        $(document).on('click', '.button-delete', function(e) {
            e.preventDefault();
            let url = `${url_main}/delete/` + $(this).attr('data-uuid');
            let label = $(this).attr('data-label');
            control.ajaxDelete(url, label)
        })

        $(document).on('change','#status_progress', function () {
            if ($(this).val() === '8') {
                $('#dokumen-cuti-konten').show();
            }else{
                $('#dokumen-cuti-konten').hide();
            }
        })

        datatable = (satuan_kerja) =>{
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
                data: 'nama_pegawai',
                className : 'text-right',
            }, {
                data: 'jenis_layanan',
                className : 'text-right',
            }, {
                data: 'status',
                className : 'text-right',
                render: function(data, type, row, meta) {
                    let label = '';
                    if (data == '1') {
                        label = '<span class="badge badge-primary">Permohonan</span>';
                    }else if(data == '2'){
                        label = '<span class="badge badge-warning">Perubahan</span>';
                    }else if(data == '3'){
                        label = '<span class="badge badge-primary">Proses Atasan Langsung</span>';
                    }else if(data == '4'){
                        label = '<span class="badge badge-primary">Proses SKPD</span>';
                    }else if(data == '5'){
                        label = '<span class="badge badge-primary">Proses BKPSDM</span>';
                    }else if(data == '6'){
                        label = '<span class="badge badge-primary">Proses SETDA</span>';
                    }else if(data == '7'){
                        label = '<span class="badge badge-danger">Tidak Disetujui</span>';
                    }else{
                        label = '<span class="badge badge-success">Selesai</span>';
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
                width: '10rem',
                orderable: false,
                render: function(data, type, full, meta) {
                    let nilai_status = Number(full.status);
                    let button_edit = '';
                    console.log(nilai_status);
                    if (role.guard !== 'web') {
                        
                        if (nilai_status >= 4) {
                            button_edit = `<a href="javascript:;" type="button" data-uuid="${data}" data-kt-drawer-show="true" data-kt-drawer-target="#side_form" class="btn btn-primary button-update btn-icon btn-sm"> 
                                    <img src="{{ asset('admin/assets/media/icons/edit.svg')}}" alt="" srcset="">
                                </a>`;
                        }else{
                            button_edit = `<button disabled type="button" data-uuid="${data}" class="btn btn-secondary btn-icon btn-sm"> 
                                    <img src="{{ asset('admin/assets/media/icons/editdisabled.svg')}}" alt="" srcset="">
                                </button>`;
                        }
                    }else{
                        if (nilai_status >= 1 && nilai_status <= 4) {
                            button_edit = `<a href="javascript:;" type="button" data-uuid="${data}" data-kt-drawer-show="true" data-kt-drawer-target="#side_form" class="btn btn-primary button-update btn-icon btn-sm"> 
                                    <img src="{{ asset('admin/assets/media/icons/edit.svg')}}" alt="" srcset="">
                                </a>`;
                        }else{
                            button_edit = `<button disabled type="button" data-uuid="${data}" class="btn btn-secondary btn-icon btn-sm"> 
                                    <img src="{{ asset('admin/assets/media/icons/editdisabled.svg')}}" alt="" srcset="">
                                </button>`;
                        }
                    }
                    
                    

                    return `
                            
                            ${button_edit}
                            <a href="${url_main}/detail/${data}" type="button" data-uuid="${data}" class="btn btn-warning btn-icon btn-sm"> 
                                <img src="{{ asset('admin/assets/media/icons/eye.svg')}}" alt="" srcset="">
                            </a>

                            <a href="javascript:;" type="button" data-uuid="${data}" data-label="${full.nama_pegawai}" class="btn btn-danger button-delete btn-icon btn-sm"> 
                                <img src="{{ asset('admin/assets/media/icons/trash.svg')}}" alt="" srcset="">
                            </a>
                            `;
                    },
            }];
            control.initDatatable(`${url_main}/datatable?satuan_kerja=${satuan_kerja}`, columns, columnDefs);
        }

        $(function() {
            // control.dragDrop();
            // datatable();
            $('#dokumen-cuti-konten').hide();
            role.guard == 'web' ? datatable(satuan_kerja_user.id_satuan_kerja) : datatable(0);
        })
    </script>
@endsection