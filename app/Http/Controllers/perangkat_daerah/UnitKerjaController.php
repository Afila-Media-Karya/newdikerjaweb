<?php

namespace App\Http\Controllers\perangkat_daerah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Requests\UnitKerjaRequest;
use App\Models\UnitKerja;
use DB;
use App\Traits\General;

class UnitKerjaController extends BaseController
{
    use General;

    public function breadcumb(){
        return [
            [
                'label' => 'Perangkat Daerah',
                'url' => '#'
            ],
            [
                'label' => 'Unit Kerja',
                'url' => '#'
            ],
        ];
    }

    public function datatable(){
        $satuan_kerja = intval(request('satuan_kerja'));
        $data = array();
        $data = DB::table('tb_unit_kerja')
        ->select('tb_unit_kerja.id','tb_unit_kerja.uuid','tb_satuan_kerja.nama_satuan_kerja','tb_unit_kerja.nama_unit_kerja')
        ->join('tb_satuan_kerja','tb_unit_kerja.id_satuan_kerja','=','tb_satuan_kerja.id')
        ->orderBy('tb_unit_kerja.id','DESC')
        ->get();
        return $this->sendResponse($data, 'Unit Kerja Fetched Success');
    }

    public function index(){
        $module = $this->breadcumb();
        $satuan_kerja = $this->option_satuan_kerja();

        return view('admin_kabupaten.perangkat_daerah.unit_kerja',compact('module','satuan_kerja'));
    }

    public function store(UnitKerjaRequest $request){
        $pegawai = array();
        try {
                $data = new UnitKerja();
                $data->id_satuan_kerja = $request->id_satuan_kerja;
                $data->nama_unit_kerja = $request->nama_unit_kerja;
                $data->waktu_masuk = $request->waktu_masuk;
                $data->waktu_keluar = $request->waktu_keluar;
                $data->waktu_apel = $request->waktu_apel;
                $data->tahun = session('tahun_penganggaran');
                $data->save(); 

        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($pegawai, 'Unit Kerja Added success');
    }

    public function update(UnitKerjaRequest $request, $params) {
        $data = array();
        try {
            DB::beginTransaction();

                $data = UnitKerja::where('uuid', $params)
                ->first();
                $data->id_satuan_kerja = $request->id_satuan_kerja;
                $data->nama_unit_kerja = $request->nama_unit_kerja;
                $data->waktu_masuk = $request->waktu_masuk;
                $data->waktu_keluar = $request->waktu_keluar;
                $data->waktu_apel = $request->waktu_apel;
                $data->tahun = session('tahun_penganggaran');
                $data->save(); 

            DB::commit();

        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Unit Kerja Updated success');
    }

    public function show($params){
       $data = array();
        try {
            $data = DB::table('tb_unit_kerja')->where('uuid',$params)->first();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Unit Kerja Show success'); 
    }

    public function option(){
        $data = array();
        try {
            $params = request('satuan_kerja');
            $data = $this->option_by_unit_kerja($params);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Unit Kerja Option success'); 
    }

    public function delete(Request $request, $params){
        $data = array();
        try {
            DB::table('tb_unit_kerja')->where('uuid',$params)->delete();
           
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Unit Kerja Delete success');
    }
}
