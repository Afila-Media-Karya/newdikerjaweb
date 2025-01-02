@extends('layouts.layout')
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
            <small class="text-danger bulan_error"></small>
        </div>
    </div>
@endsection
@section('title', 'Dashboard')
@section('content')
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <!--begin::Container-->
        <div id="kt_content_container" class="container">
            <div class="row">
                @if($role['guard'] == 'web' && $role['role'] !== '3' && $tipe_pegawai !== 'tenaga_pendidik' && $tipe_pegawai !== 'tenaga_kesehatan')
                    <div class="box-tpp">
                        <span>TPP Anda Bulan ini</span>
                        <h1>Rp 0</h1>
                        <div class="progress-bar__wrapper">
                            <label class="progress-bar__value" htmlFor="progress-bar"></label>
                            <progress id="progress-bar" value="40" max="100"></progress>
                        </div>
                        <p></p>
                    </div>
                @endif
            </div>
            <div class="row">
                @if($role['guard'] == 'web' && $role['role'] !== '3' && $tipe_pegawai !== 'tenaga_pendidik' && $tipe_pegawai !== 'tenaga_kesehatan')
                <div class="col-lg-6">
                    <div class="row">
                        
                        <div class="col-lg-6">
                            <div class="card card-xl-stretch box-widget mb-5">
                                <div class="d-flex align-items-center me-3">
                                    <img src="{{ asset('admin/assets/media/icons/dashboard/calculator.png') }}"
                                        height="56" width="56" alt="">
                                    <div class="flex-grow-1 group-labels">
                                        <div class="badges mb-2 title-kinerja" style="width:110%"></div>
                                            <div class="d-flex align-items-center flex-row-fluid flex-wrap mb-2">
                                                <div class="flex-grow-1 me-2">
                                                    <span class="text-muted fw-bold d-block" style="font-size:10px;">Maksimal</span>
                                                </div>
                                                <span class="fw-bolder text-dark maksimal-kinerja" style="text-align:right;font-size:10px;">Rp 0</span>
                                            </div>
                                            <div class="d-flex align-items-center flex-row-fluid flex-wrap mb-2">
                                                <div class="flex-grow-1 me-2">
                                                    <span class="text-muted fw-bold d-block" style="font-size:10px;">Capaian</span>
                                                </div>
                                                <span class="fw-bolder text-success capaian-kinerja" style="text-align:right;font-size:10px;">Rp 0</span>
                                            </div>
                                            <div class="d-flex align-items-center flex-row-fluid flex-wrap mb-2">
                                                <div class="flex-grow-1 me-2">
                                                    <span class="text-muted fw-bold d-block" style="font-size:10px;">Total</span>
                                                </div>
                                                <span class="fw-bolder text-primary capaian-kinerja" style="text-align:right;font-size:10px;">Rp 0</span>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card card-xl-stretch box-widget mb-5">
                                <div class="d-flex align-items-center me-3">
                                    <img src="{{ asset('admin/assets/media/icons/dashboard/calculator.png') }}"
                                        height="56" width="56" alt="">
                                    <div class="flex-grow-1 group-labels">
                                        <div class="badges mb-2 title-kehadiran" style="width:110%"></div>
                                            <div class="d-flex align-items-center flex-row-fluid flex-wrap mb-2">
                                                <div class="flex-grow-1 me-2">
                                                    <span class="text-muted fw-bold d-block" style="font-size:10px;">Maksimal</span>
                                                </div>
                                                <span class="fw-bolder text-dark maksimal-kehadiran" style="text-align:right;font-size:10px;">Rp 0</span>
                                            </div>
                                            <div class="d-flex align-items-center flex-row-fluid flex-wrap mb-2">
                                                <div class="flex-grow-1 me-2">
                                                    <span class="text-muted fw-bold d-block" style="font-size:10px;">Potongan</span>
                                                </div>
                                                <span class="fw-bolder text-danger potongan-kehadiran" style="text-align:right;font-size:10px;">Rp 0</span>
                                            </div>
                                            <div class="d-flex align-items-center flex-row-fluid flex-wrap mb-2">
                                                <div class="flex-grow-1 me-2">
                                                    <span class="text-muted fw-bold d-block" style="font-size:10px;">Total</span>
                                                </div>
                                                <span class="fw-bolder text-primary total-potongan-kehadiran" style="text-align:right;font-size:10px;">Rp 0</span>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card card-xl-stretch box-widget mb-5" style="height:110px">
                                <div class="d-flex align-items-center me-3">
                                    <img src="{{ asset('admin/assets/media/icons/dashboard/kinerja.png') }}" height="56"
                                        width="56" alt="">
                                    <div class="group-labels">
                                        <div class="badges">Kinerja</div>
                                        <div class="metrics text-center">
                                            <div class="labels">
                                                <span class="fw-bolder text-dark kinerja_target">0</span>
                                                <p>Target</p>
                                            </div>
                                            <div class="labels">
                                                <span class="fw-bolder text-dark kinerja_capaian">0</span>
                                                <p>Capaian</p>
                                            </div>
                                            <div class="labels">
                                                <span class="fw-bolder text-dark kinerja_prestasi">0</span>
                                                <p>Prestasi</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-6">
                            <div class="card card-xl-stretch box-widget mb-5" style="height:110px">
                                <div class="d-flex align-items-center me-3">
                                    <img src="{{ asset('admin/assets/media/icons/dashboard/aktivitas.png') }}"
                                        height="56" width="56" alt="">
                                    <div class="flex-grow-1 group-labels">
                                        <div class="badges" style="width:110%">Aktivitas</div>
                                        <h2 class="count_aktivitas">0</h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
                @endif
                <div class="col-lg-6">
                    <div class="card card-xl-stretch box-widget mb-5">
                        <div class="d-flex justify-content-between">
                            <div class="group-data-widget">
                                <table class="text-center" id="table-widget">
                                    <tr>
                                        <td>
                                            <span class="fw-bolder text-dark kehadiran_hadir">0</span>
                                            <p>Hadir</p>
                                        </td>
                                        <td>
                                            <span class="fw-bolder text-dark kehadiran_apel">0</span>
                                            <p>Apel</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span class="fw-bolder text-dark kehadiran_tanpa_keterangan">0</span>
                                            <p>Tanpa Keterangan</p>
                                        </td>
                                        <td>
                                            <span class="fw-bolder text-dark kehadiran_dinas_luar">0</span>
                                            <p>Dinas Luar</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span class="fw-bolder text-dark kehadiran_sakit">0</span>
                                            <p>Sakit</p>
                                        </td>
                                        <td>
                                            <span class="fw-bolder text-dark kehadiran_cuti">0</span>
                                            <p>Cuti</p>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="badges-lg-success">
                                <h1 class="kehadiran_hari_kerja">0</h1>
                                <span>Hari Kerja</span>
                            </div>
                            <div class="badges-lg-danger">
                                <h1 class="kehadiran_potongan">5%</h1>
                                <span>Potongan</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="card card-xl-stretch mb-xl-8">
                        <div class="card-header border-0">
                            <h3 class="card-title fw-bolder text-dark">INFORMASI PEGAWAI</h3>
                        </div>
                        <div class="card-body pt-2">
                            <div class="d-flex align-items-center flex-row-fluid flex-wrap mb-4">
                                <div class="flex-grow-1 me-2">
                                    <span class="text-muted fw-bold d-block">Nama</span>
                                </div>
                                <span class="fw-bolder text-dark info_nama" style="width:200px;text-align:right">-</span>
                            </div>
                            <div class="d-flex align-items-center flex-row-fluid flex-wrap mb-4">
                                <div class="flex-grow-1 me-2">
                                    <span class="text-muted fw-bold d-block">NIP</span>
                                </div>
                                <span class="fw-bolder text-dark info_nip" style="width:200px;text-align:right">-</span>
                            </div>
                            <div class="d-flex align-items-center flex-row-fluid flex-wrap mb-4">
                                <div class="flex-grow-1 me-2">
                                    <span class="text-muted fw-bold d-block">Pangkat/Gol Ruang</span>
                                </div>
                                <span class="fw-bolder text-dark info_golongan"
                                    style="width:200px;text-align:right">-</span>
                            </div>
                            <div class="d-flex align-items-center flex-row-fluid flex-wrap mb-4">
                                <div class="flex-grow-1 me-2">
                                    <span class="text-muted fw-bold d-block">Jabatan</span>
                                </div>
                                <span class="fw-bolder text-dark info_nama_jabatan" style="width:200px;text-align:right">-
                                </span>
                            </div>
                            <div class="d-flex align-items-center flex-row-fluid flex-wrap mb-4">
                                <div class="flex-grow-1 me-2">
                                    <span class="text-muted fw-bold d-block">Instansi</span>
                                </div>
                                <span class="fw-bolder text-dark info_nama_satuan_kerja"
                                    style="width:200px;text-align:right">-</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card card-xl-stretch mb-xl-8">
                        <div class="card-header border-0">
                            <h3 class="card-title fw-bolder text-dark">INFORMASI ATASAN LANGSUNG</h3>
                        </div>
                        <div class="card-body pt-2">
                            <div class="d-flex align-items-center flex-row-fluid flex-wrap mb-4">
                                <div class="flex-grow-1 me-2">
                                    <span class="text-muted fw-bold d-block">Nama</span>
                                </div>
                                <span class="fw-bolder text-dark atasan_nama"
                                    style="width:200px;text-align:right">-</span>
                            </div>
                            <div class="d-flex align-items-center flex-row-fluid flex-wrap mb-4">
                                <div class="flex-grow-1 me-2">
                                    <span class="text-muted fw-bold d-block">NIP</span>
                                </div>
                                <span class="fw-bolder text-dark atasan_nip" style="width:200px;text-align:right">-</span>
                            </div>
                            <div class="d-flex align-items-center flex-row-fluid flex-wrap mb-4">
                                <div class="flex-grow-1 me-2">
                                    <span class="text-muted fw-bold d-block">Pangkat/Gol Ruang</span>
                                </div>
                                <span class="fw-bolder text-dark atasan_golongan"
                                    style="width:200px;text-align:right">-</span>
                            </div>
                            <div class="d-flex align-items-center flex-row-fluid flex-wrap mb-4">
                                <div class="flex-grow-1 me-2">
                                    <span class="text-muted fw-bold d-block">Jabatan</span>
                                </div>
                                <span class="fw-bolder text-dark atasan_nama_jabatan"
                                    style="width:200px;text-align:right">-</span>
                            </div>
                            <div class="d-flex align-items-center flex-row-fluid flex-wrap mb-4">
                                <div class="flex-grow-1 me-2">
                                    <span class="text-muted fw-bold d-block">Instansi</span>
                                </div>
                                <span class="fw-bolder text-dark atasan_nama_satuan_kerja"
                                    style="width:200px;text-align:right">-</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="card">
                    <div class="card-body p-0">

                        <div class="container">
                            <div class="py-5 table-responsive">
                                <table id="tb_pegawai_dinilai" class="table table-row-dashed table-row-gray-300 gy-7">
                                    <thead class="text-center">
                                        <tr class="fw-bolder fs-6 text-gray-800">
                                            <th>No</th>
                                            <th>NIP</th>
                                            <th>Nama</th>
                                            <th>Jabatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
        <!--end::Container-->
    </div>
@endsection
@section('script')
    <script>
        let control = new Control();

        $(document).on('change', '#filter-bulan', function() {
            control.data_dashboard_pegawai($(this).val());
        })

        $(function() {
            control.data_dashboard_pegawai($('#filter-bulan').val());
            $('#tb_pegawai_dinilai').DataTable();
        })
    </script>
@endsection
