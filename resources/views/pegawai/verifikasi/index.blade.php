@php
    $role = hasRole();
@endphp
@extends('layouts.layout')
@section('title', 'Verifikasi Pegawai')
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
                                            <option></option>
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
                                            <th>NIP</th>
                                            <th>Nama</th>
                                            <th>Tanggal Lahir</th>
                                            <th>Status</th>
                                            <th>Verifikasi</th>
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

@section('script')
    <script>
        let control = new Control();
        let role = {!! json_encode($role) !!};
        let satuan_kerja_user = {!! json_encode($satuan_kerja_user) !!};
        let url_main = '';
        role.guard !== 'web' ? url_main = '/pegawai/verifikasi' : url_main = '/pegawai-opd/verifikasi-opd';

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
                data: 'nip',
                className : 'text-right',
            }, {
                data: 'nama',
                className : 'text-right',
            }, {
                data: null,
                className : 'text-right',
                render: function(data, type, row, meta) {
                    return `${row.tempat_lahir}, ${row.tanggal_lahir}`;
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
                data: 'status_verifikasi',
                className : 'text-center',
                render: function(data, type, row, meta) {
                    if (data == true) {
                        return `<a href="#" type="button" class="btn btn-success btn-icon btn-sm"> 
                                <img src="{{ asset('admin/assets/media/icons/checkmark.svg')}}" alt="" srcset="">
                            </a>`;
                    }else{
                        return `<a href="#" type="button" class="btn btn-danger btn-icon btn-sm"> 
                                <img src="{{ asset('admin/assets/media/icons/close.svg')}}" alt="" srcset="">
                            </a>`;
                    }
                }
            }, {
                data: 'uuid',
                className : 'text-center',
            }];
            let columnDefs = [{
                targets: -1,
                title: 'Aksi',
                width: 'auto',
                orderable: false,
                render: function(data, type, full, meta) {
                    return `
                            <a href="${url_main}/detail/${data}" type="button" data-uuid="${data}" class="btn btn-warning btn-icon btn-sm"> 
                                <img src="{{ asset('admin/assets/media/icons/eye.svg')}}" alt="" srcset="">
                            </a>
                            `;
                    },
            }];
            control.initDatatable(`${url_main}/datatable?satuan_kerja=${satuan_kerja}`, columns, columnDefs);
        }

        $(function() {
            role.guard == 'web' ? datatable(satuan_kerja_user.id_satuan_kerja) : datatable($('#satuan_kerja_filter').val());
        })
    </script>
@endsection