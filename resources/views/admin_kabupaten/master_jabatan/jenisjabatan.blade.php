@extends('layouts.layout')
@section('title', 'Jenis Jabatan')
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
                                            <th>Jenis Jabatan</th>
                                            <th>Kelas Jabatan</th>
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
            let columns = [
                {
                    data: null,
                    className : 'text-center',
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }, {
                    data: 'jenis_jabatan',
                    className : 'text-right',
                }, {
                    data: 'kelas_jabatan',
                    className : 'text-right',
                }
            ];
            let columnDefs = [
            
            ];
            control.initDatatable('/master-jabatan/jenis-jabatan/datatable', columns, columnDefs);
        }

        $(function() {
            datatable();
        })
    </script>
@endsection