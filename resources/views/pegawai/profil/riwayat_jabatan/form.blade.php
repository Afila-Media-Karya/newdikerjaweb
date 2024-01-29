<div id="side_form_riwayat_jabatan" class="bg-white" data-kt-drawer="true" data-kt-drawer-activate="true"
        data-kt-drawer-toggle="#side_form_riwayat_jabatan_button" data-kt-drawer-close="#side_form_riwayat_jabatan_close" data-kt-drawer-width="500px">
        <!--begin::Card-->
        <div class="card w-100">
            <!--begin::Card header-->
            <div class="card-header pe-5">
                <!--begin::Title-->
                <div class="card-title">
                    <!--begin::User-->
                    <div class="d-flex justify-content-center flex-column me-3">
                        <a href="#"
                            class="fs-4 fw-bolder text-gray-900 title_side_form text-hover-primary me-1 lh-1 title_side_form_riwayat_jabatan"></a>
                    </div>
                    <!--end::User-->
                </div>
                <!--end::Title-->
                <!--begin::Card toolbar-->
                <div class="card-toolbar">
                    <!--begin::Close-->
                    <div class="btn btn-sm btn-icon btn-active-light-primary" id="side_form_riwayat_jabatan_close">
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
                <form class="form-data" id="riwayat-jabatan">

                    <input type="hidden" name="id">
                    <input type="hidden" name="uuid">

                    <div class="mb-10">
                        <label class="form-label">Nama Jabatan</label>
                        <input type="text" id="nama_jabatan" class="form-control"  name="nama_jabatan" placeholder="Masukkan Nama Jabatan">
                        <small class="text-danger nama_jabatan_error"></small>
                    </div>

                   <div class="mb-10">
                        <label class="form-label">Golongan</label>
                        <select class="form-select form-control" name="golongan" data-control="select2" data-placeholder="Pilih Golongan">
                            <option></option>
                            @foreach($golongan as $val)
                                <option value="{{$val->value}}">{{$val->text}}</option>
                            @endforeach
                        </select>
                        <small class="text-danger golongan_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Nomor</label>
                        <input type="text" id="nomor" class="form-control"  name="nomor" placeholder="Masukkan Nomor">
                        <small class="text-danger nomor_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Tanggal</label>
                        <input type="date" id="tanggal" class="form-control"  name="tanggal" placeholder="Masukkan Nomor Ijazah">
                        <small class="text-danger tanggal_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Pejabat Pendantanganan</label>
                        <input type="text" id="pejabat_pendantanganan" class="form-control"  name="pejabat_pendantanganan" placeholder="Masukkan Pejabat Pendantanganan">
                        <small class="text-danger pejabat_pendantanganan_error"></small>
                    </div>

                   <div class="mb-10">
                        <label class="form-label">TMT</label>
                        <input type="date" id="tmt" class="form-control"  name="tmt">
                        <small class="text-danger tmt_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Nama Unit Kerja</label>
                        <input type="text" id="nama_unit_kerja" class="form-control"  name="nama_unit_kerja" placeholder="Masukkan Nama Unit Kerja">
                        <small class="text-danger nama_unit_kerja_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Surat Keputusan</label>
                        <input type="file" id="surat_keputusan" class="form-control"  name="surat_keputusan">
                        <small class="text-danger surat_keputusan_error"></small>
                    </div>                

                    <div class="separator separator-dashed mt-8 mb-5"></div>
                    <div class="d-flex gap-5">
                        <button type="submit" class="btn btn-primary btn-sm btn-submit d-flex align-items-center"><i
                                class="bi bi-file-earmark-diff"></i> Simpan</button>
                        <button type="reset" id="side_form_riwayat_jabatan_close"
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