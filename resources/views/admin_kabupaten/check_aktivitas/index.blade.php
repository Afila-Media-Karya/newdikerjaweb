@section('title', 'Arsip')
@extends('layouts.layout')
@section('content')

<div class="post d-flex flex-column-fluid" id="kt_post">
        <!--begin::Container-->
        <div id="kt_content_container" class="container">
            <div class="row mt-5">

                <div class="card">
                    <div class="card-body p-0">

                        <div class="container">
                            <div class="py-5">

                            <form id="form-filter">
                                <div class="row" style="margin-bottom: 1rem"> 
                                    <div class="col-lg-2">
                                        <label for="filter-valid" class="form-label" style="font-size:12px;">Bulan</label>
                                        <select class="form-control form-control-sm form-control-solid" name="bulan" id="filter-bulan">
                                        <option value="semua" selected>semua</option>
                                        @foreach (range(1, 12) as $bulan)
                                                <option value="{{ $bulan }}" {{ $bulan == date('n') ? 'selected' : '' }}>
                                                    {{ \Carbon\Carbon::parse('2023-' . $bulan . '-01')->translatedFormat('F') }}
                                                </option>
                                            @endforeach
                                        </select>        
                                    </div>
                                    <div class="col-lg">
                                        <button type="submit" class="btn btn-primary btn-sm" id="filter-btn" style="position: relative;top: 24px;">Terapkan</button>
                                        <button type="button" class="btn btn-warning btn-sm backup_button" style="position: relative;top: 24px;left:12px;">Backup</button>
                                        <button type="submit" class="btn btn-danger btn-sm delete_data" style="position: relative;top: 24px;left:20px;">Hapus Data</button>
                                    </div>
                                </div>
                            </form>

                                <table id="kt_table_data" class="table table-row-dashed table-row-gray-300 gy-7">
                                    <thead class="text-center">
                                        <tr class="fw-bolder fs-6 text-gray-800">
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>Satuan Kerja</th>
                                            <th>Aktivitas</th>
                                            <th>Keterangan</th>
                                            <th>Selisih Hari Penginputan</th>
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

        $('#export-excel,#export-pdf,#export-backup').click(function(e){
            e.preventDefault();


             let type = $(this).attr('data-type');
                let params = $('#laporan-form').serialize();
                let url_main = '';

                const parsedData = parseSerializedData(params);
                console.log(parsedData);
                if (validation(parsedData) === true) {

                    $.ajaxSetup({
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                    });
                    
                    $.ajax({
                        type: 'POST',
                        url: '{{ route("checkaktivitas.proses") }}',
                        data: params,
                        success: function (response) {
                            Swal.fire(
                                "Sukses!",
                                "Laporan Berhasil diarsipkan",
                                "success"
                            );
                            datatable(`/arsip/datatable`);
                        },
                        error: function (xhr) {
                            Swal.fire(
                                "Gagal Memproses data!",
                                "Silahkan Hubungi Admin",
                                "warning"
                            );
                        },
                        complete: function () {
                            $(".btn-submit").prop("disabled", false); // Mengaktifkan kembali tombol submit
                            $(".btn-submit").html('<i class="bi bi-file-earmark-diff"></i> Simpan');
                        },
                    });
                } 
        })

        
        $(document).on('submit', "#form-filter", function(e) {
            e.preventDefault();
           let data = $(this).serialize();
           datatable(`/check-aktivitas/datatable?${data}`);
        });

        $(document).on("click",".backup_button", function (e) {
            e.preventDefault();
            let bulan = $('#filter-bulan').val();
            window.open(`/check-aktivitas/backup?bulan=${bulan}`, '_blank');
        })

        $(document).on("click",".delete_data", function (e) {
            e.preventDefault();
            let bulan = $('#filter-bulan').val();
            // window.open(`/check-aktivitas/backup?bulan=${bulan}`, '_blank');
            let url = `/check-aktivitas/delete?bulan=${bulan}`;
            let label = 'Aktivitas lewat 5 hari bulan ' + bulan;
            control.ajaxDelete(url, label);
        })


        datatable = (url) =>{
            let columns = [{
                data: null,
                className : 'text-center',
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            }, {
                data: null,
                className : 'text-right',
                render: function(data, type, row, meta) {
                    return `${row.nama} (${row.nip})`; // Assuming 'nama' and 'nip' are the fields in your data
                }
            }, {
                data: 'nama_satuan_kerja',
                className : 'text-right',
            }, {
                data: 'aktivitas',
                className : 'text-right',
            }, {
                data: 'keterangan',
                className : 'text-right',
            }, {
                data: 'selisih_hari',
                className : 'text-right',
            }
        ];
            let columnDefs = [];

            control.initDatatable(url, columns, columnDefs);
        }

        $(function () {
            datatable(`/check-aktivitas/datatable`);
        })
    </script>
@endsection