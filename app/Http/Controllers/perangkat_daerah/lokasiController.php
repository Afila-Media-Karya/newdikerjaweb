<?php

namespace App\Http\Controllers\perangkat_daerah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Requests\LokasiRequest;
use App\Models\Lokasi;
use DB;
use App\Traits\General;

class lokasiController extends BaseController
{
    use General;

    public function breadcumb(){
        return [
            [
                'label' => 'Perangkat Daerah',
                'url' => '#'
            ],
            [
                'label' => 'Lokasi Kerja',
                'url' => '#'
            ],
        ];
    }

    public function optionLokasiSatuanKerja($params){
        $data = array();
        try {
            $data = $this->option_lokasi_satuan_kerja($params);
        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Lokasi Kerja option success');
    }

    public function optionLokasiApel($params){
        $data = array();
        try {
            $data = DB::table('tb_lokasi')->select('id','nama_lokasi as text')
            ->where('id_satuan_kerja',$params)
            ->union(
                DB::table('tb_lokasi')
                    ->select('id', 'nama_lokasi as text')
                    ->where('nama_lokasi', 'Kantor Sekretariat Daerah')
            )
            ->get(); 
        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Lokasi Apel option success');
    }

    public function datatable(){
        $data = array();
        $data = DB::table('tb_lokasi')->join('tb_satuan_kerja','tb_lokasi.id_satuan_kerja','=','tb_satuan_kerja.id')->select('tb_lokasi.id','tb_lokasi.uuid','tb_lokasi.nama_lokasi','tb_satuan_kerja.nama_satuan_kerja')->get();
        return $this->sendResponse($data, 'Data Pegawai Pensiun Fetched Success');
    }

    public function index(){
        $module = $this->breadcumb();
        $satuan_kerja = $this->option_satuan_kerja();
        return view('admin_kabupaten.perangkat_daerah.lokasi',compact('module','satuan_kerja'));
    }

    public function store(LokasiRequest $request){
        $pegawai = array();
        try {

                $data = new Lokasi();
                $data->id_satuan_kerja = $request->id_satuan_kerja;
                $data->id_unit_kerja = $request->id_unit_kerja;
                $data->nama_lokasi = $request->nama_lokasi;
                $data->longitude = $request->longitude;
                $data->latitude = $request->latitude;
                $data->radius = $request->radius;
                $data->save(); 

        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($pegawai, 'Pegawai Added success');
    }

    public function update(LokasiRequest $request, $params) {
        $data = array();
        try {

                $data = Lokasi::where('uuid', $params)
                ->first();
                $data->id_satuan_kerja = $request->id_satuan_kerja;
                $data->id_unit_kerja = $request->id_unit_kerja;
                $data->nama_lokasi = $request->nama_lokasi;
                $data->longitude = $request->longitude;
                $data->latitude = $request->latitude;
                $data->radius = $request->radius;
                $data->save();

        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Pegawai Updated success');
    }

    public function show($params){
       $data = array();
        try {
            $data = DB::table('tb_lokasi')->where('uuid',$params)->first();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Pegawai Show success'); 
    }

    public function delete(Request $request, $params){
        $data = array();
        try {
            DB::table('tb_lokasi')->where('uuid',$params)->delete();
           
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Pegawai Delete success');
    }
}
