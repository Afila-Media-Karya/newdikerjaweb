<div id="side_form_catatan_hukuman_dinas" class="bg-white" data-kt-drawer="true" data-kt-drawer-activate="true"
        data-kt-drawer-toggle="#side_form_catatan_hukuman_dinas_button" data-kt-drawer-close="#side_form_catatan_hukuman_dinas_close" data-kt-drawer-width="500px">
        <!--begin::Card-->
        <div class="card w-100">
            <!--begin::Card header-->
            <div class="card-header pe-5">
                <!--begin::Title-->
                <div class="card-title">
                    <!--begin::User-->
                    <div class="d-flex justify-content-center flex-column me-3">
                        <a href="#"
                            class="fs-4 fw-bolder text-gray-900 title_side_form text-hover-primary me-1 lh-1 title_side_form_catatan_hukuman_dinas"></a>
                    </div>
                    <!--end::User-->
                </div>
                <!--end::Title-->
                <!--begin::Card toolbar-->
                <div class="card-toolbar">
                    <!--begin::Close-->
                    <div class="btn btn-sm btn-icon btn-active-light-primary" id="side_form_catatan_hukuman_dinas_close">
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
                <form class="form-data" id="catatan-hukuman-dinas">

                    <input type="hidden" name="id">
                    <input type="hidden" name="uuid">

                    <div class="mb-10">
                        <label class="form-label">Kategori Hukuman</label>
                        <select name="kategori_hukuman" class="form-control">
                            <option selected disabled>Pilih Kategori Hukuman</option>
                            <option value="Pemberhentian Sementara (Nonaktif)">Pemberhentian Sementara (Nonaktif)</option>
                            <option value="Pemberhentian Tidak Dengan Hormat (PTDH)">Pemberhentian Tidak Dengan Hormat (PTDH)</option>
                            <option value="Pemindahan Jabatan atau Tugas">Pemindahan Jabatan atau Tugas</option>
                            <option value="Peringatan Tertulis">Peringatan Tertulis</option>
                            <option value="Peringatan Lisan">Peringatan Lisan</option>
                            <option value="Pengurangan Gaji atau Tunjangan">Pengurangan Gaji atau Tunjangan</option>
                            <option value="Penonaktifan dari Jabatan Kepemimpinan">Penonaktifan dari Jabatan Kepemimpinan</option>
                            <option value="Pencabutan Kenaikan Pangkat atau Gaji">Pencabutan Kenaikan Pangkat atau Gaji</option>
                            <option value="Pendidikan dan Pelatihan Disiplin">Pendidikan dan Pelatihan Disiplin</option>
                            <option value="Penghentian Sementara Hak Kenaikan Gaji Berkala (HKG)">Penghentian Sementara Hak Kenaikan Gaji Berkala (HKG)</option>
                        </select>
                        <small class="text-danger kategori_hukuman_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Nama Hukuman</label>
                        <input type="text" id="nama_hukuman" class="form-control"  name="nama_hukuman" placeholder="Masukkan Nama Hukuman">
                        <small class="text-danger nama_hukuman_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Nama SK</label>
                        <input type="text" id="nama_sk" class="form-control"  name="nama_sk" placeholder="Masukkan Nama SK">
                        <small class="text-danger nama_sk_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Tanggal SK</label>
                        <input type="date" id="tanggal_sk" class="form-control"  name="tanggal_sk">
                        <small class="text-danger tanggal_sk_error"></small>
                    </div>

                    <div class="row mb-10">
                        <div class="col-lg-6">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" id="tanggal_mulai" class="form-control"  name="tanggal_mulai">
                            <small class="text-danger tanggal_mulai_error"></small>
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" id="tanggal_selesai" class="form-control"  name="tanggal_selesai">
                            <small class="text-danger tanggal_selesai_error"></small>
                        </div>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Keterangan Pelanggaran</label>
                        <textarea name="keterangan_pelanggaran" class="form-control" rows="3" placeholder="Keterangan Pelanggaran"></textarea>
                        <small class="text-danger keterangan_pelanggaran_error"></small>
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
                        <button type="reset" id="side_form_catatan_hukuman_dinas_close"
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