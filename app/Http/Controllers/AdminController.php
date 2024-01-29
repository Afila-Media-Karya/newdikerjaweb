<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Requests\adminRequest;
use App\Models\Admin;
use DB;
use App\Traits\General;
use Hash;
class AdminController extends BaseController
{
    use General;
    public function breadcumb(){
        return [
            [
                'label' => 'Admin',
                'url' => '#'
            ],
        ];
    }

    public function datatable(){
        $data = DB::table('admin')->select('id','uuid','username','role','status')->get();
        return $this->sendResponse($data, 'Admin Fetched Success');
    }

    public function index(){
        $module = $this->breadcumb();
        return view('admin_kabupaten.akun.admin',compact('module'));
    }

    public function store(adminRequest $request){
        $data = array();
        try {
            $data = new Admin();
            $data->username = $request->username;
            $data->password = Hash::make($request->password);
            $data->role = $request->role;
            $data->status = $request->status_user;
            $data->save();
        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Admin Added success');
    }

    public function update(adminRequest $request, $params){
        $data = array();
        try {
            $data = Admin::where('uuid',$params)->first();
            $data->username = $request->username;
            if (isset($request->password)) {
               $data->password = Hash::make($request->password);
            }
            $data->role = $request->role;
            $data->status = $request->status_user;
            $data->save();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'User Update success');
    }

    public function show($params){
       $data = array();
        try {
            $data = DB::table('admin')
            ->select('id','uuid','username','password','role','status as status_user')
            ->where('uuid',$params)
            ->first();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Admin Update success'); 
    }

    public function delete(Request $request, $params){
        $data = array();
        try {
            $data =  DB::table('admin')->where('uuid', $params)->delete();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Admin Delete success');
    }
}
