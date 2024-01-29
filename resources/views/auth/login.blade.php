<!DOCTYPE html>
<!--
Author: Keenthemes
Product Name: Metronic - Bootstrap 5 HTML, VueJS, React, Angular & Laravel Admin Dashboard Theme
Purchase: https://1.envato.market/EA4JP
Website: http://www.keenthemes.com
Contact: support@keenthemes.com
Follow: www.twitter.com/keenthemes
Dribbble: www.dribbble.com/keenthemes
Like: www.facebook.com/keenthemes
License: For each use you must have a valid license purchased only from above link in order to legally use the theme for your project.
-->
<html lang="en">
    <!--begin::Head-->

    <head>
        <base href="../../../">
        <title>Login DIKERJA</title>
        <meta charset="utf-8" />
        <meta name="description"
            content="The most advanced Bootstrap Admin Theme on Themeforest trusted by 94,000 beginners and professionals. Multi-demo, Dark Mode, RTL support and complete React, Angular, Vue &amp; Laravel versions. Grab your copy now and get life-time updates for free." />
        <meta name="keywords"
            content="Metronic, bootstrap, bootstrap 5, Angular, VueJs, React, Laravel, admin themes, web design, figma, web development, free templates, free admin themes, bootstrap theme, bootstrap template, bootstrap dashboard, bootstrap dak mode, bootstrap button, bootstrap datepicker, bootstrap timepicker, fullcalendar, datatables, flaticon" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta property="og:locale" content="en_US" />
        <meta property="og:type" content="article" />
        <meta property="og:title"
            content="Metronic - Bootstrap 5 HTML, VueJS, React, Angular &amp; Laravel Admin Dashboard Theme" />
        <meta property="og:url" content="https://keenthemes.com/metronic" />
        <meta property="og:site_name" content="Keenthemes | Metronic" />
        <link rel="canonical" href="https://preview.keenthemes.com/metronic8" />
        <link rel="shortcut icon" href="{{ asset('admin/assets/media/logos/favicon.png') }}" />
        <!--begin::Fonts-->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
        <!--end::Fonts-->
        <!--begin::Global Stylesheets Bundle(used by all pages)-->
        <link href="{{ asset('admin/assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('admin/assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('admin/assets/css/style.css') }}" rel="stylesheet" type="text/css" />
        <!--end::Global Stylesheets Bundle-->
    </head>
    <!--end::Head-->
    <!--begin::Body-->

    <body id="kt_body" class="bg-body">
        <!--begin::Main-->
        <!--begin::Root-->
        <div class="d-flex flex-column flex-root">
            <!--begin::Authentication - Sign-in -->
            <div class="d-flex flex-column flex-lg-row flex-column-fluid">
                <!--begin::Aside-->
                <div id="aside_login" class="d-flex flex-column flex-lg-row-auto w-xl-800px positon-xl-relative d-none d-md-block">
                    <div class="d-flex flex-column position-xl-fixed top-0 bottom-0 scroll-y">
                        <div class="header_jumbotron">
                            <img src="{{ asset('admin/assets/media/logos/logo_sm.png') }}" class="side-logo" alt="" />
                            <span class="vertical-line"></span>
                            <h4>BKPSDM <br> KABUPATEN BULUKUMBA</h4>
                        </div>
                        <div class="center-box">
                            <div class="frame-box">
                                <div class="welcome-box" style="font-weight:700">Selamat Datang</div>
                                <p> DIKERJA (Dokumen Elektronik Kinerja dan Kehadiran)
                                <span style="font-weight:200">adalah platform digital untuk memudahkan Aparatur Sipil Negara (ASN) dalam melakukan pelaporan aktifitas kinerja dan kehadiran kerja berbasis Website dan Mobile App.</span></p>
                            </div>
                        </div>
                        <div class="d-flex flex-row-auto bgi-no-repeat bgi-position-x-center bgi-size-contain bgi-position-y-bottom min-h-100px min-h-lg-350px"
                            style="background-image: url(assets/media/illustrations/sketchy-1/13.png"></div>
                    </div>
                </div>
                <div class="d-flex flex-column flex-lg-row-fluid py-10">
                    <!--begin::Content-->
                    <div class="d-flex flex-center flex-column flex-column-fluid">
                        <!--begin::Wrapper-->
                        <img src="{{ asset('admin/assets/media/logos/logo_dikerja.svg') }}" alt="" />
                        <div class="w-lg-500px p-10 p-lg-15 mx-auto">

                            <form class="form w-100" action="{{ route('login.post') }}" method="POST">
                                @csrf

                                <div class="text-right mb-10">
                                    <h1 class="text-dark font-weight-bold mb-3"><b>Masuk Akun</b></h1>
                                </div>
                                <!--begin::Input group-->
                                <div class="fv-row mb-10">
                                    <!--begin::Label-->
                                    <label class="form-label fs-6 text-dark">Nomor Induk Pegawai (NIP)</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->

                                    <div class="input-group input-group-solid">
                                        <input class="form-control form-control-lg form-control-solid" type="text"
                                            name="username" autocomplete="off" placeholder="Masukkan NIP" />
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="far fa-user fa-1x mt-2"></i>
                                            </span>
                                        </div>
                                    </div>

                                    @error('username')
                                        <div class="error text-danger">{{ $message }}</div>
                                    @enderror
                                    <!--end::Input-->
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="fv-row mb-10">
                                    <!--begin::Wrapper-->
                                    <div class="d-flex flex-stack mb-2">
                                        <!--begin::Label-->
                                        <label class="form-label text-dark fs-6 mb-0">Password</label>

                                    </div>
                                    <!--end::Wrapper-->
                                    <!--begin::Input-->
                                    <div class="input-group input-group-solid">
                                        <input class="form-control form-control-lg form-control-solid" id="password"
                                            type="password" name="password" autocomplete="off"
                                            placeholder="Masukkan Password" />
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="far fa-eye fa-1x mt-2 eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                    @error('password')
                                        <div class="error text-danger">{{ $message }}</div>
                                    @enderror
                                    <!--end::Input-->
                                </div>
                                <!--end::Input group-->
                                <!--begin::Actions-->
                                <div class="text-center">
                                    <!--begin::Submit button-->
                                    <button type="submit" id="kt_sign_in_submit"
                                        class="btn btn-lg btn-primary w-100 mb-5">
                                        <span class="indicator-label">Login</span>
                                    </button>

                                    @error('failed')
                                        <div class="error text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!--end::Actions-->
                            </form>
                        </div>
                        <div class="d-flex flex-center gap-8 p-2">
                            <a href="https://play.google.com/store/apps/details?id=com.afila.simasnmaspul&hl=en-ID"
                                target="_blank">
                                <img src="{{ asset('admin/assets/media/auth/playstore.svg') }}" alt=""
                                    srcset="">
                            </a>
                            <a href="https://apps.apple.com/us/app/DIKERJA/id6474229039"
                                target="_blank">
                                <img src="{{ asset('admin/assets/media/auth/appstore.svg') }}" alt=""
                                    srcset="">
                            </a>
                        </div>
                        <!--end::Wrapper-->
                    </div>

                    <!--end::Content-->
                    <!--begin::Footer-->
                    <div class="d-flex flex-center flex-wrap fs-6 gap-5 p-5 pb-0" id="footer-login">
                        <img src="{{ asset('admin/assets/media/auth/logo_afila.svg') }}" alt=""
                            srcset="">
                        <p>Crafted by <a href="https://afila.co.id" target="_blank" style="color:#354C9F">
                                Afila Media Karya</a> </p>
                    </div>
                    <!--end::Footer-->
                </div>
                <!--end::Body-->
            </div>
            <!--end::Authentication - Sign-in-->
        </div>
        <!--end::Root-->
        <!--end::Main-->
        <!--begin::Javascript-->
        <script>
            var hostUrl = "assets/";
        </script>
        <!--begin::Global Javascript Bundle(used by all pages)-->
        <script src="{{ asset('admin/assets/plugins/global/plugins.bundle.js') }}"></script>
        <script src="{{ asset('admin/assets/js/scripts.bundle.js') }}"></script>

    </body>
    <!--end::Body-->

</html>
<script>
    $('.eye').click(function(e) {
        e.preventDefault();

        if ($('#password').prop('type') == 'password') {
            $(this).addClass(' fa-eye-slash');
            $('#password').attr('type', 'text');
        } else {
            $(this).removeClass(' fa-eye-slash');
            $('#password').attr('type', 'password');
        }
    });

    @if ($errors->any())
        Swal.fire({
            title: 'Peringatan!',
            text: '{{ $errors->first() }}', // Display the first error message
            icon: 'warning',
            confirmButtonText: 'OK'
        });
    @endif
</script>
