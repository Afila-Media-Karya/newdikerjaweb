<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Requests\PengumumanRequest;
use App\Models\Pengumuman;
use DB;
use App\Traits\General;
use Illuminate\Support\Facades\Storage;

class PengumumanController extends BaseController
{
    use General;

    public function breadcumb(){
        return [
            [
                'label' => 'Pengumuman',
                'url' => '#'
            ],
        ];
    }

    public function datatable(){
        $data = DB::table('tb_pengumuman')->select('id','uuid','judul','deskripsi','tanggal')->get();
        return $this->sendResponse($data, 'Pengumuman Fetched Success');
    }

    public function index(){
        $module = $this->breadcumb();
        return view('admin_kabupaten.pengumuman.index',compact('module'));
    }

    public function store(PengumumanRequest $request){
        $data = array();
        // dd($request->all());
        try {
            $level = '2';

            if(hasRole()['guard'] == 'web'){
                $level = '1';
            }

            $data = new Pengumuman();
            $data->judul = $request->judul;
            $data->deskripsi = $request->deskripsi;
            $data->tanggal = $request->tanggal;
            $data->level = $level;
            if (isset($request->gambar)) {
                $file_konten = $request->file('gambar');
                $filePath = Storage::disk('sftp')->put('/sftpasn/pengumuman', $file_konten);
                $data->gambar =  $filePath;
            }
            $data->save();
        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Pengumuman Added success');
    }

    public function update(PengumumanRequest $request, $params){
        $data = array();
        try {

            $level = '2';

            if(hasRole()['guard'] == 'web'){
                $level = '1';
            }

            $data = Pengumuman::where('uuid',$params)->first();
            $data->judul = $request->judul;
            $data->deskripsi = $request->deskripsi;
            $data->tanggal = $request->tanggal;
            $data->level = $level;
            if (isset($request->gambar)) {
                $file_konten = $request->file('gambar');
                $filePath = Storage::disk('sftp')->put('/sftpasn/pengumuman', $file_konten);
                $data->gambar =  $filePath;
            }
            $data->save();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Pengumuman Update success');
    }

    public function show($params){
       $data = array();
        try {
            $data = Pengumuman::where('uuid',$params)->first();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Pengumuman Update success'); 
    }

    public function delete(Request $request, $params)
    {
        try {
            // Ambil data pengumuman yang akan dihapus
            $pengumuman = DB::table('tb_pengumuman')->where('uuid', $params)->first();

            if ($pengumuman) {
                // Hapus file gambar dari storage jika ada
                if ($pengumuman->gambar) {
                    Storage::disk('sftp')->delete($pengumuman->gambar);
                }

                // Hapus data pengumuman dari database
                DB::table('tb_pengumuman')->where('uuid', $params)->delete();
            } else {
                return $this->sendError('Pengumuman not found', 'Pengumuman not found', 404);
            }

            return $this->sendResponse([], 'Pengumuman Delete success');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
    }

}
