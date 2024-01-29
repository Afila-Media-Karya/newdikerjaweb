    <table id="kt_table_data" class="table-group table-row-gray-300 gy-7">
        <thead class="text-center">
            <tr class="fw-bolder fs-6 text-gray-800">
                <th rowspan="2">No</th>
                <th rowspan="2">Diklat Fungsional</th>
                <th colspan="2">Tanggal</th>
                <th rowspan="2">Jumlah Jam</th>
                <th colspan="3">STTB</th>
                <th colspan="2">Instansi Penyelenggara</th>
                <th rowspan="2">File</th>
                @if($role['guard'] === 'web' && $path[0] == "profil")
                <th rowspan="2">Aksi</th>
                @endif
            </tr>
            <tr>
                <th>Mulai</th>
                <th>Selesai</th>
                <th>Nomor</th>
                <th>Tanggal</th>
                <th>Jabatan Pendantanganan</th>
                <th>Instansi</th>
                <th>Lokasi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($diklat_teknis as $a => $b)
                <tr class="text-center">
                    <td>{{$a+1}}</td>
                    <td>{{$b->nama_diklat_teknis}}</td>
                    <td>{{$b->tanggal_mulai}}</td>
                    <td>{{$b->tanggal_selesai}}</td>
                    <td>{{$b->jumlah_jam}}</td>
                    <td>{{$b->nomor_sttb}}</td>
                    <td>{{$b->tanggal}}</td>
                    <td>{{$b->pejabat_pendantanganan}}</td>
                    <td>{{$b->nama_instansi}}</td>
                    <td>{{$b->lokasi}}</td>
                    <td> <a href="{{ url('/get-file-pegawai?path='.$b->sertifikat) }}" target="_blank">Link</a> </td>
                    @if($role['guard'] === 'web' && $path[0] == "profil")
                    <td style="width: 8rem">
                        <a href="javascript:;" type="button" data-uuid="{{$b->uuid}}" data-kt-drawer-show="true" data-kt-drawer-target="#side_form_diklat_teknis" class="btn btn-primary button-update btn-icon btn-sm" data-modul="diklat_teknis" data-toggle="tooltip" title="edit"> 
                                    <img src="{{ asset('admin/assets/media/icons/edit.svg')}}" alt="" srcset="">
                                </a>

                                <a href="javascript:;" type="button" data-uuid="{{$b->uuid}}" data-modul="diklat_teknis" data-label="{{$b->nama_sekolah}}" class="btn btn-danger button-delete btn-icon btn-sm"> 
                                    <img src="{{ asset('admin/assets/media/icons/trash.svg')}}" data-toggle="tooltip" title="hapus">
                                </a>

                    </td>
                    @endif
                </tr>
            @endforeach
            
        </tbody>
    </table>