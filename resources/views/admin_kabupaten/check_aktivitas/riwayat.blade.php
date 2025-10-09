@extends('layouts.layout')
@section('title', 'Hari Libur')
@section('button')
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
        <!--begin::Page title-->
        <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
            data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
            class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
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
                                            <th>Pegawai</th>
                                            <th>Aktivitas</th>
                                            <th>Keterangan</th>
                                            <th>Volume</th>
                                            <th>Satuan</th>
                                            <th>Tanggal</th>
                                            <th>Di hapus Oleh</th>
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

        datatable = () =>{
            let columns = [{
                data: null,
                className : 'text-center',
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            }, {
                data: 'nama_pegawai',
                className : 'text-right',
            }, {
                data: 'aktivitas',
                className : 'text-right',
            },{
                data: 'keterangan',
                className : 'text-center',
            },{
                data: 'volume',
                className : 'text-center',
            },{
                data: 'satuan',
                className : 'text-center',
            },{
                data: 'tanggal',
                className : 'text-center',
            },{
                data: 'nama_approval',
                className : 'text-center',
            }];
            let columnDefs = [];
            control.initDatatable('/riwayat-aktivitas/datatable', columns, columnDefs);
        }

        $(function() {
            datatable();
        })
    </script>
@endsection