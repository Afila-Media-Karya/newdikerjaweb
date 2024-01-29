<?php

namespace App\Http\Controllers\pegawai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Requests\pegawaimasukRequest;
use App\Http\Requests\pegawaimasukUpdateRequest;
use App\Models\Pegawai;
use App\Models\PegawaiMasuk;
use App\Models\Jabatan;
use DB;
use App\Traits\General;
class pegawaiMasukController extends BaseController
{
    use General;

    public function breadcumb(){
        return [
            [
                'label' => 'Pegawai',
                'url' => '#'
            ],
            [
                'label' => 'Pegawai Masuk',
                'url' => '#'
            ],
        ];
    }

    public function datatable(){
        $satuan_kerja = intval(request('satuan_kerja'));

        $data = array();
        $data = DB::table('tb_pegawai_masuk')->join('tb_pegawai','tb_pegawai_masuk.id_pegawai','=','tb_pegawai.id')
        ->join('tb_satuan_kerja','tb_pegawai.id_satuan_kerja','=','tb_satuan_kerja.id')
        ->join('tb_jabatan','tb_jabatan.id_pegawai','=','tb_pegawai.id')
        ->join('tb_master_jabatan','tb_jabatan.id_master_jabatan','=','tb_master_jabatan.id')
        ->select('tb_pegawai.id','tb_pegawai.uuid','tb_pegawai.nip','tb_pegawai.nama','tb_pegawai_masuk.asal_daerah','tb_satuan_kerja.nama_satuan_kerja')
        ->where('tb_pegawai_masuk.tahun',session('tahun_penganggaran'))
        ->get();
        return $this->sendResponse($data, 'Data Pegawai Fetched Success');
    }

    public function index(){
        $module = $this->breadcumb();
        $golongan = $this->option_golongan();
        $pendidikan = $this->option_pendidikan();
        $satuan_kerja = $this->option_satuan_kerja();
        $agama = $this->option_agama();
        $satuan_kerja_user = '';

        if(hasRole()['guard'] == 'web' && hasRole()['role'] == '1'){
            $satuan_kerja_user = $this->infoSatuanKerja(hasRole()['id']);
        }

        return view('pegawai.pegawai_masuk.index',compact('module','golongan','pendidikan','satuan_kerja','satuan_kerja_user','agama'));
    }

