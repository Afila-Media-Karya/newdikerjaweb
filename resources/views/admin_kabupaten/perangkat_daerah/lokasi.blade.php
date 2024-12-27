@section('title', 'Lokasi')
@extends('layouts.layout')
@section('button')
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
        <!--begin::Page title-->
        <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
            data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
            class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
            <!--begin::Title-->
            <button class="btn btn-primary btn-sm " data-kt-drawer-show="true" data-kt-drawer-target="#side_form"
                id="button-side-form"><i class="fa fa-plus-circle" style="color:#ffffff" aria-hidden="true"></i> Tambah
                Data</button>
            <!--end::Title-->
        </div>
        <!--end::Page title-->
    </div>
@endsection
@section('content')
<div class="post d-flex flex-column-fluid" id="kt_post">
        <!--begin::Container-->
        <div id="kt_content_container" class="container">
            <div class="row">

                <div class="card">
                    <div class="card-body p-0">

                        <div class="container">
                            <div class="py-5">
                                <table id="kt_table_data" class="table table-row-dashed table-row-gray-300 gy-7">
                                    <thead class="text-center">
                                        <tr class="fw-bolder fs-6 text-gray-800">
                                            <th>No</th>
                                            <th>Nama Lokasi</th>
                                            <th>Satuan Kerja</th>
                                            <th>Unit Kerja</th>
                                            <th>Aksi</th>
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
@section('side-form')
    <div id="side_form" class="bg-white" data-kt-drawer="true" data-kt-drawer-activate="true"
        data-kt-drawer-toggle="#side_form_button" data-kt-drawer-close="#side_form_close" data-kt-drawer-width="500px">
        <!--begin::Card-->
        <div class="card w-100">
            <!--begin::Card header-->
            <div class="card-header pe-5">
                <!--begin::Title-->
                <div class="card-title">
                    <!--begin::User-->
                    <div class="d-flex justify-content-center flex-column me-3">
                        <a href="#"
                            class="fs-4 fw-bolder text-gray-900 text-hover-primary me-1 lh-1 title_side_form"></a>
                    </div>
                    <!--end::User-->
                </div>
                <!--end::Title-->
                <!--begin::Card toolbar-->
                <div class="card-toolbar">
                    <!--begin::Close-->
                    <div class="btn btn-sm btn-icon btn-active-light-primary" id="side_form_close">
                        <!--begin::Svg Icon | path: icons/duotone/Navigation/Close.svg-->
                        <span class="svg-icon svg-icon-2">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)"
                                    fill="#000000">
                                    <rect fill="#000000" x="0" y="7" width="16" height="2"
                                        rx="1" />
                                    <rect fill="#000000" opacity="0.5"
                                        transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)"
                                        x="0" y="7" width="16" height="2" rx="1" />
                                </g>
                            </svg>
                        </span>
                        <!--end::Svg Icon-->
                    </div>
                    <!--end::Close-->
                </div>
                <!--end::Card toolbar-->
            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body hover-scroll-overlay-y">
                <form class="form-data">

                    <input type="hidden" name="id">
                    <input type="hidden" name="uuid">

                    <div class="mb-10">
                        <label class="form-label">Pilih Satuan Kerja</label>
                        <select class="form-select form-control" id="id_satuan_kerja" name="id_satuan_kerja" data-control="select2" data-placeholder="Pilih Satuan Kerja">
                            <option></option>
                            @foreach($satuan_kerja as $val)
                                <option value="{{$val->value}}">{{$val->text}}</option>
                            @endforeach
                        </select>
                        <small class="text-danger id_satuan_kerja_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Unit Kerja</label>
                        <select class="form-select form-control" name="id_unit_kerja" id="id_unit_kerja" data-control="select2" data-placeholder="Pilih Unit Kerja">
                            <option></option>
                        </select>
                        <small class="text-danger id_unit_kerja_error"></small>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Nama Lokasi</label>
                        <input type="text" id="nama_lokasi" class="form-control" name="nama_lokasi" placeholder="Masukkan Nama Lokasi">
                        <small class="text-danger nama_lokasi_error"></small>
                    </div>

                    <div id="mapView" style="height: 300px;" class="mb-10"></div>

                    <div class="row mb-10">
                        <div class="col-lg-6">
                            <label class="form-label">Lattitude</label>
                            <input type="text" id="lat" class="form-control" name="latitude" placeholder="Lattitude">
                            <small class="text-danger lat_error"></small>
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label">Longitude</label>
                            <input type="text" id="long" class="form-control" name="longitude" placeholder="Longitude">
                            <small class="text-danger long_error"></small>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <label class="form-label">Radius</label>
                        <input type="number" id="radius" class="form-control" name="radius" placeholder="Masukkan Radius">
                        <small class="text-danger radius_error"></small>
                    </div>
                    

                    <div class="separator separator-dashed mt-8 mb-5"></div>
                    <div class="d-flex gap-5">
                        <button type="submit" class="btn btn-primary btn-sm btn-submit d-flex align-items-center"><i
                                class="bi bi-file-earmark-diff"></i> Simpan</button>
                        <button type="reset" id="side_form_close"
                            class="btn mr-2 btn-light btn-cancel btn-sm d-flex align-items-center"
                            style="background-color: #ea443e65; color: #EA443E"><i class="bi bi-trash-fill"
                                style="color: #EA443E"></i>Batal</button>
                    </div>
                </form>
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card-->
    </div>
