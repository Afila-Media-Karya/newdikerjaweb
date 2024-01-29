<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Requests\AbsenRequest;
use App\Http\Requests\AbsenUpdateRequest;
use Auth;
use App\Models\Absen;
use DB;
use App\Traits\General;
class KehadiranController extends BaseController
{
    use General;
    public function breadcumb(){
        return [
            [
                'label' => 'Kehadiran',
                'url' => '#'
            ],
        ];
    }

    public function datatable(){
        $tanggal = request('tanggal');
        $unit_kerja = request('satuan_kerja');
        $validation = request('validasi');
        $status = request('status');
        $data = array();
        if (isset($unit_kerja)) {
            $query = DB::table('tb_absen')
            ->leftJoin('tb_pegawai', 'tb_absen.id_pegawai', 'tb_pegawai.id')
            ->leftJoin('tb_jabatan', 'tb_jabatan.id_pegawai', 'tb_pegawai.id')
            ->leftJoin('tb_pegawai as pegawai_tmt', 'tb_absen.user_update', 'pegawai_tmt.id')
            ->leftJoin('tb_master_jabatan', 'tb_jabatan.id_master_jabatan', 'tb_master_jabatan.id')
            ->select('tb_absen.id', 'tb_absen.uuid', 'tb_absen.id_pegawai', 'tb_absen.tanggal_absen', 'tb_absen.waktu_masuk', 'tb_absen.waktu_keluar', 'tb_absen.status', 'tb_absen.validation', 'tb_pegawai.nama', 'tb_absen.user_update', 'tb_absen.user_type', 'pegawai_tmt.nama AS user_update_1', 'admin.username AS user_updated_0', 'tb_master_jabatan.kelas_jabatan')
            ->leftJoin('users', function ($join) {
                $join->on('tb_absen.user_update', '=', 'users.id_pegawai')
                    ->where('tb_absen.user_type', '=', 0);
            })
            ->leftJoin('admin', function ($join) {
                $join->on('tb_absen.user_update', '=', 'admin.id')
                    ->where('tb_absen.user_type', '<>', 0);
            })
            ->where('tb_absen.tahun', date('Y'))
            ->where('tb_absen.tanggal_absen', $tanggal)
            ->orderBy('tb_master_jabatan.kelas_jabatan', 'ASC')
            ->orderBy('tb_jabatan.id', 'ASC')
            ->where('tb_absen.tahun', session('tahun_penganggaran'));
            
            if ($validation !== 'semua') {
                $query->where('tb_absen.validation', $validation);
            }

            if ($status !== 'semua') {
                $query->where('tb_absen.status', $status);
            }

            if (hasRole()['guard'] == 'administrator') {
                $query->where('tb_jabatan.id_unit_kerja',$unit_kerja);
            }else{
                if (hasRole()['guard'] == 'web' && hasRole()['role'] == '1') {
                    $get_satuan_kerja = $this->infoSatuanKerja(Auth::user()->id_pegawai);
                    $query->where('tb_pegawai.id_satuan_kerja',$get_satuan_kerja->id_satuan_kerja);
                }

                if (hasRole()['guard'] == 'web' && hasRole()['role'] == '3') {
                    $get_satuan_kerja = $this->infoSatuanKerja(Auth::user()->id_pegawai);
                    $query->where('tb_jabatan.id_unit_kerja',$get_satuan_kerja->id_unit_kerja);
                }
            }

             $data = $query->get();
        }

        

        return $this->sendResponse($data, 'Absen Fetched Success');
    }

    public function index(){
        $module = $this->breadcumb();
        $satuan_kerja = $this->option_unit_kerja();
        $satuan_kerja_user = '';
        $pegawai_option = array();
        if(hasRole()['guard'] == 'web'){
            if (hasRole()['role'] == '1') {
                $satuan_kerja_user = $this->infoSatuanKerja(hasRole()['id_pegawai'])->id_satuan_kerja;
                $pegawai_option = DB::table('tb_pegawai')->select('id','nama as text','tipe_pegawai')->where('id_satuan_kerja',$satuan_kerja_user)->where('status','1')->get();
            }else {
                $satuan_kerja_user = $this->infoSatuanKerja(hasRole()['id_pegawai'])->id_unit_kerja;
                $pegawai_option = DB::table('tb_pegawai')
                ->leftJoin('tb_jabatan','tb_jabatan.id_pegawai','=','tb_pegawai.id')
                ->select('tb_pegawai.id','tb_pegawai.nama as text','tipe_pegawai')
                ->where('tb_jabatan.id_unit_kerja',$satuan_kerja_user)
                ->where('tb_pegawai.status','1')->get();
            }
            
        }
        return view('kehadiran.index',compact('module','satuan_kerja','satuan_kerja_user','pegawai_option'));
    }

