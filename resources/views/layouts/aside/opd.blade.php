<div class="menu-item">
    <div class="menu-content pt-8 pb-2">
        <span class=" text-uppercase fs-8 ls-1" style="color:#fff">PEGAWAI</span>
    </div>
</div>

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

@if(session('session_tipe_pegawai') == 'pegawai_administratif')

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
        <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'laporan-pegawai' && $path[1] == 'sasaran-kinerja' ? 'active' : '' }}"  href="{{ route('pegawai.laporan.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">Sasaran Kinerja</span>
            </a>
        </div>
        <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'laporan-pegawai' && $path[1] == 'kehadiran' ? 'active' : '' }}"  href="{{ route('pegawai.laporan.kehadiran.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">Kehadiran</span>
            </a>
        </div>
        <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'laporan-pegawai' && $path[1] == 'kinerja' ? 'active' : '' }}"  href="{{ route('pegawai.laporan.kinerja.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">Kinerja</span>
            </a>
        </div>
        <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'laporan-pegawai' && $path[1] == 'tpp' ? 'active' : '' }}"  href="{{ route('pegawai.laporan.tpp.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">TPP</span>
            </a>
        </div>

        
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

<div class="menu-item">
    <div class="menu-content pt-8 pb-2">
        <span class=" text-uppercase fs-8 ls-1" style="color:#fff">@if($role['role'] == '1') ADMIN SKPD @else ADMIN UNIT KERJA @endif </span>
    </div>
</div>
<div class="menu-item">
    <a class="menu-link  {{ $path[0] == 'dashboard-opd' ? 'active' : '' }}"
        href="{{ url('/dashboard-opd') }}">
        <span class="menu-icon">
            <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
            <span class="svg-icon svg-icon-2">
                <img src="{{ $path[0] == 'dashboard-opd' ? url('admin/assets/media/icons/aside/dashboardact.svg') : url('/admin/assets/media/icons/aside/dashboarddef.svg') }}"
                    alt="">
            </span>
            <!--end::Svg Icon-->
        </span>
        <span class="menu-title"
            style="{{ $path[0] == 'dashboard-opd' ? 'color: #F4BE2A' : 'color: #FFFFFF' }}">Dashboard</span>
    </a>
</div>

<div data-kt-menu-trigger="click" class="menu-item {{ $path[0] == 'pegawai-opd' ? 'show' : '' }} menu-accordion">
    <span class="menu-link">
        <span class="menu-icon">
            <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
            <span class="svg-icon svg-icon-2">
                <img src="{{ $path[0] == 'pegawai-opd' ? url('admin/assets/media/icons/aside/pegawaiact.svg') : url('/admin/assets/media/icons/aside/pegawaidef.svg') }}"
                    alt="">
            </span>
            <!--end::Svg Icon-->
        </span>
        <span class="menu-title" style="color:#ffffff;">Pegawai</span>
        <span class="menu-arrow"></span>
    </span>
    <div class="menu-sub menu-sub-accordion menu-active-bg">
        <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'pegawai-opd' && $path[1] == 'list-pegawai-opd' ? 'active' : '' }}"  href="{{ route('opd.pegawai.listpegawai.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">Daftar Pegawai</span>
            </a>
        </div>
        <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'pegawai-opd' && $path[1] == 'verifikasi-opd' ? 'active' : '' }}"  href="{{ route('opd.pegawai.verifikasi.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">Verifikasi</span>
            </a>
        </div>
        <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'pegawai-opd' && $path[1] == 'pegawai-akan-pensiun' ? 'active' : '' }}"  href="{{ route('opd.pegawai.pegawaiakanpensiun.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">Pegawai Akan Pensiun</span>
            </a>
        </div>
        <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'pegawai-opd' && $path[1] == 'pegawai-non-job' ? 'active' : '' }}"  href="{{ route('opd.pegawai.pegawainonjob.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">Pegawai Non Job</span>
            </a>
        </div>
    </div>
</div>

<div data-kt-menu-trigger="click" class="menu-item {{ $path[0] == 'jabatan-opd' ? 'show' : '' }} menu-accordion">
    <span class="menu-link">
        <span class="menu-icon">
            <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
            <span class="svg-icon svg-icon-2">
                <img src="{{ $path[0] == 'jabatan-opd' ? url('admin/assets/media/icons/aside/jabatanact.svg') : url('/admin/assets/media/icons/aside/jabatandef.svg') }}"
                    alt="">
            </span>
            <!--end::Svg Icon-->
        </span>
        <span class="menu-title" style="color:#ffffff;">Jabatan</span>
        <span class="menu-arrow"></span>
    </span>
    <div class="menu-sub menu-sub-accordion menu-active-bg">
        <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'jabatan-opd' && $path[1] == 'list-jabatan' ? 'active' : '' }}"  href="{{ route('kabupaten.Jabatan.jabatanopd.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">List Jabatan</span>
            </a>
        </div>
        <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'jabatan-opd' && $path[1] == 'jabatan-kosong' ? 'active' : '' }}"  href="{{ route('opd.Jabatan.jabatan_kosong.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">Jabatan Kosong</span>
            </a>
        </div>
        <!-- <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'jabatan-opd' && $path[1] == 'jabatan-plt' ? 'active' : '' }}"  href="{{ route('opd.Jabatan.jabatan_plt.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">Jabatan PLT</span>
            </a>
        </div> -->
    </div>
</div>

@if(session('session_tipe_pegawai') == 'pegawai_administratif')

<div class="menu-item">
    <a class="menu-link  {{ $path[0] == 'aktivitas-opd' ? 'active' : '' }}"
        href="{{ route('opd.aktivitas.index') }}">
        <span class="menu-icon">
            <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
            <span class="svg-icon svg-icon-2">
                <img src="{{ $path[0] == 'aktivitas-opd' ? url('admin/assets/media/icons/aside/aktivitasact.svg') : url('/admin/assets/media/icons/aside/aktivitasdef.svg') }}"
                    alt="">
            </span>
            <!--end::Svg Icon-->
        </span>
        <span class="menu-title"
            style="{{ $path[0] == 'aktivitas-opd' ? 'color: #F4BE2A' : 'color: #FFFFFF' }}">Aktivitas</span>
    </a>
</div>

@endif


<div class="menu-item">
    <a class="menu-link  {{ $path[0] == 'kehadiran-opd' ? 'active' : '' }}"
        href="{{ route('opd.kehadiran.index') }}">
        <span class="menu-icon">
            <span class="svg-icon svg-icon-2">
                <img src="{{ $path[0] == 'kehadiran-opd' ? url('admin/assets/media/icons/aside/kehadiranact.svg') : url('/admin/assets/media/icons/aside/kehadirandef.svg') }}"
                    alt="">
            </span>
        </span>
        <span class="menu-title"
            style="{{ $path[0] == 'kehadiran-opd' ? 'color: #F4BE2A' : 'color: #FFFFFF' }}">Kehadiran</span>
    </a>
</div>

<div data-kt-menu-trigger="click" class="menu-item {{ $path[0] == 'laporan-opd' ? 'show' : '' }} menu-accordion">
    <span class="menu-link">
        <span class="menu-icon">
            <span class="svg-icon svg-icon-2">
                <img src="{{ $path[0] == 'laporan-opd' ? url('admin/assets/media/icons/aside/laporan_act.svg') : url('/admin/assets/media/icons/aside/laporan_def.svg') }}"
                    alt="">
            </span>
        </span>
        <span class="menu-title" style="color:#ffffff;">Laporan</span>
        <span class="menu-arrow"></span>
    </span>
    <div class="menu-sub menu-sub-accordion menu-active-bg">
        <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'laporan-opd' && $path[1] == 'sasaran-kinerja' ? 'active' : '' }}"  href="{{ route('opd.laporan.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">Sasaran Kinerja</span>
            </a>
        </div>
        <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'laporan-opd' && $path[1] == 'kehadiran' ? 'active' : '' }}"  href="{{ route('opd.laporan.kehadiran.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">Kehadiran</span>
            </a>
        </div>
        @if(session('session_tipe_pegawai') == 'pegawai_administratif')
        <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'laporan-opd' && $path[1] == 'kinerja' ? 'active' : '' }}"  href="{{ route('opd.laporan.kinerja.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">Kinerja</span>
            </a>
        </div>
        <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'laporan-opd' && $path[1] == 'tpp' ? 'active' : '' }}"  href="{{ route('opd.laporan.tpp.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">TPP</span>
            </a>
        </div>
        @endif
        <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'laporan-opd' && $path[1] == 'profil' ? 'active' : '' }}"  href="{{ route('opd.laporan.profil.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">Pegawai</span>
            </a>
        </div> 
    </div>
</div>

@if(auth()->user()->role == '1' && session('session_satuan_kerja') == 'Dinas Pendidikan dan Kebudayaan')

<div class="menu-item">
    <a class="menu-link  {{ $path[0] == 'profil' ? 'active' : '' }}"
        href="{{ route('opd.perangkat_daerah.lokasi.index') }}">
        <span class="menu-icon">
            <span class="svg-icon svg-icon-2">
                <img src="{{ $path[0] == 'profil' ? url('admin/assets/media/icons/aside/kehadiranact.svg') : url('/admin/assets/media/icons/aside/kehadirandef.svg') }}"
                    alt="">
            </span>
        </span>
        <span class="menu-title"
            style="{{ $path[0] == 'profil' ? 'color: #F4BE2A' : 'color: #FFFFFF' }}">Lokasi Absen Unit Kerja</span>
    </a>
</div>

@endif

<div data-kt-menu-trigger="click" class="menu-item {{ $path[0] == 'layanan-opd' ? 'show' : '' }} menu-accordion">
    <span class="menu-link">
        <span class="menu-icon">
            <span class="svg-icon svg-icon-2">
                <img src="{{ $path[0] == 'layanan-opd' ? url('admin/assets/media/icons/aside/layananact.svg') : url('/admin/assets/media/icons/aside/layanandef.svg') }}"
                    alt="">
            </span>
        </span>
        <span class="menu-title" style="color:#ffffff;">Layanan</span>
        <span class="menu-arrow"></span>
    </span>
    <div class="menu-sub menu-sub-accordion menu-active-bg">
        <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'layanan-opd' && $path[1] == 'layanan-cuti' ? 'active' : '' }}"  href="{{ route('opd.layanan.layanancuti.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">Layanan Cuti</span>
            </a>
        </div>
    </div>
</div>

<div class="menu-item">
    <a class="menu-link  {{ $path[0] == 'akun-opd' ? 'active' : '' }}"
        href="{{ route('opd.akun.index') }}">
        <span class="menu-icon">
            <span class="svg-icon svg-icon-2">
                <img src="{{ $path[0] == 'akun-opd' ? url('admin/assets/media/icons/aside/kehadiranact.svg') : url('/admin/assets/media/icons/aside/kehadirandef.svg') }}"
                    alt="">
            </span>
        </span>
        <span class="menu-title"
            style="{{ $path[0] == 'akun-opd' ? 'color: #F4BE2A' : 'color: #FFFFFF' }}">Akun</span>
    </a>
</div>
