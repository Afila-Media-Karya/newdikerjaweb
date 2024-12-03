<?php

namespace App\Http\Controllers\review;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\Aktivitas;
use DB;
use App\Traits\General;
use Auth;

class AktivitasReviewController extends BaseController
{
    use General;

    public function breadcumb(){
        return [
            [
                'label' => 'Review',
                'url' => '#'
            ],
            [
                'label' => 'Aktivitas',
                'url' => '#'
            ],
        ];
    }

    public function configNilai($bulan,$pegawai){
        $nilai_produktivitas_kerja = 0;
        // $jabatan = $this->checkJabatanDefinitif($pegawai);

        $jabatan = DB::table('tb_pegawai')->join('tb_jabatan','tb_jabatan.id_pegawai','tb_pegawai.id')->join('tb_master_jabatan','tb_jabatan.id_master_jabatan','tb_master_jabatan.id')->select('tb_jabatan.target_waktu')->where('tb_pegawai.id',$pegawai)->first();

        $target_waktu = 0;

        $jabatan ? $target_waktu = $jabatan->target_waktu : 0;

        $data = DB::table('tb_aktivitas')
        ->selectRaw('COALESCE(SUM(waktu), 0) as capaian')
        ->whereMonth('tanggal',$bulan)
        ->where('id_pegawai',$pegawai)
        ->where("validation",1)
        ->first();

        if ($target_waktu > 0) {
            $nilai_produktivitas_kerja = ($data->capaian / $target_waktu) * 100;
        }

        if ($nilai_produktivitas_kerja > 100) {
                $nilai_produktivitas_kerja = 100;
        }

        return $nilai_produktivitas_kerja;
    }

    public function datatable(){
        $jabatan = $this->checkJabatanDefinitif(hasRole()['id_pegawai']);
        $bulan = request('bulan');

        $data = DB::table("tb_jabatan")
        ->join('tb_master_jabatan', 'tb_jabatan.id_master_jabatan', '=', 'tb_master_jabatan.id')
        ->join('tb_pegawai', 'tb_jabatan.id_pegawai', '=', 'tb_pegawai.id')
        ->leftJoin('tb_skp', 'tb_jabatan.id', '=', 'tb_skp.id_jabatan')
        ->select('tb_pegawai.id as pegawai_id','tb_pegawai.uuid as pegawai_uuid','tb_pegawai.nama', 'tb_pegawai.nip', 'tb_master_jabatan.nama_jabatan','tb_jabatan.id as id_jabatan','tb_master_jabatan.level_jabatan')
        ->where('tb_jabatan.id_parent', $jabatan->id_jabatan)
        ->groupBy('tb_pegawai.nama', 'tb_pegawai.nip', 'tb_master_jabatan.nama_jabatan','tb_jabatan.id')
        ->get();

        $data = $data->map(function ($item) use ($bulan) {
            $item->nilai = $this->configNilai($bulan, $item->pegawai_id);
            return $item;
        });

        return $this->sendResponse($data, 'Review aktivitas Fetched Success');
    }

    public function index(){
        $module = $this->breadcumb();
        return view('review.aktivitas.index',compact('module'));
    }

    public function review(){
        $module = [
            [
                'label' => 'Review',
                'url' => '#'
            ],
            [
                'label' => 'Aktivitas',
                'url' => '/review/aktivitas'
            ],
            [
                'label' => 'Review',
                'url' => '#'
            ],
        ];
        $params = request('pegawai');
        $bulan_params = request('bulan');
        $pegawai = DB::table('tb_pegawai')->select('id')->where('uuid',$params)->first()->id;
        $option_skp = $this->optionSkp($pegawai);
        // dd($option_skp);
        $aktivitas = $this->getMasterAktivitas($pegawai);
        return view('review.aktivitas.review',compact('module','pegawai','option_skp','aktivitas','bulan_params'));
    }

    public function data_review_aktivitas(){
        $pegawai = request('pegawai');
        $bulan = request('bulan');
        $data = array();

        // $target_waktu = $this->checkJabatanDefinitif(Auth::user()->id_pegawai)->target_waktu;

        $jabatan = DB::table('tb_pegawai')->join('tb_jabatan','tb_jabatan.id_pegawai','tb_pegawai.id')->join('tb_master_jabatan','tb_jabatan.id_master_jabatan','tb_master_jabatan.id')->select('tb_jabatan.target_waktu')->where('tb_pegawai.id',$pegawai)->first();

        $target_waktu = $jabatan->target_waktu;


        $data = DB::table('tb_aktivitas')
        ->select('id','uuid','tanggal','created_at as tanggal_input','aktivitas','keterangan','volume','waktu','validation')
        ->whereMonth('tanggal',$bulan)
        ->where('id_pegawai',$pegawai)
        ->where('validation',1)
        ->get();
        $data = $data->map(function ($item) use ($target_waktu) {
            $item->target_waktu =  $target_waktu;
            return $item;
        });

        return $this->sendResponse($data, 'Review Aktivitas Fetched Success');
    }

    public function postReviewAktivitas(Request $request){
        $data = array();
        try {
            foreach ($request->id_aktivitas as $key => $value) {
                $data = Aktivitas::where('id',$value)->first();
                if (isset($request->validation)) {
                    if (isset($request->validation[$key])) {
                        $data->validation = strval($request->validation[$key]);
                        $data->save();
                    }else{
                        $data->validation = '0';
                        $data->save();
                    }
                }else{
                    $data->validation = '0';
                    $data->save();
                }
            }

        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Review Aktivitas Added success');
    }



}
