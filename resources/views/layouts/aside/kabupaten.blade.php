<div class="menu-item">
    <a class="menu-link  {{ $path[0] == 'dashboard-kabupaten' ? 'active' : '' }}"
        href="{{ url('/dashboard-kabupaten') }}">
        <span class="menu-icon">
            <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
            <span class="svg-icon svg-icon-2">
                <img src="{{ $path[0] == 'dashboard-kabupaten' ? url('admin/assets/media/icons/aside/dashboardact.svg') : url('/admin/assets/media/icons/aside/dashboarddef.svg') }}"
                    alt="">
            </span>
            <!--end::Svg Icon-->
        </span>
        <span class="menu-title"
            style="{{ $path[0] == 'dashboard-kabupaten' ? 'color: #F4BE2A' : 'color: #FFFFFF' }}">Dashboard</span>
    </a>
</div>

@if($role['role'] == '2')

<div data-kt-menu-trigger="click" class="menu-item {{ $path[0] == 'pegawai' ? 'show' : '' }} menu-accordion">
    <span class="menu-link">
        <span class="menu-icon">
            <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
            <span class="svg-icon svg-icon-2">
                <img src="{{ $path[0] == 'pegawai' ? url('admin/assets/media/icons/aside/pegawaiact.svg') : url('/admin/assets/media/icons/aside/pegawaidef.svg') }}"
                    alt="">
            </span>
            <!--end::Svg Icon-->
        </span>
        <span class="menu-title" style="color:#ffffff;">Pegawai</span>
        <span class="menu-arrow"></span>
    </span>
    <div class="menu-sub menu-sub-accordion menu-active-bg">
        <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'pegawai' && $path[1] == 'list-pegawai' ? 'active' : '' }}"  href="{{ route('kabupaten.pegawai.listpegawai.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">Daftar Pegawai</span>
            </a>
        </div>
        <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'pegawai' && $path[1] == 'verifikasi' ? 'active' : '' }}"  href="{{ route('kabupaten.pegawai.verifikasi.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">Verifikasi</span>
            </a>
        </div>
        <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'pegawai' && $path[1] == 'pegawai-masuk' ? 'active' : '' }}"  href="{{ route('kabupaten.pegawai.pegawaimasuk.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">Pegawai Masuk</span>
            </a>
        </div>
        <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'pegawai' && $path[1] == 'pegawai-keluar' ? 'active' : '' }}"  href="{{ route('kabupaten.pegawai.pegawaikeluar.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">Pegawai Keluar</span>
            </a>
        </div>
        <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'pegawai' && $path[1] == 'pegawai-pensiun' ? 'active' : '' }}"  href="{{ route('kabupaten.pegawai.pegawaipensiun.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">Pegawai Pensiun</span>
            </a>
        </div>
        <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'pegawai' && $path[1] == 'pegawai-akan-pensiun' ? 'active' : '' }}"  href="{{ route('kabupaten.pegawai.pegawaiakanpensiun.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">Pegawai Akan Pensiun</span>
            </a>
        </div>

        <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'pegawai' && $path[1] == 'pegawai-non-job' ? 'active' : '' }}"  href="{{ route('kabupaten.pegawai.pegawainonjob.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">Pegawai Non Job</span>
            </a>
        </div>

    </div>
</div>

<div data-kt-menu-trigger="click" class="menu-item {{ $path[0] == 'jabatan' ? 'show' : '' }} menu-accordion">
    <span class="menu-link">
        <span class="menu-icon">
            <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
            <span class="svg-icon svg-icon-2">
                <img src="{{ $path[0] == 'jabatan' ? url('admin/assets/media/icons/aside/jabatanact.svg') : url('/admin/assets/media/icons/aside/jabatandef.svg') }}"
                    alt="">
            </span>
            <!--end::Svg Icon-->
        </span>
        <span class="menu-title" style="color:#ffffff;">Jabatan</span>
        <span class="menu-arrow"></span>
    </span>
    <div class="menu-sub menu-sub-accordion menu-active-bg">
        <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'jabatan' && $path[1] == 'list-jabatan' ? 'active' : '' }}"  href="{{ route('kabupaten.Jabatan.Jabatan.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">List Jabatan</span>
            </a>
        </div>
        <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'jabatan' && $path[1] == 'jabatan-kosong' ? 'active' : '' }}"  href="{{ route('kabupaten.Jabatan.jabatan_kosong.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">Jabatan Kosong</span>
            </a>
        </div>
        <!-- <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'jabatan' && $path[1] == 'jabatan-plt' ? 'active' : '' }}"  href="{{ route('kabupaten.Jabatan.jabatan_plt.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">Jabatan PLT</span>
            </a>
        </div> -->

        <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'jabatan' && $path[1] == 'mutasi' ? 'active' : '' }}"  href="{{ route('kabupaten.Jabatan.mutasi.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">Mutasi</span>
            </a>
        </div>        
    </div>
</div>

<div class="menu-item">
    <a class="menu-link  {{ $path[0] == 'kehadiran' ? 'active' : '' }}"
        href="{{ route('kabupaten.kehadiran.index') }}">
        <span class="menu-icon">
            <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
            <span class="svg-icon svg-icon-2">
                <img src="{{ $path[0] == 'kehadiran' ? url('admin/assets/media/icons/aside/kehadiranact.svg') : url('/admin/assets/media/icons/aside/kehadirandef.svg') }}"
                    alt="">
            </span>
            <!--end::Svg Icon-->
        </span>
        <span class="menu-title"
            style="{{ $path[0] == 'kehadiran' ? 'color: #F4BE2A' : 'color: #FFFFFF' }}">Kehadiran</span>
    </a>
</div>

<div class="menu-item">
    <a class="menu-link  {{ $path[0] == 'hari-libur' ? 'active' : '' }}"
        href="{{ route('kabupaten.harilibur.index') }}">
        <span class="menu-icon">
            <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
            <span class="svg-icon svg-icon-2">
                <img src="{{ $path[0] == 'hari-libur' ? url('admin/assets/media/icons/aside/holidayact.svg') : url('/admin/assets/media/icons/aside/holidaydef.svg') }}"
                    alt="">
            </span>
            <!--end::Svg Icon-->
        </span>
        <span class="menu-title"
            style="{{ $path[0] == 'hari-libur' ? 'color: #F4BE2A' : 'color: #FFFFFF' }}">Hari Libur</span>
    </a>
</div>

@endif

<div data-kt-menu-trigger="click" class="menu-item {{ $path[0] == 'laporan' ? 'show' : '' }} menu-accordion">
    <span class="menu-link">
        <span class="menu-icon">
            <span class="svg-icon svg-icon-2">
                <img src="{{ $path[0] == 'laporan' ? url('admin/assets/media/icons/aside/laporan_act.svg') : url('/admin/assets/media/icons/aside/laporan_def.svg') }}"
                    alt="">
            </span>
        </span>
        <span class="menu-title" style="color:#ffffff;">Laporan</span>
        <span class="menu-arrow"></span>
    </span>
    <div class="menu-sub menu-sub-accordion menu-active-bg">
        <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'laporan' && $path[1] == 'sasaran-kinerja' ? 'active' : '' }}"  href="{{ route('kabupaten.laporan.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">Sasaran Kinerja</span>
            </a>
        </div>
        <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'laporan' && $path[1] == 'kehadiran' ? 'active' : '' }}"  href="{{ route('kabupaten.laporan.kehadiran.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">Kehadiran</span>
            </a>
        </div>
        <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'laporan' && $path[1] == 'kinerja' ? 'active' : '' }}"  href="{{ route('kabupaten.laporan.kinerja.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">Kinerja</span>
            </a>
        </div>
        <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'laporan' && $path[1] == 'tpp' ? 'active' : '' }}"  href="{{ route('kabupaten.laporan.tpp.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">TPP</span>
            </a>
        </div>
        <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'laporan' && $path[1] == 'profil' ? 'active' : '' }}"  href="{{ route('kabupaten.laporan.profil.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">Pegawai</span>
            </a>
        </div>
    </div>
</div>

@if($role['role'] == '2')

<div data-kt-menu-trigger="click" class="menu-item {{ $path[0] == 'layanan' ? 'show' : '' }} menu-accordion">
    <span class="menu-link">
        <span class="menu-icon">
            <span class="svg-icon svg-icon-2">
                <img src="{{ $path[0] == 'layanan' ? url('admin/assets/media/icons/aside/layananact.svg') : url('/admin/assets/media/icons/aside/layanandef.svg') }}"
                    alt="">
            </span>
            <!--end::Svg Icon-->
        </span>
        <span class="menu-title" style="color:#ffffff;">Layanan</span>
        <span class="menu-arrow"></span>
    </span>
    <div class="menu-sub menu-sub-accordion menu-active-bg">
        <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'layanan' && $path[1] == 'master-layanan' ? 'active' : '' }}"  href="{{ route('kabupaten.layanan.masterlayanan.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">Master Layanan</span>
            </a>
        </div>
        <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'layanan' && $path[1] == 'layanan-cuti' ? 'active' : '' }}"  href="{{ route('kabupaten.layanan.layanancuti.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">Layanan Cuti</span>
            </a>
        </div>
        <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'layanan' && $path[1] == 'layanan-general' ? 'active' : '' }}"  href="{{ route('kabupaten.layanan.layanangeneral.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">Layanan General</span>
            </a>
        </div>        
    </div>
</div>


<div data-kt-menu-trigger="click" class="menu-item {{ $path[0] == 'master-jabatan' ? 'show' : '' }} menu-accordion">
    <span class="menu-link">
        <span class="menu-icon">
            <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
            <span class="svg-icon svg-icon-2">
                <img src="{{ $path[0] == 'master-jabatan' ? url('admin/assets/media/icons/aside/masterjabatandef.svg') : url('/admin/assets/media/icons/aside/masterjabatanact.svg') }}"
                    alt="">
            </span>
            <!--end::Svg Icon-->
        </span>
        <span class="menu-title" style="color:#ffffff;">Master Jabatan</span>
        <span class="menu-arrow"></span>
    </span>
    <div class="menu-sub menu-sub-accordion menu-active-bg">
        <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'master-jabatan' && $path[1] == 'master-jabatan' ? 'active' : '' }}"  href="{{ route('kabupaten.master_jabatan.master_jabatan.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">Master Jabatan</span>
            </a>
        </div>
          <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'master-jabatan' && $path[1] == 'kelompok-aktivitas' ? 'active' : '' }}"  href="{{ route('kabupaten.master_jabatan.kelompok_aktivitas.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">Kelompok Aktivitas</span>
            </a>
        </div>
        <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'master-jabatan' && $path[1] == 'jenis-jabatan' ? 'active' : '' }}"  href="{{ route('kabupaten.master_jabatan.jenis_jabatan.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">Jenis Jabatan</span>
            </a>
        </div>
    </div>
</div>

<div data-kt-menu-trigger="click" class="menu-item {{ $path[0] == 'master-data' ? 'show' : '' }} menu-accordion">
    <span class="menu-link">
        <span class="menu-icon">
            <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
            <span class="svg-icon svg-icon-2">
                <img src="{{ $path[0] == 'master-data' ? url('admin/assets/media/icons/aside/masterdataact.svg') : url('/admin/assets/media/icons/aside/masterdatadef.svg') }}"
                    alt="">
            </span>
            <!--end::Svg Icon-->
        </span>
        <span class="menu-title" style="color:#ffffff;">Master Data</span>
        <span class="menu-arrow"></span>
    </span>
    <div class="menu-sub menu-sub-accordion menu-active-bg">
        <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'master-data' && $path[1] == 'satuan' ? 'active' : '' }}"  href="{{ route('master_data.satuan.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">Satuan</span>
            </a>
            <a class="menu-link {{ $path[0] == 'master-data' && $path[1] == 'pendidikan' ? 'active' : '' }}"  href="{{ route('master_data.pendidikan.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">Pendidikan</span>
            </a>
            <a class="menu-link {{ $path[0] == 'master-data' && $path[1] == 'agama' ? 'active' : '' }}"  href="{{ route('master_data.agama.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">Agama</span>
            </a>
            <a class="menu-link {{ $path[0] == 'master-data' && $path[1] == 'golongan' ? 'active' : '' }}"  href="{{ route('master_data.golongan.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">Golongan</span>
            </a>
            <a class="menu-link {{ $path[0] == 'master-data' && $path[1] == 'eselon' ? 'active' : '' }}"  href="{{ route('master_data.eselon.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">Eselon</span>
            </a>
        </div>
    </div>
</div>

<div data-kt-menu-trigger="click" class="menu-item {{ $path[0] == 'perangkat-daerah' ? 'show' : '' }} menu-accordion">
    <span class="menu-link">
        <span class="menu-icon">
            <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
            <span class="svg-icon svg-icon-2">
                <img src="{{ $path[0] == 'perangkat-daerah' ? url('admin/assets/media/icons/aside/perangkatdaerahact.svg') : url('/admin/assets/media/icons/aside/perangkatdaerahdef.svg') }}"
                    alt="">
            </span>
            <!--end::Svg Icon-->
        </span>
        <span class="menu-title" style="color:#ffffff;">Perangkat Daerah</span>
        <span class="menu-arrow"></span>
    </span>
    <div class="menu-sub menu-sub-accordion menu-active-bg">
        <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'perangkat-daerah' && $path[1] == 'perangkat-daerah' ? 'active' : '' }}"  href="{{ route('kabupaten.perangkat_daerah.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">Perangkat Daerah</span>
            </a>
        </div>

        <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'perangkat-daerah' && $path[1] == 'unit-kerja' ? 'active' : '' }}"  href="{{ route('kabupaten.unit_kerja.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">Unit Kerja</span>
            </a>
        </div>

        <div class="menu-item">
            <a class="menu-link {{ $path[0] == 'perangkat-daerah' && $path[1] == 'lokasi' ? 'active' : '' }}"  href="{{ route('kabupaten.perangkat_daerah.lokasi.index') }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title" style="color:#ffffff;">Lokasi Kerja</span>
            </a>
        </div>
    </div>
</div>

<div class="menu-item">
    <a class="menu-link  {{ $path[0] == 'profil-kepala-daerah' ? 'active' : '' }}"
        href="{{ route('kabupaten.profil_kepala_daerah') }}">
        <span class="menu-icon">
            <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
            <span class="svg-icon svg-icon-2">
                <img src="{{ $path[0] == 'profil-kepala-daerah' ? url('admin/assets/media/icons/aside/profilkepaladaerahact.svg') : url('/admin/assets/media/icons/aside/profilkepaladaerahdef.svg') }}"
                    alt="">
            </span>
            <!--end::Svg Icon-->
        </span>
        <span class="menu-title"
            style="{{ $path[0] == 'profil-kepala-daerah' ? 'color: #F4BE2A' : 'color: #FFFFFF' }}">Profil Kepala Daerah</span>
    </a>
</div>

<div class="menu-item">
    <a class="menu-link  {{ $path[0] == 'pengumuman' ? 'active' : '' }}"
        href="{{ route('kabupaten.pengumuman.index') }}">
        <span class="menu-icon">
            <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
            <span class="svg-icon svg-icon-2">
                <img src="{{ $path[0] == 'pengumuman' ? url('admin/assets/media/icons/aside/pengumumanact.svg') : url('/admin/assets/media/icons/aside/pengumumandef.svg') }}"
                    alt="">
            </span>
            <!--end::Svg Icon-->
        </span>
        <span class="menu-title"
            style="{{ $path[0] == 'pengumuman' ? 'color: #F4BE2A' : 'color: #FFFFFF' }}">Pengumuman</span>
    </a>
</div>

<div class="menu-item">
    <a class="menu-link  {{ $path[0] == 'user' ? 'active' : '' }}"
        href="{{ route('kabupaten.pegawai.user.index') }}">
        <span class="menu-icon">
            <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
            <span class="svg-icon svg-icon-2">
                <img src="{{ $path[0] == 'user' ? url('admin/assets/media/icons/aside/useract.svg') : url('/admin/assets/media/icons/aside/userdef.svg') }}"
                    alt="">
            </span>
            <!--end::Svg Icon-->
        </span>
        <span class="menu-title"
            style="{{ $path[0] == 'user' ? 'color: #F4BE2A' : 'color: #FFFFFF' }}">User</span>
    </a>
</div>



<div class="menu-item">
    <a class="menu-link  {{ $path[0] == 'admin' ? 'active' : '' }}"
        href="{{ route('kabupaten.admin.index') }}">
        <span class="menu-icon">
            <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
            <span class="svg-icon svg-icon-2">
                <img src="{{ $path[0] == 'admin' ? url('admin/assets/media/icons/aside/adminact.svg') : url('/admin/assets/media/icons/aside/admindef.svg') }}"
                    alt="">
            </span>
            <!--end::Svg Icon-->
        </span>
        <span class="menu-title"
            style="{{ $path[0] == 'admin' ? 'color: #F4BE2A' : 'color: #FFFFFF' }}">Admin</span>
    </a>
</div>

@endif