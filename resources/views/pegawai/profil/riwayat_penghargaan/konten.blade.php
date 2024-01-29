    <table id="kt_table_data" class="table-group table-row-gray-300 gy-7">
        <thead class="text-center">
            <tr class="fw-bolder fs-6 text-gray-800">
                <th rowspan="2">No</th>
                <th rowspan="2">Nama Penghargaan</th>
                <th colspan="3">Surat Keputusan</th>
                <th colspan="2">Instansi Penyelenggara</th>
                <th rowspan="2">File</th>
                @if($role['guard'] === 'web' && $path[0] == "profil")
                <th rowspan="2">Aksi</th>
                @endif
            </tr>
            <tr>
                <th>Nomor</th>
                <th>Tanggal</th>
                <th>Jabatan Pendantanganan</th>
                <th>Instansi</th>
                <th>Lokasi</th>
            </tr>
        </thead>
        <tbody>
           @foreach($riwayat_penghargaan as $a => $b)
                <tr class="text-center">
                    <td>{{$a+1}}</td>
                    <td>{{$b->nama_penghargaan}}</td>
                    <td>{{$b->nomor_surat_keputusan}}</td>
                    <td>{{$b->tanggal}}</td>
                    <td>{{$b->pejabat_pendantanganan}}</td>
                    <td>{{$b->nama_instansi}}</td>
                    <td>{{$b->lokasi}}</td>
                    <td> <a href="{{ url('/get-file-pegawai?path='.$b->sertifikat) }}" target="_blank">Link</a> </td>
                    @if($role['guard'] === 'web' && $path[0] == "profil")
                    <td style="width: 8rem">
                        <a href="javascript:;" type="button" data-uuid="{{$b->uuid}}" data-kt-drawer-show="true" data-kt-drawer-target="#side_form_riwayat_penghargaan" class="btn btn-primary button-update btn-icon btn-sm" data-modul="riwayat_penghargaan" data-toggle="tooltip" title="edit"> 
                                    <img src="{{ asset('admin/assets/media/icons/edit.svg')}}" alt="" srcset="">
                                </a>

                                <a href="javascript:;" type="button" data-uuid="{{$b->uuid}}" data-modul="riwayat_penghargaan" data-label="{{$b->nama_sekolah}}" class="btn btn-danger button-delete btn-icon btn-sm"> 
                                    <img src="{{ asset('admin/assets/media/icons/trash.svg')}}" data-toggle="tooltip" title="hapus">
                                </a>

                    </td>
                    @endif
                </tr>
           @endforeach
        </tbody>
    </table>