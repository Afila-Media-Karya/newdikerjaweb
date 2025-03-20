<div class="menu-item">
    <a class="menu-link  {{ $path[0] == 'dashboard-pegawai' ? 'active' : '' }}"
        href="{{ url('/dashboard-pegawai') }}">
        <span class="menu-icon">
            <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
            <span class="svg-icon svg-icon-2">
                <img src="{{ $path[0] == 'dashboard-pegawai' ? url('admin/assets/media/icons/aside/dashboardact.svg') : url('/admin/assets/media/icons/aside/dashboarddef.svg') }}"
                    alt="">
            </span>
            <!--end::Svg Icon-->
        </span>
        <span class="menu-title"
            style="{{ $path[0] == 'dashboard-pegawai' ? 'color: #F4BE2A' : 'color: #FFFFFF' }}">Dashboard</span>
    </a>
</div>

<div class="menu-item">
    <a class="menu-link  {{ $path[0] == 'sasaran-kinerja' ? 'active' : '' }}"
        href="{{ route('pegawai.skp.index') }}">
        <span class="menu-icon">
            <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
            <span class="svg-icon svg-icon-2">
                <img src="{{ $path[0] == 'sasaran-kinerja' ? url('admin/assets/media/icons/aside/skpact.svg') : url('/admin/assets/media/icons/aside/skpdef.svg') }}"
                    alt="">
            </span>
            <!--end::Svg Icon-->
        </span>
        <span class="menu-title"
            style="{{ $path[0] == 'sasaran-kinerja' ? 'color: #F4BE2A' : 'color: #FFFFFF' }}">Sasaran Kinerja</span>
    </a>
</div>


@if($role['tipe_pegawai'] == 'pegawai_administratif' || $role['tipe_pegawai'] == 'tenaga_pendidik_non_guru')
<div class="menu-item">
    <a class="menu-link  {{ $path[0] == 'aktivitas' ? 'active' : '' }}"
        href="{{ route('pegawai.aktivitas.index') }}">
        <span class="menu-icon">
            <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
            <span class="svg-icon svg-icon-2">
                <img src="{{ $path[0] == 'aktivitas' ? url('admin/assets/media/icons/aside/aktivitasact.svg') : url('/admin/assets/media/icons/aside/aktivitasdef.svg') }}"
                    alt="">
            </span>
            <!--end::Svg Icon-->
        </span>
        <span class="menu-title"
            style="{{ $path[0] == 'aktivitas' ? 'color: #F4BE2A' : 'color: #FFFFFF' }}">Aktivitas</span>
    </a>
</div>
@endif

