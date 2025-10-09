<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VersionApp;
use App\Http\Requests\VersiAppRequest;
use App\Http\Controllers\BaseController as BaseController;
use DB;
class VersionAppController extends BaseController
{
    public function breadcumb(){
        return [
            [
                'label' => 'Versi Aplikasi',
                'url' => '#'
            ],
        ];
    }

    public function datatable(){
        $data = DB::table('tb_version_app')->get();
        return $this->sendResponse($data, 'Version App Fetched Success');
    }

    public function index(){
        $module = $this->breadcumb();
        return view('admin_kabupaten.versi_app.index',compact('module'));
    }

    public function store(VersiAppRequest $request){
        $data = array();
        try {
            $data = new VersionApp();
            $data->version = $request->version;
            $data->status = $request->status;
            $data->save();
        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Version App Added success');
    }

    public function update(VersiAppRequest $request, $params){
        $data = array();
        try {
            $data = VersionApp::where('uuid',$params)->first();
            $data->version = $request->version;
            $data->status = $request->status;
            $data->save();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Version App Update success');
    }

    public function show($params){
       $data = array();
        try {
            $data = VersionApp::where('uuid',$params)->first();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Version App Update success'); 
    }

    public function delete(Request $request, $params){
        $data = array();
        try {
            $data =  DB::table('tb_version_app')->where('uuid', $params)->delete();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Version App Delete success');
    }
}
