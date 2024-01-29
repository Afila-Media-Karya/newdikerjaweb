@section('title', 'Laporan Pegawai')
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
                                        <div class="row laporan-konten">
                                            
                                            <div class="col-lg-4 mb-10">
                                                <label class="form-label">Pilih Satuan Kerja</label>
                                                <select class="form-select form-control" id="satuan_kerja" name="satuan_kerja" data-control="select2" data-placeholder="Pilih satuan kerja">
                                                    <option></option>
                                                    @foreach($satuan_kerja as $val)
                                                        <option value="{{$val->value}}">{{$val->text}}</option>
                                                    @endforeach
                                                </select>
                                                <small class="text-danger satuan_kerja_error"></small>
                                            </div>

                                            <div class="col-lg-4 mb-10">
                                                <label class="form-label">Unit Kerja</label>
                                                <select class="form-select form-control" name="id_unit_kerja" id="id_unit_kerja" data-control="select2" data-placeholder="Pilih Unit Kerja">
                                                    <option></option>
                                                </select>
                                                <small class="text-danger id_unit_kerja_error"></small>
                                            </div>

                                            <div class="col-lg-4 mb-10">
                                                <label class="form-label">Pilih Pegawai</label>
                                                <select class="form-select form-control" id="pegawai" name="pegawai" data-control="select2" data-placeholder="Pilih Pegawai">
                                                    <option></option>
                                                </select>
                                                <small class="text-danger pegawai_error"></small>
                                            </div>

                                            <input type="hidden" name="nama_satuan_kerja" id="nama_satuan_kerja">
                                            
                                            <div class="d-flex align-items-center gap-2 gap-lg-3">
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
        let control = new Control();
    @if ($errors->any())
        // Initialize SweetAlert to display errors
        Swal.fire({
            title: 'Peringatan!',
            text: '{{ $errors->first() }}', // Display the first error message
            icon: 'warning',
            confirmButtonText: 'OK'
        });
    @endif

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

    $(document).on('change','#satuan_kerja', function (e) {
            e.preventDefault();
        var selectedText = $(this).find(":selected").text();
        if ($(this).val() !== '') {
            control.push_select(`/perangkat-daerah/unit-kerja/option?satuan_kerja=${$(this).val()}`,'#id_unit_kerja'); 
            $('#nama_satuan_kerja').val(selectedText);
        }
    
    })

    $(document).on('change','#id_unit_kerja', function (e) {
            e.preventDefault();
        var selectedText = $(this).find(":selected").text();
        if ($(this).val() !== '') {
            // control.push_select(`/pegawai/list-pegawai/option?satuan_kerja=${$('#satuan_kerja').val()}&unit_kerja=${$(this).val()}`,'#pegawai');
            control.push_select(`/pegawai/list-pegawai/option-by-unit-kerja?satuan_kerja=${$('#satuan_kerja').val()}&unit_kerja=${$(this).val()}`,'#pegawai');
            $('#nama_unit_kerja').val(selectedText);
        }
    })

    validation = (parsedData) =>{

        let result = true;

        if (parsedData.pegawai === '') {
            $('.pegawai_error').html('pilih pegawai tidak boleh kosong');
            result = false;
        }else{
           $('.pegawai_error').html(''); 
        }

        if (parsedData.id_unit_kerja === '') {
            $('.id_unit_kerja_error').html('pilih unit kerja tidak boleh kosong');
            result = false;
        } else {
            $('.id_unit_kerja_error').html('');
        }

        if (parsedData.satuan_kerja === '') {
            $('.satuan_kerja_error').html('pilih satuan kerja tidak boleh kosong');
            result = false;
        }else{
           $('.satuan_kerja_error').html(''); 
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
                     url_main = '/laporan/profil/cetak-laporan-profil-pegawai';
                    window.open(`${url_main}?${params}`, "_blank");
                }

               

           
        })
    </script>
@endsection