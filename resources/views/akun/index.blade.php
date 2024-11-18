@php
    $role = hasRole();
@endphp
@section('title', 'User')
@extends('layouts.layout')
@section('content')
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <!--begin::Container-->
        <div id="kt_content_container" class="container">
            <div class="row">

                <div class="card">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card-header align-items-center border-0 mt-4">
                                <h3 class="card-title align-items-start flex-column">
                                <span class="fw-bolder mb-2 text-dark">Detail Akun</span>
                                </h3>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="card-header align-items-center border-0 mt-4">
                                <h3 class="card-title align-items-start flex-column">
                                <span class="fw-bolder mb-2 text-dark">Ganti Password Akun</span>
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                       <div class="row">
                            <div class="col-lg-6">
        
                                    <div class="mb-10">
                                       <label class="form-label">Nama</label>
                                        <input type="text" id="nama" class="form-control" value="{{$account->nama}}" readonly>
                                        <small class="text-danger nama_error"></small>
                                    </div>

                                    <div class="mb-10">
                                       <label class="form-label">NIP</label>
                                        <input type="text" id="nip" class="form-control" value="{{$account->nip}}" readonly>
                                        <small class="text-danger nip_error"></small>
                                    </div>

                                    <div class="mb-10">
                                       <label class="form-label">Username</label>
                                        <input type="text" id="username" class="form-control" value="{{$account->username}}" readonly>
                                        <small class="text-danger username_error"></small>
                                    </div>

                            </div>
                            <div class="col-lg-6">
                                
                           
                                <form class="form-data">

                                    <div class="mb-10">
                                       <label class="form-label">Password lama</label>
                                        <input type="password" id="password_lama" name="password_lama" class="form-control">
                                        <small class="text-danger password_lama_error"></small>
                                    </div>

                                    <div class="mb-10">
                                       <label class="form-label">Password baru</label>
                                        <input type="password" id="password_baru" name="password_baru" class="form-control">
                                        <small class="text-danger password_baru_error"></small>
                                    </div>

                                    <div class="mb-10">
                                       <label class="form-label">Password baru ulang</label>
                                        <input type="password" id="password_baru_ulang" name="password_baru_ulang" class="form-control">
                                        <small class="text-danger password_baru_ulang_error"></small>
                                    </div>

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
        let role = {!! json_encode($role) !!};
        let url_main = '';
        role.guard === 'web' && role.role === '1' ? url_main = '/akun-opd' : url_main = '/akun';
        $(document).on('submit', ".form-data", function(e) {
            e.preventDefault();
           
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
            });

            $.ajax({
            type: 'POST',
            url: `${url_main}/change-password`,
            data: $(".form-data").serialize(),
            success: function (response) {
                console.log(response);
                $(".text-danger").html("");
                if (response.success == true) {
                swal
                    .fire({
                    text: `User berhasil di update password`,
                    icon: "success",
                    showConfirmButton: false,
                    timer: 1500
                    })
                    .then(function () {
                        window.location.href = url_main
                    });
                } else {
                Swal.fire("Gagal Memproses data!", `${response.message}`, "warning");
                }
            },
            error: function (xhr) {
                $(".text-danger").html("");
                $.each(xhr.responseJSON["errors"], function (key, value) {
                $(`.${key}_error`).html(value);
                });
            },
            });
        });


    </script>
@endsection
