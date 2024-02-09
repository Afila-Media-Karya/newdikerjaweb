@section('title', 'Kelompok Aktivitas')
@extends('layouts.layout')
@section('content')
<div class="post d-flex flex-column-fluid" id="kt_post">
    <!--begin::Container-->
    <div id="kt_content_container" class="container">
        <div class="row">
            <div class="card card-flush shadow-sm">
                <div class="card-header">
                    <h3 class="card-title">Kelompok Aktivitas</h3>
                </div>
                <div class="card-body p-0">

                    <form class="form-data">
                        <div class="container">
                            <div class="py-5">
                                <div class="row mb-10">
                                    <div class="col-lg-6">
                                        <label class="form-label">Kelompok</label>
                                        <input type="text" id="kelompok" class="form-control" name="kelompok"
                                            placeholder="Masukkan nama kelompok">
                                        <small class="text-danger kelompok_error"></small>
                                    </div>

                                    <div class="col-lg-6">
                                        <label class="form-label">Jenis Jabatan</label>
                                        <select class="form-select form-control" name="id_jenis_jabatan"
                                            data-control="select2" data-placeholder="Pilih Jenis Jabatan">
                                            <option></option>
                                            @foreach($jenis_jabatan as $val)
                                            <option value="{{$val->id}}">{{$val->text}} - kelas {{$val->kelas_jabatan}}
                                            </option>
                                            @endforeach
                                        </select>
                                        <small class="text-danger id_jenis_jabatan_error"></small>
                                    </div>
                                </div>


                                <div id="repeater-aktivitas">

                                    <div class="d-flex justify-content-between">
                                        <h5 style="position: relative;top: 11px;">Aktivitas</h5>

                                        <a href="javascript:;" data-repeater-create
                                            class="btn btn-icon btn-primary btn-sm">
                                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M12 10H10V12C10 12.55 9.55 13 9 13C8.45 13 8 12.55 8 12V10H6C5.45 10 5 9.55 5 9C5 8.45 5.45 8 6 8H8V6C8 5.45 8.45 5 9 5C9.55 5 10 5.45 10 6V8H12C12.55 8 13 8.45 13 9C13 9.55 12.55 10 12 10ZM15 0H3C1.346 0 0 1.346 0 3V15C0 16.654 1.346 18 3 18H15C16.654 18 18 16.654 18 15V3C18 1.346 16.654 0 15 0Z"
                                                    fill="white" />
                                                <mask id="mask0_285_3827" style="mask-type:luminance"
                                                    maskUnits="userSpaceOnUse" x="0" y="0" width="18" height="18">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M12 10H10V12C10 12.55 9.55 13 9 13C8.45 13 8 12.55 8 12V10H6C5.45 10 5 9.55 5 9C5 8.45 5.45 8 6 8H8V6C8 5.45 8.45 5 9 5C9.55 5 10 5.45 10 6V8H12C12.55 8 13 8.45 13 9C13 9.55 12.55 10 12 10ZM15 0H3C1.346 0 0 1.346 0 3V15C0 16.654 1.346 18 3 18H15C16.654 18 18 16.654 18 15V3C18 1.346 16.654 0 15 0Z"
                                                        fill="white" />
                                                </mask>
                                                <g mask="url(#mask0_285_3827)">
                                                    <rect x="-3" y="-3" width="24" height="24" fill="white" />
                                                </g>
                                            </svg>

                                        </a>
                                    </div>

                                    <div class="separator separator-dotted mt-2 mb-5"></div>
                                    <div class="form-group">
                                        <div data-repeater-list="repeater-aktivitas">
                                            <div data-repeater-item class="repeater-items">
                                                <div class="form-group">

                                                    <div class="d-flex justify-content-between">
                                                        <div class="mb-10" style="width:94%">
                                                            <label class="form-label">Aktivitas</label>
                                                            <textarea name="aktivitas" class="form-control"
                                                                placeholder="Masukkan Aktivitas" rows="3"></textarea>
                                                        </div>

                                                        <a href="javascript:;" data-repeater-delete type="button"
                                                            class="btn btn-danger btn-repeater-delete-output btn-icon btn-sm mt-3 mt-md-8">
                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                                    d="M4.28571 3H19.7143C20.4244 3 21 3.58547 21 4.30769V4.96154C21 5.68376 20.4244 6.26923 19.7143 6.26923H4.28571C3.57563 6.26923 3 5.68376 3 4.96154V4.30769C3 3.58547 3.57563 3 4.28571 3ZM4.57475 7.60448C4.61609 7.58598 4.66081 7.57654 4.70598 7.57679H19.292C19.3372 7.57654 19.3819 7.58598 19.4232 7.60448C19.4646 7.62299 19.5016 7.65016 19.5319 7.6842C19.5623 7.71825 19.5852 7.75842 19.5992 7.80208C19.6133 7.84575 19.6181 7.89193 19.6134 7.93763L18.5579 18.259V18.2676C18.5027 18.7448 18.2772 19.1848 17.9241 19.5041C17.5711 19.8235 17.1151 19.9999 16.6426 19.9999H7.35776C6.88517 20.0001 6.42897 19.8237 6.07575 19.5044C5.72252 19.1851 5.49688 18.745 5.44165 18.2676C5.44143 18.2646 5.44143 18.2616 5.44165 18.2586L4.38455 7.93763C4.37986 7.89193 4.3847 7.84575 4.39874 7.80208C4.41278 7.75842 4.43571 7.71825 4.46604 7.6842C4.49637 7.65016 4.53341 7.62299 4.57475 7.60448ZM14.8481 15.173C14.8146 15.0933 14.7659 15.0211 14.7048 14.9608L12.9092 13.1345L14.7048 11.3082C14.8224 11.185 14.8877 11.0196 14.8864 10.8479C14.8851 10.6761 14.8175 10.5118 14.698 10.3903C14.5786 10.2689 14.417 10.2002 14.2481 10.1989C14.0792 10.1977 13.9167 10.2641 13.7956 10.3838L12.0004 12.2097L10.2048 10.3838C10.0837 10.2641 9.92115 10.1977 9.75228 10.1989C9.58342 10.2002 9.4218 10.2689 9.30237 10.3903C9.18293 10.5118 9.11527 10.6761 9.114 10.8479C9.11273 11.0196 9.17795 11.185 9.29557 11.3082L11.0912 13.1345L9.29557 14.9608C9.17795 15.084 9.11273 15.2494 9.114 15.4211C9.11527 15.5929 9.18293 15.7572 9.30237 15.8786C9.4218 16 9.58342 16.0688 9.75228 16.07C9.92115 16.0712 10.0837 16.0048 10.2048 15.8851L12.0004 14.0593L13.7956 15.8851C13.8549 15.9473 13.9258 15.9969 14.0042 16.0309C14.0826 16.065 14.1668 16.0829 14.252 16.0835C14.3372 16.0842 14.4217 16.0676 14.5005 16.0347C14.5794 16.0019 14.651 15.9534 14.7113 15.8921C14.7716 15.8309 14.8193 15.758 14.8516 15.6778C14.8839 15.5977 14.9003 15.5117 14.8997 15.4251C14.8991 15.3384 14.8815 15.2527 14.8481 15.173Z"
                                                                    fill="white" />
                                                                <mask id="mask0_285_8655" style="mask-type:luminance"
                                                                    maskUnits="userSpaceOnUse" x="3" y="3" width="18"
                                                                    height="17">
                                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                                        d="M4.28571 3H19.7143C20.4244 3 21 3.58547 21 4.30769V4.96154C21 5.68376 20.4244 6.26923 19.7143 6.26923H4.28571C3.57563 6.26923 3 5.68376 3 4.96154V4.30769C3 3.58547 3.57563 3 4.28571 3ZM4.57475 7.60448C4.61609 7.58598 4.66081 7.57654 4.70598 7.57679H19.292C19.3372 7.57654 19.3819 7.58598 19.4232 7.60448C19.4646 7.62299 19.5016 7.65016 19.5319 7.6842C19.5623 7.71825 19.5852 7.75842 19.5992 7.80208C19.6133 7.84575 19.6181 7.89193 19.6134 7.93763L18.5579 18.259V18.2676C18.5027 18.7448 18.2772 19.1848 17.9241 19.5041C17.5711 19.8235 17.1151 19.9999 16.6426 19.9999H7.35776C6.88517 20.0001 6.42897 19.8237 6.07575 19.5044C5.72252 19.1851 5.49688 18.745 5.44165 18.2676C5.44143 18.2646 5.44143 18.2616 5.44165 18.2586L4.38455 7.93763C4.37986 7.89193 4.3847 7.84575 4.39874 7.80208C4.41278 7.75842 4.43571 7.71825 4.46604 7.6842C4.49637 7.65016 4.53341 7.62299 4.57475 7.60448ZM14.8481 15.173C14.8146 15.0933 14.7659 15.0211 14.7048 14.9608L12.9092 13.1345L14.7048 11.3082C14.8224 11.185 14.8877 11.0196 14.8864 10.8479C14.8851 10.6761 14.8175 10.5118 14.698 10.3903C14.5786 10.2689 14.417 10.2002 14.2481 10.1989C14.0792 10.1977 13.9167 10.2641 13.7956 10.3838L12.0004 12.2097L10.2048 10.3838C10.0837 10.2641 9.92115 10.1977 9.75228 10.1989C9.58342 10.2002 9.4218 10.2689 9.30237 10.3903C9.18293 10.5118 9.11527 10.6761 9.114 10.8479C9.11273 11.0196 9.17795 11.185 9.29557 11.3082L11.0912 13.1345L9.29557 14.9608C9.17795 15.084 9.11273 15.2494 9.114 15.4211C9.11527 15.5929 9.18293 15.7572 9.30237 15.8786C9.4218 16 9.58342 16.0688 9.75228 16.07C9.92115 16.0712 10.0837 16.0048 10.2048 15.8851L12.0004 14.0593L13.7956 15.8851C13.8549 15.9473 13.9258 15.9969 14.0042 16.0309C14.0826 16.065 14.1668 16.0829 14.252 16.0835C14.3372 16.0842 14.4217 16.0676 14.5005 16.0347C14.5794 16.0019 14.651 15.9534 14.7113 15.8921C14.7716 15.8309 14.8193 15.758 14.8516 15.6778C14.8839 15.5977 14.9003 15.5117 14.8997 15.4251C14.8991 15.3384 14.8815 15.2527 14.8481 15.173Z"
                                                                        fill="white" />
                                                                </mask>
                                                                <g mask="url(#mask0_285_8655)">
                                                                    <rect width="24" height="24" fill="white" />
                                                                </g>
                                                            </svg>

                                                        </a>
                                                    </div>

                                                    <div class="mb-10" style="width:94%">
                                                        <div class="row mb-10">
                                                            <div class="col-lg-6">
                                                                <label class="form-label">Beban Kerja</label>
                                                                <input type="number" id="beban_kerja"
                                                                    class="form-control" name="beban_kerja"
                                                                    placeholder="Masukkan Beban Kerja">
                                                                <small class="text-danger beban_kerja_error"></small>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <label class="form-label">Satuan</label>
                                                                <select class="form-select form-control" name="satuan"
                                                                    data-kt-repeater="select2"
                                                                    data-placeholder="Pilih Satuan">
                                                                    <option></option>
                                                                    @foreach($satuan as $val)
                                                                    <option value="{{$val->value}}">{{$val->text}}
                                                                    </option>
                                                                    @endforeach
                                                                </select>
                                                                <small class="text-danger satuan_error"></small>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-10">
                                                            <div class="col-lg-4">
                                                                <label class="form-label">Waktu (menit)</label>
                                                                <input type="number" id="waktu" class="form-control"
                                                                    name="waktu"
                                                                    placeholder="Masukkan Waktu Dalam Menit">
                                                                <small class="text-danger waktu_error"></small>
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <label class="form-label">Waktu Penyelesaian
                                                                    (Jam)</label>
                                                                <input type="number" id="waktu_penyelesaian"
                                                                    class="form-control" name="waktu_penyelesaian"
                                                                    placeholder="Masukkan Waktu Penyelesaian">
                                                                <small
                                                                    class="text-danger waktu_penyelesaian_error"></small>
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <label class="form-label">Waktu Efektif (Jam)</label>
                                                                <input type="number" id="waktu_efektif"
                                                                    class="form-control" name="waktu_efektif"
                                                                    placeholder="Masukkan Waktu Efektif">
                                                                <small class="text-danger waktu_efektif_error"></small>
                                                            </div>
                                                        </div>
                                                        <div>

                                                        </div>
                                                    </div>
                                                    <small class="text-danger repeater-aktivitas_error"></small>
                                                </div>
                                            </div>
                                            <!-- </form> -->
                                        </div>
                                    </div>

                                </div>
                                <div class="card-footer">
                                    <div class="d-flex gap-5">
                                        <button type="submit"
                                            class="btn btn-primary btn-sm btn-submit d-flex align-items-center"><i
                                                class="bi bi-file-earmark-diff"></i> Simpan</button>
                                        <a type="button" href="{{route('kabupaten.master_jabatan.kelompok_aktivitas.index')}}" id="side_form_close"
                                            class="btn mr-2 btn-light btn-cancel btn-sm d-flex align-items-center"
                                            style="background-color: #ea443e65; color: #EA443E"><i
                                                class="bi bi-trash-fill" style="color: #EA443E"></i>Batal</a>
                                    </div>
                                </div>

                    </form>
                </div>

            </div>
        </div>
        <!--end::Container-->
    </div>
    @endsection
    @section('script')
    <script src="{{ asset('admin/assets/plugins/custom/formrepeater/formrepeater.bundle.js') }}"></script>
    <script>
        let control = new Control();

        $(document).on('submit', ".form-data", function(e) {
            e.preventDefault();
        //    console.log($(this).serialize()) 

        $(".btn-submit").prop("disabled", true);
        $(".btn-submit").html(
        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...'
        );

            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
            });

            $.ajax({
            type: 'POST',
            url: '{{ route("kabupaten.master_jabatan.kelompok_aktivitas.store") }}',
            data: $(".form-data").serialize(),
            success: function (response) {
                console.log(response);
                $(".text-danger").html("");
                if (response.success == true) {
                    swal.fire({
                        text: `Kelompok Aktivitas berhasil di Tambah`,
                        icon: "success",
                        showConfirmButton: false,
                        timer: 1500,
                        })
                        .then(function () {
                            setTimeout(() => {
                                window.location.href = '{{ route("kabupaten.master_jabatan.kelompok_aktivitas.index") }}';
                            }, 1500);
                        });
                } else {
                Swal.fire("Gagal Memproses data!", `${response.message}`, "warning");
                }
            },
            error: function (xhr) {
                Swal.fire(
                    "Gagal Memproses data!",
                    "Silahkan Hubungi Admin",
                    "warning"
                );
            },
            complete: function () {
                $(".btn-submit").prop("disabled", false); // Mengaktifkan kembali tombol submit
                $(".btn-submit").html('<i class="bi bi-file-earmark-diff"></i> Simpan');
            },
            });

        });

        $(function () {
            $('#repeater-aktivitas').repeater({
                initEmpty: false,

                defaultValues: {
                    'text-input': 'foo'
                },

                show: function () {
                    $(this).slideDown();
                    $(this).find('[data-kt-repeater="select2"]').select2({
                        placeholder: 'Select option',
                        allowClear: true,
                    });
                },

                ready: function () {
                    $('[data-kt-repeater="select2"]').select2({
                        placeholder: 'Select option',
                        allowClear: true,
                    });
                },

                hide: function (deleteElement) {
                    $(this).slideUp(deleteElement);
                },
            });
        })
    </script>
    @endsection