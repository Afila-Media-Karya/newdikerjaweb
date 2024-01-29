<?php

namespace App\Http\Controllers\pegawai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Traits\General;
use DB;
class PegawaiNonJobController extends BaseController
{
    use General;
    public function breadcumb(){
        return [
            [
                'label' => 'Pegawai',
                'url' => '#'
            ],
            [
                'label' => 'Pegawai Non Job',
                'url' => '#'
            ],
        ];
    }

    public function datatable(){
        $data = array();
        $satuan_kerja = intval(request('satuan_kerja'));

        $query = DB::table('tb_pegawai')
        ->LeftJoin('tb_jabatan','tb_jabatan.id_pegawai','tb_pegawai.id')
        ->LeftJoin('tb_master_jabatan','tb_jabatan.id_master_jabatan','tb_master_jabatan.id')
        ->LeftJoin('tb_satuan_kerja','tb_pegawai.id_satuan_kerja','=','tb_satuan_kerja.id')
        ->select('tb_pegawai.id','tb_pegawai.uuid','tb_pegawai.nip','tb_pegawai.nama','tb_pegawai.status','tb_master_jabatan.nama_jabatan','tb_satuan_kerja.nama_satuan_kerja')
        ->orderBy('tb_master_jabatan.kelas_jabatan','DESC')
        ->where('tb_pegawai.tipe_pegawai','pegawai_administratif')
        // ->orWhere('tb_pegawai.tipe_pegawai','tenaga_kesehatan')
        ->whereNull('tb_jabatan.id_pegawai');

        if(hasRole()['guard'] == 'web' && hasRole()['role'] == '1'){
            $satuan_kerja_user = $this->infoSatuanKerja(hasRole()['id_pegawai']);
            $query->where('tb_pegawai.id_satuan_kerja',$satuan_kerja_user->id_satuan_kerja);
        }

        if(hasRole()['guard'] == 'web' && hasRole()['role'] == '3'){
            $satuan_kerja_user = $this->infoSatuanKerja(hasRole()['id_pegawai']);
            $query->where('tb_jabatan.id_unit_kerja',$satuan_kerja_user->id_unit_kerja);
        }

        if (hasRole()['guard'] == 'administrator') {
            if ($satuan_kerja > 0) {
                $query->where('tb_pegawai.id_satuan_kerja', $satuan_kerja);
            }   
        }

        $data = $query->get();
        return $this->sendResponse($data, 'Data Pegawai Non Job Fetched Success');
    }

    public function index(){
        $module = $this->breadcumb();
        $satuan_kerja_user = '';
        $unit_kerja = $this->option_satuan_kerja();

        if(hasRole()['guard'] == 'web' && hasRole()['role'] == '1'){
            $satuan_kerja_user = $this->infoSatuanKerja(hasRole()['id_pegawai']);
        }
        return view('pegawai.non_job.index',compact('module','satuan_kerja_user','unit_kerja'));
    }
}
