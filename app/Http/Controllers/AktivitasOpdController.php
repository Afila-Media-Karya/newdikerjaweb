<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Traits\General;
use DB;
class AktivitasOpdController extends BaseController
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

    public function configNilai($bulan,$pegawai){
        $nilai_produktivitas_kerja = 0;
        // $jabatan = $this->checkJabatanDefinitif($pegawai);

        $jabatan = DB::table('tb_pegawai')->join('tb_jabatan','tb_jabatan.id_pegawai','tb_pegawai.id')->join('tb_master_jabatan','tb_jabatan.id_master_jabatan','tb_master_jabatan.id')->select('tb_master_jabatan.target_waktu')->where('tb_pegawai.id',$pegawai)->first();

        $target_waktu = 0;

        $jabatan ? $target_waktu = $jabatan->target_waktu : 0;

        $data = DB::table('tb_aktivitas')
        ->selectRaw('COALESCE(SUM(waktu), 0) as capaian')
        ->whereMonth('tanggal',$bulan)
        ->where('id_pegawai',$pegawai)
        ->first();

        if ($target_waktu > 0) {
            $nilai_produktivitas_kerja = ($data->capaian / $target_waktu) * 100;
        }

        if ($nilai_produktivitas_kerja > 100) {
                $nilai_produktivitas_kerja = 100;
        }

        return [
            'nilai_produktivitas_kerja' => $nilai_produktivitas_kerja,
            'target_waktu' => $target_waktu,
            'capaian_waktu' => $data->capaian
        ];
    }

    public function datatable(){
        $bulan = request('bulan');
        
        $satuan_kerja = $this->infoSatuanKerja(hasRole()['id_pegawai']);

        $data = array();
        $data = DB::table('tb_pegawai')
            ->join('tb_satuan_kerja', 'tb_pegawai.id_satuan_kerja', 'tb_satuan_kerja.id')
            ->join('tb_jabatan', 'tb_jabatan.id_pegawai', 'tb_pegawai.id')
            ->join('tb_master_jabatan', 'tb_jabatan.id_master_jabatan', 'tb_master_jabatan.id')
            ->select('tb_pegawai.id', 'tb_pegawai.uuid', 'tb_pegawai.nip', 'tb_pegawai.nama')
            ->orderBy('tb_satuan_kerja.kode_satuan_kerja', 'DESC')
            ->orderBy('tb_master_jabatan.kelas_jabatan', 'ASC')
            ->orderBy('tb_jabatan.id', 'ASC')
            ->where('tb_jabatan.id_satuan_kerja',$satuan_kerja->id_satuan_kerja)
            ->where('tb_jabatan.id_unit_kerja',$satuan_kerja->id_unit_kerja)
            ->get();

        $data = $data->map(function ($item) use ($bulan) {
            $nilai = $this->configNilai($bulan, $item->id);
            $item->nilai_produktivitas_kerja = round($nilai['nilai_produktivitas_kerja'],2);
            $item->target_waktu = $nilai['target_waktu'];
            $item->capaian_waktu = intval($nilai['capaian_waktu']);
            return $item;
        });
        
        return $this->sendResponse($data, 'Review aktivitas Fetched Success');
    }

    public function index(){
        $module = $this->breadcumb();
        return view('aktivitas.opd.index',compact('module'));
    }

    public function detail(){
        $uuid = request('pegawai');
        $pegawai = DB::table('tb_pegawai')->select('id','nama')->where('uuid',$uuid)->first();
        $id_pegawai = $pegawai->id;
        $option_skp = $this->optionSkp($id_pegawai);
        $aktivitas = $this->getMasterAktivitas($id_pegawai);
        $module = [
            [
                'label' => 'Aktivitas',
                'url' => '#'
            ],
            [
                'label' => 'Aktivitas '.$pegawai->nama,
                'url' => '#'
            ],
        ];

        return view('aktivitas.opd.detail',compact('module','id_pegawai','option_skp','aktivitas','uuid'));
    }
}
