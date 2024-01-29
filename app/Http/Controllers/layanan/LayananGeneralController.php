<?php

namespace App\Http\Controllers\layanan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Requests\LayananGeneralRequest;
use App\Models\LayananGeneral;
use DB;
use Illuminate\Support\Facades\Storage;
use App\Traits\General;

class LayananGeneralController extends BaseController
{
    use General;
    public function breadcumb(){
        return [
            [
                'label' => 'Layanan',
                'url' => '#'
            ],
            [
                'label' => 'Layanan General',
                'url' => '#'
            ],
        ];
    }

    public function datatable(){
        $data = DB::table('tb_layanan_general')
        ->join('tb_pegawai','tb_layanan_general.id_pegawai','=','tb_pegawai.id')
        ->join('tb_satuan_kerja','tb_pegawai.id_satuan_kerja','=','tb_satuan_kerja.id')
        ->join('tb_layanan','tb_layanan_general.id_jenis_layanan','=','tb_layanan.id')
        ->select('tb_layanan_general.id','tb_layanan_general.uuid','tb_layanan_general.id_jenis_layanan','tb_layanan_general.id_pegawai','tb_layanan_general.id_satuan_kerja','tb_layanan_general.keterangan','tb_pegawai.nama as nama_pegawai','tb_satuan_kerja.nama_satuan_kerja','tb_layanan.nama as jenis_layanan')
        ->get();
        return $this->sendResponse($data, 'Master Layanan Fetched Success');
    }

    public function index(){
        $module = $this->breadcumb();
        $jenis_layanan = DB::table('tb_layanan')->select('nama as text','id as value')->get();
        $satuan_kerja = $this->option_satuan_kerja();
        return view('admin_kabupaten.layanan.layanangeneral',compact('module','jenis_layanan','satuan_kerja'));
    }

    public function store(LayananGeneralRequest $request){
        $data = array();
        try {
            $data = new LayananGeneral();
            $data->id_satuan_kerja = $request->id_satuan_kerja;
            $data->id_pegawai = $request->id_pegawai;
            $data->id_jenis_layanan = $request->jenis_layanan;
            $data->keterangan = $request->keterangan;
            $data->status = $request->status_general;

            if (isset($request->dokumen)) {
                $file_konten = $request->file('dokumen');
                 $filePath = Storage::disk('sftp')->put('/sftpasn/layanan-general', $file_konten);
                $data->dokumen =  $filePath;
            }

            if (isset($request->dokumen_pendukung)) {
                $file_konten = $request->file('dokumen_pendukung');
                 $filePath = Storage::disk('sftp')->put('/sftpasn/layanan-general', $file_konten);
                $data->dokumen_pendukung =  $filePath;
            }

            $data->save();
        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Layanan Added success');
    }

    public function update(LayananGeneralRequest $request, $params){
        $data = array();
        try {
            $data = LayananGeneral::where('uuid',$params)->first();
            $data->id_satuan_kerja = $request->id_satuan_kerja;
            $data->id_pegawai = $request->id_pegawai;
            $data->id_jenis_layanan = $request->jenis_layanan;
            $data->keterangan = $request->keterangan;
            $data->status = $request->status_general;

            if (isset($request->dokumen)) {
                $file_konten = $request->file('dokumen');
                 $filePath = Storage::disk('sftp')->put('/sftpasn/layanan-general', $file_konten);
                $data->dokumen =  $filePath;
            }

            if (isset($request->dokumen_pendukung)) {
                $file_konten = $request->file('dokumen_pendukung');
                 $filePath = Storage::disk('sftp')->put('/sftpasn/layanan-general', $file_konten);
                $data->dokumen_pendukung =  $filePath;
            }

            $data->save();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Layanan Update success');
    }

    public function show($params){
       $data = array();
        try {
            $data = LayananGeneral::where('uuid',$params)->first();
            $data->status_general = $data->status;
            $data->jenis_layanan = $data->id_jenis_layanan;
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Layanan Update success'); 
    }

    public function delete(Request $request, $params){
        $data = array();
        try {
            // $data =  DB::table('tb_layanan')->where('uuid', $params)->delete();

            // Ambil data pengumuman yang akan dihapus
            $layanan = DB::table('tb_layanan_general')->where('uuid', $params)->first();

            if ($layanan) {
                // Hapus file gambar dari storage jika ada
                if ($layanan->icon) {
                    Storage::disk('sftp')->delete($layanan->icon);
                }

                // Hapus data pengumuman dari database
                DB::table('tb_layanan_general')->where('uuid', $params)->delete();
            } else {
                return $this->sendError('Layanan not found', 'Layanan not found', 404);
            }

            return $this->sendResponse([], 'Layanan Delete success');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Layanan Delete success');
    }
}