    public function store(AbsenRequest $request){
        $data = array();
        try {
            // dd($request->all());
            $validation = 0;
            $request->has('validation') == true ?  $validation = intval($request->validation) : $validation = 0;

            if ($request->status == 'dinas luar' || $request->status == 'izin' || $request->status == 'sakit') {
                $validation = 0;
            }

            $check_absen = $this->checkAbsenByTanggal($request->id_pegawai,$request->tanggal_absen);
            if (is_null($check_absen)) {
                $data = new Absen();
                $data->id_pegawai = $request->id_pegawai;
                $data->tanggal_absen = $request->tanggal_absen;
                $data->waktu_masuk = $request->waktu_masuk;
                $data->status = $request->status;
                if ($request->status == 'cuti' || $request->status == 'dinas luar' || $request->status == 'sakit' || $request->status == 'izin') {
                    $data->waktu_keluar = '16:00:00';
                }else{
                    if (isset($request->waktu_keluar)) {
                        $data->waktu_keluar = $request->waktu_keluar;
                    }
                }

                $data->validation = $validation;
                hasRole()['guard'] == 'web' ? $data->user_type = 0 : $data->user_type = 1;
                $data->tahun = date('Y');
                $user_insert = 0;
                $user_update = 0;

                if ($request->tipe_pegawai == 'tenaga_kesehatan') {
                    $data->shift = $request->shift;
                }

                if (hasRole()['guard'] == 'administrator') {
                    $user_insert = hasRole()['id'];
                    $user_update = hasRole()['id'];
                }else {
                    $user_insert = hasRole()['id_pegawai'];
                    $user_update = hasRole()['id_pegawai'];
                }

                $data->user_insert = $user_insert;
                $data->user_update = $user_update;
                $data->save();
            }else{
                return $this->sendError('Pegawai dengan nip '.$check_absen->nip.' telah absen di tanggal '.$check_absen->tanggal_absen, 'Tidak bisa menambah absen!', 200);
            }

            
        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Absen Added success');
    }

    public function update(AbsenUpdateRequest $request, $params){
        $data = array();
        try {
            $validation = 0;
            $request->has('validation') ?  $validation = intval($request->validation) : $validation = 0;

            $data = Absen::where('uuid',$params)->first();
            $data->waktu_masuk = $request->waktu_masuk;
            if (isset($request->waktu_keluar)) {
                $data->waktu_keluar = $request->waktu_keluar;
            }
            $data->status = $request->status;
            $data->validation = $validation;
            $data->tahun = date('Y');
            $user_update = 0;
            hasRole()['guard'] == 'web' ? $data->user_type = 0 : $data->user_type = 1;
            if (hasRole()['guard'] == 'administrator') {
                $user_update = hasRole()['id'];
            }else {
                $user_update = hasRole()['id_pegawai'];
            }

            if ($request->tipe_pegawai == 'tenaga_kesehatan') {
                $data->shift = $request->shift;
            }

            $data->user_update = $user_update;
            $data->save();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Absen Update success');
    }

    public function konvertDetik($params){
        $waktuAwal = $params;
        // Explode waktu menjadi bagian jam, menit, dan detik
        list($jam, $menit, $detik) = explode(":", $waktuAwal);
        // Membulatkan menit
        $menitBulat = round($menit / 5) * 5;
        // Menggabungkan kembali menjadi format waktu
        $waktuBulat = sprintf("%02d:%02d:00", $jam, $menitBulat);
        return $waktuBulat;
    }

    public function show($params){
       $data = array();
        try {
            // $data = Absen::where('uuid',$params)->first();
            $data = DB::table('tb_absen')
            ->join('tb_pegawai','tb_absen.id_pegawai','tb_pegawai.id')
            ->join('tb_satuan_kerja','tb_pegawai.id_satuan_kerja','=','tb_satuan_kerja.id')
            ->join('tb_unit_kerja','tb_unit_kerja.id_satuan_kerja','=','tb_satuan_kerja.id')
            ->LeftJoin('tb_jabatan','tb_jabatan.id_pegawai','tb_pegawai.id')
            ->LeftJoin('tb_master_jabatan','tb_jabatan.id_master_jabatan','tb_master_jabatan.id')
            ->select('tb_absen.id','tb_absen.uuid','tb_absen.id_pegawai','tb_absen.tanggal_absen','tb_absen.waktu_masuk','tb_absen.waktu_keluar','tb_absen.status','tb_absen.validation','tb_jabatan.id_unit_kerja as id_satuan_kerja','tb_unit_kerja.id as id_unit_kerja','tb_absen.shift')
            ->where('tb_absen.uuid',$params)
            ->first();
            
            $data->waktu_masuk = $this->konvertDetik($data->waktu_masuk);
            if ($data->waktu_keluar !== null) {
                $data->waktu_keluar = $this->konvertDetik($data->waktu_keluar);
            }
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Absen Update success'); 
    }

    public function delete(Request $request, $params){
        $data = array();
        try {
            $data =  DB::table('tb_absen')->where('uuid', $params)->delete();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Absen Delete success');
    }

    public function validation(Request $request){
        $data = array();
        try {
            $data = Absen::where('uuid',$request->uuid)->first();
            $data->validation = $request->validation;
            $data->save();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Absen Delete success');
    }

    
}
