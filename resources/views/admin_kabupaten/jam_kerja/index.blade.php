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

                @foreach($settings as $tipe => $group)
                    @if(!$group['is_shift'])
                        {{-- NON-SHIFT employee types (administratif, pendidik, etc.) --}}
                        <div class="card mb-6">
                            <div class="card-header">
                                <h3 class="card-title fw-bold">Pengaturan Absensi {{ $group['label'] }}</h3>
                            </div>
                            <div class="card-body">
                                @php
                                    // Get representative record (first day)
                                    $firstRecord = $group['data']->first();
                                @endphp
                                @if($firstRecord)
                                    <div class="row">
                                        <div class="col-md-4 mb-5">
                                            <label class="form-label fs-7">Batas awal waktu absen datang</label>
                                            <input type="time" class="form-control form-control-sm"
                                                name="jam_kerja[{{ $firstRecord->id }}][batas_awal_masuk]"
                                                value="{{ substr($firstRecord->batas_awal_masuk, 0, 5) }}">
                                        </div>
                                        <div class="col-md-4 mb-5">
                                            <label class="form-label fs-7">Batas akhir waktu absen datang</label>
                                            <input type="time" class="form-control form-control-sm"
                                                name="jam_kerja[{{ $firstRecord->id }}][batas_akhir_masuk]"
                                                value="{{ substr($firstRecord->batas_akhir_masuk, 0, 5) }}">
                                        </div>
                                        <div class="col-md-4 mb-5">
                                            <label class="form-label fs-7">Batas akhir tepat waktu absen datang</label>
                                            <input type="time" class="form-control form-control-sm"
                                                name="jam_kerja[{{ $firstRecord->id }}][jam_masuk]"
                                                value="{{ substr($firstRecord->jam_masuk, 0, 5) }}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 mb-5">
                                            <label class="form-label fs-7">Batas awal waktu absen pulang</label>
                                            <input type="time" class="form-control form-control-sm"
                                                name="jam_kerja[{{ $firstRecord->id }}][batas_awal_pulang]"
                                                value="{{ substr($firstRecord->batas_awal_pulang, 0, 5) }}">
                                        </div>
                                        <div class="col-md-4 mb-5">
                                            <label class="form-label fs-7">Batas akhir waktu absen pulang</label>
                                            <input type="time" class="form-control form-control-sm"
                                                name="jam_kerja[{{ $firstRecord->id }}][batas_akhir_pulang]"
                                                value="{{ substr($firstRecord->batas_akhir_pulang, 0, 5) }}">
                                        </div>
                                        <div class="col-md-4 mb-5">
                                            <label class="form-label fs-7">Batas awal absen tepat waktu pulang</label>
                                            <input type="time" class="form-control form-control-sm"
                                                name="jam_kerja[{{ $firstRecord->id }}][jam_keluar]"
                                                value="{{ substr($firstRecord->jam_keluar, 0, 5) }}">
                                        </div>
                                    </div>

                                    {{-- Also update all other records for same tipe (hidden inputs to bulk update) --}}
                                    @foreach($group['data']->skip(1) as $record)
                                        <input type="hidden" name="jam_kerja[{{ $record->id }}][batas_awal_masuk]"
                                            value="{{ substr($record->batas_awal_masuk, 0, 5) }}" class="sync-batas-awal-masuk-{{ $tipe }}">
                                        <input type="hidden" name="jam_kerja[{{ $record->id }}][batas_akhir_masuk]"
                                            value="{{ substr($record->batas_akhir_masuk, 0, 5) }}"
                                            class="sync-batas-akhir-masuk-{{ $tipe }}">
                                        <input type="hidden" name="jam_kerja[{{ $record->id }}][batas_awal_pulang]"
                                            value="{{ substr($record->batas_awal_pulang, 0, 5) }}"
                                            class="sync-batas-awal-pulang-{{ $tipe }}">
                                        <input type="hidden" name="jam_kerja[{{ $record->id }}][batas_akhir_pulang]"
                                            value="{{ substr($record->batas_akhir_pulang, 0, 5) }}"
                                            class="sync-batas-akhir-pulang-{{ $tipe }}">
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    @else
                        {{-- SHIFT employee types (tenaga_kesehatan) --}}
                        <div class="card mb-6">
                            <div class="card-header">
                                <h3 class="card-title fw-bold">Pengaturan Absensi {{ $group['label'] }}</h3>
                            </div>
                            <div class="card-body">
                                @php
                                    $shifts = $group['data']->groupBy(function ($item) {
                                        return $item->shift . '_' . $item->jumlah_shift;
                                    });
                                @endphp

                                @foreach($shifts as $shiftKey => $shiftRecords)
                                    @php
                                        $firstShiftRecord = $shiftRecords->first();
                                        $shiftLabel = ucfirst($firstShiftRecord->shift) . ' (' . $firstShiftRecord->jumlah_shift . ' Shift)';
                                    @endphp

                                    <h5 class="fw-bold text-primary mt-3 mb-4">Absen Datang - Shift {{ $shiftLabel }}</h5>
                                    <div class="row">
                                        <div class="col-md-4 mb-5">
                                            <label class="form-label fs-7">Batas awal waktu absen datang Shift
                                                ({{ ucfirst($firstShiftRecord->shift) }})</label>
                                            <input type="time" class="form-control form-control-sm"
                                                name="jam_kerja[{{ $firstShiftRecord->id }}][batas_awal_masuk]"
                                                value="{{ substr($firstShiftRecord->batas_awal_masuk, 0, 5) }}">
                                        </div>
                                        <div class="col-md-4 mb-5">
                                            <label class="form-label fs-7">Batas akhir waktu absen datang Shift
                                                ({{ ucfirst($firstShiftRecord->shift) }})</label>
                                            <input type="time" class="form-control form-control-sm"
                                                name="jam_kerja[{{ $firstShiftRecord->id }}][batas_akhir_masuk]"
                                                value="{{ substr($firstShiftRecord->batas_akhir_masuk, 0, 5) }}">
                                        </div>
                                        <div class="col-md-4 mb-5">
                                            <label class="form-label fs-7">Batas akhir absen tepat waktu datang Shift
                                                ({{ ucfirst($firstShiftRecord->shift) }})</label>
                                            <input type="time" class="form-control form-control-sm"
                                                name="jam_kerja[{{ $firstShiftRecord->id }}][jam_masuk]"
                                                value="{{ substr($firstShiftRecord->jam_masuk, 0, 5) }}">
                                        </div>
                                    </div>

                                    <h5 class="fw-bold text-primary mt-3 mb-4">Absen Pulang - Shift {{ $shiftLabel }}</h5>
                                    <div class="row">
                                        <div class="col-md-4 mb-5">
                                            <label class="form-label fs-7">Batas awal waktu absen pulang Shift
                                                ({{ ucfirst($firstShiftRecord->shift) }})</label>
                                            <input type="time" class="form-control form-control-sm"
                                                name="jam_kerja[{{ $firstShiftRecord->id }}][batas_awal_pulang]"
                                                value="{{ substr($firstShiftRecord->batas_awal_pulang, 0, 5) }}">
                                        </div>
                                        <div class="col-md-4 mb-5">
                                            <label class="form-label fs-7">Batas akhir waktu absen pulang Shift
                                                ({{ ucfirst($firstShiftRecord->shift) }})</label>
                                            <input type="time" class="form-control form-control-sm"
                                                name="jam_kerja[{{ $firstShiftRecord->id }}][batas_akhir_pulang]"
                                                value="{{ substr($firstShiftRecord->batas_akhir_pulang, 0, 5) }}">
                                        </div>
                                        <div class="col-md-4 mb-5">
                                            <label class="form-label fs-7">Batas awal absen tepat waktu pulang Shift
                                                ({{ ucfirst($firstShiftRecord->shift) }})</label>
                                            <input type="time" class="form-control form-control-sm"
                                                name="jam_kerja[{{ $firstShiftRecord->id }}][jam_keluar]"
                                                value="{{ substr($firstShiftRecord->jam_keluar, 0, 5) }}">
                                        </div>
                                    </div>

                                    {{-- Hidden inputs for other days in same shift --}}
                                    @foreach($shiftRecords->skip(1) as $record)
                                        <input type="hidden" name="jam_kerja[{{ $record->id }}][batas_awal_masuk]"
                                            value="{{ substr($record->batas_awal_masuk, 0, 5) }}" class="sync-shift-{{ $shiftKey }}">
                                        <input type="hidden" name="jam_kerja[{{ $record->id }}][batas_akhir_masuk]"
                                            value="{{ substr($record->batas_akhir_masuk, 0, 5) }}">
                                        <input type="hidden" name="jam_kerja[{{ $record->id }}][batas_awal_pulang]"
                                            value="{{ substr($record->batas_awal_pulang, 0, 5) }}">
                                        <input type="hidden" name="jam_kerja[{{ $record->id }}][batas_akhir_pulang]"
                                            value="{{ substr($record->batas_akhir_pulang, 0, 5) }}">
                                    @endforeach

                                    <div class="separator separator-dashed my-5"></div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach

                {{-- APEL SETTINGS --}}
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
            // Sync visible inputs to hidden inputs for same tipe_pegawai
            @foreach($settings as $tipe => $group)
                @if(!$group['is_shift'])
                    @php $firstRecord = $group['data']->first(); @endphp
                    @if($firstRecord)
                        // Sync batas fields for {{ $tipe }}
                        $('input[name="jam_kerja[{{ $firstRecord->id }}][batas_awal_masuk]"]').on('change', function () {
                            $('.sync-batas-awal-masuk-{{ $tipe }}').val($(this).val());
                        });
                        $('input[name="jam_kerja[{{ $firstRecord->id }}][batas_akhir_masuk]"]').on('change', function () {
                            $('.sync-batas-akhir-masuk-{{ $tipe }}').val($(this).val());
                        });
                        $('input[name="jam_kerja[{{ $firstRecord->id }}][batas_awal_pulang]"]').on('change', function () {
                            $('.sync-batas-awal-pulang-{{ $tipe }}').val($(this).val());
                        });
                        $('input[name="jam_kerja[{{ $firstRecord->id }}][batas_akhir_pulang]"]').on('change', function () {
                            $('.sync-batas-akhir-pulang-{{ $tipe }}').val($(this).val());
                        });
                    @endif
                @endif
            @endforeach

            // Toggle apel reguler fields by dropdown
            @if($apelReguler->count() > 0)
                $('#select-apel-reguler').on('change', function () {
                    let selectedId = $(this).val();
                    $('.apel-reguler-fields').hide().find('input').prop('disabled', true);
                    $('.apel-reguler-fields[data-apel-id="' + selectedId + '"]').show().find('input').prop('disabled', false);
                });
                $('.apel-reguler-fields:hidden').find('input').prop('disabled', true);
            @endif

            // Toggle apel hari besar fields by dropdown
            @if($apelHariBesar->count() > 0)
                $('#select-apel-hb').on('change', function () {
                    let selectedId = $(this).val();
                    $('.apel-hb-fields').hide().find('input').prop('disabled', true);
                    $('.apel-hb-fields[data-apel-id="' + selectedId + '"]').show().find('input').prop('disabled', false);
                });
                $('.apel-hb-fields:hidden').find('input').prop('disabled', true);
            @endif

            // Submit form via AJAX
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