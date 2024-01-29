    <table id="kt_table_data" class="table-group table-row-gray-300 gy-7">
        <thead class="text-center">
            <tr class="fw-bolder fs-6 text-gray-800">
                <th>No</th>
                <th>Nama Saudara</th>
                <th>Jenis Kelamin</th>
                <th>Tempat Tanggal Lahir</th>
                <th>Pendidikan</th>
                <th>Pekerjaan</th>
                <th>Keterangan</th>
                <th>File</th>
                @if($role['guard'] === 'web' && $path[0] == "profil")
                <th>Aksi</th>
                @endif
            </tr>
        </thead>
        <tbody>
           @foreach($riwayat_saudara as $a => $b)
                <tr class="text-center">
                    <td>{{$a+1}}</td>
                    <td>{{$b->nama_saudara}}</td>
                    <td>{{$b->jk}}</td>
                    <td>{{$b->tempat_lahir}}, {{$b->tanggal_lahir}}</td>
                    <td>{{$b->pendidikan}}</td>
                    <td>{{$b->pekerjaan}}</td>
                    <td>{{$b->keterangan}}</td>
                    <td> <a href="{{ url('/get-file-pegawai?path='.$b->foto_kartu_keluarga) }}" target="_blank">Link</a> </td>
                    @if($role['guard'] === 'web' && $path[0] == "profil")
                    <td style="width: 8rem">
                        <a href="javascript:;" type="button" data-uuid="{{$b->uuid}}" data-kt-drawer-show="true" data-kt-drawer-target="#side_form_riwayat_saudara" class="btn btn-primary button-update btn-icon btn-sm" data-modul="riwayat_saudara" data-toggle="tooltip" title="edit"> 
                                    <img src="{{ asset('admin/assets/media/icons/edit.svg')}}" alt="" srcset="">
                                </a>

                                <a href="javascript:;" type="button" data-uuid="{{$b->uuid}}" data-modul="riwayat_saudara" data-label="{{$b->nama_sekolah}}" class="btn btn-danger button-delete btn-icon btn-sm"> 
                                    <img src="{{ asset('admin/assets/media/icons/trash.svg')}}" data-toggle="tooltip" title="hapus">
                                </a>

                    </td>
                    @endif
                </tr>
           @endforeach
        </tbody>
    </table>