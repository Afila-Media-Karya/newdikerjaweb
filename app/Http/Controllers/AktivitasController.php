<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Requests\AktivitasRequest;
use App\Models\Aktivitas;
use DB;
use Auth;
use App\Traits\General;
use Carbon\Carbon;

class AktivitasController extends BaseController
{
    use General;

    public function breadcumb(){
        return [
            [
                'label' => 'Aktivitas',
                'url' => '#'
            ],
        ];
    }
    
    public function getAktivitasForCalender(){
        // dd(Auth::user()->id);
        $data = DB::table('tb_aktivitas')->select('id','uuid','aktivitas as title','keterangan as description','tanggal as start')->where('id_pegawai',Auth::user()->id_pegawai)->get();
        return $data;
    }

    public function getAktivitasForCalenderbyPegawai(){
        $pegawai = request('pegawai');
        $data = DB::table('tb_aktivitas')->select('id','uuid','aktivitas as title','keterangan as description','tanggal as start')->where('id_pegawai',$pegawai)->get();
        return $data;
    }

    public function index(){
        $module = $this->breadcumb();
        $option_skp = $this->optionSkp(Auth::user()->id_pegawai);
        $aktivitas = $this->getMasterAktivitas(Auth::user()->id_pegawai);
        return view('aktivitas.index',compact('module','option_skp','aktivitas'));
    }

    public function checkMenitKinerja($params,$pegawai){
        $data = Aktivitas::select(DB::raw("SUM(waktu) as count"))->where('id_pegawai',$pegawai)->where('tanggal',$params)->first();
        if($data->count == null){
            $data->count = 0;
        }

        return $data;
    }

    public function store(AktivitasRequest $request){
        $data = array();
        try {
            date_default_timezone_set('UTC');
            $currentDate = date('Y-m-d');
            $futureDate = date('Y-m-d', strtotime('-6 days', strtotime($currentDate)));

            $pegawai = Auth::user()->id_pegawai;
            if (isset($request->id_pegawai)) {
                $pegawai = $request->id_pegawai;
            }

            // if ($request->tanggal <= $futureDate) {
            //     return $this->sendError('Tanggal aktivitas sudah lewat 5 hari', 'Gagal', 200);
            // }

            $check_absen = $this->checkAbsenByTanggal($pegawai, $request->tanggal);
            
            if (is_null($check_absen)) {
               return $this->sendError('Maaf anda belum absen di tanggal '.Carbon::parse($request->tanggal)->translatedFormat('d F Y'), 'Gagal', 200); 
            }else{
                if ($check_absen->status == 'izin' || $check_absen->status == 'sakit'  || $check_absen->status == 'cuti') {
                    return $this->sendError('Maaf anda belum menambah aktivitas karena sedang '.$check_absen->status, 'Gagal', 200); 
                }
            }

            $waktu = 0;
            $jumlah_kinerja = $this->checkMenitKinerja($request->tanggal,$pegawai);
            $total_time_recorded = $jumlah_kinerja->count;
            $requested_time = $request->waktu;
            $remaining_time = 420 - $total_time_recorded;
            $total_time = $total_time_recorded + $requested_time;

            if ($total_time > 420) {
                $exceeded_time = $total_time - 420;
                return $this->sendError('Jumlah waktu sudah mencapai batas maksimum, Anda tidak bisa menambah aktivitas lebih dari 420 menit', 'Gagal', 200); 
            } else {
                $waktu = $requested_time;
            }

            $data = new Aktivitas();
            $data->aktivitas = $request->aktivitas;
            $data->keterangan = $request->keterangan;
            $data->volume = 0;
            $data->satuan = $request->satuan;
            $data->waktu = $waktu;
            $data->tanggal = $request->tanggal;
            $data->validation = '0';
            $data->id_sasaran = $request->id_sasaran;
            $data->id_pegawai = $pegawai;
            $data->user_insert = Auth::user()->id_pegawai;
            $data->user_update = Auth::user()->id_pegawai;
            $data->save();

        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Aktivitas Added success');
    }

    public function update(AktivitasRequest $request, $params){
        $data = array();
        try {

            $waktu = 0;
            $pegawai = Auth::user()->id_pegawai;
            if (isset($request->id_pegawai)) {
                $waktu = $request->waktu;
                $pegawai = $request->id_pegawai;
            }else{
                date_default_timezone_set('UTC');
                $currentDate = date('Y-m-d');
                $futureDate = date('Y-m-d', strtotime('-6 days', strtotime($currentDate)));

                // if ($request->tanggal <= $futureDate) {
                //     return $this->sendError('Tanggal aktivitas sudah lewat 5 hari', 'Gagal', 200);
                // }

                $check_absen = $this->checkAbsenByTanggal(Auth::user()->id_pegawai, $request->tanggal);
                
                if (is_null($check_absen)) {
                return $this->sendError('Maaf anda belum absen di tanggal '.Carbon::parse($request->tanggal)->translatedFormat('d F Y'), 'Gagal', 200); 
                }else{
                    if ($check_absen->status == 'izin' || $check_absen->status == 'sakit'  || $check_absen->status == 'cuti') {
                        return $this->sendError('Maaf anda belum menambah aktivitas karena sedang '.$check_absen->status, 'Gagal', 200); 
                    }
                }

                $jumlah_kinerja = $this->checkMenitKinerja($request->tanggal,$pegawai);
                $total_time_recorded = $jumlah_kinerja->count;
                $requested_time = $request->waktu;
                $remaining_time = 420 - $total_time_recorded;
                $total_time = $total_time_recorded + $requested_time;

                if ($total_time > 420) {
                    $exceeded_time = $total_time - 420;
                    return $this->sendError('Jumlah waktu sudah mencapai batas maksimum, Anda tidak bisa menambah aktivitas lebih dari 420 menit', 'Gagal', 200); 
                } else {
                    $waktu = $requested_time;
                }
            }

            $data = Aktivitas::where('uuid',$params)->first();
            $data->aktivitas = $request->aktivitas;
            $data->keterangan = $request->keterangan;
            $data->volume = 0;
            $data->satuan = $request->satuan;
            $data->waktu = $waktu;
            $data->id_sasaran = $request->id_sasaran;
            $data->id_pegawai = $pegawai;
            $data->user_update = Auth::user()->id_pegawai;
            $data->save();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Aktivitas Update success');
    }

    public function show($params){
       $data = array();
        try {
            $data = Aktivitas::where('uuid',$params)->first();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Aktivitas Update success'); 
    }

    public function delete(Request $request, $params){
        $data = array();
        try {
            $data =  DB::table('tb_aktivitas')->where('uuid', $params)->delete();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Aktivitas Delete success');
    }


}
