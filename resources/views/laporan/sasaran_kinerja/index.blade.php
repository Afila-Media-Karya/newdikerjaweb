@php
    $role = hasRole();
@endphp
@section('title', 'Laporan Sasaran Kinerja')
@extends('layouts.layout')
@section('content')

<div class="post d-flex flex-column-fluid" id="kt_post">
        <!--begin::Container-->
        <div id="kt_content_container" class="container">
            <div class="row">

                <div class="card">
                    <div class="card-body p-0">

                        <div class="container">
                            <div class="py-10">
                                    <form id="laporan-form">
                                        <div class="laporan-konten">
                                            <div class="mb-5">
                                                <label class="form-label">Pilih Sasaran Kinerja</label>
                                                <select id="sasaran_kinerja" name="sasaran_kinerja" class="form-control">
                                                    <option selected disabled>Pilih Sasaran Kinerja</option>
                                                    <option value="Target Kinerja">Target Kinerja</option>
                                                    <option value="Realisasi Kinerja">Realisasi Kinerja</option>
                                                </select>
                                                <small class="text-danger sasaran_kinerja_error"></small>
                                            </div>

                                            <div class="d-flex align-items-center gap-2 gap-lg-3">
                                                <a href="#" id="export-excel" data-type="excel" class="btn btn-sm btn-success">
                                                    <img src="{{asset('admin/assets/media/icons/excel.svg')}}" style="position: relative; bottom: 1px;" alt="" srcset="">
                                                    Export Excel
                                                </a>
                                                <a href="#" id="export-pdf" data-type="pdf" class="btn btn-sm btn-danger">
                                                    <img src="{{asset('admin/assets/media/icons/pdf.svg')}}" style="position: relative; bottom: 1px;" alt="" srcset="">
                                                    Cetak Laporan
                                                </a>
                                            </div>
                                        </div>
                                    </form>
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
        let role = {!! json_encode($role) !!};

        parseSerializedData = (serializedData) => {
            const decodedData = decodeURIComponent(serializedData); // Decode the URI-encoded string
            const dataPairs = decodedData.split('&'); // Split into key-value pairs
            const result = {};

            for (const pair of dataPairs) {
                const [key, value] = pair.split('='); // Split each pair into key and value
                result[key] = value; // Add to the result object
            }

            return result;
        }

        validation = (parsedData) =>{

            let result = true;

            if (parsedData.sasaran_kinerja === undefined) {
            $('.sasaran_kinerja_error').html('pilih sasaran kinerja tidak boleh kosong'); 
            result = false;
            }else{
                $('.sasaran_kinerja_error').html('');
                result = true; 
            }

            return result;

        }

        $('#export-excel,#export-pdf,#export-backup').click(function(e){
            e.preventDefault();
            let type = $(this).attr('data-type');
            let params = $('#laporan-form').serialize();
            let url_main = '';
            const parsedData = parseSerializedData(params);
            
            if (validation(parsedData) === true) {
                // if (role.guard == 'administrator') {
                //     url_main = '/laporan/sasaran-kinerja/export-admin';
                // }else{
                //     if (role.role == '2') {
                        
                //     }else{
                //         url_main = '/laporan-opd/sasaran-kinerja/export-opd';
                //     }
                // }
                url_main = '/laporan-pegawai/sasaran-kinerja/export-pegawai';
                window.open(`${url_main}?${params}&type=${type}`, "_blank");
            }

        })
    </script>
@endsection