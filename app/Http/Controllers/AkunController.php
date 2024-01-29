<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Requests\AkunRequest;
use App\Models\User;
use Auth;
use DB;
use Hash;
class AkunController extends BaseController
{
    public function breadcumb(){
        return [
            [
                'label' => 'Akun',
                'url' => '#'
            ],
        ];
    }    

    public function index(){
        $module = $this->breadcumb();
        $account = DB::table('users')->join('tb_pegawai','users.id_pegawai','=','tb_pegawai.id')->select('users.id','users.uuid','users.username','tb_pegawai.nama','tb_pegawai.nip')->where('users.id',Auth::user()->id)->first();
        return view('akun.index',compact('module','account'));
    }

    public function changePassword(AkunRequest $request){
        $data = array();
        try {
            if (!Hash::check($request->input('password_lama'), $request->user()->password)) {
                return $this->sendError('Password lama tidak cocok', 'Gagal', 200);
            }

            $data = User::where('id',Auth::user()->id)->first();
            $data->password = Hash::make($request->password_baru_ulang);
            $data->save();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'User change password success');
    }
}
