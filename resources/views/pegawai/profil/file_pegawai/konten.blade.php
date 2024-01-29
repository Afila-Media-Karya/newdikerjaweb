    <table id="kt_table_data" class="table-group table-row-gray-300 gy-7">
        <thead class="text-center">
            <tr class="fw-bolder fs-6 text-gray-800">
                <th>No</th>
                <th>Nama File</th>
                <th>File</th>
                <th>Keterangan</th>
                @if($role['guard'] === 'web' && $path[0] == "profil")
                <th>Aksi</th>
                @endif
            </tr>
        </thead>
        <tbody>
           @foreach($file_pegawai as $a => $b)
                <tr class="text-center">
                    <td>{{$a+1}}</td>
                    <td>{{$b->nama_file}}</td>
                    <td>
                        <a href="/storage/{{$b->file}}" target="_blank">Tautan File</a>
                    </td>
                    <td>{{$b->keterangan}}</td>
                    @if($role['guard'] === 'web' && $path[0] == "profil")
                    <td style="width: 8rem">
                        <a href="javascript:;" type="button" data-uuid="{{$b->uuid}}" data-kt-drawer-show="true" data-kt-drawer-target="#side_form_file_pegawai" class="btn btn-primary button-update btn-icon btn-sm" data-modul="file_pegawai" data-toggle="tooltip" title="edit"> 
                                    <img src="{{ asset('admin/assets/media/icons/edit.svg')}}" alt="" srcset="">
                                </a>

                                <a href="javascript:;" type="button" data-uuid="{{$b->uuid}}" data-modul="file_pegawai" data-label="{{$b->nama_sekolah}}" class="btn btn-danger button-delete btn-icon btn-sm"> 
                                    <img src="{{ asset('admin/assets/media/icons/trash.svg')}}" data-toggle="tooltip" title="hapus">
                                </a>

                    </td>
                    @endif
                </tr>
           @endforeach
        </tbody>
    </table>