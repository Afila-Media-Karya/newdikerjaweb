@section('title', 'Laporan Kehadiran')
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
                                            <input type="hidden" name="nama_unit_kerja" id="nama_unit_kerja">
                                 

                                            <div class="col-lg-4 mb-10">                    
                                                <label class="form-label">Pilih Bulan</label>
                                                <select id="bulan" name="bulan" class="form-control">
                                                @foreach (range(1, 12) as $bulan)
                                                    <option value="{{ $bulan }}" {{ $bulan == date('n') ? 'selected' : '' }}>
                                                        {{ \Carbon\Carbon::parse('2023-' . $bulan . '-01')->translatedFormat('F') }}
                                                    </option>
                                                @endforeach
                                                </select>
                                                <small class="text-danger bulan_error"></small>
                                            </div>

                                            <div class="col-lg-4 mb-10 status_kepegawai_form">                    
                                                <label class="form-label">Status Kepegawaian</label>
                                                <select id="status_kepegawaian" name="status_kepegawaian" class="form-control">
                                                <option selected disabled>Pilih</option>
                                                <option value="PNS">PNS</option>
                                                <option value="PPPK">PPPK</option>
                                                </select>
                                                <small class="text-danger bulan_error"></small>
                                            </div>

                                            <div class="col-lg-4 mb-10 tipe_kepegawai_form">                    
                                                <label class="form-label">Tipe Pegawai</label>
                                                <select id="tipe_pegawai" name="tipe_pegawai" class="form-control">

                                                </select>
                                                <small class="text-danger bulan_error"></small>
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

    validation = (parsedData) =>{

        let result = true;

        if (parsedData.satuan_kerja === '') {
            $('.satuan_kerja_error').html('pilih satuan kerja tidak boleh kosong');
            result = false;
        }else{
        $('.satuan_kerja_error').html(''); 
        }

        if (parsedData.id_unit_kerja === '') {
            $('.id_unit_kerja_error').html('pilih unit kerja tidak boleh kosong');
            result = false;
        } else {
            $('.id_unit_kerja_error').html('');
        }

        if (parsedData.bulan === undefined) {
        $('.bulan_error').html('pilih bulan tidak boleh kosong'); 
            result = false;
        }else{
            $('.bulan_error').html('');
        }

        if (parsedData.pegawai === '') {
            $('.pegawai_error').html('pilih pegawai tidak boleh kosong');
            result = false;
        }else{
        $('.pegawai_error').html(''); 
        }
        return result;

    }

        $(document).on('change','#satuan_kerja', function (e) {
             e.preventDefault();
            var selectedText = $(this).find(":selected").text();
            if ($(this).val() !== '') {
                control.push_select_laporan(`/perangkat-daerah/unit-kerja/option?satuan_kerja=${$(this).val()}`,'#id_unit_kerja'); 
            //    control.push_select_laporan(`/pegawai/list-pegawai/option/${$(this).val()}`,'#pegawai');
               $('#nama_satuan_kerja').val(selectedText);

               if (selectedText == 'Dinas Pendidikan dan Kebudayaan' || selectedText == 'Dinas Kesehatan') {
                    $('.tipe_kepegawai_form').show();
                    optionTipepegawai(selectedText);
               }else{
                    $('.tipe_kepegawai_form').hide();
               }
            }
        
        })

        $(document).on('change','#id_unit_kerja', function (e) {
             e.preventDefault();
            var selectedText = $(this).find(":selected").text();
            if ($(this).val() !== '') {
               control.push_select_laporan(`/pegawai/list-pegawai/option-by-unit-kerja?satuan_kerja=${$('#satuan_kerja').val()}&unit_kerja=${$(this).val()}`,'#pegawai');
               $('#nama_unit_kerja').val(selectedText);
            }
        })

        $(document).on('change','#pegawai', function (e) {
            e.preventDefault();
            let val = $(this).val();
            var selectedText = $('#satuan_kerja').find(":selected").text();

            if (val !== 'all') {
                $('.status_kepegawai_form').hide();   
                $('.tipe_kepegawai_form').hide();   
            }else{
                $('.status_kepegawai_form').show();
                if (selectedText == 'Dinas Pendidikan dan Kebudayaan' || selectedText == 'Dinas Kesehatan') {
                    optionTipepegawai(selectedText);
                    $('.tipe_kepegawai_form').show();
                }else{
                    $('.tipe_kepegawai_form').hide();   
                }
            }
        })

        optionTipepegawai = (params) => {
            var options = [];

            if (params == 'Dinas Pendidikan dan Kebudayaan') {
                options = [
                    { value: 'pegawai_administratif', text: 'Pegawai Administratif' },
                    { value: 'tenaga_pendidik', text: 'Tenaga Pendidik' },
                    { value: 'tenaga_pendidik_non_guru', text: 'Tenaga Pendidik non Guru' }
                ]
            }else{
                options = [
                    { value: 'pegawai_administratif', text: 'Pegawai Administratif' },
                    { value: 'tenaga_kesehatan', text: 'Tenaga Kesehatan' }
                ]
            }


            var selectElement = $('#tipe_pegawai');
            
            // Mengosongkan select element jika ada option sebelumnya
            selectElement.empty();

            // Menambahkan option ke dalam select
            options.forEach(function(option) {
                selectElement.append(new Option(option.text, option.value));
            });
        }

        $('#export-excel,#export-pdf,#export-backup').click(function(e){
            e.preventDefault();


             let type = $(this).attr('data-type');
                let params = $('#laporan-form').serialize();
                let url_main = '';

                const parsedData = parseSerializedData(params);

                if (validation(parsedData) === true) {
                    if ($('#pegawai').val() !== "all") {
                        url_main = '/laporan/kehadiran/export-pegawai-bulan';
                    }else{
                        url_main = '/laporan/kehadiran/export-opd-bulan';
                    }
                    window.open(`${url_main}?${params}&type=${type}`, "_blank");
                } 
        })

        $(function () {
            $('.tipe_kepegawai_form').hide();   
            // $("#kt_daterangepicker_1").daterangepicker();
        })
    </script>
@endsection