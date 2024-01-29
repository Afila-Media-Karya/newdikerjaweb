<div id="side_form_riwayat_anak" class="bg-white" data-kt-drawer="true" data-kt-drawer-activate="true"
        data-kt-drawer-toggle="#side_form_riwayat_anak_button" data-kt-drawer-close="#side_form_riwayat_anak_close" data-kt-drawer-width="500px">
        <!--begin::Card-->
        <div class="card w-100">
            <!--begin::Card header-->
            <div class="card-header pe-5">
                <!--begin::Title-->
                <div class="card-title">
                    <!--begin::User-->
                    <div class="d-flex justify-content-center flex-column me-3">
                        <a href="#"
                            class="fs-4 fw-bolder text-gray-900 title_side_form text-hover-primary me-1 lh-1 title_side_form_riwayat_anak"></a>
                    </div>
                    <!--end::User-->
                </div>
                <!--end::Title-->
                <!--begin::Card toolbar-->
                <div class="card-toolbar">
                    <!--begin::Close-->
                    <div class="btn btn-sm btn-icon btn-active-light-primary" id="side_form_riwayat_anak_close">
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
                <form class="form-data" id="riwayat-anak">

                    <input type="hidden" name="id">
                    <input type="hidden" name="uuid">

                    <div class="row mb-10">
                        <label class="form-label">Nama Anak</label>
                        <input type="text" id="nama_anak" class="form-control"  name="nama_anak" placeholder="Masukkan Nama Anak">
                        <small class="text-danger nama_anak_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Jenis Kelamin</label>
                        <select name="jk" class="form-control">
                            <option selected disabled>Pilih Jenis Kelamin</option>
                            <option value="L">Laki-Laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                        <small class="text-danger jk_error"></small>
                    </div>

                    <div class="row mb-10">

                        <div class="col-lg-6">
                            <label class="form-label">Tempat Lahir</label>
                            <input type="text" id="tempat_lahir" class="form-control"  name="tempat_lahir" placeholder="Masukkan Tempat Lahir">
                            <small class="text-danger tempat_lahir_error"></small>
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" id="tanggal_lahir" class="form-control"  name="tanggal_lahir">
                            <small class="text-danger tanggal_lahir_error"></small>
                        </div>

                    </div>

                    <div class="mb-10">
                        <label class="form-label">Status Perkawinan</label>
                        <select name="status_perkawinan" class="form-control">
                            <option selected disabled>Pilih Status Perkawinan</option>
                            <option value="Kawin">Kawin</option>
                            <option value="Belum Kawin">Belum Kawin</option>
                            <option value="Cerai Hidup">Cerai Hidup</option>
                            <option value="Cerai Mati">Cerai Mati</option>
                        </select>
                        <small class="text-danger status_perkawinan_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Memperoleh Tunjangan</label>
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" name="memperoleh_tunjangan" type="radio" value="Memperoleh" id="Memperoleh"/>
                                    <label class="form-check-label" for="L">
                                        Memperoleh
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-8">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" name="memperoleh_tunjangan" type="radio" value="Tidak Memperoleh" id="Tidak_Memperoleh"/>
                                    <label class="form-check-label" for="P">
                                        Tidak_Memperoleh
                                    </label>
                                </div>
                            </div>
                        </div>
                        <small class="text-danger memperoleh_tunjangan_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Pendidikan</label>
                        <select class="form-select form-control" name="pendidikan" data-control="select2" data-placeholder="Pilih Pendidikan">
                            <option></option>
                            @foreach($pendidikan as $val)
                                <option value="{{$val->value}}">{{$val->text}}</option>
                            @endforeach
                        </select>
                        <small class="text-danger pendidikan_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Pekerjaan</label>
                        <input type="text" id="pekerjaan" class="form-control"  name="pekerjaan" placeholder="Masukkan Pekerjaan">
                        <small class="text-danger pekerjaan_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" class="form-control" rows="3" placeholder="Masukkan Keterangan"></textarea>
                        <small class="text-danger keterangan_error"></small>
                    </div>
                    
                     <div class="mb-10">
                        <label class="form-label">Foto Kartu Keluarga</label>
                        <input type="file" id="foto_kartu_keluarga" class="form-control"  name="foto_kartu_keluarga">
                        <small class="text-danger foto_kartu_keluarga_error"></small>
                    </div>                

                    <div class="separator separator-dashed mt-8 mb-5"></div>
                    <div class="d-flex gap-5">
                        <button type="submit" class="btn btn-primary btn-sm btn-submit d-flex align-items-center"><i
                                class="bi bi-file-earmark-diff"></i> Simpan</button>
                        <button type="reset" id="side_form_riwayat_anak_close"
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