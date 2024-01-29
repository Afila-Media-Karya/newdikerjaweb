@extends('layouts.layout')
@section('style')
    <link rel="stylesheet" href="https://unpkg.com/placeholder-loading/dist/css/placeholder-loading.min.css">
@endsection
@section('title', 'Dashboard')
@section('button')
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-end">
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            <select id="filter-bulan" name="bulan" class="form-control form-control-solid">
                @foreach (range(1, 12) as $bulan)
                    <option value="{{ $bulan }}" {{ $bulan == date('n') ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::parse('2023-' . $bulan . '-01')->translatedFormat('F') }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
@endsection
@section('content')
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <!--begin::Container-->
        <div id="kt_content_container" class="container">
            <!-- <div class="row">
                                            <div class="card">
                                                <div class="card-header">Dynamic Data</div>
                                                <div class="card-body" id="dynamic_content">
                                                
                                                </div>
                                            </div>
                                        </div> -->
            <div class="row mb-5">
                <div class="col-lg-4">
                    <div class="card card-xl-stretch">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex flex-stack">
                                <!--begin::Section-->
                                <div class="d-flex align-items-center me-2">
                                    <!--begin::Symbol-->
                                    <!-- <div class="symbol"> -->
                                    <img src="{{ asset('admin/assets/media/icons/dashboard/kinerja.png') }}" height="56"
                                        width="56" alt="">
                                    <!-- </div> -->
                                    <!--end::Symbol-->
                                    <!--begin::Title-->
                                    <div>
                                        <div class="label-widget text-hover-primary fw-bolder"> Kinerja </div>
                                    </div>
                                    <!--end::Title-->
                                </div>
                                <div class="label-nilai persentase_skp">0</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card card-xl-stretch">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex flex-stack">
                                <!--begin::Section-->
                                <div class="d-flex align-items-center me-2">
                                    <!--begin::Symbol-->
                                    <!-- <div class="symbol"> -->
                                    <img src="{{ asset('admin/assets/media/icons/dashboard/aktivitas.png') }}"
                                        height="56" width="56" alt="">
                                    <!-- </div> -->
                                    <!--end::Symbol-->
                                    <!--begin::Title-->
                                    <div>
                                        <div class="label-widget text-hover-primary fw-bolder"> Aktivitas </div>
                                    </div>
                                    <!--end::Title-->
                                </div>
                                <div class="label-nilai persentase_kinerja">0</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card card-xl-stretch">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex flex-stack">
                                <!--begin::Section-->
                                <div class="d-flex align-items-center me-2">
                                    <!--begin::Symbol-->
                                    <!-- <div class="symbol"> -->
                                    <img src="{{ asset('admin/assets/media/icons/dashboard/pegawai_dinilai.png') }}"
                                        height="56" width="56" alt="">
                                    <!-- </div> -->
                                    <!--end::Symbol-->
                                    <!--begin::Title-->
                                    <div>
                                        <div class="label-widget text-hover-primary fw-bolder"> Kehadiran </div>
                                    </div>
                                    <!--end::Title-->
                                </div>
                                <div class="label-nilai persentase_kehadiran">0 %</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-5">
                <div class="col-lg-6">
                    <div class="card">
                        <h3 class="card-titles">
                            <span class="card-label fw-bolder fs-3 mb-1">Pegawai</span>
                        </h3>
                        <div class="card-body py-5">
                            <div class="row">
                                <div class="col-lg-5">
                                    <div class="widget-lg">
                                        <div class="img-bg-widget">
                                            <img src="{{ asset('admin/assets/media/icons/dashboard/group_user.svg') }}"
                                                alt="" srcset="">
                                        </div>

                                        <div class="d-flex justify-content-between p-5">
                                            <h3>Jumlah Pegawai</h3>
                                            <img src="{{ asset('admin/assets/media/icons/dashboard/boy-front-color.png') }}"
                                                alt="">
                                        </div>
                                        <div class="group-value-widget-lg">
                                            <h1 class="jumlah_pegawai"></h1>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-7">
                                    <div class="d-flex gap-4 mb-5">
                                        <div class="box-gray-widget-lg">
                                            <span>Definitif</span>
                                            <h1 class="jml_definitif">0</h1>
                                        </div>
                                        <div class="box-gray-widget-lg">
                                            <span>PLT</span>
                                            <h1 class="jml_plt">0</h1>
                                        </div>
                                        <div class="box-gray-widget-lg">
                                            <span>Kosong</span>
                                            <h1 class="jabatan_kosong">0</h1>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-4">
                                        <div class="box-gray-widget-lg">
                                            <span>Keluar</span>
                                            <h1 class="pegawai_keluar">0</h1>
                                        </div>
                                        <div class="box-gray-widget-lg">
                                            <span>Pensiun</span>
                                            <h1 class="pegawai_pensiun">0</h1>
                                        </div>
                                        <div class="box-gray-widget-lg">
                                            <span>Masuk</span>
                                            <h1 class="pegawai_masuk">0</h1>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <h3 class="card-titles">
                            <span class="card-label fw-bolder fs-3 mb-1">Jenis Kelamin</span>
                        </h3>
                        <div class="card-body py-5">

                            <div class="row">
                                <div class="col-lg-5">
                                    <div class="circle-background"></div>
                                    <div class="group-progress">
                                        <div class="customProgressBar" role="progressbar" aria-valuenow="65"
                                            aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <!-- data-bs-toggle="tooltip" data-bs-placement="right" data-bs-dismiss="click" data-bs-trigger="hover" data-bs-original-title="Check out 20 more demos" -->
                                </div>
                                <div class="col-lg-7" id="jenis-kelamin-box">
                                    <div class="box-jk-widget jk-success mb-2">
                                        <div class="d-flex">
                                            <img src="{{ asset('admin/assets/media/icons/dashboard/man.svg') }}"
                                                alt="" srcset="">
                                            <div class="content-widget-jk">
                                                <span>Laki Laki</span>
                                                <h1 class="jenis_kelamin_l">0</h1>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="box-jk-widget jk-primary mb-2">
                                        <div class="d-flex">
                                            <img src="{{ asset('admin/assets/media/icons/dashboard/women.svg') }}"
                                                alt="" srcset="">
                                            <div class="content-widget-jk">
                                                <span>Perempuan</span>
                                                <h1 class="jenis_kelamin_p">0</h1>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-5">
                <div class="col-lg-6">
                    <div class="card card-xl-stretch">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex flex-stack">
                                <!--begin::Section-->
                                <div class="d-flex align-items-center me-2">
                                    <!--begin::Symbol-->
                                    <!-- <div class="symbol"> -->
                                    <img src="{{ asset('admin/assets/media/icons/dashboard/pendidikan_menengah.svg') }}"
                                        alt="">
                                    <!-- </div> -->
                                    <!--end::Symbol-->
                                    <!--begin::Title-->
                                    <div>
                                        <div class="label-widget text-hover-primary fw-bolder"> Pendidikan Menengah (SMP &
                                            SMA) </div>
                                    </div>
                                    <!--end::Title-->
                                </div>
                                <div class="label-nilai-dark pendidikan_menengah">0</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card card-xl-stretch">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex flex-stack">
                                <!--begin::Section-->
                                <div class="d-flex align-items-center me-2">
                                    <!--begin::Symbol-->
                                    <!-- <div class="symbol"> -->
                                    <img src="{{ asset('admin/assets/media/icons/dashboard/pendidikan_tinggi.svg') }}"
                                        alt="">
                                    <!-- </div> -->
                                    <!--end::Symbol-->
                                    <!--begin::Title-->
                                    <div>
                                        <div class="label-widget text-hover-primary fw-bolder"> Pendidikan Tinggi (DI - S3)
                                        </div>
                                    </div>
                                    <!--end::Title-->
                                </div>
                                <div class="label-nilai-dark pendidikan_tinggi">0</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-5">
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-body golongan-widget">
                            <div class="d-flex justify-content-between">
                                <span>Golongan I</span>
                                <p class="golongan1">0</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-body golongan-widget">
                            <div class="d-flex justify-content-between">
                                <span>Golongan II</span>
                                <p class="golongan2">0</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-body golongan-widget">
                            <div class="d-flex justify-content-between">
                                <span>Golongan III</span>
                                <p class="golongan3">0</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-body golongan-widget">
                            <div class="d-flex justify-content-between">
                                <span>Golongan IV</span>
                                <p class="golongan4">0</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-5" id="kontent-rank-opd">


            </div>
        </div>
        <!--end::Container-->
    </div>
@endsection
@section('script')
    <script>
        let control = new Control();

        $(document).on('change', '#filter-bulan', function() {
            control.persentase_dashboard($(this).val());
        })

        $(function() {
            control.persentase_dashboard($('#filter-bulan').val());
            $('#tb_pegawai_dinilai').DataTable();
        })
    </script>
@endsection
