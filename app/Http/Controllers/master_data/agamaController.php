<?php

namespace App\Http\Controllers\master_data;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Requests\master_data\agamaRequest;
use App\Models\Agama;
use DB;
class agamaController extends BaseController
{

    public function breadcumb(){
        return [
            [
                'label' => 'Master Data',
                'url' => '#'
            ],
            [
                'label' => 'Agama',
                'url' => '#'
            ],
        ];
    }

    public function datatable(){
        $data = DB::table('tb_agama')->select('id','uuid','agama','status')->orderBy('id','DESC')->get();
        return $this->sendResponse($data, 'Data Indikator Pelayanan Fetched Success');
    }

    public function index(){
        $module = $this->breadcumb();
        return view('admin_kabupaten.master_data.agama',compact('module'));
    }

    public function store(agamaRequest $request){
        $data = array();
        try {
            $data = new Agama();
            $data->agama = $request->agama;
            $data->status = $request->status;
            $data->save();
        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Agama Added success');
    }

    public function update(agamaRequest $request, $params){
        $data = array();
        try {
            $data = Agama::where('uuid',$params)->first();
            $data->agama = $request->agama;
            $data->status = $request->status;
            $data->save();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Agama Update success');
    }

    public function show($params){
       $data = array();
        try {
            $data = Agama::where('uuid',$params)->first();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Agama Update success'); 
    }

    public function delete(Request $request, $params){
        $data = array();
        try {
            $data =  DB::table('tb_agama')->where('uuid', $params)->delete();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Agama Delete success');
    }

}
