<?php

namespace App\Http\Controllers\layanan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Requests\LayananRequest;
use App\Models\Layanan;
use DB;
use Illuminate\Support\Facades\Storage;

class MasterLayananController extends BaseController
{
    public function breadcumb(){
        return [
            [
                'label' => 'Layanan',
                'url' => '#'
            ],
            [
                'label' => 'Master Layanan',
                'url' => '#'
            ],
        ];
    }

    public function datatable(){
        $data = DB::table('tb_layanan')->select('id','uuid','icon','nama','url','keterangan')->where('status',1)->get();
        return $this->sendResponse($data, 'Master Layanan Fetched Success');
    }

    public function index(){
        $module = $this->breadcumb();
        return view('admin_kabupaten.layanan.masterlayanan',compact('module'));
    }

    public function store(LayananRequest $request){
        $data = array();
        try {
            $data = new Layanan();
            $data->icon = $request->icon;
            $data->nama = $request->nama;
            $data->url = $request->url;
            $data->status = $request->status;
            $data->keterangan = $request->keterangan;
            if (isset($request->gambar)) {
                $file_konten = $request->file('gambar');
                 $filePath = Storage::disk('sftp')->put('/sftpasn/layanan', $file_konten);
                $data->icon =  $filePath;
            }
            $data->save();
        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Layanan Added success');
    }

    public function update(LayananRequest $request, $params){
        $data = array();
        try {
            $data = Layanan::where('uuid',$params)->first();
            $data->icon = $request->icon;
            $data->nama = $request->nama;
            $data->url = $request->url;
            $data->status = $request->status;
            $data->keterangan = $request->keterangan;
            if (isset($request->gambar)) {
                $file_konten = $request->file('gambar');
                $filePath = Storage::disk('sftp')->put('/sftpasn/layanan', $file_konten);
                $data->icon =  $filePath;
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
            $data = Layanan::where('uuid',$params)->first();
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
            $layanan = DB::table('tb_layanan')->where('uuid', $params)->first();

            if ($layanan) {
                // Hapus file gambar dari storage jika ada
                if ($layanan->icon) {
                    Storage::disk('sftp')->delete($layanan->icon);
                }

                // Hapus data pengumuman dari database
                DB::table('tb_layanan')->where('uuid', $params)->delete();
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
