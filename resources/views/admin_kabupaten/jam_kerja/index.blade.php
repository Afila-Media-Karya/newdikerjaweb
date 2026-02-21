@extends('layouts.layout')
@section('title', 'Pengaturan Web Absensi')
@section('button')
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
        <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
            data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
            class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
            <h3 class="fw-bold m-0">Pengaturan Web Absensi</h3>
        </div>
    </div>
@endsection
@section('content')
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container">
            <form id="form-settings" method="POST">
                @csrf

                {{-- ============================================== --}}
                {{-- NON-SHIFT JAM KERJA (Unified Dropdown) --}}
                {{-- ============================================== --}}
                @if($nonShiftTipeList->count() > 0)
                    <div class="card mb-6">
                        <div class="card-header">
                            <h3 class="card-title fw-bold">Pengaturan Waktu Absensi</h3>
                        </div>
                        <div class="card-body">
                            {{-- Dropdown filters --}}
                            <div class="row mb-5">
                                <div class="col-md-4">
                                    <label class="form-label fs-7">Pilih Tipe Pegawai</label>
                                    <select class="form-select form-select-sm" id="select-jk-tipe">
                                        @foreach($nonShiftTipeList as $tipe)
                                            <option value="{{ $tipe }}">
                                                {{ $tipeLabels[$tipe] ?? ucwords(str_replace('_', ' ', $tipe)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fs-7">Pilih Hari</label>
                                    <select class="form-select form-select-sm" id="select-jk-hari">
                                        {{-- Options populated by JS based on tipe pegawai --}}
                                    </select>
                                </div>
                            </div>

                            {{-- Form fields for each tipe+hari combination --}}
                            @foreach($jamKerjaAll as $record)
                                <div class="jk-fields" data-tipe="{{ $record->tipe_pegawai }}" data-hari="{{ $record->hari }}"
                                    style="display:none;">
                                    <div class="row">
                                        <div class="col-md-4 mb-5">
                                            <label class="form-label fs-7">Batas awal waktu absen datang</label>
                                            <input type="time" class="form-control form-control-sm"
                                                name="jam_kerja[{{ $record->id }}][batas_awal_masuk]"
                                                value="{{ substr($record->batas_awal_masuk, 0, 5) }}">
                                        </div>
                                        <div class="col-md-4 mb-5">
                                            <label class="form-label fs-7">Batas akhir waktu absen datang</label>
                                            <input type="time" class="form-control form-control-sm"
                                                name="jam_kerja[{{ $record->id }}][batas_akhir_masuk]"
                                                value="{{ substr($record->batas_akhir_masuk, 0, 5) }}">
                                        </div>
                                        <div class="col-md-4 mb-5">
                                            <label class="form-label fs-7">Batas akhir tepat waktu absen datang</label>
                                            <input type="time" class="form-control form-control-sm"
                                                name="jam_kerja[{{ $record->id }}][jam_masuk]"
                                                value="{{ substr($record->jam_masuk, 0, 5) }}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 mb-5">
                                            <label class="form-label fs-7">Batas awal waktu absen pulang</label>
                                            <input type="time" class="form-control form-control-sm"
                                                name="jam_kerja[{{ $record->id }}][batas_awal_pulang]"
                                                value="{{ substr($record->batas_awal_pulang, 0, 5) }}">
                                        </div>
                                        <div class="col-md-4 mb-5">
                                            <label class="form-label fs-7">Batas akhir waktu absen pulang</label>
                                            <input type="time" class="form-control form-control-sm"
                                                name="jam_kerja[{{ $record->id }}][batas_akhir_pulang]"
                                                value="{{ substr($record->batas_akhir_pulang, 0, 5) }}">
                                        </div>
                                        <div class="col-md-4 mb-5">
                                            <label class="form-label fs-7">Batas awal absen tepat waktu pulang</label>
                                            <input type="time" class="form-control form-control-sm"
                                                name="jam_kerja[{{ $record->id }}][jam_keluar]"
                                                value="{{ substr($record->jam_keluar, 0, 5) }}">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- ============================================== --}}
                {{-- SHIFT-BASED JAM KERJA (Unified Dropdown) --}}
                {{-- ============================================== --}}
                @if($shiftTipeList->count() > 0)
                    @php
                        $shiftGroups = $jamKerjaShift->groupBy(function ($item) {
                            return $item->shift . '_' . $item->jumlah_shift;
                        })->keys()->unique();
                    @endphp
                    <div class="card mb-6">
                        <div class="card-header">
                            <h3 class="card-title fw-bold">Pengaturan Waktu Absensi (Shift)</h3>
                        </div>
                        <div class="card-body">
                            {{-- Dropdown filters --}}
                            <div class="row mb-5">
                                <div class="col-md-3">
                                    <label class="form-label fs-7">Pilih Tipe Pegawai</label>
                                    <select class="form-select form-select-sm" id="select-jk-shift-tipe">
                                        @foreach($shiftTipeList as $tipe)
                                            <option value="{{ $tipe }}">
                                                {{ $tipeLabels[$tipe] ?? ucwords(str_replace('_', ' ', $tipe)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fs-7">Pilih Shift</label>
                                    <select class="form-select form-select-sm" id="select-jk-shift-shift">
                                        {{-- Options populated by JS --}}
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fs-7">Pilih Hari</label>
                                    <select class="form-select form-select-sm" id="select-jk-shift-hari">
                                        {{-- Options populated by JS --}}
                                    </select>
                                </div>
                            </div>

                            {{-- Form fields for each tipe+shift+hari combination --}}
                            @foreach($jamKerjaShift as $record)
                                @php $shiftKey = $record->shift . '_' . $record->jumlah_shift; @endphp
                                <div class="jk-shift-fields" data-tipe="{{ $record->tipe_pegawai }}"
                                    data-shift-key="{{ $shiftKey }}" data-hari="{{ $record->hari }}" style="display:none;">

                                    <h5 class="fw-bold text-primary mb-4">Absen Datang - Shift {{ ucfirst($record->shift) }}
                                        ({{ $record->jumlah_shift }} Shift)</h5>
                                    <div class="row">
                                        <div class="col-md-4 mb-5">
                                            <label class="form-label fs-7">Batas awal waktu absen datang</label>
                                            <input type="time" class="form-control form-control-sm"
                                                name="jam_kerja[{{ $record->id }}][batas_awal_masuk]"
                                                value="{{ substr($record->batas_awal_masuk, 0, 5) }}">
                                        </div>
                                        <div class="col-md-4 mb-5">
                                            <label class="form-label fs-7">Batas akhir waktu absen datang</label>
                                            <input type="time" class="form-control form-control-sm"
                                                name="jam_kerja[{{ $record->id }}][batas_akhir_masuk]"
                                                value="{{ substr($record->batas_akhir_masuk, 0, 5) }}">
                                        </div>
                                        <div class="col-md-4 mb-5">
                                            <label class="form-label fs-7">Batas akhir tepat waktu absen datang</label>
                                            <input type="time" class="form-control form-control-sm"
                                                name="jam_kerja[{{ $record->id }}][jam_masuk]"
                                                value="{{ substr($record->jam_masuk, 0, 5) }}">
                                        </div>
                                    </div>

                                    <h5 class="fw-bold text-primary mt-3 mb-4">Absen Pulang - Shift {{ ucfirst($record->shift) }}
                                        ({{ $record->jumlah_shift }} Shift)</h5>
                                    <div class="row">
                                        <div class="col-md-4 mb-5">
                                            <label class="form-label fs-7">Batas awal waktu absen pulang</label>
                                            <input type="time" class="form-control form-control-sm"
                                                name="jam_kerja[{{ $record->id }}][batas_awal_pulang]"
                                                value="{{ substr($record->batas_awal_pulang, 0, 5) }}">
                                        </div>
                                        <div class="col-md-4 mb-5">
                                            <label class="form-label fs-7">Batas akhir waktu absen pulang</label>
                                            <input type="time" class="form-control form-control-sm"
                                                name="jam_kerja[{{ $record->id }}][batas_akhir_pulang]"
                                                value="{{ substr($record->batas_akhir_pulang, 0, 5) }}">
                                        </div>
                                        <div class="col-md-4 mb-5">
                                            <label class="form-label fs-7">Batas awal absen tepat waktu pulang</label>
                                            <input type="time" class="form-control form-control-sm"
                                                name="jam_kerja[{{ $record->id }}][jam_keluar]"
                                                value="{{ substr($record->jam_keluar, 0, 5) }}">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- ============================================== --}}
                {{-- APEL SETTINGS (unchanged) --}}
                {{-- ============================================== --}}
                @php
                    $apelReguler = $apelSettings->where('jenis', 'reguler');
                    $apelHariBesar = $apelSettings->where('jenis', 'hari_besar');
                @endphp

                @if($apelReguler->count() > 0)
                    <div class="card mb-6">
                        <div class="card-header">
                            <h3 class="card-title fw-bold">Pengaturan Absensi Apel Pagi</h3>
                        </div>
                        <div class="card-body">
                            <div class="row mb-5">
                                <div class="col-md-4">
                                    <label class="form-label fs-7">Pilih Tipe Pegawai</label>
                                    <select class="form-select form-select-sm" id="select-apel-reguler">
                                        @foreach($apelReguler as $apel)
                                            <option value="{{ $apel->id }}">
                                                {{ $tipeLabels[$apel->tipe_pegawai] ?? ucwords(str_replace('_', ' ', $apel->tipe_pegawai)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @foreach($apelReguler as $apel)
                                <div class="row apel-reguler-fields" data-apel-id="{{ $apel->id }}"
                                    style="{{ $loop->first ? '' : 'display:none;' }}">
                                    <div class="col-md-6 mb-5">
                                        <label class="form-label fs-7">Batas awal absen apel</label>
                                        <input type="time" class="form-control form-control-sm"
                                            name="jam_apel[{{ $apel->id }}][batas_awal]"
                                            value="{{ substr($apel->batas_awal, 0, 5) }}">
                                    </div>
                                    <div class="col-md-6 mb-5">
                                        <label class="form-label fs-7">Batas akhir absen apel</label>
                                        <input type="time" class="form-control form-control-sm"
                                            name="jam_apel[{{ $apel->id }}][batas_akhir]"
                                            value="{{ substr($apel->batas_akhir, 0, 5) }}">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($apelHariBesar->count() > 0)
                    <div class="card mb-6">
                        <div class="card-header">
                            <h3 class="card-title fw-bold">Pengaturan Absensi Apel Hari Besar</h3>
                        </div>
                        <div class="card-body">
                            <div class="row mb-5">
                                <div class="col-md-4">
                                    <label class="form-label fs-7">Pilih Tipe Pegawai</label>
                                    <select class="form-select form-select-sm" id="select-apel-hb">
                                        @foreach($apelHariBesar as $apel)
                                            <option value="{{ $apel->id }}">
                                                {{ $tipeLabels[$apel->tipe_pegawai] ?? ucwords(str_replace('_', ' ', $apel->tipe_pegawai)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @foreach($apelHariBesar as $apel)
                                <div class="row apel-hb-fields" data-apel-id="{{ $apel->id }}"
                                    style="{{ $loop->first ? '' : 'display:none;' }}">
                                    <div class="col-md-6 mb-5">
                                        <label class="form-label fs-7">Batas awal absen apel hari besar</label>
                                        <input type="time" class="form-control form-control-sm"
                                            name="jam_apel[{{ $apel->id }}][batas_awal]"
                                            value="{{ substr($apel->batas_awal, 0, 5) }}">
                                    </div>
                                    <div class="col-md-6 mb-5">
                                        <label class="form-label fs-7">Batas akhir absen apel hari besar</label>
                                        <input type="time" class="form-control form-control-sm"
                                            name="jam_apel[{{ $apel->id }}][batas_akhir]"
                                            value="{{ substr($apel->batas_akhir, 0, 5) }}">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- SAVE BUTTON --}}
                <div class="d-flex justify-content-start mb-10">
                    <button type="submit" class="btn btn-primary btn-sm px-6" id="btn-save">
                        <i class="bi bi-check-circle me-2"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function () {
            // ============================================
            // Data: hari kerja per tipe pegawai
            // ============================================
            var hariKerjaPerTipe = @json($hariKerjaPerTipe);
            var namaHari = @json($namaHari);

            // ============================================
            // NON-SHIFT: Tipe Pegawai + Hari dropdowns
            // ============================================
            function updateJkHariDropdown() {
                var tipe = $('#select-jk-tipe').val();
                var hariList = hariKerjaPerTipe[tipe] || [];
                var $hariSelect = $('#select-jk-hari');
                $hariSelect.empty();
                $.each(hariList, function (i, hariNum) {
                    $hariSelect.append('<option value="' + hariNum + '">' + (namaHari[hariNum] || 'Hari ' + hariNum) + '</option>');
                });
                updateJkFields();
            }

            function updateJkFields() {
                var tipe = $('#select-jk-tipe').val();
                var hari = $('#select-jk-hari').val();

                // Hide all and disable inputs
                $('.jk-fields').hide().find('input').prop('disabled', true);

                // Show matching fields
                $('.jk-fields[data-tipe="' + tipe + '"][data-hari="' + hari + '"]')
                    .show().find('input').prop('disabled', false);
            }

            $('#select-jk-tipe').on('change', updateJkHariDropdown);
            $('#select-jk-hari').on('change', updateJkFields);

            // Initialize on page load
            if ($('#select-jk-tipe').length) {
                updateJkHariDropdown();
            }

            // ============================================
            // SHIFT: Tipe + Shift + Hari dropdowns
            // ============================================
            @if($shiftTipeList->count() > 0)
                // Build shift data from rendered elements
                var shiftData = {};
                $('.jk-shift-fields').each(function () {
                    var tipe = $(this).data('tipe');
                    var shiftKey = $(this).data('shift-key');
                    var hari = $(this).data('hari');
                    if (!shiftData[tipe]) shiftData[tipe] = {};
                    if (!shiftData[tipe][shiftKey]) shiftData[tipe][shiftKey] = [];
                    if (shiftData[tipe][shiftKey].indexOf(hari) === -1) {
                        shiftData[tipe][shiftKey].push(hari);
                    }
                });

                function updateShiftDropdown() {
                    var tipe = $('#select-jk-shift-tipe').val();
                    var shifts = shiftData[tipe] || {};
                    var $shiftSelect = $('#select-jk-shift-shift');
                    $shiftSelect.empty();
                    $.each(shifts, function (shiftKey) {
                        var parts = shiftKey.split('_');
                        var label = parts[0].charAt(0).toUpperCase() + parts[0].slice(1) + ' (' + parts[1] + ' Shift)';
                        $shiftSelect.append('<option value="' + shiftKey + '">' + label + '</option>');
                    });
                    updateShiftHariDropdown();
                }

                function updateShiftHariDropdown() {
                    var tipe = $('#select-jk-shift-tipe').val();
                    var shiftKey = $('#select-jk-shift-shift').val();
                    var hariList = (shiftData[tipe] && shiftData[tipe][shiftKey]) ? shiftData[tipe][shiftKey] : [];
                    var $hariSelect = $('#select-jk-shift-hari');
                    $hariSelect.empty();
                    $.each(hariList, function (i, hariNum) {
                        $hariSelect.append('<option value="' + hariNum + '">' + (namaHari[hariNum] || 'Hari ' + hariNum) + '</option>');
                    });
                    updateShiftFields();
                }

                function updateShiftFields() {
                    var tipe = $('#select-jk-shift-tipe').val();
                    var shiftKey = $('#select-jk-shift-shift').val();
                    var hari = $('#select-jk-shift-hari').val();

                    // Hide all shift fields and disable inputs
                    $('.jk-shift-fields').hide().find('input').prop('disabled', true);

                    // Show matching fields
                    $('.jk-shift-fields[data-tipe="' + tipe + '"][data-shift-key="' + shiftKey + '"][data-hari="' + hari + '"]')
                        .show().find('input').prop('disabled', false);
                }

                $('#select-jk-shift-tipe').on('change', updateShiftDropdown);
                $('#select-jk-shift-shift').on('change', updateShiftHariDropdown);
                $('#select-jk-shift-hari').on('change', updateShiftFields);

                // Initialize
                updateShiftDropdown();
            @endif

            // ============================================
            // APEL: Toggle fields by dropdown (unchanged)
            // ============================================
            @if($apelReguler->count() > 0)
                $('#select-apel-reguler').on('change', function () {
                    let selectedId = $(this).val();
                    $('.apel-reguler-fields').hide().find('input').prop('disabled', true);
                    $('.apel-reguler-fields[data-apel-id="' + selectedId + '"]').show().find('input').prop('disabled', false);
                });
                $('.apel-reguler-fields:hidden').find('input').prop('disabled', true);
            @endif

            @if($apelHariBesar->count() > 0)
                $('#select-apel-hb').on('change', function () {
                    let selectedId = $(this).val();
                    $('.apel-hb-fields').hide().find('input').prop('disabled', true);
                    $('.apel-hb-fields[data-apel-id="' + selectedId + '"]').show().find('input').prop('disabled', false);
                });
                $('.apel-hb-fields:hidden').find('input').prop('disabled', true);
            @endif

            // ============================================
            // SUBMIT: AJAX save (unchanged)
            // ============================================
            $('#form-settings').on('submit', function (e) {
                e.preventDefault();
                let formData = $(this).serialize();

                $('#btn-save').prop('disabled', true).html('<i class="spinner-border spinner-border-sm me-2"></i>Menyimpan...');

                $.ajax({
                    url: "{{ route('kabupaten.jamkerja.save') }}",
                    type: 'POST',
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Pengaturan berhasil disimpan',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message || 'Terjadi kesalahan'
                            });
                        }
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan server'
                        });
                    },
                    complete: function () {
                        $('#btn-save').prop('disabled', false).html('<i class="bi bi-check-circle me-2"></i>Simpan Perubahan');
                    }
                });
            });
        });
    </script>
@endsection