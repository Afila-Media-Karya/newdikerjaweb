@extends('layouts.layout')
@section('title', 'Detail Layanan Cuti')
@section('content')
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <!--begin::Container-->
        <div id="kt_content_container" class="container">
            <div class="row">

                <div class="card">
                    <div class="card-body p-0">

                        <div class="container">
                            <div class="py-5">

                                <div class="row mb-10">
                                    <div class="col-lg-6">
                                        <label class="form-label">Status</label><br>
                                        @php
                                            if ($data->status == '1') {
                                                $status = 'Permohonan';
                                                $color = 'primary';
                                            } elseif ($data->status == '2') {
                                                $status = 'Perubahan';
                                                $color = 'warning';
                                            } elseif ($data->status == '3') {
                                                $status = 'Proses Atasan Langsung';
                                                $color = 'primary';
                                            } elseif ($data->status == '4') {
                                                $status = 'Proses SKPD';
                                                $color = 'primary';
                                            } elseif ($data->status == '5') {
                                                $status = 'Proses BKPSDM';
                                                $color = 'primary';
                                            } elseif ($data->status == '6') {
                                                $status = 'Proses SETDA';
                                                $color = 'primary';
                                            } elseif ($data->status == '7') {
                                                $status = 'Tidak Disetujui';
                                                $color = 'danger';
                                            } else {
                                                $status = 'Selesai';
                                                $color = 'success';
                                            }
                                        @endphp
                                        <span class="badge badge-{{ $color }}">{{ $status }}</span>
                                    </div>
                                </div>
                                <hr>
                                <div class="row mb-10">
                                    <div class="col-lg-6">
                                        <label class="form-label">NIP</label>
                                        <input type="text" class="form-control form-control-sm"
                                            value="{{ $data->nip }}" disabled>
                                    </div>

                                    <div class="col-lg-6">
                                        <label class="form-label">Nama</label>
                                        <input type="text" class="form-control form-control-sm"
                                            value="{{ $data->nama_pegawai }}" disabled>
                                    </div>
                                </div>

                                <div class="row mb-10">
                                    <div class="col-lg-6">
                                        <label class="form-label">Satuan Kerja</label>
                                        <input type="text" class="form-control form-control-sm"
                                            value="{{ $data->nama_satuan_kerja }}" disabled>
                                    </div>

                                    <div class="col-lg-6">
                                        <label class="form-label">Jenis Cuti</label>
                                        <input type="text" class="form-control form-control-sm"
                                            value="{{ $data->jenis_layanan }}" disabled>
                                    </div>
                                </div>

                                <div class="row mb-10">
                                    <div class="col-lg-6">
                                        <label class="form-label">Tanggal Mulai</label>
                                        <input type="text" class="form-control form-control-sm"
                                            value="{{ \Carbon\Carbon::parse($data->tanggal_mulai)->format('j F Y') }}"
                                            disabled>
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="form-label">Tanggal Selesai</label>
                                        <input type="text" class="form-control form-control-sm"
                                            value="{{ \Carbon\Carbon::parse($data->tanggal_akhir)->format('j F Y') }}"
                                            disabled>
                                    </div>
                                </div>

                                <div class="row mb-10">
                                    <div class="col-lg-6">
                                        <label class="form-label">Alamat Selama Cuti</label>
                                        <textarea id="alamat" class="form-control" name="alamat" rows="5" disabled>{{ $data->alamat }}</textarea>
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="form-label">Alasan</label>
                                        <textarea id="alamat" class="form-control" name="alamat" rows="5" disabled>{{ $data->alasan }}</textarea>
                                    </div>
                                </div>

                                <div class="row mb-10">
                                    <div class="col-lg-6">
                                        <label class="form-label">Keterangan</label>
                                        <textarea id="alamat" class="form-control" name="keterangan" rows="5" disabled>{{ $data->keterangan }}</textarea>
                                    </div>
                                </div>


                                <a href="/layanan-pegawai/layanan-cuti" type="button"
                                    class="btn btn-primary button-update btn-sm">
                                    Kembali
                                </a>

                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
        <!--end::Container-->
    </div>
@endsection
