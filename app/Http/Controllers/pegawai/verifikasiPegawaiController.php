<?php

namespace App\Http\Controllers\pegawai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\Pegawai;
use App\Models\profil\RiwayatPendidikanFormal;
use App\Models\profil\RiwayatPendidikanNonFormal;
use App\Models\profil\RiwayatKepangkatan;
use App\Models\profil\RiwayatJabatan;
use App\Models\profil\CatatanHukumanDinas;
use App\Models\profil\DiklatStruktural;
use App\Models\profil\DiklatFungsional;
use App\Models\profil\DiklatTeknis;
use App\Models\profil\RiwayatPenghargaan;
use App\Models\profil\RiwayatIstri;
use App\Models\profil\RiwayatAnak;
use App\Models\profil\RiwayatOrangTua;
use App\Models\profil\RiwayatSaudara;
use App\Models\profil\RiwayatKeahlian;
use App\Models\profil\RiwayatBahasa;
use App\Models\profil\FilePegawai;
use DB;
use App\Traits\General;
use Auth;

class verifikasiPegawaiController extends BaseController
{
    use General;

    public function breadcumb(){
        return [
            [
                'label' => 'Pegawai',
                'url' => '#'
            ],
            [
                'label' => 'Verifikasi',
                'url' => '#'
            ],
        ];
    }

    public function datatable(){
        $satuan_kerja = intval(request('satuan_kerja'));

        $data = array();
        $role = hasRole();
        $query = DB::table('tb_pegawai')
        ->leftJoin('tb_satuan_kerja', 'tb_pegawai.id_satuan_kerja', 'tb_satuan_kerja.id')
        ->leftJoin('tb_jabatan','tb_jabatan.id_pegawai','=','tb_pegawai.id')
        ->leftJoin('tb_master_jabatan', 'tb_jabatan.id_master_jabatan', 'tb_master_jabatan.id')
        ->select('tb_pegawai.id','tb_pegawai.uuid','tb_pegawai.nip','tb_pegawai.nama','tb_pegawai.tempat_lahir','tb_pegawai.tanggal_lahir','tb_pegawai.status','tb_pegawai.status_verifikasi')
        ->orderBy('tb_satuan_kerja.kode_satuan_kerja', 'DESC')
        ->orderBy('tb_master_jabatan.kelas_jabatan', 'ASC')
        ->orderBy('tb_jabatan.id', 'ASC');

        if ($satuan_kerja > 0) {
            $query->where('tb_pegawai.id_satuan_kerja',$satuan_kerja);
        }

        

        if ($role['guard'] == 'web' && $role['role'] == '3') {
            $get_satuan_kerja = $this->infoSatuanKerja(Auth::user()->id_pegawai);
            $query->where('tb_jabatan.id_unit_kerja',$get_satuan_kerja->id_unit_kerja);
        }


        $data = $query->get();
        return $this->sendResponse($data, 'Data Pegawai Fetched Success');
    }

    public function index(){
        $satuan_kerja = $this->option_satuan_kerja();
        $module = $this->breadcumb();
        $satuan_kerja_user = '';
        if(hasRole()['guard'] == 'web' && hasRole()['role'] == '1'){
            $satuan_kerja_user = $this->infoSatuanKerja(hasRole()['id_pegawai']);
        }
        return view('pegawai.verifikasi.index',compact('module','satuan_kerja','satuan_kerja_user'));
    }

    public function verifikasi(Request $request){
        $data = array();
        try {
            $data = Pegawai::where('uuid',$request->uuid)->first();
            if(hasRole()['guard'] == 'web' && hasRole()['role'] == '1'){

                if ($data->status_verifikasi === 1) {
                       return $this->sendError('Status Verikasi telah di accept', 'Gagal', 200);
                }

                $data->verifikasi_opd = $request->value;
            }else{

                if ($data->verifikasi_opd === 1 && $request->value === '1') {
                    $data->status_verifikasi = $request->value;
                }else{

                    if ($data->verifikasi_opd === 1) {
                       return $this->sendError('Status Verikasi telah di accept', 'Gagal', 200);
                    }

                    return $this->sendError('Status Verikasi admin opd belum di accept', 'Gagal', 200);
                }
            }
            
            $data->save();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Pegawai Berhasil di Verifikasi');
    }

    public function detail($params){
        $module =  [
            [
                'label' => 'Pegawai',
                'url' => '#'
            ],
            [
                'label' => 'Verifikasi',
                'url' => '/pegawai/verifikasi'
            ],
            [
                'label' => 'Detail',
                'url' => '#'
            ],
        ];

        $data = DB::table('tb_pegawai')->where('uuid',$params)->first();

        $riwayat_pendidikan_formal = RiwayatPendidikanFormal::where('id_pegawai',$data->id)->get();
        $riwayat_pendidikan_non_formal = RiwayatPendidikanNonFormal::where('id_pegawai',$data->id)->get();
        $riwayat_kepangkatan =  RiwayatKepangkatan::where('id_pegawai',$data->id)->get();
        $riwayat_jabatan =  RiwayatJabatan::where('id_pegawai',$data->id)->get();
        $catatan_hukuman_dinas =  CatatanHukumanDinas::where('id_pegawai',$data->id)->get();
        $diklat_struktral = DiklatStruktural::where('id_pegawai',$data->id)->get();
        $diklat_fungsional = DiklatFungsional::where('id_pegawai',$data->id)->get();
        $diklat_teknis = DiklatTeknis::where('id_pegawai',$data->id)->get();
        $riwayat_penghargaan = RiwayatPenghargaan::where('id_pegawai',$data->id)->get();
        $riwayat_istri = RiwayatIstri::where('id_pegawai',$data->id)->get();
        $riwayat_anak = RiwayatAnak::where('id_pegawai',$data->id)->get();
        $riwayat_orang_tua = RiwayatOrangTua::where('id_pegawai',$data->id)->get();
        $riwayat_saudara = RiwayatSaudara::where('id_pegawai',$data->id)->get();
        $riwayat_keahlian = RiwayatKeahlian::where('id_pegawai',$data->id)->get();
        $riwayat_bahasa = RiwayatBahasa::where('id_pegawai',$data->id)->get();
        $file_pegawai = FilePegawai::where('id_pegawai',$data->id)->get();

        return view('pegawai.verifikasi.detail',compact('module','data','riwayat_pendidikan_formal','riwayat_pendidikan_non_formal','riwayat_kepangkatan','riwayat_jabatan','catatan_hukuman_dinas','diklat_struktral','diklat_fungsional','diklat_teknis','riwayat_penghargaan','riwayat_istri','riwayat_anak','riwayat_orang_tua','riwayat_saudara','riwayat_keahlian','riwayat_bahasa','file_pegawai','params'));
    }
}
