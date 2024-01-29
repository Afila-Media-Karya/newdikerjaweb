<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Requests\HariLiburRequest;
use App\Models\HariLibur;
use DB;

class ProfilKepalaDaerahController extends BaseController
{
    public function breadcumb(){
        return [
            [
                'label' => 'Profil Kepala Daerah',
                'url' => '#'
            ],
        ];
    }

    public function index(){
        $module = $this->breadcumb();
        $data = DB::table('tb_profil_daerah')->first();
        return view('admin_kabupaten.profil_kepala_daerah.index',compact('module','data'));
    }
}