@if($role['tipe_pegawai'] == 'pegawai_administratif' || $role['tipe_pegawai'] == 'tenaga_pendidik_non_guru' || preg_match('/Kepala UPT SPF/i', session('session_nama_jabatan')))
<div data-kt-menu-trigger="click" class="menu-item {{ $path[0] == 'review' ? 'show' : '' }} menu-accordion">
    <span class="menu-link">
        <span class="menu-icon">
            <span class="svg-icon svg-icon-2">
                <img src="{{ $path[0] == 'review' ? url('admin/assets/media/icons/aside/jabatanact.svg') : url('/admin/assets/media/icons/aside/jabatandef.svg') }}"
                    alt="">
            </span>
        </span>
        <span class="menu-title" style="color:#ffffff;">Review</span>
        <span class="menu-arrow"></span>
    </span>
    <div class="menu-sub menu-sub-accordion menu-active-bg">
        <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'review' && $path[1] == 'sasaran-kinerja' ? 'active' : '' }}"  href="{{ route('pegawai.review.sasaran_kinerja.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">Sasaran Kinerja</span>
            </a>
        </div>
        <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'review' && $path[1] == 'aktivitas' ? 'active' : '' }}"  href="{{ route('pegawai.review.aktivitas.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">Aktivitas</span>
            </a>
        </div>
        <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'review' && $path[1] == 'realisasi-skp' ? 'active' : '' }}"  href="{{ route('pegawai.review.realisasi.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">Realisasi</span>
            </a>
        </div>
    </div>
</div>
@endif

@if($role['tipe_pegawai'] == 'pegawai_administratif' || $role['tipe_pegawai'] == 'tenaga_pendidik_non_guru')
<div class="menu-item">
    <a class="menu-link  {{ $path[0] == 'realisasi' ? 'active' : '' }}"
        href="{{ route('pegawai.realisasi.index') }}">
        <span class="menu-icon">
            <span class="svg-icon svg-icon-2">
                <img src="{{ $path[0] == 'profil' ? url('admin/assets/media/icons/aside/realisasiact.svg') : url('/admin/assets/media/icons/aside/realisasidef.svg') }}"
                    alt="">
            </span>
        </span>
        <span class="menu-title"
            style="{{ $path[0] == 'profil' ? 'color: #F4BE2A' : 'color: #FFFFFF' }}">Realisasi</span>
    </a>
</div>
@endif


<div data-kt-menu-trigger="click" class="menu-item {{ $path[0] == 'laporan-pegawai' ? 'show' : '' }} menu-accordion">
    <span class="menu-link">
        <span class="menu-icon">
            <span class="svg-icon svg-icon-2">
                <img src="{{ $path[0] == 'laporan-pegawai' ? url('admin/assets/media/icons/aside/laporan_act.svg') : url('/admin/assets/media/icons/aside/laporan_def.svg') }}"
                    alt="">
            </span>
        </span>
        <span class="menu-title" style="color:#ffffff;">Laporan</span>
        <span class="menu-arrow"></span>
    </span>
    <div class="menu-sub menu-sub-accordion menu-active-bg">

    @if($role['tipe_pegawai'] == 'pegawai_administratif' || $role['tipe_pegawai'] == 'tenaga_pendidik_non_guru')
        <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'laporan-pegawai' && $path[1] == 'sasaran-kinerja' ? 'active' : '' }}"  href="{{ route('pegawai.laporan.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">Sasaran Kinerja</span>
            </a>
        </div>
    @endif    
        <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'laporan-pegawai' && $path[1] == 'kehadiran' ? 'active' : '' }}"  href="{{ route('pegawai.laporan.kehadiran.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">Kehadiran</span>
            </a>
        </div>
    @if($role['tipe_pegawai'] == 'pegawai_administratif' || $role['tipe_pegawai'] == 'tenaga_pendidik_non_guru')
        <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'laporan-pegawai' && $path[1] == 'kinerja' ? 'active' : '' }}"  href="{{ route('pegawai.laporan.kinerja.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">Kinerja</span>
            </a>
        </div>
    @endif
    @if($role['tipe_pegawai'] == 'pegawai_administratif')
        <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'laporan-pegawai' && $path[1] == 'tpp' ? 'active' : '' }}"  href="{{ route('pegawai.laporan.tpp.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">TPP</span>
            </a>
        </div>
    @endif
    </div>
</div>

<div data-kt-menu-trigger="click" class="menu-item {{ $path[0] == 'layanan-pegawai' ? 'show' : '' }} menu-accordion">
    <span class="menu-link">
        <span class="menu-icon">
            <span class="svg-icon svg-icon-2">
                <img src="{{ $path[0] == 'layanan-pegawai' ? url('admin/assets/media/icons/aside/layananact.svg') : url('/admin/assets/media/icons/aside/layanandef.svg') }}"
                    alt="">
            </span>
        </span>
        <span class="menu-title" style="color:#ffffff;">Layanan</span>
        <span class="menu-arrow"></span>
    </span>
    <div class="menu-sub menu-sub-accordion menu-active-bg">
        <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'layanan-pegawai' && $path[1] == 'layanan-cuti' ? 'active' : '' }}"  href="{{ route('pegawai.layanan_cuti_pegawai.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">Layanan Cuti</span>
            </a>
        </div>
    </div>
</div>

<div class="menu-item">
    <a class="menu-link  {{ $path[0] == 'profil' ? 'active' : '' }}"
        href="{{ route('pegawai.profil.index') }}">
        <span class="menu-icon">
            <span class="svg-icon svg-icon-2">
                <img src="{{ $path[0] == 'profil' ? url('admin/assets/media/icons/aside/kehadiranact.svg') : url('/admin/assets/media/icons/aside/kehadirandef.svg') }}"
                    alt="">
            </span>
        </span>
        <span class="menu-title"
            style="{{ $path[0] == 'profil' ? 'color: #F4BE2A' : 'color: #FFFFFF' }}">Profil</span>
    </a>
</div>

<div class="menu-item">
    <a class="menu-link  {{ $path[0] == 'akun' ? 'active' : '' }}"
        href="{{ route('pegawai.akun.index') }}">
        <span class="menu-icon">
            <span class="svg-icon svg-icon-2">
                <img src="{{ $path[0] == 'akun' ? url('admin/assets/media/icons/aside/kehadiranact.svg') : url('/admin/assets/media/icons/aside/kehadirandef.svg') }}"
                    alt="">
            </span>
        </span>
        <span class="menu-title"
            style="{{ $path[0] == 'akun' ? 'color: #F4BE2A' : 'color: #FFFFFF' }}">Akun</span>
    </a>
</div>
