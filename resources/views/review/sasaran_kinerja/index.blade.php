@extends('layouts.layout')
@section('title', 'Review Sasaran Kinerja')
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
                                            <th>Jabatan</th>
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
@section('script')
    <script>
        let control = new Control();

        $(document).on('click', '.button-review', function () {
            let jabatan = $(this).attr('data-jabatan');
            let level = $(this).attr('data-level');
            window.location.href = `/review/sasaran-kinerja/review?jabatan=${jabatan}&level=${level}`
        })

        datatable = () =>{
            let columns = [{
                data: null,
                className : 'text-center',
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            }, {
                data: 'nip',
                className : 'text-center',
            }, {
                data: 'nama',
                className : 'text-center',
            }, {
                data: 'nama_jabatan',
                className : 'text-center'
            }, {
                data: 'status_review',
                className : 'text-center',
                render: function(data, type, row, meta) {
                    let label = '';
                    let color = '';
                    if (data == 'Selesai') {
                        label = data;
                        color = 'success';
                    }else if(data == 'Belum Review'){
                        label = data;
                        color = 'danger';
                    }else{
                        label = data;
                        color = 'warning';
                    }

                   return `<span class="badge badge-${color}">${label}</span>`;
                }
            }, {
                data: 'uuid',
                className : 'text-center',
            }
            ];
            let columnDefs = [{
                targets: -1,
                title: 'Aksi',
                width: '9rem',
                orderable: false,
                render: function(data, type, full, meta) {
                    return `
                            <a href="javascript:;" type="button" data-jabatan="${full.id_jabatan}" data-level="${full.level_jabatan}" class="btn btn-primary button-review btn-block btn-sm"> 
                                <img src="{{ asset('admin/assets/media/icons/review.svg')}}" alt="" srcset="">
                            </a>
                            `;
                    },
            }];
            control.initDatatable('/review/sasaran-kinerja/datatable', columns, columnDefs);
        }

        $(function() {
            datatable();
        })
    </script>
@endsection