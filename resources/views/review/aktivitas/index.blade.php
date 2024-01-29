@extends('layouts.layout')
@section('title', 'Review Aktifitas')
@section('content')
<div class="post d-flex flex-column-fluid" id="kt_post">
        <!--begin::Container-->
        <div id="kt_content_container" class="container">
            <div class="row">

                <div class="card">
                    <div class="card-body p-0">

                        <div class="container">
                            <div class="py-5">

                            <div class="filter-bulan">
                            <select id="filter-bulan" class="form-control form-control-solid">
                                @foreach (range(1, 12) as $bulan)
                                    <option value="{{ $bulan }}" {{ $bulan == date('n') ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::parse('2023-' . $bulan . '-01')->translatedFormat('F') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                                <table id="kt_table_data" class="table table-row-dashed table-row-gray-300 gy-7">
                                    <thead class="text-center">
                                        <tr class="fw-bolder fs-6 text-gray-800">
                                            <th>No</th>
                                            <th>NIP</th>
                                            <th>Nama</th>
                                            <th>Jabatan</th>
                                            <th>Nilai</th>
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
            let uuid = $(this).attr('data-uuid');
            window.location.href = `/review/aktivitas/review?pegawai=${uuid}&bulan=${$("#filter-bulan").val()}`;
        })
        
        $(document).on('change','#filter-bulan', function () {
            datatable($(this).val());
        })

        datatable = (bulan) =>{
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
                data: 'nilai',
                className : 'text-center',
                render: function(data, type, row, meta) {
                  let color = '';
                  
                  if (data <= 50) {
                    color = 'danger';
                  }else if(data > 50 && data <= 70){
                    color = 'warning';
                  }else{
                    color = 'success';
                  }
                  
                   return `<span class="badge badge-${color}">${data.toFixed(2)}</span>`;
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
                            <a href="javascript:;" type="button" data-uuid="${full.pegawai_uuid}" class="btn btn-primary button-review btn-block btn-sm"> 
                                <img src="{{ asset('admin/assets/media/icons/review.svg')}}" alt="" srcset="">
                            </a>
                            `;
                    },
            }];
            control.initDatatable(`/review/aktivitas/datatable?bulan=${bulan}`, columns, columnDefs);
        }

        $(function() {
            datatable($('#filter-bulan').val());
        })
    </script>
@endsection