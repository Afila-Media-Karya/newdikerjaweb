<?php

namespace App\Http\Controllers\master_aktivitas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Requests\MasterAkvititasRequest;
use App\Models\MasterAkvititas;
use DB;
use App\Traits\General;
class MasterAktivitasController extends BaseController
{
    use General;
   public function breadcumb(){
        return [
            [
                'label' => 'Master Aktivitas',
                'url' => '#'
            ],
            [
                'label' => 'Master Aktivitas',
                'url' => '#'
            ],
        ];
    }

    public function datatable(){
        $data = DB::table('tb_master_aktivitas')->join('tb_kelompok_jabatan','tb_master_aktivitas.id_kelompok_jabatan','=','tb_kelompok_jabatan.id')->select('tb_master_aktivitas.id','tb_master_aktivitas.uuid','tb_master_aktivitas.aktivitas','tb_master_aktivitas.satuan','tb_master_aktivitas.waktu','tb_master_aktivitas.jenis','tb_kelompok_jabatan.kelompok')->get();
        return $this->sendResponse($data, 'Master Aktivitas Fetched Success');
    }

    public function index(){
        $module = $this->breadcumb();
        $satuan = $this->option_satuan();
        $kelompok_jabatan = $this->option_kelompok_jabatan_all();
        return view('admin_kabupaten.master_aktivitas.master_aktivitas',compact('module','satuan','kelompok_jabatan'));
    }

    public function store(MasterAkvititasRequest $request){
        $data = array();
        try {
            $data = new MasterAkvititas();
            $data->aktivitas = $request->aktivitas;
            $data->satuan = $request->satuan;
            $data->waktu = $request->waktu;
            $data->jenis = $request->jenis;
            $data->id_kelompok_jabatan = $request->id_kelompok_jabatan;
            $data->save();
        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Master Aktivitas Added success');
    }

    public function update(MasterAkvititasRequest $request, $params){
        $data = array();
        try {
            $data = MasterAkvititas::where('uuid',$params)->first();
            $data->aktivitas = $request->aktivitas;
            $data->satuan = $request->satuan;
            $data->waktu = $request->waktu;
            $data->jenis = $request->jenis;
            $data->id_kelompok_jabatan = $request->id_kelompok_jabatan;
            $data->save();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Master Aktivitas Update success');
    }

    public function show($params){
       $data = array();
        try {
            $data = MasterAkvititas::where('uuid',$params)->first();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Master Aktivitas Update success'); 
    }

    public function delete(Request $request, $params){
        $data = array();
        try {
            $data =  DB::table('tb_master_aktivitas')->where('uuid', $params)->delete();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Master Aktivitas Delete success');
    } 
}
