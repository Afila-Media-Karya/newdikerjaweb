<?php

namespace App\Http\Controllers\master_data;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Requests\master_data\pendidikanRequest;
use App\Models\Pendidikan;
use DB;

class pendidikanController extends BaseController
{
    public function breadcumb(){
        return [
            [
                'label' => 'Master Data',
                'url' => '#'
            ],
            [
                'label' => 'Pendidikan',
                'url' => '#'
            ],
        ];
    }

    public function datatable(){
        $data = DB::table('tb_pendidikan')->select('id','uuid','kode','pendidikan','status')->get();
        return $this->sendResponse($data, 'Data Pendidikan Fetched Success');
    }

    public function index(){
        $module = $this->breadcumb();
        return view('admin_kabupaten.master_data.pendidikan',compact('module'));
    }

    public function store(pendidikanRequest $request){
        $data = array();
        try {
            $data = new Pendidikan();
            $data->pendidikan = $request->pendidikan;
            $data->status = $request->status;
            $data->save();
        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Pendidikan Added success');
    }

    public function update(pendidikanRequest $request, $params){
        $data = array();
        try {
            $data = Pendidikan::where('uuid',$params)->first();
            $data->pendidikan = $request->pendidikan;
            $data->status = $request->status;
            $data->save();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Pendidikan Update success');
    }

    public function show($params){
       $data = array();
        try {
            $data = Pendidikan::where('uuid',$params)->first();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Pendidikan Update success'); 
    }

    public function delete(Request $request, $params){
        $data = array();
        try {
            $data =  DB::table('tb_pendidikan')->where('uuid', $params)->delete();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Pendidikan Delete success');
    }
}
