@extends('layouts.layout')
@section('style')
    <style>
        .table-group thead th {
            border: 1px solid #dee2e6;
        }

        /* Tambahkan padding pada sel */
        .table-group td, .table-group th {
            border: 1px solid #dee2e6;
            padding: 0.5rem; /* Sesuaikan sesuai kebutuhan Anda */
        }
    </style>
@endsection
@section('title', 'Realisasi')
@section('content')
<div class="post d-flex flex-column-fluid" id="kt_post">
        <!--begin::Container-->
        <div id="kt_content_container" class="container">
            <div class="row">

                <div class="card">
                    <div class="card-body p-0">

                        <div class="d-flex justify-content-end mt-5" style="position:relative; right:30px;">
                            <span class="svg-icon svg-icon-1">
                                <svg style="position: relative;left: 34px; top: 10px;" xmlns="http://www.w3.org/2000/svg"
                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                    viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24"></rect>
                                        <path
                                            d="M14.2928932,16.7071068 C13.9023689,16.3165825 13.9023689,15.6834175 14.2928932,15.2928932 C14.6834175,14.9023689 15.3165825,14.9023689 15.7071068,15.2928932 L19.7071068,19.2928932 C20.0976311,19.6834175 20.0976311,20.3165825 19.7071068,20.7071068 C19.3165825,21.0976311 18.6834175,21.0976311 18.2928932,20.7071068 L14.2928932,16.7071068 Z"
                                            fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                                        <path
                                            d="M11,16 C13.7614237,16 16,13.7614237 16,11 C16,8.23857625 13.7614237,6 11,6 C8.23857625,6 6,8.23857625 6,11 C6,13.7614237 8.23857625,16 11,16 Z M11,18 C7.13400675,18 4,14.8659932 4,11 C4,7.13400675 7.13400675,4 11,4 C14.8659932,4 18,7.13400675 18,11 C18,14.8659932 14.8659932,18 11,18 Z"
                                            fill="#000000" fill-rule="nonzero"></path>
                                    </g>
                                </svg>
                            </span>

                            <input type="text" id="search_" class="form-control w-250px ps-15"
                                placeholder="Search">
                        </div>

                        <div class="container">
                            <div class="py-5">
                                <table id="kt_table_data" class="table table-group table-row-gray-300 gy-7">
                                    <thead class="text-center">
                                        <tr class="fw-bolder fs-6 text-gray-800">
                                            <th>No</th>
                                            <th>Jenis Kinerja</th>
                                            <th>Rencana Kerja</th>
                                            <th>Indikator Kinerja Individu</th>
                                            <th>Target</th>
                                            <th>Satuan</th>
                                            <th>Realisasi</th>
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
                            class="fs-4 fw-bolder text-gray-900 text-hover-primary me-1 lh-1 title_side_form">Realisasi Pegawai</a>
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
                    <div id="konten-form">

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
<script src="{{ asset('admin/assets/plugins/custom/formrepeater/formrepeater.bundle.js') }}"></script>
<script src="https://cdn.jsdelivr.net/gh/ashl1/datatables-rowsgroup@v1.0.0/dataTables.rowsGroup.js"></script>

    <script>
        let control = new Control();
        let level = {!! json_encode($level) !!};
        $(document).on('click', '#button-side-form', function() {
            control.overlay_form('Tambah', 'Sasaran Kinerja');
        })

        $(document).on('submit', ".form-data", function(e) {
            e.preventDefault();
            control.submitFormMultipart('/realisasi/realisasi-skp/', 'Realisasi','Realisasi Sasaran Kinerja', 'POST');
        });

        $(document).on('click', '.button-update', function(e) {
            e.preventDefault();
            $.ajax({
                url : '/realisasi/show/' + $(this).attr('data-uuid'),
                type : 'GET',
                success : function (res) {
                    if (res.data) {
                            let form = '';
                            let label = '';
                            let no = 1;
                            $.each(res.data, function (x,y) {

                                level > 2 ? label = y.aspek_skp : label = no++;

                               form += `
                               <input type="hidden" name="uuid[${x}]" value="${y.uuid}">
                                <div class="form-group row">
                                    <div class="form-group mb-5">
                                        <label for="exampleTextarea">Indikator ${label}</label>
                                    </div>
                                    <div class="row mb-10">
                                        <div class="col-md-12">
                                            <label class="form-label">Indikator</label>
                                            <p>${y.iki}</p>  
                                        </div>
                                    </div>
                                    <div class="row mb-10">
                                        <div class="col-lg-6">
                                            <label class="form-label">Target</label>
                                            <div>
                                                <span class="badge badge-success" style="width:100%">${y.target}</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <label class="form-label">Satuan</label>
                                            <div>
                                                <span class="badge badge-success" style="width:100%">${y.satuan}</span>
                                            </div>
                                        </div>
                                </div>
                                    <div class="row mb-10">
                                        <div class="col-lg-6">
                                            <label class="form-label">Realisasi</label>
                                            <input type="number" class="form-control realisasi" name="realisasi[${x}]" value="${y.realisasi}" placeholder="Realisasi" max="${y.target}">
                                            <small class="text-danger realisasi[0]_error"></small>
                                        </div>
                                        <div class="col-lg-6">
                                            <label class="form-label">Jenis Satuan</label>
                                            <input type="text" class="form-control " name="satuan" value="${y.satuan}" disabled>
                                            <small class="text-danger satuan_error"></small>
                                        </div>
                                    </div>
                                </div>
                            `; 
                            })
                            $('#konten-form').html(form);
                            
                        
                    }
                    
               

                },
                error : function (xhr) {
                    alert('gagal');
                }
            })
        })

        $(document).on('input','.realisasi', function () {
            var maxValue = parseInt($(this).attr('max'));

            if ($(this).val() > maxValue) {
                $(this).val(maxValue);
            }
        })

        $(document).on('keyup', '#search_', function(e) {
            e.preventDefault();
            control.searchTable(this.value);
        })

        $(document).on('click', '.button-delete', function(e) {
            e.preventDefault();
            let url = '/sasaran-kinerja/delete/' + $(this).attr('data-uuid');
            let label = $(this).attr('data-label');
            control.ajaxDelete(url, label)
        })

        $('#repeater_iki').repeater({
            initEmpty: false,

            defaultValues: {
                'text-input': 'foo'
            },

            show: function () {
                $(this).slideDown();
                updateErrorClasses($(this));
            },

            hide: function (deleteElement) {
                $(this).slideUp(deleteElement);
            }

        });

        var arrays = [];
        function updateErrorClasses(repeaterElement) {
            repeaterElement.find('[class^="form-control"]').each(function(index,element) {
                var newIndex = index + 1;
                var nameAttributeValue = $(element).attr('name');
                
               var matches = nameAttributeValue.match(/\[([^\]]+)\]/g);

                if (matches) {
                    let indexes = matches[0].replace(/\[|\]/g, '');
                    var lastMatch = matches[matches.length - 1]; // Mengambil cocokan terakhir
                    var extractedValue = lastMatch.substring(1, lastMatch.length - 1); // Menghilangkan kurung siku [..]
                    // console.log(indexes); 
                    $(this).siblings('.text-danger').removeClass().addClass('text-danger repeater_iki_' + extractedValue + '_' + indexes + '_error');
                } else {
                    console.log("Tidak ada nilai yang dapat diekstrak.");
                }
            });
        }

        datatable = async () =>{

            var currentNumber = null;
            var cntNumber = 0;
            var current = null;
            var cnt = 0;

            let table = $('#kt_table_data');
            table.DataTable().clear().destroy();
            await table.dataTable().fnClearTable();
            await table.dataTable().fnDraw();
            await table.dataTable().fnDestroy();

            table.DataTable({
                responsive: true,
                pageLength: 10,
                order: [
                    [1, 'desc']
                ],
                processing: true,
                ajax: '/realisasi/datatable',
                columns: [
                {
                    data: 'id_skp',
                    className : 'text-center',
                    render: function(data, type, row, meta) {
                        let id = row.id_skp;

                        if (row.id_skp != currentNumber) {
                            currentNumber = row.id_skp;
                            cntNumber++;
                        }

                        if (row.id_skp != current) {
                            current = row.id_skp;
                            cnt = 1;
                        } else {
                            cnt++;
                        }
                        return cntNumber;
                    }
                }, {
                    data: 'jenis',
                    className : 'text-center',
                }, {
                    data: 'rencana',
                    className : 'text-center',
                }, {
                    data: 'iki',
                    className : 'text-center',
                }, {
                    data: 'target',
                    className : 'text-center'
                }, {
                    data: 'satuan',
                    className : 'text-center'
                }, {
                    data: 'realisasi',
                    className : 'text-center'
                }, {
                    data: 'uuid',
                    className : 'text-center',
                }
                ],
                columnDefs: [
                    {
                        targets: 1,
                        visible: false,
                        render: function(data, type, full, meta) {
                            return `<span class="badge badge-success">Aktif</span>`;
                        },
                    },
                    {
                        targets: -1,
                        title: 'Aksi',
                        width: 'auto',
                        orderable: false,
                        render: function(data, type, full, meta) {
                            let button_realisasi = '';

                            if (full.validation > 0) {
                                button_realisasi = `
                                   
                                    
                                    <div class="text-start" style="position: relative;left: 10px;">
                                        <a href="javascript:;" type="button" class="btn btn-secondary btn-sm mr-2" disabled> 
                                            Realisasi
                                        </a>
                                        <div class="mb-2">
                                            <span class="fw-bolder">Status</span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="badge badge-success">Di Setujui</span>
                                        </div>
                                        <div>
                                            <small>Penilai telah menyetujui</small>
                                        </div>
                                    </div>

                                    `;
                            } else {
                                button_realisasi = `
                                    <div class="text-start" style="position: relative;left: 10px;">
                                        <a href="javascript:;" type="button" data-uuid="${data}" data-kt-drawer-show="true" data-kt-drawer-target="#side_form" class="btn btn-primary button-update btn-sm mr-2" disabled> 
                                            Realisasi
                                        </a>
                                        <div class="mb-2">
                                            <span class="fw-bolder">Status</span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="badge badge-danger">Belum disetujui</span>
                                        </div>
                                        <div>
                                            <small>Penilai tidak setujui, karena <br> tidak sesuai dengan realisasi</small>
                                        </div>
                                    </div>`;
                            }


                            return `
                                    ${button_realisasi}
                                    `;
                            },
                    }
                ],
                 rowGroup: {
                    dataSrc: 'jenis', // Grup berdasarkan jenis
                    startRender: function(rows, group) {
                        // console.log(group);
                        let label = '';
                        let color = '';
                        if (group == 'utama') {
                            label = 'A. KINERJA UTAMA';
                            color = 'primary';
                        }else{
                            label = 'B. KINERJA TAMBAHAN';
                            color = 'success';
                        }
                        return $('<tr/>')
                            .append(`<td colspan="7"><span style="margin-left:36px" class="badge badge-${color}">${label}</span></td>`)
                            .attr('data-name', group);
                    },
                },
                "rowsGroup": [-1, 0, 2],
                "ordering": false,
            });
        }

        $(function() {
            datatable();
        })
    </script>
@endsection