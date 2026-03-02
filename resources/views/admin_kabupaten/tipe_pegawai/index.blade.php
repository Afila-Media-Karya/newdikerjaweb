@extends('layouts.layout')
@section('title', 'Tipe Pegawai')
@section('button')
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
        <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
            data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
            class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
            <button class="btn btn-primary btn-sm" data-kt-drawer-show="true" data-kt-drawer-target="#side_form"
                id="button-side-form"><i class="fa fa-plus-circle" style="color:#ffffff" aria-hidden="true"></i> Tambah
                Data</button>
        </div>
    </div>
@endsection
@section('content')
    <div class="post d-flex flex-column-fluid" id="kt_post">
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
                                            <th>Kode</th>
                                            <th>Nama</th>
                                            <th>Keterangan</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('side-form')
    <div id="side_form" class="bg-white" data-kt-drawer="true" data-kt-drawer-activate="true"
        data-kt-drawer-toggle="#side_form_button" data-kt-drawer-close="#side_form_close" data-kt-drawer-width="500px">
        <div class="card w-100">
            <div class="card-header pe-5">
                <div class="card-title">
                    <div class="d-flex justify-content-center flex-column me-3"><a href="#"
                            class="fs-4 fw-bolder text-gray-900 text-hover-primary me-1 lh-1 title_side_form"></a></div>
                </div>
                <div class="card-toolbar">
                    <div class="btn btn-sm btn-icon btn-active-light-primary" id="side_form_close"><span
                            class="svg-icon svg-icon-2"><svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px"
                                viewBox="0 0 24 24">
                                <g transform="translate(12,12) rotate(-45) translate(-12,-12) translate(4,4)"
                                    fill="#000000">
                                    <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1" />
                                    <rect fill="#000000" opacity="0.5"
                                        transform="translate(8,8) rotate(-270) translate(-8,-8)" x="0" y="7" width="16"
                                        height="2" rx="1" />
                                </g>
                            </svg></span></div>
                </div>
            </div>
            <div class="card-body hover-scroll-overlay-y">
                <form class="form-data">
                    <input type="hidden" name="id"><input type="hidden" name="uuid">
                    <div class="mb-10">
                        <label class="form-label">Kode</label>
                        <input type="text" class="form-control" name="kode" placeholder="Contoh: pegawai_administratif">
                        <small class="text-danger kode_error"></small>
                    </div>
                    <div class="mb-10">
                        <label class="form-label">Nama</label>
                        <input type="text" class="form-control" name="nama" placeholder="Contoh: Pegawai Administratif">
                        <small class="text-danger nama_error"></small>
                    </div>
                    <div class="mb-10">
                        <label class="form-label">Keterangan</label>
                        <input type="text" class="form-control" name="keterangan" placeholder="Opsional">
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
        </div>
    </div>
@endsection
@section('script')
    <script>
        let control = new Control();
        $(document).on('click', '#button-side-form', function () { control.overlay_form('Tambah', 'Tipe Pegawai'); });
        $(document).on('submit', ".form-data", function (e) {
            e.preventDefault();
            let type = $(this).attr('data-type');
            if (type == 'add') { control.submitFormMultipart('/tipe-pegawai/store', 'Tambah', 'Tipe Pegawai', 'POST'); }
            else { let uuid = $("input[name='uuid']").val(); control.submitFormMultipart('/tipe-pegawai/update/' + uuid, 'Update', 'Tipe Pegawai', 'POST'); }
        });
        $(document).on('click', '.button-update', function (e) { e.preventDefault(); control.overlay_form('Update', 'Tipe Pegawai', '/tipe-pegawai/show/' + $(this).attr('data-uuid')); });
        $(document).on('click', '.button-delete', function (e) { e.preventDefault(); control.ajaxDelete('/tipe-pegawai/delete/' + $(this).attr('data-uuid'), $(this).attr('data-label')); });
        datatable = () => {
            let columns = [
                { data: null, className: 'text-center', render: (d, t, r, m) => m.row + m.settings._iDisplayStart + 1 },
                { data: 'kode', className: 'text-left' },
                { data: 'nama', className: 'text-left' },
                { data: 'keterangan', className: 'text-left' },
                { data: 'is_active', className: 'text-center', render: (d) => d ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-danger">Nonaktif</span>' },
                { data: 'uuid', className: 'text-center' }
            ];
            let columnDefs = [{
                targets: -1, title: 'Aksi', width: '9rem', orderable: false, render: (data, t, full) => `
                <a href="javascript:;" data-uuid="${data}" data-kt-drawer-show="true" data-kt-drawer-target="#side_form" class="btn btn-primary button-update btn-icon btn-sm"><img src="{{ asset('admin/assets/media/icons/edit.svg')}}" alt=""></a>
                <a href="javascript:;" data-uuid="${data}" data-label="${full.nama}" class="btn btn-danger button-delete btn-icon btn-sm"><img src="{{ asset('admin/assets/media/icons/trash.svg')}}" alt=""></a>`
            }];
            control.initDatatable('/tipe-pegawai/datatable', columns, columnDefs);
        }
        $(function () { datatable(); })
    </script>
@endsection