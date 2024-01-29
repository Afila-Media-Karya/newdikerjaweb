<?php

namespace App\Http\Controllers\review;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\SasaranKinerja;
use DB;
use App\Traits\General;

class SasaranKinerjaReviewController extends BaseController
{
    use General;

    public function breadcumb(){
        return [
            [
                'label' => 'Review',
                'url' => '#'
            ],
            [
                'label' => 'Sasaran Kinerja',
                'url' => '#'
            ],
        ];
    }

    public function datatable(){
        $jabatan = $this->checkJabatanDefinitif(hasRole()['id_pegawai']);
        $data = DB::table("tb_jabatan")
        ->join('tb_master_jabatan', 'tb_jabatan.id_master_jabatan', '=', 'tb_master_jabatan.id')
        ->join('tb_pegawai', 'tb_jabatan.id_pegawai', '=', 'tb_pegawai.id')
        ->leftJoin('tb_skp', 'tb_jabatan.id', '=', 'tb_skp.id_jabatan')
        ->select('tb_pegawai.nama', 'tb_pegawai.nip', 'tb_master_jabatan.nama_jabatan','tb_jabatan.id as id_jabatan','tb_master_jabatan.level_jabatan')
        ->selectRaw('
            CASE
                WHEN COUNT(tb_skp.id) = 0 THEN "Belum Review"
                WHEN SUM(tb_skp.kesesuaian) = COUNT(tb_skp.id) THEN "Selesai"
                WHEN SUM(tb_skp.kesesuaian) = 0 THEN "Belum Sesuai"
                ELSE "Belum Sesuai"
            END AS status_review
        ')
        ->where('tb_jabatan.id_parent', $jabatan->id_jabatan)
        ->groupBy('tb_pegawai.nama', 'tb_pegawai.nip', 'tb_master_jabatan.nama_jabatan','tb_jabatan.id')
        ->get();


        return $this->sendResponse($data, 'Review Sasaran Kinerja Fetched Success');
    }



    public function index(){
        $module = $this->breadcumb();
        return view('review.sasaran_kinerja.index',compact('module'));
    }

    public function review(){
        $jabatan = request('jabatan');
        $level = intval(request('level'));
        $data = array();
        $module = [
            [
                'label' => 'Review',
                'url' => '#'
            ],
            [
                'label' => 'Sasaran Kinerja',
                'url' => '#'
            ],
            [
                'label' => 'Review Sasaran Kinerja',
                'url' => '#'
            ],
        ];
        

        if ($level > 2) {
            return view('review.sasaran_kinerja.review_skp_pegawai',compact('module','jabatan','level'));
        }

        return view('review.sasaran_kinerja.review_skp_kepala',compact('module','jabatan','level'));
        
    }

    public function data_review_skp(){
        $jabatan = request('jabatan');
        $level = intval(request('level'));
        $data = array();
        $count_skp = DB::table('tb_skp')->where('id_jabatan',$jabatan)->count();

        if ($level > 2) {
            $data = DB::table('tb_skp as skp_pegawai')
            ->leftJoin('tb_skp as skp_atasan','skp_pegawai.id_skp_atasan','=','skp_atasan.id')
            ->leftJoin('tb_aspek_skp', 'tb_aspek_skp.id_skp', 'skp_pegawai.id')
            ->select('tb_aspek_skp.id', 'skp_pegawai.uuid', 'skp_pegawai.jenis', 'skp_pegawai.rencana', 'tb_aspek_skp.iki', 'tb_aspek_skp.target', 'tb_aspek_skp.satuan', 'tb_aspek_skp.id_skp','skp_atasan.rencana as skp_atasan_langsung','tb_aspek_skp.aspek_skp','skp_pegawai.id as id_skp','skp_pegawai.kesesuaian','skp_pegawai.keterangan','skp_pegawai.validation')
            ->groupBy('tb_aspek_skp.id', 'skp_pegawai.id') // Tambafhkan skp_pegawai.id ke dalam GROUP BY
            ->orderBy('skp_pegawai.jenis', 'ASC')
            ->where('skp_pegawai.id_jabatan',$jabatan)
            ->get();
        }else{
            $data = DB::table('tb_skp')
            ->leftJoin('tb_aspek_skp', 'tb_aspek_skp.id_skp', 'tb_skp.id')
            ->select('tb_aspek_skp.id', 'tb_skp.uuid', 'tb_skp.jenis', 'tb_skp.rencana', 'tb_aspek_skp.iki', 'tb_aspek_skp.target', 'tb_aspek_skp.satuan', 'tb_aspek_skp.id_skp','tb_skp.id as id_skp','tb_skp.kesesuaian','tb_skp.keterangan','tb_skp.validation')
            ->groupBy('tb_aspek_skp.id', 'tb_skp.id') // Tambahkan tb_skp.id ke dalam GROUP BY
            ->orderBy('tb_skp.jenis', 'ASC')
            ->where('tb_skp.id_jabatan',$jabatan)
            ->get();
        }

        $data = $data->map(function ($item) use ($count_skp) {
            $item->jumlah_skp = $count_skp;
            return $item;
        });

        return $this->sendResponse($data, 'SKP Review Fetched Success');
    }

    public function postReviewSkp(Request $request){
    
        $data = array();
        try {
        $result = [];

        foreach ($request->id_skp as $key => $value) {
            if (!isset($result[$value])) {
                $result[$value] = $key;
            }
        }

        $result = array_flip($result);
        
        foreach ($result as $i => $k) {            
            $data = SasaranKinerja::where('id', $k)->first();
            $data->kesesuaian = $request->kesesuaian[$i];
            $data->keterangan = $request->keterangan[$i];
            $data->save();
        }

        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Review SKP Added success');

    }

}
