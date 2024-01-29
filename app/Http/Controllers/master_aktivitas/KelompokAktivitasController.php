<?php

namespace App\Http\Controllers\master_aktivitas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Requests\KelompokAktivitasRequest;
use App\Models\KelompokAktivitas;
use App\Models\MasterAkvititas;
use DB;
use App\Traits\General;

class KelompokAktivitasController extends BaseController
{
    use General;
    public function breadcumb(){
        return [
            [
                'label' => 'Master Aktivitas',
                'url' => '#'
            ],
            [
                'label' => 'Kelompok Aktivitas',
                'url' => '#'
            ],
        ];
    }

    public function datatable(){
        $data = DB::table('tb_kelompok_jabatan')->join('tb_jenis_jabatan','tb_kelompok_jabatan.id_jenis_jabatan','=','tb_jenis_jabatan.id')->select('tb_kelompok_jabatan.uuid','tb_kelompok_jabatan.id','tb_kelompok_jabatan.kelompok','tb_kelompok_jabatan.id_jenis_jabatan','tb_jenis_jabatan.jenis_jabatan')->orderBy('tb_kelompok_jabatan.id_jenis_jabatan','DESC')->get();
        return $this->sendResponse($data, 'Master Aktivitas Fetched Success');
    }

    public function index(){
        $module = $this->breadcumb();
        return view('admin_kabupaten.master_aktivitas.kelompok_aktivitas',compact('module'));
    }


    public function create(){
        $module = [
            [
                'label' => 'Master Jabatan',
                'url' => '#'
            ],
            [
                'label' => 'Kelompok Aktivitas',
                'url' => '#'
            ],[
                'label' => 'Tambah Kelompok Aktivitas',
                'url' => '#'
            ],
        ];
        $jenis_jabatan = $this->option_jenis_jabatan_all();
        $satuan = $this->option_satuan();
        return view('admin_kabupaten.master_aktivitas.create',compact('module','jenis_jabatan','satuan'));
    }

    public function store(KelompokAktivitasRequest $request){
        $data = array();
        try {
            DB::beginTransaction();
                $data = new KelompokAktivitas();
                $data->kelompok = $request->kelompok;
                $data->id_jenis_jabatan = $request->id_jenis_jabatan;
                $data->save();

                foreach ($request['repeater-aktivitas'] as $key => $value) {
                    $master_aktivitas = new MasterAkvititas();
                    $master_aktivitas->aktivitas = $value['aktivitas'];
                    $master_aktivitas->satuan = $value['satuan'];
                    $master_aktivitas->beban_kerja = $value['beban_kerja'];
                    $master_aktivitas->waktu = $value['waktu'];
                    $master_aktivitas->waktu_penyelesaian = $value['waktu_penyelesaian'];
                    $master_aktivitas->id_kelompok_jabatan = $data->id;
                    $master_aktivitas->waktu_efektif = $value['waktu_efektif'];
                    $master_aktivitas->jenis = 'khusus';
                    $master_aktivitas->save();
                }
            DB::commit();
        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Kelompok Aktivitas Added success');
    }

    public function update(KelompokAktivitasRequest $request, $params){
        $data = array();
        try {

            DB::beginTransaction();

            $data = KelompokAktivitas::where('id',$params)->first();
            $data->kelompok = $request->kelompok;
            $data->id_jenis_jabatan = $request->id_jenis_jabatan;
            $data->save();

            MasterAkvititas::where('id_kelompok_jabatan',$data->id)->delete();

            foreach ($request['repeater-aktivitas'] as $key => $value) {
                    $master_aktivitas = new MasterAkvititas();
                    $master_aktivitas->aktivitas = $value['aktivitas'];
                    $master_aktivitas->satuan = $value['satuan'];
                    $master_aktivitas->beban_kerja = $value['beban_kerja'];
                    $master_aktivitas->waktu = $value['waktu'];
                    $master_aktivitas->waktu_penyelesaian = $value['waktu_penyelesaian'];
                    $master_aktivitas->id_kelompok_jabatan = $data->id;
                    $master_aktivitas->waktu_efektif = $value['waktu_efektif'];
                    $master_aktivitas->jenis = 'khusus';
                    $master_aktivitas->save();
                }

            DB::commit();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Kelompok Aktivitas Update success');
    }

    public function edit($params){
       $data = array();
       $module = [
            [
                'label' => 'Master Jabatan',
                'url' => '#'
            ],
            [
                'label' => 'Kelompok Aktivitas',
                'url' => '#'
            ],[
                'label' => 'Update Kelompok Aktivitas',
                'url' => '#'
            ],
        ];
        $jenis_jabatan = $this->option_jenis_jabatan_all();
        $satuan = $this->option_satuan();
        $data = KelompokAktivitas::where('uuid',$params)->first();
       return view('admin_kabupaten.master_aktivitas.edit',compact('module','jenis_jabatan','satuan','data'));
    }

    public function show($params){
        $data = array();
        try {
            $data = MasterAkvititas::where('id_kelompok_jabatan',$params)->get();
            // $data = KelompokAktivitas::where('uuid',$params)->first();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Kelompok Aktivitas Delete success');
    }

    public function delete(Request $request, $params){
        $data = array();
        try {
            MasterAkvititas::where('id_kelompok_jabatan',$data->id)->delete();
            $data =  DB::table('tb_kelompok_jabatan')->where('id', $params)->delete();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Kelompok Aktivitas Delete success');
    }
}
