@section('title', 'Aktivitas')
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
                                            <th>Pegawai</th>
                                            <th>Capaian Produktivitas</th>
                                            <th>Target Waktu</th>
                                            <th>Persentase</th>
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

        $(document).on('click', '.button-update', function(e) {
            e.preventDefault();
            let url = '/master-data/agama/show/' + $(this).attr('data-uuid');
            control.overlay_form('Update', 'Agama', url);
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
                data: 'nama',
                className : 'text-right',
            }, {
                data: 'capaian_waktu',
                className : 'text-right',
            }, {
                data: 'target_waktu',
                className : 'text-right',
            }, {
                data: 'nilai_produktivitas_kerja',
                className : 'text-right',
                render: function(data, type, row, meta) {
                  let color = '';
                  
                  if (data <= 50) {
                    color = 'danger';
                  }else if(data > 50 && data <= 70){
                    color = 'warning';
                  }else{
                    color = 'success';
                  }
                  
                   return `<span class="badge badge-${color}">${data}</span>`;
                }
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
                            <a href="/aktivitas-opd/detail-pegawai?pegawai=${data}" type="button" class="btn btn-primary button-review btn-sm"> 
                                <img src="{{ asset('admin/assets/media/icons/review.svg')}}" alt="" srcset="">
                            </a>
                    `;
                    },
            }];
            control.initDatatable(`/aktivitas-opd/datatable?bulan=${bulan}`, columns, columnDefs);
        }

        $(function() {
            datatable($('#filter-bulan').val());
        })
    </script>
@endsection