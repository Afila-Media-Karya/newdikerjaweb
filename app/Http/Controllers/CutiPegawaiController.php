<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\LayananCuti;
use App\Http\Requests\CutiPegawaiRequest;
use App\Http\Requests\CutiPegawaiUpdateRequest;
use DB;
use App\Traits\General;
use Auth;
use Illuminate\Support\Facades\Storage;
class CutiPegawaiController extends BaseController
{
    use General;
    public function breadcumb(){
        return [
            [
                'label' => 'Layanan',
                'url' => '#'
            ],
            [
                'label' => 'Layanan Cuti',
                'url' => '#'
            ],
        ];
    }

    public function datatable(){
        $data = array();
        $data = DB::table('tb_layanan_cuti')->join('tb_pegawai','tb_layanan_cuti.id_pegawai','=','tb_pegawai.id')->select("tb_layanan_cuti.id",'tb_layanan_cuti.uuid','tb_pegawai.nip','tb_pegawai.nama','tb_layanan_cuti.jenis_layanan','tb_layanan_cuti.status')->where('tb_layanan_cuti.id_pegawai',Auth::user()->id_pegawai)->get();
        return $this->sendResponse($data, 'Layanan Cuti Fetched Success');
    }

    public function index(){
        $module = $this->breadcumb();
        return view('pegawai.cuti.index',compact('module'));
    }

    public function store(CutiPegawaiRequest $request){
        $data = array();
        try {
            $data = new LayananCuti();
            $data->jenis_layanan = $request->jenis_layanan;
            $data->alasan = $request->alasan;
            $data->tanggal_mulai = $request->tanggal_mulai;
            $data->tanggal_akhir = $request->tanggal_akhir;
            $data->alamat = $request->alamat;
            $data->status = '1';
            $data->id_pegawai = Auth::user()->id_pegawai;
            if (isset($request->dokumen)) {
                $file_konten = $request->file('dokumen');
                $filePath = Storage::disk('sftp')->put('/sftpasn/dokumen_cuti', $file_konten);
                $data->dokumen =  $filePath;
            }
            $data->save();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Layanan Update success');
    }

    public function update(CutiPegawaiUpdateRequest $request, $params){
        $data = array();
        try {
            $data = LayananCuti::where('uuid',$params)->first();
            $data->alasan = $request->alasan;
            $data->tanggal_mulai = $request->tanggal_mulai;
            $data->tanggal_akhir = $request->tanggal_akhir;
            $data->alamat = $request->alamat;
            $data->status = '1';
            $data->keterangan = '-';
            $data->id_pegawai = Auth::user()->id_pegawai;
            if (isset($request->dokumen)) {
                $file_konten = $request->file('dokumen');
                $filePath = Storage::disk('sftp')->put('/sftpasn/dokumen_cuti', $file_konten);
                $data->dokumen =  $filePath;
            }
            $data->save();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Layanan Update success');
    }

    public function detail($params){
        $module =  [
            [
                'label' => 'Layanan',
                'url' => '#'
            ],
            [
                'label' => 'Layanan Cuti',
                'url' => '/layanan-pegawai/layanan-cuti'
            ],
            [
                'label' => 'Detail Cuti',
                'url' => '#'
            ],
        ];

        $data = DB::table('tb_layanan_cuti')->join('tb_pegawai','tb_layanan_cuti.id_pegawai','tb_pegawai.id')->join('tb_satuan_kerja','tb_pegawai.id_satuan_kerja','=','tb_satuan_kerja.id')->select('tb_layanan_cuti.id','tb_layanan_cuti.uuid','tb_layanan_cuti.jenis_layanan','tb_layanan_cuti.status','tb_layanan_cuti.alasan','tb_layanan_cuti.tanggal_mulai','tb_layanan_cuti.tanggal_akhir','tb_layanan_cuti.alamat','tb_layanan_cuti.dokumen','tb_layanan_cuti.keterangan','tb_pegawai.nama as nama_pegawai','tb_pegawai.nip','tb_satuan_kerja.nama_satuan_kerja')->where('tb_layanan_cuti.uuid',$params)->first();

        return view('pegawai.cuti.detail',compact('module','data'));
    }

    public function show($params){
       $data = array();
        try {
            $data = LayananCuti::where('uuid',$params)->first();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Layanan Update success'); 
    }

    public function delete(Request $request, $params){
        $data = array();
        try {
            DB::table('tb_layanan_cuti')->where('uuid', $params)->delete();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Layanan Delete success');
    }
}
