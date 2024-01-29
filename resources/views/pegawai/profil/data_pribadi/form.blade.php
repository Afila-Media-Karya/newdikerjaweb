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
                            class="fs-4 fw-bolder text-gray-900 text-hover-primary me-1 lh-1 title_side_form">Data Pribadi</a>
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
                <form class="form-data" id="data-pribadi">

                    <input type="hidden" name="id" value="{{$data->id}}">
                    <input type="hidden" name="uuid" value="{{$data->uuid}}">

                    <div class="mb-10">
                        <label class="form-label">NIP</label>
                        <input type="text" id="nip" class="form-control" value="{{$data->nip}}" name="nip" placeholder="Masukkan NIP">
                        <small class="text-danger nip_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Nama</label>
                        <input type="text" id="nama" class="form-control" name="nama" value="{{$data->nama}}" placeholder="Masukkan Nama">
                        <small class="text-danger nama_error"></small>
                    </div>

                    <div class="row mb-10">
                        <div class="col-lg-6">
                            <label class="form-label">Tempat Lahir</label>
                            <input type="text" id="tempat_lahir" class="form-control" name="tempat_lahir" value="{{$data->tempat_lahir}}" placeholder="Masukkan tempat lahir">
                            <small class="text-danger tempat_lahir_error"></small>
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" id="tanggal_lahir" class="form-control" value="{{$data->tanggal_lahir}}" name="tanggal_lahir">
                            <small class="text-danger tanggal_lahir_error"></small>
                        </div>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Jenis Kelamin</label>
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" name="jenis_kelamin" @if($data->jenis_kelamin) == 'L' checked @endif type="radio" value="L" id="L"/>
                                    <label class="form-check-label" for="L">
                                        Laki Laki
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" name="jenis_kelamin" @if($data->jenis_kelamin) == 'P' checked @endif type="radio" value="P" id="P"/>
                                    <label class="form-check-label" for="P">
                                        Perempuan
                                    </label>
                                </div>
                            </div>
                        </div>
                        <small class="text-danger jenis_kelamin_error"></small>
                    </div>
                    
                    <div class="mb-10">
                        <label class="form-label">Agama</label>
                        <select class="form-select form-control" name="agama" data-control="select2" data-placeholder="Pilih Agama">
                            <option></option>
                            @foreach($agama as $val)
                                <option value="{{$val->value}}" @if($data->agama == $val->value ) selected @endif>{{$val->text}}</option>
                            @endforeach
                        </select>
                        <small class="text-danger agama_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Status Perkawinan</label>
                        <select name="status_perkawinan" class="form-control">
                            <option selected disabled>Pilih Status Perkawinan</option>
                            <option value="Kawin" @if($data->status_perkawinan == 'Kawin' ) selected @endif>Kawin</option>
                            <option value="Belum Kawin" @if($data->status_perkawinan == 'Belum Kawin') selected @endif>Belum Kawin</option>
                            <option value="Cerai Hidup" @if($data->status_perkawinan == 'Cerai Hidup' ) selected @endif>Cerai Hidup</option>
                            <option value="Cerai Mati" @if($data->status_perkawinan == 'Cerai Mati' ) selected @endif>Cerai Mati</option>
                        </select>
                        <small class="text-danger status_perkawinan_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">TMT Pegawai</label>
                        <input type="date" id="tmt_pegawai" class="form-control" value="{{$data->tmt_pegawai}}" name="tmt_pegawai">
                        <small class="text-danger tmt_pegawai_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Golongan</label>
                        <select class="form-select form-control" name="golongan" data-control="select2" data-placeholder="Pilih Golongan">
                            <option></option>
                            @foreach($golongan as $val)
                                <option value="{{$val->value}}" @if($data->golongan == $val->value ) selected @endif>{{$val->text}}</option>
                            @endforeach
                        </select>
                        <small class="text-danger golongan_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">TMT Jabatan</label>
                        <input type="date" id="tmt_jabatan" class="form-control" value="{{$data->tmt_jabatan}}" name="tmt_jabatan">
                        <small class="text-danger tmt_jabatan_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">TMT Golongan</label>
                        <input type="date" id="tmt_golongan" class="form-control" value="{{$data->tmt_golongan}}" name="tmt_golongan">
                        <small class="text-danger tmt_golongan_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Pendidikan</label>
                        <select class="form-select form-control" name="pendidikan" data-control="select2" data-placeholder="Pilih Pendidikan">
                            <option></option>
                            @foreach($pendidikan as $val)
                                <option value="{{$val->value}}" @if($data->pendidikan == $val->value ) selected @endif>{{$val->text}}</option>
                            @endforeach
                        </select>
                        <small class="text-danger pendidikan_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Pendidikan Lulus</label>
                        <input type="date" id="pendidikan_lulus" class="form-control" value="{{$data->pendidikan_lulus}}" name="pendidikan_lulus">
                        <small class="text-danger pendidikan_lulus_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Pendidikan Struktural</label>
                        <input type="text" id="pendidikan_struktural" class="form-control" value="{{$data->pendidikan_struktural}}" name="pendidikan_struktural">
                        <small class="text-danger pendidikan_struktural_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Pendidikan Struktural Lulus</label>
                        <input type="date" id="pendidikan_struktural_lulus" class="form-control" value="{{$data->pendidikan_struktural_lulus}}" name="pendidikan_struktural_lulus">
                        <small class="text-danger pendidikan_struktural_lulus_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Status Kepegawaian</label>
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" name="status_kepegawaian" @if($data->status_kepegawaian == 'PNS') checked  @endif type="radio" value="PNS" id="PNS"/>
                                    <label class="form-check-label" for="PNS">
                                        PNS
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" name="status_kepegawaian" @if($data->status_kepegawaian == 'PPPK') checked  @endif type="radio" value="PPPK" id="PPPK"/>
                                    <label class="form-check-label" for="PPPK">
                                        PPPK
                                    </label>
                                </div>
                            </div>
                        </div>
                        <small class="text-danger status_kepegawaian_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Tipe Pegawai</label>
                        <select name="tipe_pegawai" class="form-control">
                            <option selected disabled>Pilih Status Perkawinan</option>
                            <option value="pegawai_administratif" @if($data->tipe_pegawai == 'pegawai_administratif') selected  @endif>Pegawai Administratif</option>
                            <option value="tenaga_pendidik" @if($data->tipe_pegawai == 'tenaga_pendidik') selected  @endif>Tenaga Pendidik</option>
                            <option value="tenaga_kesehatan" @if($data->tipe_pegawai == 'tenaga_kesehatan') selected  @endif>Tenaga Kesehatan</option>
                        </select>
                        <small class="text-danger tipe_pegawai_error"></small>
                    </div>

                    <input type="hidden" name="id_satuan_kerja" value="{{$satuan_kerja_user->id_satuan_kerja}}">

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