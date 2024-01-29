<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Requests\userRequest;
use App\Http\Requests\userUpdateRequest;
use App\Models\User;
use DB;
use App\Traits\General;
use Hash;
class UserController extends BaseController
{
    use General;
    public function breadcumb(){
        return [
            [
                'label' => 'User',
                'url' => '#'
            ],
        ];
    }

    public function datatable(){
        $satuan_kerja = intval(request('satuan_kerja'));
        $data = array();
        
        $query = DB::table('users')
        ->LeftJoin('tb_pegawai','users.id_pegawai','=','tb_pegawai.id')
        ->LeftJoin('tb_jabatan', 'tb_jabatan.id_pegawai', 'tb_pegawai.id')
        ->LeftJoin('tb_master_jabatan', 'tb_jabatan.id_master_jabatan', 'tb_master_jabatan.id')
        ->LeftJoin('tb_satuan_kerja','tb_pegawai.id_satuan_kerja','=','tb_satuan_kerja.id')
        ->select('users.id','users.uuid','users.username','users.role','tb_pegawai.nama','tb_satuan_kerja.nama_satuan_kerja')
        ->orderBy('tb_satuan_kerja.kode_satuan_kerja', 'DESC')
        ->orderBy('tb_master_jabatan.kelas_jabatan', 'ASC')
        ->orderBy('tb_jabatan.id', 'ASC');
        
        if ($satuan_kerja > 0) {
           $query->where('tb_jabatan.id_unit_kerja',$satuan_kerja);
        }
        $data = $query->get();
        return $this->sendResponse($data, 'User Fetched Success');
    }

    public function index(){
        $module = $this->breadcumb();
        $satuan_kerja = $this->option_unit_kerja();
        return view('admin_kabupaten.akun.user',compact('module','satuan_kerja'));
    }

    public function reset(Request $request){
        $data = array();
        try {
            $data = User::where('uuid',$request->uuid)->first();
            $data->password = Hash::make('dangkemaspul');
            $data->save();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'User Password Reset success');
    }

    public function store(userRequest $request){
        $data = array();
        try {
            $data = new User();
            $data->id_pegawai = $request->id_pegawai;
            $data->username = $request->username;
            $data->password = Hash::make($request->password);
            $data->role = $request->role;
            $data->status = $request->status_user;
            $data->save();
        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'User Added success');
    }

    public function update(userUpdateRequest $request, $params){
        $data = array();
        try {
            // dd($request->all());
            // password_confirmation
            $data = User::where('uuid',$params)->first();
            $data->id_pegawai = $request->id_pegawai;
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
            $data = DB::table('users')
            ->join('tb_pegawai','users.id_pegawai','=','tb_pegawai.id')
            ->join('tb_satuan_kerja','tb_pegawai.id_satuan_kerja','=','tb_satuan_kerja.id')
            ->join('tb_unit_kerja','tb_unit_kerja.id_satuan_kerja','=','tb_satuan_kerja.id')
            ->select('users.id','users.uuid','users.id_pegawai','users.username','users.role','users.status as status_user','tb_satuan_kerja.id as id_satuan_kerja','tb_unit_kerja.id as id_unit_kerja')
            ->where('users.uuid',$params)
            ->first();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'User Update success'); 
    }

    public function delete(Request $request, $params){
        $data = array();
        try {
            $data =  DB::table('users')->where('uuid', $params)->delete();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'User Delete success');
    }
}
