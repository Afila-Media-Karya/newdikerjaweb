<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Requests\SasaranKinerjaRequest;
use App\Models\AspekSkp;
use DB;
use App\Traits\General;

class RealisasiSkpController extends BaseController
{
    use General;

    public function breadcumb(){
        return [
            [
                'label' => 'Realisasi',
                'url' => '#'
            ],
        ];
    }

    public function datatable(){
        $data = array();
        $jabatan = $this->checkJabatanDefinitif(hasRole()['id_pegawai']);
        $level = intval($jabatan->level_jabatan);

        $data = DB::table('tb_skp')
            ->leftJoin('tb_aspek_skp', 'tb_aspek_skp.id_skp', 'tb_skp.id')
            ->select('tb_aspek_skp.id', 'tb_skp.uuid','tb_skp.validation', 'tb_skp.jenis', 'tb_skp.rencana', 'tb_aspek_skp.iki', 'tb_aspek_skp.target', 'tb_aspek_skp.satuan', 'tb_aspek_skp.realisasi', 'tb_aspek_skp.id_skp')
            ->groupBy('tb_aspek_skp.id', 'tb_skp.id') // Tambahkan tb_skp.id ke dalam GROUP BY
            ->orderBy('tb_skp.jenis', 'ASC')
            ->where('tb_skp.id_jabatan',$jabatan->id_jabatan)
            ->get();

        return $this->sendResponse($data, 'Sasaran Kinerja Fetched Success');
    }

    public function index(){
        $module = $this->breadcumb();
        $jabatan = $this->checkJabatanDefinitif(hasRole()['id_pegawai']);
        $level = intval($jabatan->level_jabatan);
        return view('pegawai.realisasi.index',compact('module','level'));
    }

    public function show($params){
        $skp = DB::table('tb_skp')->where('uuid',$params)->first();
        $data = DB::table('tb_aspek_skp')->select('uuid','id_skp','iki','aspek_skp','target','realisasi','satuan')->where('id_skp',$skp->id)->get();

        return $this->sendResponse($data, 'Realisasi Show Success');
    }

    public function update(Request $request){
        $data = array();
        try {

            foreach ($request->realisasi as $item) {
                if ($item === null) {
                    return $this->sendError('Nilai realisasi harus terisi semua', 'Gagal', 200);
                }
            }

            foreach ($request->uuid as $key => $value) {
                $data = AspekSkp::where('uuid',$value)->first();
                $data->realisasi = $request->realisasi[$key];
                $data->save();
            }
        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Absen Added success');
    }

}
