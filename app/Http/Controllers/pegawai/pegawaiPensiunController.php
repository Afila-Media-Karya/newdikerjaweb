<?php

namespace App\Http\Controllers\pegawai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Requests\pegawaipensiunRequest;
use App\Models\Pegawai;
use App\Models\PegawaiPensiun;
use App\Models\Jabatan;
use DB;
use App\Traits\General;
use Auth;

class pegawaiPensiunController extends BaseController
{
    use General;

    public function breadcumb(){
        return [
            [
                'label' => 'Pegawai',
                'url' => '#'
            ],
            [
                'label' => 'Pegawai Pensiun',
                'url' => '#'
            ],
        ];
    }

    public function datatable(){
        $data = array();
        $satuan_kerja_user = '';
        

        $query = DB::table('tb_pegawai_pensiun')
        ->Leftjoin('tb_pegawai','tb_pegawai_pensiun.id_pegawai','=','tb_pegawai.id')
        ->Leftjoin('tb_jabatan','tb_jabatan.id_pegawai','tb_pegawai.id')
        ->Leftjoin('tb_master_jabatan','tb_jabatan.id_master_jabatan','tb_master_jabatan.id')
        ->select('tb_pegawai_pensiun.id','tb_pegawai.id as id_pegawai','tb_pegawai_pensiun.jabatan_terakhir','tb_pegawai.uuid as uuid_pegawai','tb_pegawai_pensiun.uuid','tb_pegawai.nip','tb_pegawai.nama','tb_master_jabatan.nama_jabatan','tb_jabatan.id_satuan_kerja','tb_jabatan.id_unit_kerja');

        if(hasRole()['guard'] == 'web'){
            $satuan_kerja_user = $this->infoSatuanKerja(hasRole()['id_pegawai']);

            if (hasRole()['role'] == '1') {
                if ($satuan_kerja_user->id_satuan_kerja) {
                    $query->where('tb_pegawai.id_satuan_kerja',$satuan_kerja_user->id_satuan_kerja);
                }
            }
            
            if (hasRole()['role'] == '3') {
                if ($satuan_kerja_user->id_unit_kerja) {
                    $query->where('tb_jabatan.id_unit_kerja',$satuan_kerja_user->id_unit_kerja);
                }
            }
            
        }

        $data = $query->get();
        return $this->sendResponse($data, 'Data Pegawai Pensiun Fetched Success');
    }

    public function index(){
        $module = $this->breadcumb();
        $satuan_kerja = $this->option_satuan_kerja();
        $satuan_kerja_user = '';
        $option_pegawai = array();
        $pegawaiAkanPensiun = array();

        if(hasRole()['guard'] == 'web' && hasRole()['role'] == '1'){
            $satuan_kerja_user = $this->infoSatuanKerja(hasRole()['id_pegawai']);
            $pegawaiAkanPensiun = $this->option_akan_pensiun($satuan_kerja_user->id_satuan_kerja);
        }

        if(hasRole()['guard'] == 'web' && hasRole()['role'] == '3'){
            $satuan_kerja_user = $this->infoSatuanKerja(hasRole()['id_pegawai']);
            $pegawaiAkanPensiun = $this->option_akan_pensiun_by_unit_kerja($satuan_kerja_user->id_unit_kerja);
        }

        

        return view('pegawai.pegawai_pensiun.index',compact('module','satuan_kerja','satuan_kerja_user','pegawaiAkanPensiun'));
    }

