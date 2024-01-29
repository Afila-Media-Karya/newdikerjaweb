<?php

namespace App\Http\Controllers\pegawai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Requests\pegawaikeluarRequest;
use App\Models\Pegawai;
use App\Models\PegawaiKeluar;
use App\Models\Jabatan;
use DB;
use App\Traits\General;
class pegawaiKeluarController extends BaseController
{
    use General;

    public function breadcumb(){
        return [
            [
                'label' => 'Pegawai',
                'url' => '#'
            ],
            [
                'label' => 'Pegawai Keluar',
                'url' => '#'
            ],
        ];
    }

    public function datatable(){
        $satuan_kerja = intval(request('satuan_kerja'));
        $data = array();
        $data = DB::table('tb_pegawai_keluar')->join('tb_pegawai','tb_pegawai_keluar.id_pegawai','=','tb_pegawai.id')
        ->select('tb_pegawai_keluar.id','tb_pegawai.id as id_pegawai','tb_pegawai.uuid as uuid_pegawai','tb_pegawai_keluar.uuid','tb_pegawai.nip','tb_pegawai.nama','tb_pegawai_keluar.tujuan_daerah')
        ->where('tb_pegawai_keluar.tahun',session('tahun_penganggaran'))
        ->get();
        return $this->sendResponse($data, 'Data Pegawai Fetched Success');
    }

    public function index(){
        $module = $this->breadcumb();
        $satuan_kerja = $this->option_satuan_kerja();

        return view('pegawai.pegawai_keluar.index',compact('module','satuan_kerja'));
    }

    public function store(pegawaikeluarRequest $request){
        $pegawai = array();
        try {

            $pegawai_tmp = DB::table('tb_pegawai')->where('id',$request->id_pegawai)->first();
            $jabatan_terakhir = null;
            if ($this->checkJabatan($pegawai_tmp->uuid)) {
                $jabatan_terakhir = $this->checkJabatan($pegawai_tmp->uuid)->id_jabatan;
            }
            
                DB::beginTransaction();

                $pegawai = new PegawaiKeluar();
                $pegawai->tujuan_daerah = $request->tujuan_daerah;
                $pegawai->id_satuan_kerja = $request->id_satuan_kerja;
                $pegawai->id_unit_kerja = $request->id_unit_kerja;
                $pegawai->id_pegawai = $request->id_pegawai;
                $pegawai->tmt = $request->tmt;
                $pegawai->id_jabatan_terakhir = $jabatan_terakhir;
                $pegawai->user_insert = hasRole()['id'];
                $pegawai->tahun = date('Y');
                $pegawai->save();
                
                $pegawai_list = Pegawai::where('id',$pegawai->id_pegawai)->first();
                $pegawai_list->status = '3';
                $pegawai_list->save();

                 if ($this->checkJabatan($pegawai_tmp->uuid)) {
                    $jabatan = Jabatan::where('id_pegawai',$pegawai->id_pegawai)->first();
                    $jabatan->id_pegawai = null;
                    $jabatan->save();
                }

                DB::commit();

        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($pegawai, 'Pegawai Added success');
    }

    public function update(pegawaikeluarRequest $request, $params) {
        try {
            DB::beginTransaction();

                $pegawai = PegawaiKeluar::where('uuid', $params)
                ->where('tahun', session('tahun_penganggaran'))
                ->first();
                $pegawai->tujuan_daerah = $request->tujuan_daerah;
                $pegawai->id_satuan_kerja = $request->id_satuan_kerja;
                $pegawai->id_unit_kerja = $request->id_unit_kerja;
                $pegawai->id_pegawai = $request->id_pegawai;
                $pegawai->tmt = $request->tmt;
                $pegawai->user_insert = hasRole()['id'];
                $pegawai->save();

            DB::commit();

        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($pegawai, 'Pegawai Updated success');
    }

    public function show($params){
       $data = array();
        try {
            $data = DB::table('tb_pegawai_keluar')->where('uuid',$params)->first();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Pegawai Show success'); 
    }

    public function detail($params){
        $module =  [
            [
                'label' => 'Pegawai',
                'url' => '#'
            ],
            [
                'label' => 'Pegawai Masuk',
                'url' => '/pegawai/pegawai-masuk'
            ],
            [
                'label' => 'Detail Pegawai',
                'url' => '#'
            ],
        ];
        
        $data = DB::table('tb_pegawai_keluar')
        ->Leftjoin('tb_pegawai','tb_pegawai_keluar.id_pegawai','=','tb_pegawai.id')
        ->Leftjoin('tb_satuan_kerja','tb_pegawai.id_satuan_kerja','=','tb_satuan_kerja.id')
        ->Leftjoin('tb_jabatan','tb_jabatan.id_pegawai','=','tb_pegawai.id')
        ->select('tb_pegawai.id','tb_pegawai.uuid','tb_pegawai.nip','tb_pegawai.nama','tb_pegawai.tempat_lahir','tb_pegawai.tanggal_lahir','tb_pegawai.jenis_kelamin','tb_pegawai.agama','tb_pegawai.status_perkawinan','tb_pegawai.tmt_pegawai','tb_pegawai.golongan','tb_pegawai.tmt_golongan','tb_pegawai.tmt_jabatan','tb_pegawai.pendidikan','tb_pegawai.pendidikan_lulus','tb_pegawai.pendidikan_struktural','tb_pegawai.pendidikan_struktural_lulus','tb_pegawai.id_satuan_kerja','tb_pegawai_keluar.tujuan_daerah','tb_pegawai_keluar.id_jabatan_terakhir','tb_pegawai_keluar.tmt')
        ->where('tb_pegawai.uuid',$params)
        ->first();

        $jabatan_terakhir = DB::table('tb_master_jabatan')->select('nama_jabatan')->where('id',$data->id_jabatan_terakhir)->first();
        $data->nama_jabatan = !is_null($jabatan_terakhir) ? $jabatan_terakhir->nama_jabatan : '-';
      
        return view('pegawai.pegawai_keluar.detail',compact('data','module'));
    }

    public function delete(Request $request, $params)
    {
        try {
           
            DB::table('tb_pegawai_keluar')->where('uuid',$params)->delete();
            
            return $this->sendResponse([], 'Pegawai Delete success');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Error deleting employee.', $e->getMessage(), 500);
        }
    }

}