    public function store(pegawaimasukRequest $request){
        $pegawai = array();
        try {
            DB::beginTransaction();

            $pegawai = new Pegawai();
            $pegawai->id_satuan_kerja = $request->id_satuan_kerja;
            $pegawai->nip = $request->nip;
            $pegawai->nama = $request->nama;
            $pegawai->tempat_lahir = $request->tempat_lahir;
            $pegawai->tanggal_lahir = $request->tanggal_lahir;
            $pegawai->jenis_kelamin = $request->jenis_kelamin;
            $pegawai->agama = $request->agama;
            $pegawai->status_perkawinan = $request->status_perkawinan;
            $pegawai->tmt_pegawai = $request->tmt_pegawai;
            $pegawai->golongan = $request->golongan;
            $pegawai->tmt_golongan = $request->tmt_golongan;
            $pegawai->tmt_jabatan = $request->tmt_jabatan;
            $pegawai->pendidikan = $request->pendidikan;
            $pegawai->pendidikan_lulus = $request->pendidikan_lulus;
            $pegawai->pendidikan_struktural = $request->pendidikan_struktural;
            $pegawai->pendidikan_struktural_lulus = $request->pendidikan_struktural_lulus;
            $pegawai->status = '1';
            $pegawai->save();

            $pegawaimasuk = new PegawaiMasuk();
            $pegawaimasuk->id_pegawai = $pegawai->id;
            $pegawaimasuk->asal_daerah = $request->asal_daerah;
            $pegawaimasuk->id_jabatan_masuk = $request->id_jabatan_masuk;
            $pegawaimasuk->tmt = $request->tmt;
            $pegawaimasuk->user_insert = hasRole()['id'];
            $pegawaimasuk->save();

            $jabatan = Jabatan::where('id',$pegawaimasuk->id_jabatan_masuk)->first();
            $jabatan->id_pegawai = $pegawai->id;
            $jabatan->status = 'definitif';
            $jabatan->pembayaran = '0';
            $jabatan->user_insert = hasRole()['id'];
            $jabatan->save();

            DB::commit();

        

        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($pegawai, 'Pegawai Added success');
    }

    public function update(pegawaimasukUpdateRequest $request, $params) {
        try {
            DB::beginTransaction();

            $pegawai = Pegawai::where('uuid',$params)->first();
            $pegawai->id_satuan_kerja = $request->id_satuan_kerja;
            $pegawai->nip = $request->nip;
            $pegawai->nama = $request->nama;
            $pegawai->tempat_lahir = $request->tempat_lahir;
            $pegawai->tanggal_lahir = $request->tanggal_lahir;
            $pegawai->jenis_kelamin = $request->jenis_kelamin;
            $pegawai->agama = $request->agama;
            $pegawai->status_perkawinan = $request->status_perkawinan;
            $pegawai->tmt_pegawai = $request->tmt_pegawai;
            $pegawai->golongan = $request->golongan;
            $pegawai->tmt_golongan = $request->tmt_golongan;
            $pegawai->tmt_jabatan = $request->tmt_jabatan;
            $pegawai->pendidikan = $request->pendidikan;
            $pegawai->pendidikan_lulus = $request->pendidikan_lulus;
            $pegawai->pendidikan_struktural = $request->pendidikan_struktural;
            $pegawai->pendidikan_struktural_lulus = $request->pendidikan_struktural_lulus;
            $pegawai->status = '1';
            $pegawai->save();

            $pegawaimasuk = PegawaiMasuk::where('id_pegawai', $pegawai->id)
                ->where('tahun', session('tahun_penganggaran'))
                ->first();
            $pegawaimasuk->asal_daerah = $request->asal_daerah;
            if (isset($request->id_jabatan_masuk)) {
                $pegawaimasuk->id_jabatan_masuk = $request->id_jabatan_masuk;
            }
            $pegawaimasuk->tmt = $request->tmt;
            $pegawaimasuk->user_insert = hasRole()['id'];
            $pegawaimasuk->save();

            $jabatan = Jabatan::where('id_pegawai', $pegawai->id)->first();
            $jabatan->id_pegawai = $pegawai->id;
            $jabatan->id_master_jabatan = $pegawaimasuk->id_jabatan_masuk;
            $jabatan->id_satuan_kerja = $pegawai->id_satuan_kerja;
            $jabatan->status = 'definitif';
            $jabatan->pembayaran = '0';
            $jabatan->user_insert = hasRole()['id'];
            $jabatan->save();

            DB::commit();

        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($pegawai, 'Pegawai Updated success');
    }

    public function show($params){
       $data = array();
        try {
            $data = DB::table('tb_pegawai_masuk')
            ->join('tb_pegawai','tb_pegawai_masuk.id_pegawai','=','tb_pegawai.id')
            ->join('tb_satuan_kerja','tb_pegawai.id_satuan_kerja','=','tb_satuan_kerja.id')
            ->select('tb_pegawai.id','tb_pegawai.uuid','tb_pegawai.nip','tb_pegawai.nama','tb_pegawai.tempat_lahir','tb_pegawai.tanggal_lahir','tb_pegawai.jenis_kelamin','tb_pegawai.agama','tb_pegawai.status_perkawinan','tb_pegawai.tmt_pegawai','tb_pegawai.golongan','tb_pegawai.tmt_golongan','tb_pegawai.tmt_jabatan','tb_pegawai.pendidikan','tb_pegawai.pendidikan_lulus','tb_pegawai.pendidikan_struktural','tb_pegawai.pendidikan_struktural_lulus','tb_pegawai.id_satuan_kerja','tb_pegawai_masuk.asal_daerah','tb_pegawai_masuk.id_jabatan_masuk','tb_pegawai_masuk.tmt')->where('tb_pegawai.uuid',$params)->first();
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
        $data = DB::table('tb_pegawai_masuk')
        ->join('tb_pegawai','tb_pegawai_masuk.id_pegawai','=','tb_pegawai.id')
        ->join('tb_satuan_kerja','tb_pegawai.id_satuan_kerja','=','tb_satuan_kerja.id')
        ->join('tb_jabatan','tb_jabatan.id_pegawai','=','tb_pegawai.id')
        ->join('tb_master_jabatan','tb_jabatan.id_master_jabatan','=','tb_master_jabatan.id')
        ->select('tb_pegawai.id','tb_pegawai.uuid','tb_pegawai.nip','tb_pegawai.nama','tb_pegawai.tempat_lahir','tb_pegawai.tanggal_lahir','tb_pegawai.jenis_kelamin','tb_pegawai.agama','tb_pegawai.status_perkawinan','tb_pegawai.tmt_pegawai','tb_pegawai.golongan','tb_pegawai.tmt_golongan','tb_pegawai.tmt_jabatan','tb_pegawai.pendidikan','tb_pegawai.pendidikan_lulus','tb_pegawai.pendidikan_struktural','tb_pegawai.pendidikan_struktural_lulus','tb_pegawai.id_satuan_kerja','tb_pegawai_masuk.asal_daerah','tb_pegawai_masuk.id_jabatan_masuk','tb_pegawai_masuk.tmt','tb_master_jabatan.nama_jabatan')
        ->where('tb_pegawai.uuid',$params)
        ->first();

        return view('pegawai.pegawai_masuk.detail',compact('data','module'));
    }

    public function delete(Request $request, $params){
        $data = array();
        try {
            $pegawai = Pegawai::where('uuid',$params)->first();
            DB::table('tb_pegawai')->where('id', $pegawai->id)->delete();
            DB::table('tb_pegawai_masuk')->where('id_pegawai', $pegawai->id)->delete();
           
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Pegawai Delete success');
    }
}
