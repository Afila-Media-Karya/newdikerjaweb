<?php

namespace App\Http\Controllers\Jabatan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Requests\MutasiRequest;
use App\Models\Mutasi;
use App\Models\Jabatan;
use App\Models\Pegawai;
use DB;
use App\Traits\General;

class MutasiController extends BaseController
{
    use General;
    public function breadcumb(){
        return [
            [
                'label' => 'Jabatan',
                'url' => '#'
            ],
            [
                'label' => 'Mutasi',
                'url' => '#'
            ],
        ];
    }

    public function datatable(){
        $data = array();
        $satuan_kerja = intval(request('satuan_kerja'));
        $query = DB::table('tb_mutasi')->join('tb_pegawai','tb_mutasi.id_pegawai','=','tb_pegawai.id')
        ->join('tb_jabatan as jabatan_lama','tb_mutasi.id_jabatan_lama','=','jabatan_lama.id')
        ->join('tb_master_jabatan as master_jabatan_lama','jabatan_lama.id_master_jabatan','=','master_jabatan_lama.id')
        ->join('tb_jabatan as jabatan_baru','tb_mutasi.id_jabatan_baru','=','jabatan_baru.id')
        ->join('tb_master_jabatan as master_jabatan_baru','jabatan_baru.id_master_jabatan','=','master_jabatan_baru.id')
        ->select('tb_mutasi.id','tb_mutasi.uuid','tb_pegawai.nip','tb_pegawai.nama','master_jabatan_lama.nama_jabatan as jabatan_lama','master_jabatan_baru.nama_jabatan as jabatan_baru','jabatan_lama.status as status_jabatan_lama','jabatan_baru.status as status_jabatan_baru')
        ->where('tb_mutasi.tahun',session('tahun_penganggaran'));

        if ($satuan_kerja > 0) {
           $query->where('jabatan_lama.id_unit_kerja',$satuan_kerja);
        }

        $data = $query->get();
        return $this->sendResponse($data, 'Jabatan Fetched Success');
    }

    public function index(){
        $module = $this->breadcumb();
        $satuan_kerja = $this->option_satuan_kerja();
        $unit_kerja = $this->option_unit_kerja();
        return view('jabatan.mutasi',compact('module','satuan_kerja','unit_kerja'));
    }

    public function store(MutasiRequest $request){
         try {
            $data = array();
       
            DB::beginTransaction();

            $check_jabatan = $this->checkJabatanAll($request->id_pegawai);
            $jabatan_lama = 0;

            if (count($check_jabatan) > 0) {
                foreach ($check_jabatan as $key => $value) {

                    if ($value->status === 'definitif') {
                        $jabatan_lama = $value->id_jabatan;
                    }

                    $jabatan = Jabatan::where('id',$value->id_jabatan)->first();
                    if ($jabatan) {
                       $jabatan->id_pegawai = null;
                        $jabatan->save();   
                    }
                }
            }

            $data = new Mutasi();
            $data->id_satuan_kerja = $request->id_satuan_kerja;
            $data->id_pegawai = $request->id_pegawai;
            $data->id_satuan_kerja_baru = $request->id_satuan_kerja_baru;
            $data->id_jabatan_lama = $jabatan_lama;
            $data->id_jabatan_baru = $request->id_jabatan_baru;
            $data->tmt = $request->tmt;
            $data->tahun = date('Y');
            $data->save();

            $pegawai = Pegawai::where('id',$request->id_pegawai)->first();
            $pegawai->id_satuan_kerja = $data->id_satuan_kerja_baru;
            $pegawai->save();

            $jabatan_baru = Jabatan::where('id',$request->id_jabatan_baru)->first();
            $jabatan_baru->id_pegawai = $data->id_pegawai;
            $jabatan_baru->status = 'definitif';
            $jabatan_baru->save();


            DB::commit();
            // AMBIL JABATAN LAMA

            // JABATAN LAMA DI KOSONGKAN

            // JABATAN BARU TAMBAH
            
        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Jabatan Added success');
    }

    public function update(MutasiRequest $request, $params){
        $data = array();
        try {
            $data = Jabatan::where('uuid',$params)->first();
            $data->id_pegawai = $request->id_pegawai;
            $data->id_master_jabatan = $request->id_master_jabatan;
            $data->id_satuan_kerja = $request->id_satuan_kerja;
            $data->status = $request->status;
            $data->pembayaran = $request->pembayaran;
            $data->save();            
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Jabatan Update success');
    }

    public function show($params){
       $data = array();
        try {
            $data = Mutasi::where('uuid',$params)->first();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Jabatan Update success'); 
    }

    public function detail($params){
        $module =  [
            [
                'label' => 'Jabatan',
                'url' => '#'
            ],
            [
                'label' => 'Mutasi',
                'url' => '/jabatan/mutasi'
            ],
            [
                'label' => 'Detail Mutasi',
                'url' => '#'
            ],
        ];

        $data = DB::table('tb_mutasi')
        ->join('tb_pegawai','tb_mutasi.id_pegawai','tb_pegawai.id')
        ->join('tb_jabatan as jabatan_lama','tb_mutasi.id_jabatan_lama','=','jabatan_lama.id')
        ->join('tb_master_jabatan as master_jabatan_lama','jabatan_lama.id_master_jabatan','=','master_jabatan_lama.id')
        ->join('tb_jabatan as jabatan_baru','tb_mutasi.id_jabatan_baru','=','jabatan_baru.id')
        ->join('tb_master_jabatan as master_jabatan_baru','jabatan_baru.id_master_jabatan','=','master_jabatan_baru.id')
        ->join('tb_satuan_kerja as satuan_kerja_lama','tb_mutasi.id_satuan_kerja','=','satuan_kerja_lama.id')
        ->join('tb_satuan_kerja as satuan_kerja_baru','tb_mutasi.id_satuan_kerja_baru','=','satuan_kerja_baru.id')
        ->select('tb_pegawai.id','tb_pegawai.uuid','tb_pegawai.nip','tb_pegawai.nama','tb_pegawai.tempat_lahir','tb_pegawai.tanggal_lahir','tb_pegawai.jenis_kelamin','tb_pegawai.agama','tb_pegawai.status_perkawinan','tb_pegawai.tmt_pegawai','tb_pegawai.golongan','tb_pegawai.tmt_golongan','tb_pegawai.tmt_jabatan','tb_pegawai.pendidikan','tb_pegawai.pendidikan_lulus','tb_pegawai.pendidikan_struktural','tb_pegawai.pendidikan_struktural_lulus','tb_pegawai.id_satuan_kerja','master_jabatan_lama.nama_jabatan as jabatan_lama','master_jabatan_baru.nama_jabatan as jabatan_baru','tb_mutasi.tmt as tmt_mutasi','satuan_kerja_lama.nama_satuan_kerja as satuan_kerja_lama','satuan_kerja_baru.nama_satuan_kerja as satuan_kerja_baru')
        ->where('tb_mutasi.uuid',$params)
        ->first();

        return view('jabatan.detailmutasi',compact('module','data'));
    }

    public function delete(Request $request, $params){
        $data = array();
        try {
            $data =  DB::table('tb_mutasi')->where('uuid', $params)->delete();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Jabatan Delete success');
    }
}
