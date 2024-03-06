@php
    $role = hasRole();
@endphp
@extends('layouts.layout')
@section('style')
   <link href="{{ asset('admin/assets/plugins/custom/fullcalendar/fullcalendar.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection
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
@section('title', 'Aktifitas')
@section('content')
<div class="post d-flex flex-column-fluid" id="kt_post">
        <!--begin::Container-->
        <div id="kt_content_container" class="container">
            <div class="row">

                <div class="card">
                    <div class="card-body p-0">

                        <div class="container">
                            <div class="py-5">
                                    <div id="kt_docs_fullcalendar_basic"></div>

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
                        <div class="col-lg-4">
                            <label class="form-label">Hasil</label>
                            <input type="number" id="hasil" class="form-control" name="volume">
                            <small class="text-danger volume_error"></small>  
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label">Satuan</label>
                            <input type="text" id="satuan" class="form-control" name="satuan" placeholder="Satuan" readonly>
                            <small class="text-danger satuan_error"></small>
                        </div>
                        <div class="col-lg-4">
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
                        <button type="button" class="btn mr-2 btn-danger btn-delete btn-sm d-flex align-items-center"><i class="bi bi-trash-fill"
                                style="color: #ffffff"></i>Hapus</button>
                        <button type="reset" id="side_form_close"
                            class="btn btn-dark mr-2 btn-cancel btn-sm d-flex align-items-center">Tutup</button>
                    </div>
                </form>
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card-->
    </div>
