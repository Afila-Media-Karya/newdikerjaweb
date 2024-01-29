@php
    $role = hasRole();
@endphp
@extends('layouts.layout')
@section('button')

    <div id="kt_toolbar_container" class="container-fluid d-flex flex-end">
        <div class="d-flex align-items-center gap-2 gap-lg-3">

            @if($role['guard'] == 'administrator')
                <a href="{{ route('kabupaten.Jabatan.jabatan_plt.cetak') }}" class="btn btn-primary btn-sm" target="_blank">
                <img src="{{ asset('admin/assets/media/icons/printer.svg') }}" alt="" srcset="">
                Cetak
            </a>
            @else
            <a href="{{ route('opd.Jabatan.jabatan_plt.cetak',['satuan_kerja' => $satuan_kerja_user->id_satuan_kerja]) }}" class="btn btn-primary btn-sm" target="_blank">
                <img src="{{ asset('admin/assets/media/icons/printer.svg') }}" alt="" srcset="">
                Cetak
            </a>
            @endif
        </div>
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
                                            <th>Jabatan</th>
                                            <th>Pejabat</th>
                                            <th>Atasan Langsung</th>
                                            <th>Satuan Kerja</th>
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

    @if ($errors->any())
        // Initialize SweetAlert to display errors
        Swal.fire({
            title: 'Peringatan!',
            text: '{{ $errors->first() }}', // Display the first error message
            icon: 'warning',
            confirmButtonText: 'OK'
        });
    @endif

        let control = new Control();
        let satuan_kerja_user = {!! json_encode($satuan_kerja_user) !!};
        let role = {!! json_encode($role) !!};
        let url_main = '';
        role.guard !== 'web' ? url_main = '/jabatan/jabatan-plt' : url_main = '/jabatan-opd/jabatan-plt';
        datatable = (satuan_kerja) =>{
            let columns = [{
                data: null,
                className : 'text-center',
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            }, {
                data: 'jabatan',
                className : 'text-center',
            }, {
                data: 'pejabat',
                className : 'text-center',
                render: function(data, type, row, meta) {
                   return data !== null ?  data :  '-';
                }
            }, {
                data: 'atasan_langsung',
                className : 'text-center',
            }, {
                data: 'nama_satuan_kerja',
                className : 'text-center',
            }, {
                data: 'pegawai_uuid',
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
                            <a href="${url_main}/detail/${data}" type="button" data-uuid="${data}" class="btn btn-warning btn-icon btn-sm"> 
                                <img src="{{ asset('admin/assets/media/icons/eye.svg')}}" alt="" srcset="">
                            </a>
                            `;
                    },
            }];
            control.initDatatable(`${url_main}/datatable?satuan_kerja=${satuan_kerja}`, columns, columnDefs);
        }

        $(function() {
            role.guard == 'web' ? datatable(satuan_kerja_user.id_satuan_kerja) : datatable(0);
        })
    </script>
@endsection