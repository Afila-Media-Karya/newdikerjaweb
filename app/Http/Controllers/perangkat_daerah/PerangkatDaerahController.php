<?php

namespace App\Http\Controllers\perangkat_daerah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Requests\perangkatdaerahRequest;
use App\Models\PerangkatDaerah;
use DB;
use App\Traits\General;

class PerangkatDaerahController extends BaseController
{
    use General;

    public function breadcumb(){
        return [
            [
                'label' => 'Perangkat Daerah',
                'url' => '#'
            ],
            [
                'label' => 'Perangkat Daerah',
                'url' => '#'
            ],
        ];
    }

    public function datatable(){
        $satuan_kerja = intval(request('satuan_kerja'));
        $data = array();
        $data = DB::table('tb_satuan_kerja')->select('id','uuid','inisial_satuan_kerja','nama_satuan_kerja')->orderBy('tb_satuan_kerja.id','DESC')->get();
        return $this->sendResponse($data, 'Data Pegawai Pensiun Fetched Success');
    }

    public function index(){
        $module = $this->breadcumb();
        $satuan_kerja = $this->option_satuan_kerja();

        return view('admin_kabupaten.perangkat_daerah.perangkat_daerah',compact('module','satuan_kerja'));
    }

    public function store(perangkatdaerahRequest $request){
        $pegawai = array();
        try {

                $data = new PerangkatDaerah();
                $data->nama_satuan_kerja = $request->nama_satuan_kerja;
                $data->inisial_satuan_kerja = $request->inisial_satuan_kerja;
                $data->tahun = session('tahun_penganggaran');
                $data->save(); 

        

        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($pegawai, 'Pegawai Added success');
    }

    public function update(perangkatdaerahRequest $request, $params) {
        $data = array();
        try {
            DB::beginTransaction();

                $data = PerangkatDaerah::where('uuid', $params)
                ->first();
                $data->nama_satuan_kerja = $request->nama_satuan_kerja;
                $data->inisial_satuan_kerja = $request->inisial_satuan_kerja;
                $data->tahun = session('tahun_penganggaran');
                $data->save(); 

            DB::commit();

        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Pegawai Updated success');
    }

    public function show($params){
       $data = array();
        try {
            $data = DB::table('tb_satuan_kerja')->where('uuid',$params)->first();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Pegawai Show success'); 
    }

    public function delete(Request $request, $params){
        $data = array();
        try {
            DB::table('tb_satuan_kerja')->where('uuid',$params)->delete();
           
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Pegawai Delete success');
    }
}
