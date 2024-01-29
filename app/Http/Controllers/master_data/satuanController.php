<?php

namespace App\Http\Controllers\master_data;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Requests\master_data\satuanRequest;
use App\Models\Satuan;
use DB;

class satuanController extends BaseController
{
    public function breadcumb(){
        return [
            [
                'label' => 'Master Data',
                'url' => '#'
            ],
            [
                'label' => 'Satuan',
                'url' => '#'
            ],
        ];
    }

    public function datatable(){
        $data = DB::table('tb_satuan')->select('id','uuid','kode','satuan','status')->get();
        return $this->sendResponse($data, 'Data Satuan Fetched Success');
    }

    public function index(){
        $module = $this->breadcumb();
        return view('admin_kabupaten.master_data.satuan',compact('module'));
    }

    public function store(satuanRequest $request){
        $data = array();
        try {
            $data = new Satuan();
            $data->satuan = $request->satuan;
            $data->status = $request->status;
            $data->save();
        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Satuan Added success');
    }

    public function update(satuanRequest $request, $params){
        $data = array();
        try {
            $data = Satuan::where('uuid',$params)->first();
            $data->satuan = $request->satuan;
            $data->status = $request->status;
            $data->save();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'satuan Update success');
    }

    public function show($params){
       $data = array();
        try {
            $data = Satuan::where('uuid',$params)->first();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'satuan Update success'); 
    }

    public function delete(Request $request, $params){
        $data = array();
        try {
            $data =  DB::table('tb_satuan')->where('uuid', $params)->delete();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'satuan Delete success');
    }
}