@endsection
@section('script')
    <script src="{{ asset('admin/assets/plugins/custom/fullcalendar/fullcalendar.bundle.js') }}"></script>
    <script>
        let control = new Control();

        $(document).on('click', '#button-side-form', function() {
            // $('#tanggal').prop('disabled',false);
            $("#tanggal").removeAttr("readonly");
            control.overlay_form('Tambah', 'Aktivitas');
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

        $(document).on('click','.btn-delete', function (e) {
           e.preventDefault();
           let label = $(this).attr('data-label');
           
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
            });

            Swal.fire({
            title: `Apakah anda yakin akan menghapus data aktivitas ?`,
            text: "Anda tidak akan dapat mengembalikan ini!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, Hapus itu!",
            }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                url: `/aktivitas/delete/${$("input[name='uuid']").val()}`,
                type: "DELETE",
                data: {
                    uuid : $("input[name='uuid']").val(),
                },
                success: function () {
                    swal.fire({
                    title: "Berhasil!",
                    text: "Aktivitas berhasil di hapus.",
                    icon: "success",
                    showConfirmButton: false,
                    timer: 1500,
                    });
                    setTimeout(() => {
                       window.location.href = `/aktivitas`; 
                    }, 1500);
                },
                error: function (xhr) {
                    if (xhr.statusText == "Unprocessable Content") {
                    Swal.fire(
                        `${xhr.responseJSON.data}`,
                        `${xhr.responseJSON.message}`,
                        "warning"
                    );
                    }
                },
                });
            }
            });
        })

        // dateConfig = () => {
        //     const tanggalAbsenInput = document.getElementById("tanggal");

        //     // Dapatkan tanggal hari ini
        //     const today = new Date();

        //     // Hitung tanggal 5 hari sebelum hari ini
        //     const fiveDaysAgo = new Date(today);
        //     fiveDaysAgo.setDate(today.getDate() - 5);

        //     const UnlimitedDaysAgo = new Date(today);
        //     UnlimitedDaysAgo.setDate(today.getDate() - 100);

        //     // Format tanggal ke dalam string YYYY-MM-DD
        //     const todayString = today.toISOString().split("T")[0];
        //     const fiveDaysAgoString = fiveDaysAgo.toISOString().split("T")[0];
        //     const UnlimitedDaysAgoString = UnlimitedDaysAgo.toISOString().split("T")[0];

        //     // Set nilai atribut min dan max pada elemen input tanggal
        //     tanggalAbsenInput.min = fiveDaysAgoString; // 5 hari sebelum hari ini
        //     tanggalAbsenInput.max = todayString; // Hari ini
            
        //     // Contoh: Menampilkan tanggal saat ini pada elemen input
        //     tanggalAbsenInput.value = todayString;

        //     // Menambahkan listener untuk event change pada input tanggal
        //     tanggalAbsenInput.addEventListener("change", function () {
        //         const selectedDate = new Date(this.value);
        //         const dayOfWeek = selectedDate.getDay(); // 0 = Minggu, 1 = Senin, ..., 6 = Sabtu

        //         // Menonaktifkan tanggal pada hari Sabtu dan Minggu
        //         if (dayOfWeek === 0 || dayOfWeek === 6) {
        //             // Menonaktifkan opsi untuk tanggal yang dipilih
        //             const tanggalOptions = document.querySelectorAll("#tanggal_absen option");
        //             tanggalOptions.forEach(option => {
        //                 const optionDate = new Date(option.value);
        //                 if (optionDate.getDay() === dayOfWeek) {
        //                     option.disabled = true;
        //                 } else {
        //                     option.disabled = false;
        //                 }
        //             });

        //             Swal.fire(
        //                 "Anda tidak dapat memilih hari sabtu dan minggu",
        //                 "Silahkan pilih hari yang lain",
        //                 "warning"
        //             );
        //             this.value = ""; // Mengosongkan nilai input
        //         }
        //     });
        // };


        const element = document.getElementById("kt_docs_fullcalendar_basic");

        var todayDate = moment().startOf("day");
        var YM = todayDate.format("YYYY-MM");
        var YESTERDAY = todayDate.clone().subtract(1, "day").format("YYYY-MM-DD");
        var TODAY = todayDate.format("YYYY-MM-DD");
        var TOMORROW = todayDate.clone().add(1, "day").format("YYYY-MM-DD");

        var calendarEl = document.getElementById("kt_docs_fullcalendar_basic");
        var calendar = new FullCalendar.Calendar(calendarEl, {
            lang: 'id',
            locale: 'id',
            headerToolbar: {
                left: "prev,next today",
                center: "title",
                right: "dayGridMonth,listMonth"
            },

            height: 800,
            contentHeight: 780,
            aspectRatio: 3,  // see: https://fullcalendar.io/docs/aspectRatio

            nowIndicator: true,
            now: TODAY + "T09:25:00", // just for demo

            views: {
                dayGridMonth: { buttonText: "month",  dayMaxEventRows: 2, },
                timeGridWeek: { buttonText: "week" },
                timeGridDay: { buttonText: "day" }
            },

            initialView: "dayGridMonth",
            initialDate: TODAY,
            editable: true,
            navLinks: true,
            events: '{{ route("pegawai.aktivitas.getAktivitasForCalender") }}',

            eventContent: function (info) {
                var element = $(info.el);

                if (info.event.extendedProps && info.event.extendedProps.description) {
                    if (element.hasClass("fc-day-grid-event")) {
                        element.data("content", info.event.extendedProps.description);
                        element.data("placement", "top");
                        KTApp.initPopover(element);
                    } else if (element.hasClass("fc-time-grid-event")) {
                        element.find(".fc-title").append("<div class='fc-description'>" + info.event.extendedProps.description + "</div>");
                    } else if (element.find(".fc-list-item-title").lenght !== 0) {
                        element.find(".fc-list-item-title").append("<div class='fc-description'>" + info.event.extendedProps.description + "</div>");
                    }
                }
            }
        });


         calendar.on('eventClick', function(data) {   
             let uuid = data.event['_def']['extendedProps']['uuid']; 
             
             $.ajax({
                url : `/aktivitas/show/${uuid}`,
                type : 'GET',
                success :  function (res) {
                    $('#button-side-form').trigger('click');
                    control.overlay_form('Update', 'Aktivitas');
                    $("#tanggal").attr("readonly", "readonly");
                    $.each(res.data, function (x, y) {
                        $("input[name='" + x + "']").val(y);
                        $("select[name='" + x + "']").val(y);
                        $("textarea[name='" + x + "']").val(y);
                        $("select[name='" + x + "']").trigger("change");
                    });    
                },
                error : function (xhr) {
                    alert('gagal');
                }
             })
        });    

        // function maxdate() {
        //     const inputElement = document.getElementById("tanggal");
        //     const fiveDaysAgo = new Date();
        //     fiveDaysAgo.setDate(fiveDaysAgo.getDate() - 3);
        //     minDate = fiveDaysAgo.toISOString().split("T")[0]
        //     inputElement.setAttribute("min", fiveDaysAgo.toISOString().split("T")[0]);
        // }

        $(document).on('submit', ".form-data", function(e) {
            
            e.preventDefault();
            let type = $(this).attr('data-type');
            let url = '';
            let role_data = '';
            if (type == 'add') {
                // control.submitFormMultipart(`/aktivitas/store`, 'Tambah', 'Aktivitas','POST');
                url = `/aktivitas/store`;
                role_data = 'Tambah';
            } else {
                let uuid = $("input[name='uuid']").val();
                url = `/aktivitas/update/${uuid}`;
                role_data = 'Update';
            }

            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
            });

            $.ajax({
            type: 'POST',
            url: url,
            data: $(".form-data").serialize(),
            success: function (response) {
                console.log(response);
                $(".text-danger").html("");
                if (response.success == true) {
                swal
                    .fire({
                    text: `Aktivitas berhasil di ${role_data}`,
                    icon: "success",
                    showConfirmButton: false,
                    timer: 1500,
                    })
                    .then(function () {
                    $("#side_form_close").trigger("click");
                    calendar.refetchEvents();
                    $("form")[0].reset();
                    $("#from_select").val(null).trigger("change");
                    });
                } else {
                $("form")[0].reset();
                $("#from_select").val(null).trigger("change");
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


            $(function () {
                // dateConfig();
                calendar.render();
            })

    </script>
@endsection