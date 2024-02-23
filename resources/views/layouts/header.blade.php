@php
    $role = hasRole();
@endphp
<div id="kt_header" style="" class="header align-items-stretch">
    <!--begin::Container-->
    <div class="container-fluid d-flex align-items-stretch justify-content-between">
        <!--begin::Aside mobile toggle-->
        <div class="d-flex align-items-center d-lg-none ms-n3 me-1" title="Show aside menu">
            <div class="btn btn-icon btn-active-light-primary w-30px h-30px w-md-40px h-md-40px"
                id="kt_aside_mobile_toggle">
                <!--begin::Svg Icon | path: icons/duotone/Text/Menu.svg-->
                <span class="svg-icon svg-icon-2x mt-1">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                        height="24px" viewBox="0 0 24 24" version="1.1">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <rect x="0" y="0" width="24" height="24" />
                            <rect fill="#000000" x="4" y="5" width="16" height="3"
                                rx="1.5" />
                            <path
                                d="M5.5,15 L18.5,15 C19.3284271,15 20,15.6715729 20,16.5 C20,17.3284271 19.3284271,18 18.5,18 L5.5,18 C4.67157288,18 4,17.3284271 4,16.5 C4,15.6715729 4.67157288,15 5.5,15 Z M5.5,10 L18.5,10 C19.3284271,10 20,10.6715729 20,11.5 C20,12.3284271 19.3284271,13 18.5,13 L5.5,13 C4.67157288,13 4,12.3284271 4,11.5 C4,10.6715729 4.67157288,10 5.5,10 Z"
                                fill="#000000" opacity="0.3" />
                        </g>
                    </svg>
                </span>
                <!--end::Svg Icon-->
            </div>
        </div>
        <!--end::Aside mobile toggle-->
        <!--begin::Mobile logo-->
        <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0">
            <a href="#" class="d-lg-none">
                <img src="{{ asset('admin/assets/media/logos/bulkum.svg') }}" style="width: 37px;" alt=""
                            srcset="">
            </a>
        </div>
        <!--end::Mobile logo-->
        <!--begin::Wrapper-->
        <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1">
            <!--begin::Navbar-->
            <div class="d-flex align-items-stretch" id="kt_header_nav">
                <!--begin::Menu wrapper-->
                <div class="header-menu align-items-stretch" data-kt-drawer="true" data-kt-drawer-name="header-menu"
                    data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true"
                    data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="end"
                    data-kt-drawer-toggle="#kt_header_menu_mobile_toggle" data-kt-swapper="true"
                    data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_body', lg: '#kt_header_nav'}">
                    <!--begin::Menu-->
                    <div class="menu menu-lg-rounded menu-column menu-lg-row menu-state-bg menu-title-gray-700 menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-400 fw-bold my-5 my-lg-0 align-items-stretch"
                        id="#kt_header_menu" data-kt-menu="true">
                        <div class="menu-item me-lg-1">
                            <div class="title-header-topbar">

                                    <nav aria-label="breadcrumb">
                                        <ol class="breadcrumb">
                                            @foreach($module as $modul)
                                                <li class="breadcrumb-item"><a href="{{ $modul['url'] }}">{{ $modul['label'] }}</a></li>
                                            @endforeach
                                        </ol>
                                    </nav>

                            </div>
                        </div>


                    </div>
                    <!--end::Menu-->
                </div>
                <!--end::Menu wrapper-->
            </div>
            <!--end::Navbar-->

            <!--begin::Topbar-->
            <div class="d-flex align-items-stretch flex-shrink-0">

                <div class="d-flex align-items-stretch flex-shrink-0">
                    <div class="row g-3 align-items-center" style="position: relative;right: 42px;">

                        <!-- <div class="input-group mb-5">
                                            <input type="text" class="form-control" placeholder="Tahun Penganggaran" readonly aria-label="Tahun Penganggaran" aria-describedby="basic-addon2"/>
                                            <span class="input-group-text" id="basic-addon2">@example.com</span>
                                        </div> -->

                        <div class="input-group mt-3">
                        <input type="text" class="form-control form-control-sm" disabled value="Tahun Penganggaran">
                        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">{{ session('tahun_penganggaran') }}</button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @php
                                $tahun_anggaran = session()->has('tahun_penganggaran') ? (int) session('tahun_penganggaran') : (int) date('Y');
                            @endphp
                            @for ($i = 2023; $i <= 2025; $i++)
                                <a class="dropdown-item p-3" href="{{ route('set-tahun-penganggaran', ['tahun' => $i]) }}" 
                                @if ($i == $tahun_anggaran) selected @endif>{{ $i }}</a>
                            @endfor
                        </ul>
                    </div>

                    </div>

                    <!--begin::User-->
                    <div class="d-flex align-items-center ms-1 ms-lg-3" id="kt_header_user_menu_toggle">

                    <div class="cursor-pointer symbol symbol-30px symbol-md-40px" data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                      <img src="{{session('session_foto')}}" alt="user">

                      <div class="cursor-pointer symbol symbol-30px symbol-md-40px" style="position:relative;left:10px;" data-kt-menu-trigger="click"
                            data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end" data-kt-menu-flip="bottom">
                            <div class="text-gray-700">Hai, <span
                                    class="text-gray-900">{{ session('session_nama') }}</span></div>
                        </div>
                    </div>
                        
                        <!--begin::Menu-->
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold py-4 fs-6 w-275px"
                            data-kt-menu="true">

                            
                                    <div class="menu-item px-3">
                                        <div class="menu-content px-3">
                                            <!--begin::Avatar-->
                                            <div class="symbol symbol-50px me-5 d-flex justify-content-center mb-3">
                                                <img alt="Logo" src="{{ session('session_foto') }}">
                                            </div>
                                            <!--end::Avatar-->
                                            <!--begin::Username-->
                                            <div class="text-center">
                                                <!-- <a href="#" class="fw-bold text-muted text-hover-primary fs-7"></a> -->
                                                <p>{{ session('session_nama') }}</p>
                                                <div class="fw-bolder text-center fs-5">
                                                    <small>{{ session('session_nama_jabatan') }}</small>
                                                </div>
                                                
                                            </div>
                                            <!--end::Username-->
                                        </div>
                                    </div>

                            @if(count($jabatan) > 0)
                                @foreach($jabatan as $val)
                        
                                    <div class="menu-item px-5">
                                        <a href="/change-session-jabatan/{{$val->status}}?jabatan={{$val->nama_jabatan}}&kode={{$val->id_jabatan}}" @if($val->status == session('session_jabatan') && $val->id_jabatan == session('session_jabatan_kode')) style="background:#F3FAFE" @endif class="menu-link px-5">
                                        <span class="menu-title position-relative">{{ $val->text }}
                                             @if($val->status == session('session_jabatan') && $val->id_jabatan == session('session_jabatan_kode'))
                                            <span class="fs-8 rounded bg-light px-3 py-2 position-absolute translate-middle-y top-50 end-0">
                                                <img class="w-15px h-15px rounded-1 ms-2" src="{{ asset('admin/assets/media/icons/checkmark-circle.png') }}" alt="">
                                            </span>
                                            @endif
                                        </span>
                                        </a>
                                    
                                    </div>
                                    <div class="separator my-2"></div>
                                @endforeach
                            @endif
                      
                            <!--begin::Menu item-->
                            <div class="menu-item px-5">
                            
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-grid">
                                    @csrf
                                    @method('POST')
                                    <button type="submit" style="border: none;" class="menu-link px-5"
                                        id="sign-out">Sign
                                        Out</button>
                                </form>
                            </div>
                            <!--end::Menu item-->
                        </div>
                        <!--end::Menu-->
                        <!--end::Menu wrapper-->
                    </div>
                    <!--end::User -->
                    <!--begin::Heaeder menu toggle-->
                    <div class="d-flex align-items-center d-lg-none ms-2 me-n3" title="Show header menu">
                        <div class="btn btn-icon btn-active-light-primary w-30px h-30px w-md-40px h-md-40px"
                            id="kt_header_menu_mobile_toggle">
                            <!--begin::Svg Icon | path: icons/duotone/Text/Toggle-Right.svg-->
                            <span class="svg-icon svg-icon-1">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24" />
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M22 11.5C22 12.3284 21.3284 13 20.5 13H3.5C2.6716 13 2 12.3284 2 11.5C2 10.6716 2.6716 10 3.5 10H20.5C21.3284 10 22 10.6716 22 11.5Z"
                                            fill="black" />
                                        <path opacity="0.5" fill-rule="evenodd" clip-rule="evenodd"
                                            d="M14.5 20C15.3284 20 16 19.3284 16 18.5C16 17.6716 15.3284 17 14.5 17H3.5C2.6716 17 2 17.6716 2 18.5C2 19.3284 2.6716 20 3.5 20H14.5ZM8.5 6C9.3284 6 10 5.32843 10 4.5C10 3.67157 9.3284 3 8.5 3H3.5C2.6716 3 2 3.67157 2 4.5C2 5.32843 2.6716 6 3.5 6H8.5Z"
                                            fill="black" />
                                    </g>
                                </svg>
                            </span>
                            <!--end::Svg Icon-->
                        </div>
                    </div>
                    <!--end::Heaeder menu toggle-->
                </div>
                <!--end::Toolbar wrapper-->
            </div>
            <!--end::Topbar-->
        </div>
        <!--end::Wrapper-->
    </div>
    <!--end::Container-->
</div>
