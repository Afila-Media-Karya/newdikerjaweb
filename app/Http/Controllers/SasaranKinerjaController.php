<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Requests\SasaranKinerjaRequest;
use App\Models\SasaranKinerja;
use App\Models\AspekSkp;
use DB;
use App\Traits\General;
class SasaranKinerjaController extends BaseController
{
    use General;

    public function breadcumb(){
        return [
            [
                'label' => 'Sasaran Kinerja',
                'url' => '#'
            ],
        ];
    }

    public function datatable(){
        $data = array();
        $jabatan = $this->checkJabatanDefinitif(hasRole()['id_pegawai']);
        $level = intval($jabatan->level_jabatan);

        if ($level > 2) {
            $data = DB::table('tb_skp as skp_pegawai')
            ->leftJoin('tb_skp as skp_atasan','skp_pegawai.id_skp_atasan','=','skp_atasan.id')
            ->leftJoin('tb_aspek_skp', 'tb_aspek_skp.id_skp', 'skp_pegawai.id')
            ->select('tb_aspek_skp.id', 'skp_pegawai.uuid', 'skp_pegawai.jenis', 'skp_pegawai.rencana', 'tb_aspek_skp.iki', 'tb_aspek_skp.target', 'tb_aspek_skp.satuan', 'tb_aspek_skp.id_skp','skp_atasan.rencana as skp_atasan_langsung','tb_aspek_skp.aspek_skp')
            ->groupBy('tb_aspek_skp.id', 'skp_pegawai.id') // Tambafhkan skp_pegawai.id ke dalam GROUP BY
            ->orderBy('skp_pegawai.jenis', 'ASC')
            ->where('skp_pegawai.tahun',session('tahun_penganggaran'))
            ->where('skp_pegawai.id_jabatan',$jabatan->id_jabatan)
            ->get();
        }else{
            $data = DB::table('tb_skp')
            ->leftJoin('tb_aspek_skp', 'tb_aspek_skp.id_skp', 'tb_skp.id')
            ->select('tb_aspek_skp.id', 'tb_skp.uuid', 'tb_skp.jenis', 'tb_skp.rencana', 'tb_aspek_skp.iki', 'tb_aspek_skp.target', 'tb_aspek_skp.satuan', 'tb_aspek_skp.id_skp')
            ->groupBy('tb_aspek_skp.id', 'tb_skp.id') // Tambahkan tb_skp.id ke dalam GROUP BY
            ->orderBy('tb_skp.jenis', 'ASC')
            ->where('tb_skp.tahun',session('tahun_penganggaran'))
            ->where('tb_skp.id_jabatan',$jabatan->id_jabatan)
            ->get();
        }

        

        return $this->sendResponse($data, 'Sasaran Kinerja Fetched Success');
    }

    public function skp_atasan_langsung($pegawai){
        $data = array();
        $jabatan = $this->checkJabatanDefinitif($pegawai);
        $parent = $this->checkReviewer($jabatan->id_parent);
        if (isset($jabatan) && isset($parent)) {
            $data = DB::table('tb_skp')->select('id','rencana as text')->where('id_jabatan',$parent->jabatan_id_atasan)->where('jenis','utama')->get();   
        }
        return $data;
    }

    public function index(){
        $module = $this->breadcumb();
        $jabatan = $this->checkJabatanDefinitif(hasRole()['id_pegawai']);
        $level = intval($jabatan->level_jabatan);
        
        if ($level > 2 ) {
            $skp_atasan = $this->skp_atasan_langsung(hasRole()['id_pegawai']);
           return view('sasaran_kinerja.index2',compact('module','level','skp_atasan'));
        }

        return view('sasaran_kinerja.index',compact('module','level'));
    }

    public function checkReviewer($params){
        return DB::table('tb_master_jabatan')->join('tb_jabatan','tb_jabatan.id_master_jabatan','=','tb_master_jabatan.id')->join('tb_pegawai','tb_jabatan.id_pegawai','=','tb_pegawai.id')->select('tb_jabatan.id as jabatan_id_atasan')->where('tb_master_jabatan.id',$params)->first();
    }

