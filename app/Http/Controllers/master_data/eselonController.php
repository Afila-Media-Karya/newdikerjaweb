<?php

namespace App\Http\Controllers\master_data;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Requests\master_data\eselonRequest;
use App\Models\Eselon;
use DB;

class eselonController extends BaseController
{
    public function breadcumb(){
        return [
            [
                'label' => 'Master Data',
                'url' => '#'
            ],
            [
                'label' => 'Eselon',
                'url' => '#'
            ],
        ];
    }

    public function datatable(){
        $data = DB::table('tb_eselon')->select('id','uuid','eselon','status')->orderBy('id','DESC')->get();
        return $this->sendResponse($data, 'Data Indikator Pelayanan Fetched Success');
    }

    public function index(){
        $module = $this->breadcumb();
        return view('admin_kabupaten.master_data.eselon',compact('module'));
    }

    public function store(eselonRequest $request){
        $data = array();
        try {
            $data = new Eselon();
            $data->eselon = $request->eselon;
            $data->status = $request->status;
            $data->save();
        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Eselon Added success');
    }

    public function update(eselonRequest $request, $params){
        $data = array();
        try {
            $data = Eselon::where('uuid',$params)->first();
            $data->eselon = $request->eselon;
            $data->status = $request->status;
            $data->save();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Eselon Update success');
    }

    public function show($params){
       $data = array();
        try {
            $data = Eselon::where('uuid',$params)->first();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Eselon Update success'); 
    }

    public function delete(Request $request, $params){
        $data = array();
        try {
            $data =  DB::table('tb_eselon')->where('uuid', $params)->delete();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Eselon Delete success');
    }
}
