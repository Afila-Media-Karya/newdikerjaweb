<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Requests\HariLiburRequest;
use App\Models\HariLibur;
use DB;

class HariLiburController extends BaseController
{
    public function breadcumb(){
        return [
            [
                'label' => 'Hari Libur',
                'url' => '#'
            ],
        ];
    }

    public function datatable(){
        $data = DB::table('tb_libur')->select('id','uuid','nama_libur','tanggal_mulai','tanggal_selesai')->where('tahun',session('tahun_penganggaran'))->get();
        return $this->sendResponse($data, 'Hari Libur Fetched Success');
    }

    public function index(){
        $module = $this->breadcumb();
        return view('admin_kabupaten.harilibur.index',compact('module'));
    }

    public function store(HariLiburRequest $request){
        $data = array();
        try {
            $data = new HariLibur();
            $data->nama_libur = $request->nama_libur;
            $data->tanggal_mulai = $request->tanggal_mulai;
            $data->tanggal_selesai = $request->tanggal_selesai;
            $data->tipe = $request->tipe;
            $data->tahun = date('Y');
            $data->save();
        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Hari Libur Added success');
    }

    public function update(HariLiburRequest $request, $params){
        $data = array();
        try {
            $data = HariLibur::where('uuid',$params)->first();
            $data->nama_libur = $request->nama_libur;
            $data->tanggal_mulai = $request->tanggal_mulai;
            $data->tanggal_selesai = $request->tanggal_selesai;
            $data->tipe = $request->tipe;
            $data->save();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Hari Libur Update success');
    }

    public function show($params){
       $data = array();
        try {
            $data = HariLibur::where('uuid',$params)->first();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Hari Libur Update success'); 
    }

    public function delete(Request $request, $params){
        $data = array();
        try {
            $data =  DB::table('tb_libur')->where('uuid', $params)->delete();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Hari Libur Delete success');
    }
}
