@php
    $role = hasRole();
@endphp
@extends('layouts.layout')
@section('title', 'Kehadiran')
@section('button')
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
        <!--begin::Page title-->
        <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
            data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
            class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
            <!--begin::Title-->
            <button class="btn btn-primary btn-sm " data-kt-drawer-show="true" data-kt-drawer-target="#side_form"
                id="button-side-form"><i class="fa fa-plus-circle" style="color:#ffffff" aria-hidden="true"></i> Tambah
                Data</button>
            <!--end::Title-->
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

                            <form id="form-filter">
                                <div class="row" style="margin-bottom: 1rem">
                                    @if($role['guard'] == 'administrator' && $role['role'] == '2')
                                        <div class="col-lg-3">
                                            <label for="filter-tanggal" class="form-label" style="font-size:12px;">Unit Kerja</label>
                                            <select name="satuan_kerja" id="satker-filter" data-control="select2" data-placeholder="Pilih Unit Kerja" class="form-control form-control-sm form-control-solid">
                                                <option></option>
                                                @foreach($satuan_kerja as $val)
                                                    <option value="{{$val->value}}">{{$val->text}}</option>
                                                @endforeach
                                            </select>                                    
                                        </div>
                                    @endif    
                                    <div class="col-lg-2">
                                    <label for="filter-tanggal" class="form-label" style="font-size:12px;">Tanggal</label>
                                        <input type="date" value="{{ date('Y-m-d') }}" name="tanggal" id="filter-tanggal" class="form-control form-control-sm form-control-solid" id="filter-tanggal">
                                    </div>
                                    <div class="col-lg-2">
                                    <label for="filter-valid" class="form-label" style="font-size:12px;">Validation</label>
                                        <select class="form-control form-control-sm form-control-solid" name="validasi" id="filter-validasi">
                                        <option value="semua" selected>semua</option>
                                            <option value="0">invalid</option>
                                            <option value="1">valid</option>
                                            
                                        </select>        
                                    </div>
                                    <div class="col-lg-2">
                                    <label for="filter-status" class="form-label" style="font-size:12px;">Status</label>
                                        <select class="form-control form-control-sm form-control-solid" name="status" id="filter-status">
                                        <option value="semua" selected>Semua</option>
                                            <option value="hadir">Hadir</option>
                                            <option value="dinas luar">Dinas luar</option>
                                            <option value="izin">Izin</option>
                                            <option value="sakit">Sakit</option>
                                            <option value="apel">Apel</option>
                                        </select>        
                                    </div>
                                    <div class="col-lg">
                                        <button type="submit" class="btn btn-primary btn-sm" id="filter-btn" style="position: relative;top: 24px;">Terapkan</button>
                                    </div>
                                </div>
                            </form>

                                <table id="kt_table_data" class="table table-row-dashed table-row-gray-300 gy-7">
                                    <thead class="text-center">
                                        <tr class="fw-bolder fs-6 text-gray-800">
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>Jenis</th>
                                            <th>Waktu Masuk</th>
                                            <th>Waktu Pulang</th>
                                            <th>Di absenkan oleh</th>
                                            <th>Validasi</th>
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

                    @if($role['guard'] == 'administrator')
                        <div class="mb-10">
                            <label class="form-label">Pilih Unit Kerja</label>
                            <select class="form-select form-control" id="id_satuan_kerja" name="id_satuan_kerja" data-control="select2" data-placeholder="Pilih Satuan Kerja">
                                <option></option>
                                @foreach($satuan_kerja as $val)
                                    <option value="{{$val->value}}">{{$val->text}}</option>
                                @endforeach
                            </select>
                            <small class="text-danger id_satuan_kerja_error"></small>
                        </div>

                        <div class="mb-10">
                            <label class="form-label">Pilih Pegawai</label>
                            <select class="form-select form-control" id="id_pegawai" name="id_pegawai" data-control="select2" data-placeholder="Pilih Pegawai">
                                <option></option>
                            </select>
                            <small class="text-danger id_pegawai_error"></small>
                        </div>
                    @endif

                    @if($role['guard'] == 'web')
                    <input type="hidden" name="id_satuan_kerja" value="{{ $satuan_kerja_user }}">
                        <div class="mb-10">
                            <label class="form-label">Pilih Pegawai</label>
                            <select class="form-select form-control" id="id_pegawai" name="id_pegawai" data-control="select2" data-placeholder="Pilih Pegawai">
                                <option></option>
                                @foreach($pegawai_option as $value)
                                    <option value="{{ $value->id }}" data-tipe="{{ $value->tipe_pegawai }}">{{ $value->text }}</option>
                                @endforeach
                            </select>
                            <small class="text-danger id_pegawai_error"></small>
                        </div>
                    @endif

                    <div class="mb-10">
                        <label class="form-label">Tanggal</label>
                        <input type="date" id="tanggal_absen" class="form-control" name="tanggal_absen">
                        <small class="text-danger tanggal_absen_error"></small>
                    </div>

                    <div class="row mb-10">
                        <div class="col-md-6">
                            <label class="form-label">Waktu Masuk</label>
                            <input type="time" id="waktu_masuk" class="form-control" name="waktu_masuk">
                            <small class="text-danger waktu_masuk_error"></small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Waktu Keluar</label>
                            <input type="time" id="waktu_keluar" class="form-control" name="waktu_keluar">
                            <small class="text-danger waktu_keluar_error"></small>
                        </div>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Status</label>
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" name="status" type="radio" value="hadir" id="hadir"/>
                                    <label class="form-check-label" for="hadir">
                                        Hadir
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" name="status" type="radio" value="izin" id="izin"/>
                                    <label class="form-check-label" for="izin">
                                        Izin
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" name="status" type="radio" value="sakit" id="sakit"/>
                                    <label class="form-check-label" for="sakit">
                                        Sakit
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-4 mt-4">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" name="status" type="radio" value="apel" id="apel"/>
                                    <label class="form-check-label" for="apel">
                                        Apel
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-4 mt-4">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" name="status" type="radio" value="dinas luar" id="dinas luar"/>
                                    <label class="form-check-label" for="dinas luar">
                                        Dinas Luar
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-4 mt-4">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" name="status" type="radio" value="cuti" id="cuti"/>
                                    <label class="form-check-label" for="cuti">
                                        Cuti
                                    </label>
                                </div>
                            </div>
                        </div>
                        <small class="text-danger status_error"></small>
                    </div>

                    <div class="mb-10" id="shift-konten">
                        <label class="form-label">Shift</label>
                        <select class="form-select form-control" id="shift" name="shift" data-control="select2" data-placeholder="Pilih Shit">
                            <option></option>
                            <option value="pagi">Pagi</option>
                            <option value="siang">Siang</option>
                            <option value="malam">Malam</option>
                        </select>
                        <small class="text-danger shift_error"></small>
                    </div>

                    <input type="hidden" name="tipe_pegawai">

                    <div class="mb-10">
                        <label class="form-label">Validasi</label>
                        <div class="form-check form-switch form-check-custom form-check-solid">
                            <input class="form-check-input" name="validation" type="checkbox" value="1" id="validation"/>
                        </div>
                        <small class="text-danger validation_error"></small>
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
        let role = {!! json_encode($role) !!};
        let satuan_kerja_user = {!! json_encode($satuan_kerja_user) !!};
        let url_main = '';
        let tipe_pegawai = '';

        role.guard !== 'web' ? url_main = '/kehadiran' : url_main = '/kehadiran-opd';

        $(document).on('click', '#button-side-form', function() {
                        $("#id_satuan_kerja").prop("disabled", false);
            $("#id_pegawai").prop("disabled", false);
            $("#tanggal_absen").prop("disabled", false);
            control.overlay_form('Tambah', 'Kehadiran');
            
        })

        $(document).on('submit', ".form-data", function(e) {
            e.preventDefault();
            let type = $(this).attr('data-type');
            if (type == 'add') {
                control.submitFormMultipart(`${url_main}/store`, 'Tambah', 'Kehadiran','POST');
            } else {
                let uuid = $("input[name='uuid']").val();
                control.submitFormMultipart(`${url_main}/update/` + uuid, 'Update','Kehadiran', 'POST');
            }
        });


        if (role.guard == 'administrator') {
            $(document).on('change','#id_satuan_kerja', function (e) {
                e.preventDefault();
                if ($(this).val() !== '') {
                    control.push_select(`/perangkat-daerah/unit-kerja/option?satuan_kerja=${$(this).val()}`,'#id_unit_kerja'); 
                    role.guard !== 'web' ? control.push_select_pegawai(`/pegawai/list-pegawai/option-by-unit-kerja?satuan_kerja=&unit_kerja=${$(this).val()}`, '#id_pegawai') : control.push_select_pegawai(`/pegawai-opd/list-pegawai-opd/option?satuan_kerja=${$(this).val()}`, '#id_pegawai');
                }else{
                    $('#id_pegawai').empty().trigger('change');
                }      
                
            })
        }

        $(document).on('click', '.button-update', function(e) {
            e.preventDefault();
            let url = `${url_main}/show/` + $(this).attr('data-uuid');
            control.overlay_form('Update', 'Kehadiran', url);
        })

        $(document).on('click', '.button-delete', function(e) {
            e.preventDefault();
            let url = `${url_main}/delete/` + $(this).attr('data-uuid');
            let label = $(this).attr('data-label');
            control.ajaxDelete(url, label)
        })

        $(document).on('change','[name="status"]', function () {
            // // Periksa apakah checkbox tersebut dicentang atau tidak
            
            if ($(this).is(':checked')) {
                $(this).prop("checked", false);
                $(this).prop("checked", true);
            }

        });

        // $('#tanggal_absen').change(function() {
        //     // Mendapatkan nilai hari dari tanggal yang dipilih
        //     var selectedDate = new Date($(this).val());
        //     // Menggunakan toLocaleDateString untuk mendapatkan nilai hari
        //     var dayOfWeek = selectedDate.toLocaleDateString('en-US', { weekday: 'numeric' });

        //     // Jika pengguna memilih "apel" dan tanggal bukan hari Senin, batalkan pilihan "apel"
        //     if ($('#apel').is(':checked') && dayOfWeek !== '1') {
        //         $('#apel').prop('checked', false);
        //         Swal.fire(
        //             "Anda tidak dapat memilih Apel apabila bukan hari Senin",
        //             "Silahkan Pilih Status absen yang lain",
        //             "warning"
        //         );
        //     }
        // });


        // Fungsi untuk menangani perubahan pada input radio status
        // $('input[name="status"]').change(function() {
        //     // Jika pengguna memilih "apel" dan tanggal bukan hari Senin, batalkan pilihan "apel"
        //     if ($(this).val() === 'apel' && $('#tanggal_absen').val() !== '') {
        //         var selectedDate = new Date($('#tanggal_absen').val());
        //         var dayOfWeek = selectedDate.getDay(); // 0 = Minggu, 1 = Senin, ..., 6 = Sabtu

        //         if (dayOfWeek !== 1) {
        //             $(this).prop('checked', false);
        //             // $('.status_error').text('Anda tidak dapat memilih "Apel" jika tanggal bukan hari Senin.');
        //             Swal.fire(
        //                 "Anda tidak dapat memilih Apel apabila bukan hari Senin",
        //                 "Silahkan Pilih Status absen yang lain",
        //                 "warning"
        //             );
        //         }
        //     }
        // });

        $(document).on('change','#id_pegawai',function () {
            // console.log();
            if ($(this).val() !== null) {
                var selectedTipe = $(this).find(':selected').data('tipe');
                if (selectedTipe == 'tenaga_kesehatan') {
                   $('#shift-konten').show(); 
                }else{
                   $('#shift-konten').hide();  
                }
                $("input[name='tipe_pegawai']").val(selectedTipe);
                tipe_pegawai = selectedTipe;
            }
        });

        dateConfig = () => {
            const tanggalAbsenInput = document.getElementById("tanggal_absen");

            // Dapatkan tanggal hari ini
            const today = new Date();

            // Hitung tanggal 5 hari sebelum hari ini
            const fiveDaysAgo = new Date(today);
            fiveDaysAgo.setDate(today.getDate() - 5);

            const UnlimitedDaysAgo = new Date(today);
            UnlimitedDaysAgo.setDate(today.getDate() - 1000);

            // Format tanggal ke dalam string YYYY-MM-DD
            const todayString = today.toISOString().split("T")[0];
            const fiveDaysAgoString = fiveDaysAgo.toISOString().split("T")[0];
            const UnlimitedDaysAgoString = UnlimitedDaysAgo.toISOString().split("T")[0];

            // Set nilai atribut min dan max pada elemen input tanggal
            tanggalAbsenInput.min = role.guard !== 'web' ? UnlimitedDaysAgoString : fiveDaysAgoString; // 5 hari sebelum hari ini
            tanggalAbsenInput.max = todayString; // Hari ini
            
            // Contoh: Menampilkan tanggal saat ini pada elemen input
            tanggalAbsenInput.value = todayString;

            // Menambahkan listener untuk event change pada input tanggal
            tanggalAbsenInput.addEventListener("change", function () {
                const selectedDate = new Date(this.value);
                const dayOfWeek = selectedDate.getDay(); // 0 = Minggu, 1 = Senin, ..., 6 = Sabtu

                // Menonaktifkan tanggal pada hari Sabtu dan Minggu
                if (tipe_pegawai == 'pegawai_administratif') {
                    if (dayOfWeek === 0 || dayOfWeek === 6) {
                        // Menonaktifkan opsi untuk tanggal yang dipilih
                        const tanggalOptions = document.querySelectorAll("#tanggal_absen option");
                        tanggalOptions.forEach(option => {
                            const optionDate = new Date(option.value);
                            if (optionDate.getDay() === dayOfWeek) {
                                option.disabled = true;
                            } else {
                                option.disabled = false;
                            }
                        });

                        Swal.fire(
                            "Anda tidak dapat memilih hari sabtu dan minggu",
                            "Silahkan pilih hari yang lain",
                            "warning"
                        );
                        this.value = ""; // Mengosongkan nilai input
                    }
                }
                
            });
        };

        function validation(data_value, data_uuid) {

            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
            });

            $.ajax({
                    type: 'POST',
                    url: `${url_main}/validation`,
                    data: {
                        validation : data_value,
                        uuid : data_uuid,
                    },
                    success: function (response) {
                        $(".text-danger").html("");
                        if (response.success == true) {
                        swal
                            .fire({
                            text: `Absen berhasil di validasi`,
                            icon: "success",
                            showConfirmButton: false,
                            timer: 1500,
                            })
                            .then(function () {
                                role.guard == 'web' ? datatable(satuan_kerja_user.id_satuan_kerja,$('#filter-tanggal').val(), $('#filter-validasi').val(),$('#filter-status').val()) : datatable($('#satker-filter').val(), $('#filter-tanggal').val(), $('#filter-validasi').val(),$('#filter-status').val());
                            });
                        } else {
                            Swal.fire("Gagal Memproses data!", `${response.message}`, "warning");
                        }
                    },
                    error: function (xhr) {
                    Swal.fire("Gagal Memproses data!", 'Gagal', "warning");
                    },
                });
        }

        $(document).on('click','.btn-validation', function () {

            let data_value = $(this).attr('data-value')
            let data_uuid = $(this).attr('data-uuid');

            if (role.guard !== 'web') {
                validation(data_value,data_uuid)
            }else{
                let tanggalParams = $(this).attr('data-tanggal');
                console.log(tanggalParams);
                let tanggalAwal = new Date(tanggalParams);
                // Tanggal hari ini
                let tanggalHariIni = new Date();

                // Hitung jarak hari
                let jarakHari = Math.floor((tanggalHariIni - tanggalAwal) / (1000 * 60 * 60 * 24));
                if (jarakHari <= 6) {
                    validation(data_value,data_uuid)
                }else{
                    Swal.fire("Gagal Memproses data!", 'Waktu sudah lewat dari 5 hari', "warning");
                }
                
            }
        })

        $(document).on('click','#filter-btn', function (e) {
            e.preventDefault();
           
            let satker = ''; 
            role.guard !== 'web' ? satker = $('#satker-filter').val() : satker = satuan_kerja_user.id_satuan_kerja;
            let tanggal = $('#filter-tanggal').val(); 
            let validasi =  $('#filter-validasi').val();
            let status =  $('#filter-status').val();

            datatable(satker,tanggal,validasi,status);
        })

        datatable = (satuan_kerja,tanggal,validasi,status) =>{
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
                data: 'status',
                className : 'text-right',
            }, {
                data: 'waktu_masuk',
                className : 'text-right',
            }, {
                data: 'waktu_keluar',
                className : 'text-right',
                render: function(data, type, row, meta) {
                    return data !== null ? data : 'belum absen';
                }
            }, {
                data: null,
                className : 'text-right',
                render: function(data, type, row, meta) {
                    // console.log(`${data.id_pegawai} - ${data.user_update}`)
                //    return data.user_type  == 1 ? `<span class="badge badge-warning">${data.user_updated_0}</span>` : `<span class="badge badge-success">${data.user_update_1}</span>`;

                    if (data.user_type  == 1) {
                        return `<span class="badge badge-warning">${data.user_updated_0}</span>`;
                    }else{
                        if (data.id_pegawai !== data.user_update) {
                            return `<span class="badge badge-warning">${data.user_update_1}</span>`;    
                        }else{
                            return `<span class="badge badge-success">${data.user_update_1}</span>`;
                        }
                        
                    }
                }
            }, {
                data: 'validation',
                className : 'text-right',
                render: function(data, type, row, meta) {
                    console.log(data);
                    if (data === 1) {
                        return `<a href="#" type="button" data-value="0" data-uuid="${row.uuid}" data-tanggal="${row.tanggal_absen}" class="btn btn-success btn-validation btn-icon btn-sm"> 
                                <img src="{{ asset('admin/assets/media/icons/checkmark.svg')}}" alt="" srcset="">
                            </a>`;
                    }else{
                        return `<a href="#" type="button" data-value="1" data-uuid="${row.uuid}" data-tanggal="${row.tanggal_absen}" class="btn btn-danger btn-validation btn-icon btn-sm"> 
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
                width: '9rem',
                orderable: false,
                render: function(data, type, full, meta) {
                    return `
                            <a href="javascript:;" type="button" data-uuid="${data}" data-kt-drawer-show="true" data-kt-drawer-target="#side_form" class="btn btn-primary button-update btn-icon btn-sm"> 
                                <img src="{{ asset('admin/assets/media/icons/edit.svg')}}" alt="" srcset="">
                            </a>

                            <a href="javascript:;" type="button" data-uuid="${data}" data-label="${full.nama}" class="btn btn-danger button-delete btn-icon btn-sm"> 
                                <img src="{{ asset('admin/assets/media/icons/trash.svg')}}" alt="" srcset="">
                            </a>
                            `;
                    },
            }];
            control.initDatatable(`${url_main}/datatable?satuan_kerja=${satuan_kerja}&tanggal=${tanggal}&validasi=${validasi}&status=${status}`, columns, columnDefs);
        }

        $(function() {
            dateConfig();
            $('#shift-konten').hide();
            role.guard == 'web' ? datatable(satuan_kerja_user.id_satuan_kerja,$('#filter-tanggal').val(), $('#filter-validasi').val(),$('#filter-status').val()) : datatable($('#satker-filter').val(), $('#filter-tanggal').val(), $('#filter-validasi').val(),$('#filter-status').val());
        })
    </script>
@endsection