    public function store(SasaranKinerjaRequest $request){
        $data = array();
        try {

            DB::beginTransaction();

            if (intval($request->level) <= 2) {
                foreach ($request->repeater_iki as $item) {
                    foreach ($item as $value) {
                        if ($value === null) {
                            return $this->sendError('Indikator Kerja Individu harus terisi semua', 'Gagal', 200);
                        }
                    }
                }
            }

            $jabatan = $this->checkJabatanDefinitif(hasRole()['id_pegawai']);
            if (is_null($jabatan)) {
                return $this->sendError('Maaf anda belum bisa menambah aktivitas, anda belum mempunyai jabatan', 'Gagal', 200);
            }

            $data = new SasaranKinerja();
            $data->jenis = $request->jenis;
            $data->rencana = $request->rencana;
            $data->id_jabatan = $jabatan->id_jabatan;
            $data->id_satuan_kerja = $jabatan->id_satuan_kerja;
            if (intval($request->level) > 2 ) {
                $data->id_skp_atasan = $request->id_skp_atasan;
            }else{
                $data->id_skp_atasan = 0;
            }

            if (intval($request->level) <= 2) {
                $data->id_reviewer = 0;
            }else{
                $data->id_reviewer = $this->checkReviewer($jabatan->id_parent)->jabatan_id_atasan;
            }
            $data->validation = 0;
            $data->kesesuaian = 0;
            $data->save();

            if (intval($request->level) > 2) {

                foreach ($request->aspek_skp as $key => $value) {
                    $aspek_skp = new AspekSkp();
                    $aspek_skp->aspek_skp = $value;
                    $aspek_skp->iki = $request->{"iki_iki_{$value}"};
                    $aspek_skp->target = $request->{"iki_target_{$value}"};
                    $aspek_skp->realisasi = 0;
                    $aspek_skp->satuan = $request->{"iki_satuan_{$value}"};
                    $aspek_skp->id_skp = $data->id;
                    $aspek_skp->save();
                }
            }else{
    
                foreach ($request->repeater_iki as $key => $value) {
                    $aspek_skp = new AspekSkp();
                    $aspek_skp->aspek_skp = 'iki';
                    $aspek_skp->iki = $value['iki'];
                    $aspek_skp->target = $value['target'];
                    $aspek_skp->realisasi = 0;
                    $aspek_skp->satuan = $value['satuan'];
                    $aspek_skp->id_skp = $data->id;
                    $aspek_skp->save();
                }
            }

            DB::commit();

        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Pengumuman Added success');
    }

    public function update(SasaranKinerjaRequest $request, $params){
        $data = array();
        try {

            DB::beginTransaction();

            if (intval($request->level) <= 2) {
                foreach ($request->repeater_iki as $item) {
                    foreach ($item as $value) {
                        if ($value === null) {
                            return $this->sendError('Indikator Kerja Individu harus terisi semua', 'Gagal', 200);
                        }
                    }
                }
            }

            $jabatan = $this->checkJabatanDefinitif(hasRole()['id_pegawai']);
            if (is_null($jabatan)) {
                return $this->sendError('Maaf anda belum bisa menambah aktivitas, anda belum mempunyai jabatan', 'Gagal', 200);
            }


            $data =  SasaranKinerja::where('uuid',$params)->first();
            $data->jenis = $request->jenis;
            $data->rencana = $request->rencana;
            $data->id_jabatan = $jabatan->id_jabatan;
            $data->id_satuan_kerja = $jabatan->id_satuan_kerja;
            if (intval($request->level) > 2 ) {
                $data->id_skp_atasan = $request->id_skp_atasan;
            }else{
                $data->id_skp_atasan = 0;
            }

            if (intval($request->level) <= 2) {
                $data->id_reviewer = 0;
            }else{
                $data->id_reviewer = $this->checkReviewer($jabatan->id_parent)->jabatan_id_atasan;
            }
            $data->validation = 0;
            $data->save();


            AspekSkp::where('id_skp',$data->id)->delete();

            if (intval($request->level) > 2) {

                foreach ($request->aspek_skp as $key => $value) {
                    $aspek_skp = new AspekSkp();
                    $aspek_skp->aspek_skp = $value;
                    $aspek_skp->iki = $request->{"iki_iki_{$value}"};
                    $aspek_skp->target = $request->{"iki_target_{$value}"};
                    $aspek_skp->realisasi = 0;
                    $aspek_skp->satuan = $request->{"iki_satuan_{$value}"};
                    $aspek_skp->id_skp = $data->id;
                    $aspek_skp->save();
                }
            }else{
    
                foreach ($request->repeater_iki as $key => $value) {
                    $aspek_skp = new AspekSkp();
                    $aspek_skp->aspek_skp = 'iki';
                    $aspek_skp->iki = $value['iki'];
                    $aspek_skp->target = $value['target'];
                    $aspek_skp->realisasi = 0;
                    $aspek_skp->satuan = $value['satuan'];
                    $aspek_skp->id_skp = $data->id;
                    $aspek_skp->save();
                }
            }

            DB::commit();
            
            $data->save();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Sasaran Kinerja Update success');
    }

    public function show($params){
       $data = array();
        try {

        $jabatan = $this->checkJabatanDefinitif(hasRole()['id_pegawai']);
        $level = intval($jabatan->level_jabatan);

        if ($level > 2 ) {
            $data = DB::table('tb_skp')->where('uuid',$params)->first();
            $aspek = DB::table('tb_aspek_skp')->where('id_skp',$data->id)->get();
            $data->iki_iki_kuantitas = $aspek[0]->iki;
            $data->iki_target_kuantitas = $aspek[0]->target;
            $data->iki_satuan_kuantitas = $aspek[0]->satuan;
            $data->iki_iki_kualitas = $aspek[1]->iki;
            $data->iki_target_kualitas = $aspek[1]->target;
            $data->iki_satuan_kualitas = $aspek[1]->satuan;
            $data->iki_iki_waktu = $aspek[2]->iki;
            $data->iki_target_waktu = $aspek[2]->target;
            $data->iki_satuan_waktu = $aspek[2]->satuan;


        }else{
            $data = SasaranKinerja::with('AspekSkp')->where('uuid',$params)->first();
        }

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Sasaran Kinerja Update success'); 
    }

    public function delete(Request $request, $params)
    {
        try {

            $data = SasaranKinerja::where('uuid',$params)->first();

            $checkSKPterpakai = DB::table('tb_skp')->where('id_skp_atasan',$data->id)->exists();

            if (!$checkSKPterpakai) {
                AspekSkp::where('id_skp',$data->id)->delete();
                $data->delete();
            }else{
                return $this->sendError('SKP terpakai oleh bawahan', 'Gagal', 422);
            }

            
            

            return $this->sendResponse([], 'Sasaran Kinerja Delete success');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
    }
}