    public function store(pegawaipensiunRequest $request){
        $pegawai = array();
        try {
            DB::beginTransaction();

            $pegawai_tmp = DB::table('tb_pegawai')->select('uuid')->where('id',$request->id_pegawai)->first();
            $jabatan = $this->checkJabatan($pegawai_tmp->uuid);
          
            // if ($this->checkJabatan($pegawai_tmp->uuid)) {
                $pegawai = new PegawaiPensiun();
                $pegawai->id_satuan_kerja = $request->id_satuan_kerja;
                $pegawai->id_pegawai = $request->id_pegawai;
                $pegawai->tmt = $request->tmt;
                $pegawai->jabatan_terakhir = !is_null($jabatan) ? $jabatan->nama_jabatan : '-';
                $pegawai->user_insert = hasRole()['id'];
                $pegawai->save(); 

                $pegawai_list = Pegawai::where('id',$request->id_pegawai)->first();
                $pegawai_list->status = '2';
                $pegawai_list->save();

                if (!is_null($jabatan)) {
                    $jabatan = Jabatan::where('id_pegawai',$request->id_pegawai)->first();
                    $jabatan->id_pegawai = null;
                    $jabatan->save();
                }
                
            // }else{
            //     return $this->sendError('Pegawai tersebut tidak mempunyai jabatan', 'Gagal Memproses Data', 200);
            // }

            
            DB::commit();

        

        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($pegawai, 'Pegawai Added success');
    }

    public function update(pegawaipensiunRequest $request, $params) {
        try {
            DB::beginTransaction();

                $pegawai = PegawaiPensiun::where('uuid', $params)
                ->where('tahun', session('tahun_penganggaran'))
                ->first();
                $pegawai->id_satuan_kerja = $request->id_satuan_kerja;
                $pegawai->id_pegawai = $request->id_pegawai;
                $pegawai->tmt = $request->tmt;
                $pegawai->user_update = hasRole()['id'];
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
            $data = DB::table('tb_pegawai_pensiun')->where('uuid',$params)->first();
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

        $data = DB::table('tb_pegawai_pensiun')->Leftjoin('tb_pegawai','tb_pegawai_pensiun.id_pegawai','=','tb_pegawai.id')->Leftjoin('tb_satuan_kerja','tb_pegawai.id_satuan_kerja','=','tb_satuan_kerja.id')->Leftjoin('tb_jabatan','tb_jabatan.id_pegawai','=','tb_pegawai.id')->Leftjoin('tb_master_jabatan','tb_jabatan.id_master_jabatan','=','tb_jabatan.id')->select('tb_pegawai.id','tb_pegawai.uuid','tb_pegawai.nip','tb_pegawai.nama','tb_pegawai.tempat_lahir','tb_pegawai.tanggal_lahir','tb_pegawai.jenis_kelamin','tb_pegawai.agama','tb_pegawai.status_perkawinan','tb_pegawai.tmt_pegawai','tb_pegawai.golongan','tb_pegawai.tmt_golongan','tb_pegawai.tmt_jabatan','tb_pegawai.pendidikan','tb_pegawai.pendidikan_lulus','tb_pegawai.pendidikan_struktural','tb_pegawai.pendidikan_struktural_lulus','tb_pegawai.id_satuan_kerja','tb_pegawai_pensiun.jabatan_terakhir','tb_pegawai_pensiun.tmt','tb_master_jabatan.nama_jabatan')->where('tb_pegawai.uuid',$params)->first();

        return view('pegawai.pegawai_pensiun.detail',compact('data','module'));
    }

    public function breadcumb_akan_pensiun(){
        return [
            [
                'label' => 'Pegawai',
                'url' => '#'
            ],
            [
                'label' => 'Pegawai Pensiun',
                'url' => '#'
            ],
        ];
    }

    public function index_akan_pensiun(){
        $module = $this->breadcumb_akan_pensiun();
        $unit_kerja = $this->option_satuan_kerja();
        return view('pegawai.pegawai_pensiun.akanpensiun',compact('module','unit_kerja'));
    }

    public function datatable_akan_pensiun(){
        try {
            $semuaPegawai = array();
            $satuan_kerja = intval(request('satuan_kerja'));
            $query = DB::table('tb_pegawai')->leftJoin('tb_jabatan','tb_jabatan.id_pegawai','tb_pegawai.id')->leftJoin('tb_master_jabatan','tb_jabatan.id_master_jabatan','tb_master_jabatan.id')->leftJoin('tb_satuan_kerja','tb_pegawai.id_satuan_kerja','=','tb_satuan_kerja.id')->select('tb_pegawai.id','tb_pegawai.uuid','tb_pegawai.id_satuan_kerja','tb_pegawai.nip','tb_pegawai.nama','tb_master_jabatan.nama_jabatan','tb_master_jabatan.jenis_jabatan','tb_jabatan.status','tb_satuan_kerja.nama_satuan_kerja','tb_master_jabatan.level_jabatan','tb_master_jabatan.kelas_jabatan');

            $role = hasRole();

            if ($role['guard'] == 'web' && $role['role'] == '1') {
                $satuan_kerja = $this->infoSatuanKerja(Auth::user()->id_pegawai)->id_satuan_kerja;
                $query->where('tb_pegawai.id_satuan_kerja',$satuan_kerja);
            }elseif ($role['guard'] == 'web' && $role['role'] == '3') {
                $unit_kerja = $this->infoSatuanKerja(Auth::user()->id_pegawai)->id_unit_kerja;
                $query->where('tb_jabatan.id_unit_kerja',$unit_kerja);
            }

            if (hasRole()['guard'] == 'administrator') {
                if ($satuan_kerja > 0) {
                    $query->where('tb_pegawai.id_satuan_kerja', $satuan_kerja);
                }   
            }

            $semuaPegawai = $query->get();
            
            // Array untuk menyimpan pegawai yang akan pensiun
            $pegawaiAkanPensiun = [];

            // Iterasi semua pegawai
            foreach ($semuaPegawai as $pegawai) {
                $nipPegawai = $pegawai->nip;

                // Mengurai NIP untuk mendapatkan tanggal lahir
                $tahun = substr($nipPegawai, 0, 4);
                $bulan = substr($nipPegawai, 4, 2);
                $tanggal = substr($nipPegawai, 6, 2);

                $tanggalLahir = "$tahun-$bulan-$tanggal";

                // Memeriksa apakah pegawai akan pensiun
                $usiaPensiun = 58; // Ganti dengan usia pensiun yang sesuai

                if ($pegawai->kelas_jabatan >= 13) {
                    $usiaPensiun = 60; 
                }

                $tanggalPensiun = date("Y-m-d", strtotime("+$usiaPensiun years", strtotime($tanggalLahir)));

                $tahunPensiun = $tahun + $usiaPensiun;

                if ($tahunPensiun <= date('Y')) {
                    $pegawai->tanggal_pensiun = $tanggalPensiun;
                    $pegawaiAkanPensiun[] = $pegawai;
                }
            }

            // Sekarang, $pegawaiAkanPensiun akan berisi pegawai yang akan pensiun, dengan key "tanggal_pensiun" di setiap pegawai.

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($pegawaiAkanPensiun, 'Pegawai akan pensiun success');

    }

    public function option($params){
        $pegawaiAkanPensiun = $this->option_akan_pensiun($params);
        return $this->sendResponse($pegawaiAkanPensiun, 'Pegawai Pensiun option success'); 
    }

    public function delete(Request $request, $params){
        $data = array();
        try {
                DB::beginTransaction();
                $data = PegawaiPensiun::where('uuid', $params)->first();

                // Memeriksa apakah data ditemukan
                if ($data) {
                    // Update status pegawai di tb_pegawai
                    $pegawai_back = DB::table('tb_pegawai')
                        ->where('id', $data->id_pegawai)
                        ->update(['status' => '1']);

                    // Menghapus data PegawaiPensiun
                    $data->delete();
                } else {
                    return $this->sendError('Data PegawaiPensiun tidak ditemukan.', 'Data PegawaiPensiun tidak ditemukan.', 200);
                }

                DB::commit();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Jabatan Delete success');
    }
}





            