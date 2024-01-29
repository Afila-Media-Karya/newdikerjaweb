<?php

namespace App\Http\Controllers\master_jabatan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Requests\pegawaiRequest;
use App\Models\JenisJabatan;
use DB;
use App\Traits\General;

class JenisJabatanController extends BaseController
{

    public function breadcumb(){
        return [
            [
                'label' => 'Master Jabatan',
                'url' => '#'
            ],
            [
                'label' => 'Jenis Jabatan',
                'url' => '#'
            ],
        ];
    }

    public function datatable(){
        $data = array();
        $data = DB::table('tb_jenis_jabatan')->select('id','uuid','level','jenis_jabatan','kelas_jabatan')->get();
        return $this->sendResponse($data, 'Data Jenis Jabatan Fetched Success');
    }

    public function index(){
        $module = $this->breadcumb();
        return view('admin_kabupaten.master_jabatan.jenisjabatan',compact('module'));
    }
}
