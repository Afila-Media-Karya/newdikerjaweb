@php
    $path = explode('/', request()->path());
    $role = hasRole();
@endphp
<div class="aside-menu aside-menu-custom flex-column-fluid">
    <!--begin::Aside Menu-->
    <div class="hover-scroll-overlay-y mb-5 mb-lg-5" id="kt_aside_menu_wrapper" data-kt-scroll="true"
        data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-height="auto"
        data-kt-scroll-dependencies="#kt_aside_logo, #kt_aside_footer" data-kt-scroll-wrappers="#kt_aside_menu"
        data-kt-scroll-offset="0">
        <div class="menu-title" style="background-color: #0D47A1; padding: 0 25px; color: #FFFFFF; font-size: 12px">
            {{session('session_satuan_kerja')}}
        </div>
        <!--begin::Menu-->
        <div class="menu menu-column mt-2 menu-title-gray-800 menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-500"
            id="#kt_aside_menu" data-kt-menu="true">


            @if($role['guard'] == 'administrator' && $role['role'] == '2')
                @include('layouts.aside.kabupaten')
            @elseif($role['guard'] == 'web' && $role['role'] == '1' || $role['role'] == '3')    
                @include('layouts.aside.opd')
            @else
                @include('layouts.aside.pegawai')
            @endif

        </div>
        <!--end::Menu-->
    </div>
</div>

@section('script')
    <script>
        $(document).ready(function() {
          
        });
    </script>
@endsection
