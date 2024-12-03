@extends('layouts.layout')
@section('style')
    <style>

        .table-group thead th {
            border: 1px solid #dee2e6;
        }

        .table-group tfoot th {
            border: 1px solid #dee2e6;
        }

        /* Tambahkan padding pada sel */
        .table-group td, .table-group th {
            border: 1px solid #dee2e6;
            padding: 0.5rem; /* Sesuaikan sesuai kebutuhan Anda */
        }

        .table-group tbody tr:last-child td {
            border-bottom: 1px solid #dee2e6;
        }
    </style>
@endsection
@section('title', 'Review Aktifitas')
@section('content')
<div class="post d-flex flex-column-fluid" id="kt_post">
        <!--begin::Container-->
        <div id="kt_content_container" class="container">
            <div class="row">

                <div class="card">
                    <div class="card-body p-0">

                    <div class="filter-bulan">
                            <select id="filter-bulan" class="form-control form-control-solid">
                                @foreach (range(1, 12) as $bulan)
                                    <option value="{{ $bulan }}" {{ $bulan == $bulan_params ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::parse('2023-' . $bulan . '-01')->translatedFormat('F') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="container">
                            <div class="py-5">
                                <form id="form-aktivitas-review">
                                <table id="kt_table_data" class="table-group table-row-gray-300 gy-7">
                                    <thead class="text-center">
                                        <tr class="fw-bolder fs-6 text-gray-800">
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>Tanggal Input</th>
                                            <th>Aktivitas</th>
                                            <th>Hasil</th>
                                            <th>Waktu</th>
                                            <th> 
                                                <div class="form-check form-check-custom form-check-solid">
                                                    <input class="form-check-input" type="checkbox" id="flexCheckDefault"/>
                                                    <label class="form-check-label" for="flexCheckDefault">
                                                        Status
                                                    </label>
                                                </div>  
                                            </th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot  class="text-center">
                                        <tr>
                                            <td colspan="5"><b>Capaian Produktivitas Kerja (Menit)</b></td>
                                            <td colspan="3"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="5"><b>Total target waktu</b></td>
                                            <td colspan="3"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="5"><b>Persentase kinerja</b></td>
                                            <td colspan="3"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                                <div class="d-flex justify-content-end gap-5">
                                    <button type="submit" class="btn btn-primary btn-sm btn-submit d-flex align-items-center"><i class="bi bi-file-earmark-diff"></i> Simpan</button>
                                    <a href="{{route('pegawai.review.aktivitas.index')}}" class="btn mr-2 btn-light btn-cancel btn-sm d-flex align-items-center" style="background-color: #ea443e65; color: #EA443E"><i class="bi bi-trash-fill" style="color: #EA443E"></i>Batal</a>
                                </div>
                                </form>

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
                            class="fs-4 fw-bolder text-gray-900 text-hover-primary me-1 lh-1 title_side_form"></a>
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

                    <input type="hidden" name="id">
                    <input type="hidden" name="uuid">

                    <div class="mb-10">
                        <label class="form-label">Tanggal Kegiatan</label>
                        <input type="date" id="tanggal" class="form-control" name="tanggal">
                        <small class="text-danger tanggal_error"></small>
                    </div>

                    <input type="hidden" name="id_pegawai" value="{{$pegawai}}">

                    <div class="mb-10">
                        <label class="form-label">Sasaran Kinerja</label>
                        <select class="form-select form-control" name="id_sasaran" data-control="select2" data-placeholder="Pilih Sasaran Kerja">
                            <option></option>
                            <option value="0">Non Sasaran</option>
                            @foreach($option_skp as $value)
                                <option value="{{$value->id}}">{{$value->text}}</option>
                            @endforeach
                        </select>
                        <small class="text-danger id_sasaran_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Nama  Aktivitas</label>
                        <select class="form-select form-control" name="aktivitas" id="aktivitas_name" data-control="select2" data-placeholder="Pilih Aktivitas">
                            <option></option>
                            @foreach($aktivitas as $value)
                                <option value="{{$value->text}}" data-uuid="{{$value->uuid}}">{{$value->text}}</option>
                            @endforeach
                            
                        </select>
                        <small class="text-danger aktivitas_error"></small>
                    </div>

                    <div class="row mb-10">
                        <div class="col-lg-6">
                            <label class="form-label">Satuan</label>
                            <input type="text" id="satuan" class="form-control" name="satuan" placeholder="Satuan" readonly>
                            <small class="text-danger satuan_error"></small>
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label">Waktu</label>
                            <input type="text" id="waktu" class="form-control" name="waktu" placeholder="0 Menit" readonly>
                            <small class="text-danger waktu_error"></small>
                        </div>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="5"></textarea>
                        <small class="text-danger keterangan_error"></small>
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
    <script>
        let control = new Control();    
        let pegawai = {!! json_encode($pegawai) !!};

        $(document).on('change','#filter-bulan', function () {
            datatable($(this).val());
        })

        $(document).on('click', '.button-update', function(e) {
            e.preventDefault();
            let url = '/aktivitas/show/' + $(this).attr('data-uuid');
            control.overlay_form('Update', 'Aktivitas', url);
        })

        $(document).on('click', '.button-delete', function(e) {
            e.preventDefault();
            let url = '/aktivitas/delete/' + $(this).attr('data-uuid');
            let label = $(this).attr('data-label');
            control.ajaxDelete(url, label)
        })


        $(document).on('submit', ".form-data", function(e) {
            e.preventDefault();
            let uuid = $("input[name='uuid']").val();
            control.submitFormMultipart(`/aktivitas/update/${uuid}`, 'Update','Aktivitas', 'POST');
        });

        $(document).on('change','#flexCheckDefault', function () {
             // Mendapatkan nilai properti checked dari checkbox
                var isChecked = $(this).prop('checked');

                // Memeriksa apakah checkbox tercentang atau tidak
                if (isChecked) {
                    $('.form-check-input').prop('checked',true);
                    // Lakukan sesuatu jika checkbox tercentang
                } else {
                    $('.form-check-input').prop('checked',false);
                    // Lakukan sesuatu jika checkbox tidak tercentang
                }
        })

        $(document).on('change','#aktivitas_name', function () {
            var selectedUuid = $(this).find(':selected').data('uuid');
            $.ajax({
                url : `/master-aktivitas-pegawai/master-aktivitas/show/${selectedUuid}`,
                type : 'GET',
                success : function (response) {
                    $("input[name='satuan']").val(response.data.satuan);
                    $("input[name='waktu']").val(response.data.waktu);
                },
                error : function (error) {
                    alert('gagal');
                }
            })
        })

        $(document).on('submit', "#form-aktivitas-review", function(e) {
            e.preventDefault();

            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
            });

            $.ajax({
            type: 'POST',
            url: '/review/aktivitas/post-review-aktivitas',
            data: $(this).serialize(),
            success: function (response) {
                console.log(response);
                $(".text-danger").html("");
                if (response.success == true) {
                swal
                    .fire({
                    text: `Aktivitas berhasil di review`,
                    icon: "success",
                    showConfirmButton: false,
                    timer: 1500,
                    })
                    .then(function () {
                        datatable($('#filter-bulan').val());
                    });
                } else {
                    Swal.fire("Gagal Memproses data!", `${response.message}`, "warning");
                }
            },
            error: function (xhr) {
                console.log(xhr);
                if (xhr.statusText == "Method Not Allowed") {
                Swal.fire(
                    "Gagal Memproses data!",
                    "Silahkan Hubungi Admin",
                    "warning"
                );
                }

                $(".text-danger").html("");
                $.each(xhr.responseJSON["errors"], function (key, value) {
                $(`.${key}_error`).html(" " + value);
                });
            },
            });

        });

        datatable = async (bulan) => {
       
            $('#kt_table_data').DataTable().clear().destroy();
            await $('#kt_table_data').dataTable().fnClearTable();
            await $('#kt_table_data').dataTable().fnDraw();
            await $('#kt_table_data').dataTable().fnDestroy();
            $("#kt_table_data").DataTable({
              responsive: true,
              paging: false,
              order: [[0, "asc"]],
              processing: true,
              ajax: `/review/aktivitas/data-review-aktivitas?pegawai=${pegawai}&bulan=${bulan}`,
              columns: [
                {
                data: null,
                className : 'text-center',
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }, {
                    data: 'tanggal',
                    className : 'text-center',
                    width: '8rem',
                    render: function(data, type, row, meta) {
                        var tanggalAwal = data;
                        var tanggalObjek = new Date(tanggalAwal);
                        var tanggal = tanggalObjek.getDate();
                        var bulan = tanggalObjek.toLocaleString('default', { month: 'long' }); // Menampilkan nama bulan dalam bahasa default
                        var tahun = tanggalObjek.getFullYear();

                        var tanggalAkhir = tanggal + " " + bulan + " " + tahun;
                        return tanggalAkhir;
                    }
                }, {
                    data: 'tanggal_input',
                    className : 'text-center',
                    width: '8rem',
                    render: function(data, type, row, meta) {
                        var tanggalAwal = data;
                        var tanggalObjek = new Date(tanggalAwal);
                        var tanggal = tanggalObjek.getDate();
                        var bulan = tanggalObjek.toLocaleString('default', { month: 'long' }); // Menampilkan nama bulan dalam bahasa default
                        var tahun = tanggalObjek.getFullYear();

                        var tanggalAkhir = tanggal + " " + bulan + " " + tahun;
                        return tanggalAkhir;
                    }
                }, {
                    data: 'aktivitas',
                    className : 'text-center'
                }, {
                    data: 'volume',
                    className : 'text-center',
                }, {
                    data: 'waktu',
                    className : 'text-center',
                }, {
                    data: 'validation',
                    className : 'text-center',
                }, {
                    data: 'uuid',
                    className : 'text-center',
                }
              ],
              columnDefs: [
                {
                    targets: 6,
                    // width : '8rem',
                    render: function(data, type, full, meta) {
      
                         let isChecked = data === 1 ? 'checked' : '';
                    return `
                        <input type="hidden" name="id_aktivitas[${meta.row}]" value="${full.id}">
                        <div class="form-check form-switch form-check-custom form-check-solid">
                            <input class="form-check-input" type="checkbox" name="validation[${meta.row}]" value="1" id="flexSwitchDefault${meta.row}" ${isChecked} />
                        </div>
                        `;
                    },
                },
                {
                    targets: -1,
                    title: 'Aksi',
                    width: '9rem',
                    orderable: false,
                    render: function(data, type, full, meta) {
                        return `
                                <a href="javascript:;" type="button" data-uuid="${data}" data-kt-drawer-show="true" data-kt-drawer-target="#side_form" class="btn btn-primary button-update btn-icon btn-sm"> 
                                <img src="{{ asset('admin/assets/media/icons/edit.svg')}}" alt="" srcset="">
                            </a>

                            <a href="javascript:;" type="button" data-uuid="${data}" data-label="${full.aktivitas}" class="btn btn-danger button-delete btn-icon btn-sm"> 
                                <img src="{{ asset('admin/assets/media/icons/trash.svg')}}" alt="" srcset="">
                            </a>
                                `;
                        },
                }
              ],
              footerCallback: function (row, data, start, end, display) {
                    console.log(data);
                    let capaian_prod_kinerja = 0;
                    $.each(data, function (x,y) {
                        capaian_prod_kinerja += y.waktu;
                    })

                    var lastData = data[end - 1];
                    

                   if (lastData) {
                        let nilai_produktivitas_kerja = 0;
                        var tfoot = $(this).closest('table').find('tfoot');
                        var firstRow = $(tfoot).find('tr:eq(0)');
                        var secondRow = $(tfoot).find('tr:eq(1)'); 
                        var thirdRow = $(tfoot).find('tr:eq(2)'); 
                        var cellsFirstRow = firstRow.find('td');
    
                        cellsFirstRow.eq(1).addClass('text-center').html(`<span class="badge badge-primary">${capaian_prod_kinerja}</span>`);

                        var cellsSecondRow = secondRow.find('td');
                        cellsSecondRow.eq(1).addClass('text-center').html(`<span class="badge badge-primary">${lastData.target_waktu}</span>`);

                        var cellsThirdRow = thirdRow.find('td');

                        if (lastData.target_waktu > 0) {
                            nilai_produktivitas_kerja = (capaian_prod_kinerja / lastData.target_waktu) * 100;
                        }

                        if (nilai_produktivitas_kerja > 100) {
                                nilai_produktivitas_kerja = 100;
                        }

                        cellsThirdRow.eq(1).addClass('text-center').html(`<span class="badge badge-primary">${nilai_produktivitas_kerja.toFixed(2)}</span>`);
                    }
                }
            });
        }

        $(function() {
            datatable($('#filter-bulan').val());
        })
    </script>
@endsection