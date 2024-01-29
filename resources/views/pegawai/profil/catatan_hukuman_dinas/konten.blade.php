    <table id="kt_table_data" class="table-group table-row-gray-300 gy-7">
        <thead class="text-center">
            <tr class="fw-bolder fs-6 text-gray-800">
                <th rowspan="2">No</th>
                <th rowspan="2">Kategori Hukum</th>
                <th rowspan="2">Nama Hukuman</th>
                <th colspan="2">SK</th>
                <th colspan="2">Lama</th>
                <th rowspan="2">Keterangan</th>
                <th rowspan="2">File</th>
                @if($role['guard'] === 'web' && $path[0] == "profil")
                    <th rowspan="2">Aksi</th>
                @endif
            </tr>
            <tr>
                <th>Nama SK</th>
                <th>Tanggal SK</th>
                <th>Tanggal Mulai</th>
                <th>Tanggal Selesai</th>
            </tr>
        </thead>
        <tbody>
            @foreach($catatan_hukuman_dinas as $a => $b)
                <tr class="text-center">
                    <td>{{$a+1}}</td>
                    <td>{{$b->kategori_hukuman}}</td>
                    <td>{{$b->nama_hukuman}}</td>
                    <td>{{$b->nama_sk}}</td>
                    <td>{{$b->tanggal_sk}}</td>
                    <td>{{$b->tanggal_mulai}}</td>
                    <td>{{$b->tanggal_selesai}}</td>
                    <td>{{$b->keterangan_pelanggaran}}</td>
                    <td> <a href="{{ url('/get-file-pegawai?path='.$b->surat_keputusan) }}" target="_blank">Link</a> </td>
                     @if($role['guard'] === 'web' && $path[0] == "profil")
                        <td style="width: 8rem">
                            <a href="javascript:;" type="button" data-uuid="{{$b->uuid}}" data-kt-drawer-show="true" data-kt-drawer-target="#side_form_catatan_hukuman_dinas" class="btn btn-primary button-update btn-icon btn-sm" data-modul="catatan_hukuman_dinas" data-toggle="tooltip" title="edit"> 
                                        <img src="{{ asset('admin/assets/media/icons/edit.svg')}}" alt="" srcset="">
                                    </a>

                                    <a href="javascript:;" type="button" data-uuid="{{$b->uuid}}" data-modul="catatan_hukuman_dinas" data-label="{{$b->nama_sekolah}}" class="btn btn-danger button-delete btn-icon btn-sm"> 
                                        <img src="{{ asset('admin/assets/media/icons/trash.svg')}}" data-toggle="tooltip" title="hapus">
                                    </a>

                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>