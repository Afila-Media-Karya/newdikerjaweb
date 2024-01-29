    <h3 class="text-center">Riwayat Keahlian</h3>
    <table id="kt_table_data" class="table-group table-row-gray-300 gy-7">
        <thead class="text-center">
            <tr class="fw-bolder fs-6 text-gray-800">
                <th>No</th>
                <th>Nama Keahlian</th>
                <th>Level Keahlian</th>
                <th>Tanggal Mulai Keahlian</th>
                <th>Pelatihan</th>
                <th>Predikat</th>
                <th>File</th>
                @if($role['guard'] === 'web' && $path[0] == "profil")
                <th>Aksi</th>
                @endif
            </tr>
        </thead>
        <tbody>
           @foreach($riwayat_keahlian as $a => $b)
                <tr class="text-center">
                    <td>{{$a+1}}</td>
                    <td>{{$b->nama_keahlian}}</td>
                    <td>{{$b->level_keahlian}}</td>
                    <td>{{$b->tanggal}}</td>
                    <td>{{$b->pelatihan}}</td>
                    <td>{{$b->predikat}}</td>
                    <td> <a href="{{ url('/get-file-pegawai?path='.$b->sertifikat) }}" target="_blank">Link</a> </td>
                    @if($role['guard'] === 'web' && $path[0] == "profil")
                    <td style="width: 8rem">
                        <a href="javascript:;" type="button" data-uuid="{{$b->uuid}}" data-kt-drawer-show="true" data-kt-drawer-target="#side_form_riwayat_tambahan" class="btn btn-primary button-update btn-icon btn-sm" data-modul="riwayat_tambahan" data-type="keahlian" data-toggle="tooltip" title="edit"> 
                                    <img src="{{ asset('admin/assets/media/icons/edit.svg')}}" alt="" srcset="">
                                </a>

                                <a href="javascript:;" type="button" data-uuid="{{$b->uuid}}" data-modul="riwayat_tambahan" data-label="{{$b->nama_sekolah}}" data-type="keahlian" class="btn btn-danger button-delete btn-icon btn-sm"> 
                                    <img src="{{ asset('admin/assets/media/icons/trash.svg')}}" data-toggle="tooltip" title="hapus">
                                </a>
                    </td>
                    @endif
                </tr>
           @endforeach
        </tbody>
    </table>

    <h3 class="text-center">Riwayat Kemampuan Bahasa</h3>
    <table id="kt_table_data1" class="table-group table-row-gray-300 gy-7">
        <thead class="text-center">
            <tr class="fw-bolder fs-6 text-gray-800">
                <th rowspan="2">No</th>
                <th rowspan="2">Bahasa</th>
                <th colspan="4">Level Keahlian</th>
                <th rowspan="2">Tanggal Mulai Keahlian</th>
                <th rowspan="2">Pelatihan</th>
                <th rowspan="2">Predikat</th>
                <th rowspan="2">File</th>
                @if($role['guard'] === 'web' && $path[0] == "profil")
                <th rowspan="2">Aksi</th>
                @endif
            </tr>
            <tr>
                <th>Membaca</th>
                <th>Mendengarkan</th>
                <th>Menulis</th>
                <th>Berbicara</th>
            </tr>
        </thead>
        <tbody>
           @foreach($riwayat_bahasa as $a => $b)
                <tr class="text-center">
                    <td>{{$a+1}}</td>
                    <td>{{$b->nama_bahasa}}</td>
                    <td>{{$b->level_keahlian_membaca}}</td>
                    <td>{{$b->level_keahlian_mendengarkan}}</td>
                    <td>{{$b->level_keahlian_menulis}}</td>
                    <td>{{$b->level_keahlian_berbicara}}</td>
                    <td>{{$b->tanggal}}</td>
                    <td>{{$b->pelatihan}}</td>
                    <td>{{$b->predikat}}</td>
                    <td> <a href="{{ url('/get-file-pegawai?path='.$b->sertifikat) }}" target="_blank">Link</a> </td>
                    @if($role['guard'] === 'web' && $path[0] == "profil")
                        <td style="width: 8rem">
                            <a href="javascript:;" type="button" data-uuid="{{$b->uuid}}" data-kt-drawer-show="true" data-kt-drawer-target="#side_form_riwayat_tambahan" data-type="bahasa" class="btn btn-primary button-update btn-icon btn-sm" data-modul="riwayat_tambahan" data-toggle="tooltip" title="edit"> 
                                        <img src="{{ asset('admin/assets/media/icons/edit.svg')}}" alt="" srcset="">
                                    </a>

                            <a href="javascript:;" type="button" data-uuid="{{$b->uuid}}" data-modul="riwayat_tambahan" data-label="{{$b->nama_sekolah}}" data-type="bahasa" class="btn btn-danger button-delete btn-icon btn-sm"> 
                                <img src="{{ asset('admin/assets/media/icons/trash.svg')}}" data-toggle="tooltip" title="hapus">
                            </a>
                        </td>
                    @endif
                </tr>
           @endforeach
        </tbody>
    </table>