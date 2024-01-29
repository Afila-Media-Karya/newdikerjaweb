@php
    $role = hasRole();
@endphp
@extends('layouts.layout')
@section('title', 'Pegawai Non Job')
@section('content')
<div class="post d-flex flex-column-fluid" id="kt_post">
        <!--begin::Container-->
        <div id="kt_content_container" class="container">
            <div class="row">

                <div class="card">
                    <div class="card-body p-0">

                        <div class="container">
                            <div class="py-5">

                                @if ($role['guard'] == 'administrator' && $role['role'] == '2')
                                    <div style="position:absolute">
                                        <div class="btn-group"
                                            style="position: relative;left: 26rem;top: 12px;width:38rem;">
                                            <select name="tenaga_kesehatan_id" id="satuan_kerja_filter"
                                                data-control="select2" data-placeholder="Filter Satuan Kerja"
                                                class="form-control form-control-sm form-control-solid">
                                                <option selected value="0">Filter by Unit Kerja</option>
                                                @foreach ($unit_kerja as $val)
                                                    <option value="{{ $val->value }}">{{ $val->text }}</option>
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
                                            <th>Satuan Kerja</th>
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
        role.guard !== 'web' ? url_main = '/pegawai/pegawai-non-job' : url_main = '/pegawai-opd/pegawai-non-job';

        $(document).on('change', '#satuan_kerja_filter', function() {
            datatable($(this).val());
        })

        datatable = (satker) =>{
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
                data: 'nama_satuan_kerja',
                className : 'text-center',
            }];
            let columnDefs = [];
            control.initDatatable(`${url_main}/datatable?satuan_kerja=${satker}`, columns, columnDefs);
        }

        $(function() {
            datatable(0);
        })
    </script>
@endsection