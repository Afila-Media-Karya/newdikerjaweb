@extends('layouts.layout')
@section('style')
    <style>
        .table-group thead th {
            border: 1px solid #dee2e6;
        }

        /* Tambahkan padding pada sel */
        .table-group td,
        .table-group th {
            border: 1px solid #dee2e6;
            padding: 0.5rem;
            /* Sesuaikan sesuai kebutuhan Anda */
        }
    </style>
@endsection
@section('title', 'Review Sasaran Kinerja')
@section('content')
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <!--begin::Container-->
        <div id="kt_content_container" class="container">
            <div class="row">

                <div class="card">
                    <div class="card-body p-0">

                        <div class="d-flex justify-content-end mt-5" style="position:relative; right:30px;">
                            <span class="svg-icon svg-icon-1">
                                <svg style="position: relative;left: 34px; top: 10px;" xmlns="http://www.w3.org/2000/svg"
                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                    viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24"></rect>
                                        <path
                                            d="M14.2928932,16.7071068 C13.9023689,16.3165825 13.9023689,15.6834175 14.2928932,15.2928932 C14.6834175,14.9023689 15.3165825,14.9023689 15.7071068,15.2928932 L19.7071068,19.2928932 C20.0976311,19.6834175 20.0976311,20.3165825 19.7071068,20.7071068 C19.3165825,21.0976311 18.6834175,21.0976311 18.2928932,20.7071068 L14.2928932,16.7071068 Z"
                                            fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                                        <path
                                            d="M11,16 C13.7614237,16 16,13.7614237 16,11 C16,8.23857625 13.7614237,6 11,6 C8.23857625,6 6,8.23857625 6,11 C6,13.7614237 8.23857625,16 11,16 Z M11,18 C7.13400675,18 4,14.8659932 4,11 C4,7.13400675 7.13400675,4 11,4 C14.8659932,4 18,7.13400675 18,11 C18,14.8659932 14.8659932,18 11,18 Z"
                                            fill="#000000" fill-rule="nonzero"></path>
                                    </g>
                                </svg>
                            </span>

                            <input type="text" id="search_" class="form-control w-250px ps-15" placeholder="Search">
                        </div>

                        <div class="container">
                            <div class="py-5">
                                <form id="form-skp-review">
                                    <table id="kt_table_data" class="table table-group table-row-gray-300 gy-7">
                                        <thead class="text-center">
                                            <tr class="fw-bolder fs-6 text-gray-800">
                                                <th>No</th>
                                                <th>Jenis Kinerja</th>
                                                <th>Rencana Kerja</th>
                                                <th>Indikator Kinerja Individu</th>
                                                <th>Target</th>
                                                <th>Satuan</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                    <div class="d-flex justify-content-end gap-5">
                                        <button type="submit"
                                            class="btn btn-primary btn-sm btn-submit d-flex align-items-center"><i
                                                class="bi bi-file-earmark-diff"></i> Simpan</button>
                                        <a href="{{ route('pegawai.review.sasaran_kinerja.index') }}"
                                            class="btn mr-2 btn-light btn-cancel btn-sm d-flex align-items-center"
                                            style="background-color: #ea443e65; color: #EA443E"><i class="bi bi-trash-fill"
                                                style="color: #EA443E"></i>Batal</a>
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
    <script src="{{ asset('admin/assets/plugins/custom/formrepeater/formrepeater.bundle.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/gh/ashl1/datatables-rowsgroup@v1.0.0/dataTables.rowsGroup.js"></script>

    <script>
        let control = new Control();
        let jabatan = {!! json_decode($jabatan) !!};
        let level = {!! json_decode($level) !!};

        $(document).on('keyup', '#search_', function(e) {
            e.preventDefault();
            control.searchTable(this.value);
        })

        $(document).on('submit', "#form-skp-review", function(e) {
            e.preventDefault();

            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
            });

            $.ajax({
                type: 'POST',
                url: '/review/sasaran-kinerja/post-review-skp',
                data: $(this).serialize(),
                success: function(response) {
                    console.log(response);
                    $(".text-danger").html("");
                    if (response.success == true) {
                        swal
                            .fire({
                                text: `SKP berhasil di review`,
                                icon: "success",
                                showConfirmButton: false,
                                timer: 1500,
                            })
                            .then(function() {
                                setTimeout(() => {
                                    window.location.href = '/review/sasaran-kinerja';
                                }, 800);
                            });
                    } else {
                        Swal.fire("Gagal Memproses data!", `${response.message}`, "warning");
                    }
                },
                error: function(xhr) {
                    console.log(xhr);
                    if (xhr.statusText == "Method Not Allowed") {
                        Swal.fire(
                            "Gagal Memproses data!",
                            "Silahkan Hubungi Admin",
                            "warning"
                        );
                    }

                    $(".text-danger").html("");
                    $.each(xhr.responseJSON["errors"], function(key, value) {
                        $(`.${key}_error`).html(" " + value);
                    });
                },
            });

        });

        datatable = async () => {

            var currentNumber = null;
            var cntNumber = 0;
            var current = null;
            var cnt = 0;

            let table = $('#kt_table_data');
            table.DataTable().clear().destroy();

            // await table.dataTable().clear().draw();
            await table.dataTable().fnClearTable();
            await table.dataTable().fnDraw();
            await table.dataTable().fnDestroy();

            table.DataTable({
                responsive: true,
                paging: false,
                order: [
                    [1, 'desc']
                ],
                processing: true,
                ajax: `/review/sasaran-kinerja/data-review-skp?jabatan=${jabatan}&level=${level}`,
                columns: [{
                    data: 'id_skp',
                    className: 'text-center',
                    render: function(data, type, row, meta) {
                        let id = row.id_skp;

                        if (row.id_skp != currentNumber) {
                            currentNumber = row.id_skp;
                            cntNumber++;
                        }

                        if (row.id_skp != current) {
                            current = row.id_skp;
                            cnt = 1;
                        } else {
                            cnt++;
                        }
                        return cntNumber;
                    }
                }, {
                    data: 'jenis',
                    className: 'text-center',
                }, {
                    data: 'rencana',
                    width: '35rem',
                    className: 'text-center',
                }, {
                    data: 'iki',
                    className: 'text-center',
                }, {
                    data: 'target',
                    className: 'text-center'
                }, {
                    data: 'satuan',
                    className: 'text-center'
                }, {
                    data: 'uuid',
                }],
                columnDefs: [{
                        targets: 1,
                        visible: false,
                        render: function(data, type, full, meta) {
                            return `<span class="badge badge-success">Aktif</span>`;
                        },
                    },
                    {
                        targets: -1,
                        title: 'Aksi',
                        width: '18rem',
                        orderable: false,
                        render: function(data, type, full, meta) {
                            let checked = full.kesesuaian == 1 ? 'checked' : '';
                            let unchecked = full.kesesuaian == 0 ? 'checked' : '';
                            let keterangan = '';
                            full.keterangan !== null ? keterangan = full.keterangan : keterangan =
                                '-';

                            let originalRowIndex = meta.row % full.jumlah_skp;
                            return `
                            <input type="hidden" value="${full.id_skp}" id="id_skp[${originalRowIndex}]" name="id_skp[${meta.row}]" class="test"/>
                                <div class="mb-10">
                                    <label class="form-label">Kesesuaian SKP</label>
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="form-check form-check-custom form-check-solid">
                                                <input class="form-check-input" name="kesesuaian[${meta.row}]" ${checked} type="radio" value="1"/>
                                                <label class="form-check-label" for="L">
                                                    Sesuai
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-check form-check-custom form-check-solid">
                                                <input class="form-check-input" name="kesesuaian[${meta.row}]" ${unchecked} type="radio" value="0" />
                                                <label class="form-check-label" for="P">
                                                    Tidak
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <small class="text-danger jenis_kelamin_error"></small>
                                </div>

                                

                                <div class="mb-10">
                                    <label class="form-label">Keterangan</label>
                                    <textarea id="keterangan" class="form-control" name="keterangan[${meta.row}]" rows="3" style="position:relative;right:3px;">
                                        ${keterangan}
                                    </textarea>
                                    <small class="text-danger keterangan_error"></small>
                                </div>
                            `;
                        },
                    }
                ],
                rowGroup: {
                    dataSrc: 'jenis', // Grup berdasarkan jenis
                    startRender: function(rows, group) {
                        // console.log(group);
                        let label = '';
                        let color = '';
                        if (group == 'utama') {
                            label = 'A. KINERJA UTAMA';
                            color = 'primary';
                        } else {
                            label = 'B. KINERJA TAMBAHAN';
                            color = 'success';
                        }
                        return $('<tr/>')
                            .append(
                                `<td colspan="6"><span style="margin-left:36px" class="badge badge-${color}">${label}</span></td>`
                            )
                            .attr('data-name', group);
                    },
                },
                "rowsGroup": [-1, 0, 2],
                "ordering": false,
            });
        }

        $(function() {
            datatable();
        })
    </script>
@endsection
