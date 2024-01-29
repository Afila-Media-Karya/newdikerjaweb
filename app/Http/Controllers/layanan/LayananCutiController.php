<?php

namespace App\Http\Controllers\layanan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Requests\LayananRequest;
use App\Models\LayananCuti;
use DB;
use App\Traits\General;
use Illuminate\Support\Facades\Storage;
use Auth;
class LayananCutiController extends BaseController
{
    use General;
    public function breadcumb(){
        return [
            [
                'label' => 'Layanan',
                'url' => '#'
            ],
            [
                'label' => 'Layanan Cuti',
                'url' => '#'
            ],
        ];
    }

    public function datatable(){
        $data = array();
        $satuan_kerja = intval(request('satuan_kerja'));
        $role = hasRole();
        $query = DB::table('tb_layanan_cuti')
        ->join('tb_pegawai','tb_layanan_cuti.id_pegawai','=','tb_pegawai.id')
        ->leftJoin('tb_jabatan','tb_jabatan.id_pegawai','=','tb_pegawai.id')
        ->select('tb_layanan_cuti.id','tb_layanan_cuti.uuid','tb_layanan_cuti.jenis_layanan','tb_layanan_cuti.status','tb_pegawai.nama as nama_pegawai','tb_pegawai.nip')
        ->whereYear('tb_layanan_cuti.created_at',session('tahun_penganggaran'));

         if ($role['guard'] == 'web' && $role['role'] == '1') {
            $get_satuan_kerja = $this->infoSatuanKerja(Auth::user()->id_pegawai);
            $query->where('tb_pegawai.id_satuan_kerja',$get_satuan_kerja->id_satuan_kerja);
        }

        if ($role['guard'] == 'web' && $role['role'] == '3') {
            $get_satuan_kerja = $this->infoSatuanKerja(Auth::user()->id_pegawai);
            $query->where('tb_jabatan.id_unit_kerja',$get_satuan_kerja->id_unit_kerja);
        }
        
        if ($satuan_kerja > 0) {
            $query->where('tb_pegawai.id_satuan_kerja',$satuan_kerja);
        }

        $data = $query->get();
        return $this->sendResponse($data, 'Layanan Cuti Fetched Success');
    }

    public function index(){
        $module = $this->breadcumb();
        $satuan_kerja_user = '';
        if(hasRole()['guard'] == 'web' && hasRole()['role'] == '1'){
            $satuan_kerja_user = $this->infoSatuanKerja(hasRole()['id_pegawai']);
        }


        return view('admin_kabupaten.layanan.layanancuti',compact('module','satuan_kerja_user'));
    }

    public function update(Request $request, $params){
        $data = array();
        try {

            if ($request->status == '8') {
                $validated = $request->validate([
                    'dokumen_cuti' => 'required|file|max:500|mimes:pdf',
                ]);
            }

            $data = LayananCuti::where('uuid',$params)->first();
            $data->keterangan = $request->keterangan;
            $data->status = $request->status;

            if (isset($request->dokumen_cuti)) {
                $file_konten = $request->file('dokumen_cuti');
                $filePath = Storage::disk('sftp')->put('/sftpasn/dokumen_cuti', $file_konten);
                $data->dokumen_cuti =  $filePath;
            }
            $data->save();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Layanan Update success');
    }

    public function detail($params){
        $module =  [
            [
                'label' => 'Layanan',
                'url' => '#'
            ],
            [
                'label' => 'Layanan Cuti',
                'url' => '/layanan/layanan-cuti'
            ],
            [
                'label' => 'Detail Cuti',
                'url' => '#'
            ],
        ];

        $data = DB::table('tb_layanan_cuti')->join('tb_pegawai','tb_layanan_cuti.id_pegawai','tb_pegawai.id')->join('tb_satuan_kerja','tb_pegawai.id_satuan_kerja','=','tb_satuan_kerja.id')->select('tb_layanan_cuti.id','tb_layanan_cuti.uuid','tb_layanan_cuti.jenis_layanan','tb_layanan_cuti.status','tb_layanan_cuti.alasan','tb_layanan_cuti.tanggal_mulai','tb_layanan_cuti.tanggal_akhir','tb_layanan_cuti.alamat','tb_layanan_cuti.dokumen','tb_layanan_cuti.keterangan','tb_pegawai.nama as nama_pegawai','tb_pegawai.nip','tb_satuan_kerja.nama_satuan_kerja')->where('tb_layanan_cuti.uuid',$params)->first();

        return view('admin_kabupaten.layanan.detaillayanancuti',compact('module','data'));
    }

    public function show($params){
       $data = array();
        try {
            $data = LayananCuti::where('uuid',$params)->first();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Layanan Update success'); 
    }

    public function delete(Request $request, $params){
        $data = array();
        try {
            DB::table('tb_layanan_cuti')->where('uuid', $params)->delete();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Layanan Delete success');
    }
}
