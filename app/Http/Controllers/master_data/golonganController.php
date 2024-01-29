<?php

namespace App\Http\Controllers\master_data;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Requests\master_data\golonganRequest;
use App\Models\Golongan;
use DB;
class golonganController extends BaseController
{
    public function breadcumb(){
        return [
            [
                'label' => 'Master Data',
                'url' => '#'
            ],
            [
                'label' => 'Golongan',
                'url' => '#'
            ],
        ];
    }

    public function datatable(){
        $data = DB::table('tb_golongan')->select('id','uuid','kode','golongan','status')->orderBy('id','DESC')->get();
        return $this->sendResponse($data, 'Data Golongan Fetched Success');
    }

    public function index(){
        $module = $this->breadcumb();
        return view('admin_kabupaten.master_data.golongan',compact('module'));
    }

    public function store(golonganRequest $request){
        $data = array();
        try {
            $data = new Golongan();
            $data->golongan = $request->golongan;
            $data->status = $request->status;
            $data->save();
        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Golongan Added success');
    }

    public function update(golonganRequest $request, $params){
        $data = array();
        try {
            $data = Golongan::where('uuid',$params)->first();
            $data->golongan = $request->golongan;
            $data->status = $request->status;
            $data->save();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Golongan Update success');
    }

    public function show($params){
       $data = array();
        try {
            $data = Golongan::where('uuid',$params)->first();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Golongan Update success'); 
    }

    public function delete(Request $request, $params){
        $data = array();
        try {
            $data =  DB::table('tb_golongan')->where('uuid', $params)->delete();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Golongan Delete success');
    }
}