@endsection
@section('script')
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA97drRATl2BEEoFPEqpF1o9Jk0wenosuU&callback=initMap&libraries=v=weekly,places&sensor=false" defer></script>
    <script>
        let control = new Control();
        let role = {!! json_encode($role) !!};
        let guard = {!! json_encode($guard) !!};
        console.log(role);
        console.log(guard);

        let path = '';
            if (parseInt(role) > 0 && guard == 'web') {
                path = 'perangkat-daerah-opd';
            }else{
                path = 'perangkat-daerah';
            }
        


        
        $(document).on('click', '#button-side-form', function() {
            control.overlay_form('Tambah', 'Lokasi Kerja');
        })

        $(document).on('submit', ".form-data", function(e) {
            e.preventDefault();
            let type = $(this).attr('data-type');
            if (type == 'add') {
                control.submitFormMultipart(`/${path}/lokasi/store`, 'Tambah', 'Lokasi Kerja','POST');
            } else {
                let uuid = $("input[name='uuid']").val();
                control.submitFormMultipart(`/${path}/lokasi/update/` + uuid, 'Update','Lokasi Kerja', 'POST');
            }
        });

        $(document).on('change','#id_satuan_kerja', function () {
            if ($(this).val() !== '') {
                let val = $(this).val();
                control.push_select(`/${path}/unit-kerja/option?satuan_kerja=${val}`,'#id_unit_kerja'); 
            }
        })

        $(document).on('click', '.button-update', function(e) {
            e.preventDefault();
            let url = `/${path}/lokasi/show/` + $(this).attr('data-uuid');
            control.overlay_form('Update', 'Perangkat Daerah', url);
            setTimeout(() => {
               var lat = $('#lat').val(); 
               var lng = $('#long').val(); 
               initMap(lat, lng);
            }, 500);
        })

        $(document).on('click', '.button-delete', function(e) {
            e.preventDefault();
            let url = `/${path}/lokasi/delete/` + $(this).attr('data-uuid');
            let label = $(this).attr('data-label');
            control.ajaxDelete(url, label)
        })

        let marker;
        function initMap(lat = null, lng = null) {
                lat = lat ? lat : '-5.558543';
                lng = lng ? lng : '120.1909133,17';
                
                var center = { lat: parseFloat(lat), lng: parseFloat(lng) };
                const bounds = new google.maps.LatLngBounds();
                const map = new google.maps.Map(document.getElementById("mapView"), {
                    zoom: 14,
                    center: center,
                    mapTypeControl: true,
                    mapTypeControlOptions: {
                        style: google.maps.MapTypeControlStyle.DROPDOWN_MENU,
                        // mapTypeId: google.maps.MapTypeId.SATELLITE
                        mapTypeId: 'satellite'
                    },
                });

                const geocoder = new google.maps.Geocoder();
             // build request
                


            marker = new google.maps.Marker({
                position: center,
                map,
            });
            google.maps.event.addListener(map, 'click', function(event) {
                setMarker(this, event.latLng);
                // geocodeLatLng(geocoder, map);

            });

        }

        function setMarker(map, markerPosition) {
            const geocoder = new google.maps.Geocoder();
            const service = new google.maps.DistanceMatrixService();
            console.log(markerPosition)
            if( marker ){
                marker.setPosition(markerPosition);
            } else {
                marker = new google.maps.Marker({
                    position: markerPosition,
                    map: map
                });
            }
            map.setZoom(16);
            map.setCenter(markerPosition);
                // isi nilai koordinat ke form
                // document.getElementById("setLongitude").value = markerPosition.lng();
                // document.getElementById("setLatitude").value = markerPosition.lat();
                document.getElementById('lat').value = markerPosition.lat();
                 document.getElementById('long').value = markerPosition.lng();
                // Get Lokasi
                // lat_locationlong_location


        }

        datatable = () =>{
            let columns = [{
                data: null,
                className : 'text-center',
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            }, {
                data: 'nama_lokasi',
                className : 'text-right',
            }, {
                data: 'nama_satuan_kerja',
                className : 'text-right',
            }, {
                data: 'nama_unit_kerja',
                className : 'text-right',
            }, {
                data: 'uuid',
                className : 'text-center',
            }];
            let columnDefs = [{
                targets: -1,
                title: 'Aksi',
                width: '9rem',
                orderable: false,
                render: function(data, type, full, meta) {
                    return `
                            <a href="javascript:;" type="button" data-uuid="${data}" data-kt-drawer-show="true" data-kt-drawer-target="#side_form" class="btn btn-primary button-update btn-icon btn-sm"> 
                                <img src="{{ asset('admin/assets/media/icons/edit.svg')}}" alt="" srcset="">
                            </a>

                            <a href="javascript:;" type="button" data-uuid="${data}" data-label="${full.inisial_satuan_kerja}" class="btn btn-danger button-delete btn-icon btn-sm"> 
                                <img src="{{ asset('admin/assets/media/icons/trash.svg')}}" alt="" srcset="">
                            </a>
                            `;
                    },
            }];
            control.initDatatable(`/${path}/lokasi/datatable`, columns, columnDefs);
        }

        $(function() {    
            initMap();
            datatable();
        })
    </script>
@endsection