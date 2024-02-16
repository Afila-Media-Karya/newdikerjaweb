<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use Auth;
use App\Traits\General;
use App\Traits\Presensi;
use DB;
class DashboardController extends BaseController
{
     use General;
         use Presensi;
    public function index(){
        if (auth()->check()) {
            return redirect()->back();
        }
        return redirect()->route('login');
    }

    public function breadcumb(){
        return [
            [
                'label' => 'Dashboard',
                'url' => '#'
            ],
        ];
    }

    // Users
    public function pegawai(){
        $module = $this->breadcumb();
        // dd(session('session_nama'));
        return view('dashboard.users.index',compact('module'));
    }

    public function admin_opd(){
        $module = $this->breadcumb();
        return view('dashboard.users.adminopd',compact('module'));
    }
    
    // ADMIN
    public function admin_kabupaten(){
        $module = $this->breadcumb();
        return view('dashboard.admin.kabupaten',compact("module"));
    }

    public function super_admin(){
        $module = $this->breadcumb();
        return view('dashboard.admin.superadmin',compact("module"));
    }

    public function keuangan(){
        $module = $this->breadcumb();
        return view('dashboard.admin.keuangan',compact("module"));
    }
    //END ADMIN

    public function setTahunAnggaran(){
        session(['tahun_penganggaran' => request('tahun', date('Y'))]);
        return redirect()->back();
    }

    public function change_session($params){
        $jabatan = request('jabatan');
        session(['session_jabatan' => $params]);
        session(['session_nama_jabatan' => $jabatan]);
        return redirect()->back()->with('success', 'Sesi jabatan anda berhasil di ubah');
    }

    public function data_pegawai(){
        $result = array();
        $bulan = request('bulan');
        $jabatan = $this->checkJabatanDefinitif(Auth::user()->id_pegawai);
        $pegawai = $this->findPegawai(Auth::user()->id_pegawai);
        $atasan = $this->findAtasan(Auth::user()->id_pegawai);
        $tpp = (new LaporanTppController)->data_tpp_pegawai(Auth::user()->id_pegawai, $bulan);

        $pegawai_dinilai = DB::table("tb_jabatan")
        ->join('tb_master_jabatan', 'tb_jabatan.id_master_jabatan', '=', 'tb_master_jabatan.id')
        ->join('tb_pegawai', 'tb_jabatan.id_pegawai', '=', 'tb_pegawai.id')
        ->select('tb_pegawai.nama', 'tb_pegawai.nip', 'tb_master_jabatan.nama_jabatan','tb_jabatan.id as id_jabatan','tb_master_jabatan.level_jabatan')
        ->where('tb_jabatan.id_parent', $jabatan->id_jabatan)
        ->groupBy('tb_pegawai.nama', 'tb_pegawai.nip', 'tb_master_jabatan.nama_jabatan','tb_jabatan.id')
        ->get();

        $persentase_skp = $this->persentase_skp($jabatan->id_jabatan);
        $persentase_kinerja = $this->persentase_kinerja($bulan, Auth::user()->id_pegawai);

        $result = [
            'pegawai' => $pegawai,
            'atasan' => $atasan,
            'tpp' => $tpp,
            'jumlah_pegawai_dinilai' => count($pegawai_dinilai),
            'persentase_skp' => $persentase_skp,
            'persentase_kinerja' => $persentase_kinerja,
            'pegawai_dinilai' => $pegawai_dinilai,
        ];
        return $this->sendResponse($result, 'Data Dashboard Fetched Success');
    }

    public function nilai_persentase_skp_by_opd($satuan_kerja){
        $data = (new LaporanSasaranKinerjaController)->data_skp_by_satuan_kerja2($satuan_kerja);

        $total_nilai = 0;
        $total_utama = 0;
        $nilai_skp = 0;
        foreach ($data as $key => $value) {
            $nilai_utama = 0;
            $nilai_tambahan = 0;
            $total_nilai = 0;
            if ($value->level_jabatan == 1 || $value->level_jabatan == 2) {

                if (count($value->skp_utama)) {
                    $jumlah_data = 0;
                    $sum_nilai_iki = 0;
                    foreach ($value->skp_utama as $index => $val) {

                        foreach ($val->aspek_skp as $k => $v) {
                            $single_rate = $v->target > 0 ? ($v->realisasi / $v->target) * 100 : 0;
                            if ($single_rate > 110) {
                                $nilai_iki = 110 + ((120 - 110) / (110 - 101)) * (110 - 101);
                            } elseif ($single_rate >= 101 && $single_rate <= 110) {
                                $nilai_iki = 110 + ((120 - 110) / (110 - 101)) * ($single_rate - 101);
                            } elseif ($single_rate == 100) {
                                $nilai_iki = 109;
                            } elseif ($single_rate >= 80 && $single_rate <= 99) {
                                $nilai_iki = 70 + ((89 - 70) / (99 - 80)) * ($single_rate - 80);
                            } elseif ($single_rate >= 60 && $single_rate <= 79) {
                                $nilai_iki = 50 + ((69 - 50) / (79 - 60)) * ($single_rate - 60);
                            } elseif ($single_rate >= 0 && $single_rate <= 79) {
                                $nilai_iki = (49 / 59) * $single_rate;
                            }
                            $sum_nilai_iki += $nilai_iki;
                            $jumlah_data++;
                        }
                    }
                    $jumlah_data > 0 ? $nilai_utama = round($sum_nilai_iki / $jumlah_data, 1) : $nilai_utama = 0;
                } else {
                    $nilai_utama = 0;
                }

                if (count($value->skp_tambahan)) {
                    $total_tambahan = 0;
                    foreach ($value->skp_tambahan as $index => $value) {
                        foreach ($value->aspek_skp as $k => $v) {
                            $single_rate = ($v->realisasi / $v->target) * 100;
        
                            if ($single_rate > 110) {
                                $nilai_iki = 110 + ((120 - 110) / (110 - 101)) * (110 - 101);
                            } elseif ($single_rate >= 101 && $single_rate <= 110) {
                                $nilai_iki = 110 + ((120 - 110) / (110 - 101)) * ($single_rate - 101);
                            } elseif ($single_rate == 100) {
                                $nilai_iki = 109;
                            } elseif ($single_rate >= 80 && $single_rate <= 99) {
                                $nilai_iki = 70 + ((89 - 70) / (99 - 80)) * ($single_rate - 80);
                            } elseif ($single_rate >= 60 && $single_rate <= 79) {
                                $nilai_iki = 50 + ((69 - 50) / (79 - 60)) * ($single_rate - 60);
                            } elseif ($single_rate >= 0 && $single_rate <= 79) {
                                $nilai_iki = (49 / 59) * $single_rate;
                            }

                            if ($nilai_iki > 110) {
                                $total_tambahan += 2.4;
                            } elseif ($nilai_iki >= 101 && $nilai_iki <= 110) {
                                $total_tambahan += 1.6;
                            } elseif ($nilai_iki == 100) {
                                $total_tambahan += 1.0;
                            } elseif ($nilai_iki >= 80 && $nilai_iki <= 99) {
                                $total_tambahan += 0.5;
                            } elseif ($nilai_iki >= 60 && $nilai_iki <= 79) {
                                $total_tambahan += 0.3;
                            } elseif ($nilai_iki >= 0 && $nilai_iki <= 79) {
                                $total_tambahan += 0.1;
                            }
                        }
                    }
                    $nilai_tambahan = $total_tambahan;
                } else {
                    $nilai_tambahan = 0;
                }


                
            }else{
                
                if (count($value->skp_utama) > 0) {
                    
                    $total_utama = 0;
                    $data_utama = 0;
                    foreach ($value->skp_utama as $index => $val) {
                        $data_utama++;
                        $sum_capaian = 0;
                        foreach ($val->aspek_skp as $k => $v) {
                            $capaian_iki = ($v->realisasi / $v->target) * 100;

                            if ($capaian_iki > 100) {
                                $nilai_iki = 16;
                            } elseif ($capaian_iki == 100) {
                                $nilai_iki = 13;
                            } elseif ($capaian_iki >= 80 && $capaian_iki <= 99) {
                                $nilai_iki = 8;
                            } elseif ($capaian_iki >= 60 && $capaian_iki <= 79) {
                                $nilai_iki = 3;
                            } elseif ($capaian_iki >= 0 && $capaian_iki <= 59) {
                                $nilai_iki = 1;
                            }
                            $sum_capaian += $nilai_iki;
                        }
                        
                        if ($sum_capaian > 42) {
                            $total_utama += 120;
                        } elseif ($sum_capaian >= 34) {
                            $total_utama += 100;
                        } elseif ($sum_capaian >= 19) {
                            $total_utama += 80;
                        } elseif ($sum_capaian >= 7) {
                            $total_utama += 60;
                        } elseif ($sum_capaian >= 3) {
                            $total_utama += 25;
                        } elseif ($sum_capaian >= 0) {
                            $total_utama += 25;
                        }
                    }
                    
                    $nilai_utama = $data_utama > 0 ? round($total_utama / $data_utama, 1) : 0;  
                } 

                if (count($value->skp_utama) > 0) {
                    
                        $total_tambahan = 0;
                        foreach ($value->skp_tambahan as $keyy => $val) {
                            
                            foreach ($val->aspek_skp as $k => $v) {
                                $capaian_iki = ($v->realisasi / $v->target) * 100;

                                if ($capaian_iki >= 101) {
                                    $nilai_iki = 16;
                                } elseif ($capaian_iki == 100) {
                                    $nilai_iki = 13;
                                } elseif ($capaian_iki >= 80 && $capaian_iki <= 99) {
                                    $nilai_iki = 8;
                                } elseif ($capaian_iki >= 60 && $capaian_iki <= 79) {
                                    $nilai_iki = 3;
                                } elseif ($capaian_iki >= 0 && $capaian_iki <= 79) {
                                    $nilai_iki = 1;
                                }
                                $sum_capaian += $nilai_iki;
                            }

                            if ($sum_capaian >= 42) {
                                $total_tambahan += 2.4;
                            } elseif ($sum_capaian >= 34) {
                                $total_tambahan += 1.6;
                            } elseif ($sum_capaian >= 19) {
                                $total_tambahan += 1;
                            } elseif ($sum_capaian >= 7) {
                                $total_tambahan += 0.5;
                            } elseif ($sum_capaian >= 3) {
                                $total_tambahan += 0.1;
                            } elseif ($sum_capaian >= 0) {
                                $total_tambahan += 0.1;
                            }

                        }
                        $nilai_tambahan = $total_tambahan;
                }


            }
            $total_nilai = $nilai_utama + $nilai_tambahan;
            $nilai_skp += $total_nilai;
        }
        $pembagi = count($data);
        $persentase_skp = 0;
        $pembagi > 0 ? $persentase_skp = ($nilai_skp / $pembagi) : 0;
        return $persentase_skp;
    }

    public function nilai_persentase_kinerja_by_opd($satuan_kerja,$bulan){

        $data = DB::table('tb_pegawai')
            ->select(
                'tb_pegawai.id',
                'tb_pegawai.nama',
                'tb_pegawai.nip',
                'tb_pegawai.golongan',
                'tb_master_jabatan.nama_jabatan',
                'tb_jabatan.target_waktu',
                'tb_master_jabatan.kelas_jabatan',
                DB::raw('COALESCE(SUM(tb_aktivitas.waktu), 0) as capaian_waktu')
            )
            ->join('tb_jabatan', 'tb_jabatan.id_pegawai', 'tb_pegawai.id')
            ->join('tb_master_jabatan', 'tb_jabatan.id_master_jabatan', '=', 'tb_master_jabatan.id')
            ->join('tb_satuan_kerja', 'tb_pegawai.id_satuan_kerja', '=', 'tb_satuan_kerja.id')
            ->leftJoin('tb_aktivitas', function ($join) use ($bulan) {
                $join->on('tb_aktivitas.id_pegawai', '=', 'tb_pegawai.id')
                    ->whereMonth('tb_aktivitas.tanggal', $bulan);
            })
            ->where('tb_pegawai.status', '=', '1')
            ->groupBy(
                'tb_pegawai.id',
                'tb_pegawai.nama',
                'tb_pegawai.nip',
                'tb_pegawai.golongan',
                'tb_master_jabatan.nama_jabatan',
                'tb_jabatan.target_waktu',
                'tb_master_jabatan.kelas_jabatan'
            )
            ->orderBy('tb_master_jabatan.kelas_jabatan', 'DESC');

        if ($satuan_kerja !== null) {
            $data->where('tb_satuan_kerja.id', $satuan_kerja);
        }

        $data = $data->get();

        $total_aktivitas = $data->reduce(function ($carry, $value) {
            $golongan = $value->golongan ?? '';
            $target_nilai = $value->target_waktu ?? 0;

            $target_nilai > 0 ? $nilai_kinerja = (intval($value->capaian_waktu) / $target_nilai) * 100 : $nilai_kinerja = 0;

            if ($nilai_kinerja > 100) {
                $nilai_kinerja = 100;
            }

            return $carry + $nilai_kinerja;
        }, 0);

        $pembagi = count($data);
        $persentase_kinerja = $pembagi > 0 ? ($total_aktivitas / $pembagi) : 0;

        return $persentase_kinerja;
    }

    public function nilai_persentase_kehadiran_by_opd($satuan_kerja, $bulan){
        $tahun = date('Y');
        $tanggal_awal = date("$tahun-$bulan-01");
        $tanggal_akhir = date("Y-m-t", strtotime($tanggal_awal));

        $jml_hari_kerja = $this->jumlahHariKerja($bulan);
        $data = array();

        $result = 0;

        $data = DB::table('tb_pegawai')
        ->join('tb_absen', 'tb_pegawai.id', '=', 'tb_absen.id_pegawai')
        ->join('tb_satuan_kerja', 'tb_pegawai.id_satuan_kerja', '=', 'tb_satuan_kerja.id')
        ->whereMonth('tb_absen.tanggal_absen', $bulan)
        ->whereYear('tb_absen.tanggal_absen', date('Y'))
        ->where('tb_absen.status', 'hadir')
        ->where('tb_pegawai.id_satuan_kerja',$satuan_kerja)
        ->select(
            'tb_satuan_kerja.nama_satuan_kerja as satuan_kerja',
            DB::raw('COUNT(tb_absen.id) as jumlah_hadir'),
            DB::raw('COUNT(DISTINCT tb_pegawai.id) as jumlah_pegawai')
        )
        ->groupBy('tb_satuan_kerja.nama_satuan_kerja')
        ->first();

        if (!is_null($data)) {
            $pembagi = $jml_hari_kerja * $data->jumlah_pegawai;
            $result = $pembagi > 0 ? $data->jumlah_hadir / $pembagi * 100 : 0;
        }

        return $result;

    }

    public function pegawai_jumlah(){

        $role = hasRole();
        $bulan = request('bulan');
        
        $data = array();
        $satuan_kerja = '';
        $pegawai = array();
        
        // $role['guard'] !== 'administrator' ? $satuan_kerja = $this->infoSatuanKerja(Auth::user()->id_pegawai)->id_satuan_kerja : $satuan_kerja = null;

        if ($role['guard'] !== 'administrator') {
            $role['role'] == '1' ? $satuan_kerja = $this->infoSatuanKerja(Auth::user()->id_pegawai)->id_satuan_kerja : $satuan_kerja = $this->infoSatuanKerja(Auth::user()->id_pegawai)->id_unit_kerja;
        }else{
            $satuan_kerja = null;
        }

        $query_pegawai = array();

        if ($role['role'] == '1' || $role['guard'] == 'administrator') {
            $query_pegawai = DB::table('tb_pegawai')
            ->select('id','uuid','golongan','jenis_kelamin','pendidikan');

            if ($satuan_kerja !== null) {
                $query_pegawai->where('id_satuan_kerja',$satuan_kerja);
            }
        }else{
           $query_pegawai = DB::table('tb_pegawai')
            ->leftJoin('tb_jabatan', 'tb_jabatan.id_pegawai', '=', 'tb_pegawai.id')
            ->leftJoin('tb_master_jabatan','tb_jabatan.id_master_jabatan','=','tb_master_jabatan.id')
            ->select('tb_pegawai.id','tb_pegawai.nama','tb_pegawai.uuid','tb_pegawai.golongan','tb_pegawai.jenis_kelamin','tb_pegawai.pendidikan')
            ->where('tb_jabatan.id_unit_kerja',$satuan_kerja);
        }

        $pegawai = $query_pegawai->get();

        $golongan1 = 0;
        $golongan2 = 0;
        $golongan3 = 0;
        $golongan4 = 0;
        $jenis_kelamin_l = 0;
        $jenis_kelamin_p = 0;
        $pendidikan_menengah = 0;
        $pendidikan_tinggi = 0;

        foreach ($pegawai as $key => $value) {
            if (isset($value->golongan) && $value->golongan !== null) {
                if (isset($value->golongan) && $value->golongan !== null) {
                    $golongan = explode("/", $value->golongan);
                    if (count($golongan) >= 2) {
                        $golongan = $golongan[1];
                        if (strstr($golongan, 'IV')) {
                            $golongan4 += 1;
                        } elseif (strstr($golongan, 'III')) {
                            $golongan3 += 1;
                        } elseif (strstr($golongan, 'II')) {
                            $golongan2 += 1;
                        } elseif (strstr($golongan, 'I')) {
                            $golongan1 += 1;
                        }
                    }
                }


                if ($value->jenis_kelamin == 'L') {
                    $jenis_kelamin_l += 1;
                }else {
                    $jenis_kelamin_p += 1;
                }

                if ($value->pendidikan == 'SMP (Sekolah Menengah Pertama)' || $value->pendidikan == 'SMA (Sekolah Menengah Atas)') {
                    $pendidikan_menengah += 1;
                }

                if ($value->pendidikan == 'Diploma I/Akta I' || $value->pendidikan == 'Diploma II/Akta II' || $value->pendidikan == 'Diploma III/Akta III' || $value->pendidikan == 'Diploma IV/Akta IV' || $value->pendidikan == 'S1/Sarjana' || $value->pendidikan == 'S2/Pasca Sarjana' || $value->pendidikan == 'S3/Doktor/Ph.D' ) {
                    $pendidikan_tinggi += 1;
                }
            }
        }

        $pegawai_keluar = 0;
        $query_pegawai_keluar = array();

        if ($role['role'] == '1' || $role['guard'] == 'administrator') {
            $query_pegawai_keluar = DB::table('tb_pegawai_keluar');
            if ($satuan_kerja !== null) {
                $query_pegawai_keluar->where('id_satuan_kerja',$satuan_kerja);
            }
        }else{
            $query_pegawai_keluar = DB::table('tb_pegawai_keluar')
            ->leftJoin('tb_pegawai','tb_pegawai_keluar.id_pegawai','=','tb_pegawai.id')
            ->leftJoin('tb_jabatan', 'tb_jabatan.id_pegawai', '=', 'tb_pegawai.id')
            ->leftJoin('tb_master_jabatan','tb_jabatan.id_master_jabatan','=','tb_master_jabatan.id');
            if ($satuan_kerja !== null) {
                $query_pegawai_keluar->where('tb_jabatan.id_unit_kerja',$satuan_kerja);
            }
        }
        $pegawai_keluar = $query_pegawai_keluar->count();

        $pegawai_masuk = 0;
        $query_pegawai_masuk = array();

        if ($role['role'] == '1' || $role['guard'] == 'administrator') {
            $query_pegawai_masuk = DB::table('tb_pegawai_masuk')
            ->join('tb_pegawai','tb_pegawai_masuk.id_pegawai','=','tb_pegawai_masuk.id');
            if ($satuan_kerja !== null) {
                    $query_pegawai_masuk->where('tb_pegawai.id_satuan_kerja',$satuan_kerja);
            }
        }else {
            $query_pegawai_masuk = DB::table('tb_pegawai_masuk')
            ->leftJoin('tb_pegawai','tb_pegawai_masuk.id_pegawai','=','tb_pegawai.id')
            ->leftJoin('tb_jabatan', 'tb_jabatan.id_pegawai', '=', 'tb_pegawai.id')
            ->leftJoin('tb_master_jabatan','tb_jabatan.id_master_jabatan','=','tb_master_jabatan.id');
            if ($satuan_kerja !== null) {
                $query_pegawai_masuk->where('tb_jabatan.id_unit_kerja',$satuan_kerja);
            }
        }
        $pegawai_masuk = $query_pegawai_masuk->count();


        $pegawai_pensiun = 0;
        $query_pegawai_pensiun = array();

        if ($role['role'] == '1' || $role['guard'] == 'administrator') {
            $query_pegawai_pensiun = DB::table('tb_pegawai_pensiun');
            if ($satuan_kerja !== null) {
                $query_pegawai_pensiun->where('id_satuan_kerja',$satuan_kerja);
            }
        }else {
            $query_pegawai_pensiun = DB::table('tb_pegawai_pensiun')
            ->leftJoin('tb_pegawai','tb_pegawai_pensiun.id_pegawai','=','tb_pegawai.id')
            ->leftJoin('tb_jabatan', 'tb_jabatan.id_pegawai', '=', 'tb_pegawai.id')
            ->leftJoin('tb_master_jabatan','tb_jabatan.id_master_jabatan','=','tb_master_jabatan.id');
            if ($satuan_kerja !== null) {
                $query_pegawai_pensiun->where('tb_jabatan.id_unit_kerja',$satuan_kerja);
            }
        }

        $pegawai_pensiun =  $query_pegawai_pensiun->count();

        $jabatan = array();

        $jabatan_query = DB::table('tb_jabatan')
        ->join('tb_pegawai','tb_jabatan.id_pegawai','=','tb_pegawai.id')
        ->selectRaw('COALESCE(COUNT(CASE WHEN tb_jabatan.status = "definitif" THEN tb_jabatan.status END), 0) as jml_definitif')
        ->selectRaw('COALESCE(COUNT(CASE WHEN tb_jabatan.status = "plt" THEN tb_jabatan.status END), 0) as jml_plt');
        if ($satuan_kerja !== null) {
            $jabatan_query->where('tb_jabatan.id_satuan_kerja',$satuan_kerja);
        }
       $jabatan = $jabatan_query->first();



        $jabatan_kosong = 0;
        $query_jabatan_kosong = DB::table('tb_jabatan')
        ->whereNull('tb_jabatan.id_pegawai');

        if ($satuan_kerja !== null) {
            $query_jabatan_kosong->where('tb_jabatan.id_satuan_kerja',$satuan_kerja);
        }
        $jabatan_kosong = $query_jabatan_kosong->count();

        $total_jk = $jenis_kelamin_l + $jenis_kelamin_p;

        $persen_l = $total_jk > 0 ? $jenis_kelamin_l / $total_jk * 100 : 0;
        $persen_p = $total_jk > 0 ?  $jenis_kelamin_p / $total_jk * 100 : 0;


        $persentase_skp = $role['guard'] !== 'administrator' ? round($this->nilai_persentase_skp_by_opd($satuan_kerja)) : 0;
        $persentase_kinerja = $role['guard'] !== 'administrator' ? round($this->nilai_persentase_kinerja_by_opd($satuan_kerja, $bulan)) : 0;
        $persentase_kehadiran = $role['guard'] !== 'administrator' ? round($this->nilai_persentase_kehadiran_by_opd($satuan_kerja, $bulan)) : 0;

        $data = [
            'golongan1' => $golongan1,
            'golongan2' => $golongan2,
            'golongan3' => $golongan3,
            'golongan4' => $golongan4,
            'jenis_kelamin_l' => $jenis_kelamin_l,
            'jenis_kelamin_p' => $jenis_kelamin_p,
            'persentase_laki' => round($persen_l,2),
            'persentase_perempuan' => round($persen_p,2),
            'pendidikan_menengah' => $pendidikan_menengah,
            'pendidikan_tinggi' => $pendidikan_tinggi,
            'jumlah_pegawai' => count($pegawai),
            'jml_definitif' => $jabatan->jml_definitif,
            'jml_plt' => $jabatan->jml_plt,
            'jabatan_kosong' => $jabatan_kosong,
            'pegawai_keluar' => $pegawai_keluar,
            'pegawai_masuk' => $pegawai_masuk,
            'pegawai_pensiun' => $pegawai_pensiun,
            'persentase_skp' => $persentase_skp,
            'persentase_kinerja' => $persentase_kinerja,
            'persentase_kehadiran' => $persentase_kehadiran,
            'role' => $role['guard']
        ];

        return $this->sendResponse($data, 'Data Dashboard Fetched Success');
    }

    public function data_opd(){
        $role = hasRole();
        
        $data = array();
        $bulan = request("bulan");
        $satuan_kerja = '';
        $pegawai = array();
        
        $role['guard'] !== 'administrator' ? $satuan_kerja = $this->infoSatuanKerja(Auth::user()->id_pegawai)->id_satuan_kerja : $satuan_kerja = null;
        
        $persentase_skp = $this->nilai_persentase_skp_by_opd($satuan_kerja);
        $persentase_kinerja = $this->nilai_persentase_kinerja_by_opd($satuan_kerja,$bulan);  
        $persentase_kehadiran =  $this->nilai_persentase_kehadiran_by_opd($satuan_kerja, $bulan);

   

        $data = [
            'persentase_skp' => round($persentase_skp,2),
            'persentase_kinerja' => round($persentase_kinerja,2),
            'persentase_kehadiran' => round($persentase_kehadiran,2),
        ];

        return $this->sendResponse($data, 'Data Dashboard Fetched Success');
    }

    public function data_per_skpd(){
        $bulan = request('bulan');
        $data = DB::table('tb_satuan_kerja')->select('id','uuid','nama_satuan_kerja')->get();

        $data = $data->map(function ($item) use ($bulan) {
            $jabatan =DB::table('tb_jabatan')
            ->join('tb_pegawai','tb_jabatan.id_pegawai','=','tb_pegawai.id')
            ->selectRaw('COALESCE(COUNT(CASE WHEN tb_jabatan.status = "definitif" THEN tb_jabatan.status END), 0) as jml_definitif')
            ->selectRaw('COALESCE(COUNT(CASE WHEN tb_jabatan.status = "plt" THEN tb_jabatan.status END), 0) as jml_plt')
            ->where('tb_jabatan.id_satuan_kerja',$item->id)
            ->first();

            $pendidikan = DB::table('tb_pegawai')
            ->selectRaw('
                SUM(CASE
                    WHEN golongan LIKE "%Juru Muda/Ia%" OR golongan LIKE "%Juru Muda Tingkat I/Ib%" OR golongan LIKE "%Juru/Ic%" OR golongan LIKE "%Juru Tingkat I/Id%" THEN 1
                    ELSE 0
                END) AS golongan1,
                SUM(CASE
                    WHEN golongan LIKE "%Pengatur Muda/IIa%" OR golongan LIKE "%Pengatur Muda Tingkat I/IIb%" OR golongan LIKE "%Pengatur/IIc%" OR golongan LIKE "%Pengatur Tingkat I/IId%" THEN 1
                    ELSE 0
                END) AS golongan2,
                SUM(CASE
                    WHEN golongan LIKE "%Penata Muda/IIIa%" OR golongan LIKE "%Penata Muda Tingkat I/IIIb%" OR golongan LIKE "%Penata/IIIc%" OR golongan LIKE "%Penata Tingkat I/IIId%" THEN 1
                    ELSE 0
                END) AS golongan3,
                SUM(CASE
                    WHEN golongan LIKE "%Pembina/IVa%" OR golongan LIKE "%Pembina Tingkat I/IVb%" OR golongan LIKE "%Pembina Utama Muda/IVc%" OR golongan LIKE "%Pembina Utama Madya/IVd%" OR golongan LIKE "%Pembina Utama/IVe%" THEN 1
                    ELSE 0
                END) AS golongan4,
                SUM(CASE
                    WHEN pendidikan = "SMP (Sekolah Menengah Pertama)" OR pendidikan = "SMA (Sekolah Menengah Atas)" THEN 1
                    ELSE 0
                END) AS pendidikan_menengah,
                SUM(CASE
                    WHEN pendidikan IN ("Diploma I/Akta I", "Diploma II/Akta II", "Diploma III/Akta III", "Diploma IV/Akta IV", "S1/Sarjana", "S2/Pasca Sarjana", "S3/Doktor/Ph.D") THEN 1
                    ELSE 0
                END) AS pendidikan_tinggi,
                SUM(CASE
                    WHEN jenis_kelamin = "L" THEN 1
                    ELSE 0
                END) AS jml_laki,
                SUM(CASE
                    WHEN jenis_kelamin = "P" THEN 1
                    ELSE 0
                END) AS jml_perempuan
            ')
            ->where('id_satuan_kerja',$item->id)
            ->first();

            $item->persentase_skp = $this->nilai_persentase_skp_by_opd($item->id);

            $item->jumlah_pegawai = DB::table('tb_pegawai')->where('id_satuan_kerja',$item->id)->count();
            $item->persentase_skp = round($this->nilai_persentase_skp_by_opd($item->id));
            $item->persentase_kinerja = round($this->nilai_persentase_kinerja_by_opd($item->id, $bulan));
            $item->persentase_kehadiran = round($this->nilai_persentase_kehadiran_by_opd($item->id, $bulan));
            $item->jabatan_kosong = DB::table('tb_jabatan')->whereNull('tb_jabatan.id_pegawai')->where('tb_jabatan.id_satuan_kerja',$item->id)->count();
            $item->jml_definitif = $jabatan->jml_definitif;
            $item->jml_plt = $jabatan->jml_plt;
            $item->pendidikan_menengah = $pendidikan->pendidikan_menengah;
            $item->pendidikan_tinggi = $pendidikan->pendidikan_tinggi;
            $item->golongan1 = $pendidikan->golongan1;
            $item->golongan2 = $pendidikan->golongan2;
            $item->golongan3 = $pendidikan->golongan3;
            $item->golongan4 = $pendidikan->golongan4;
            $item->jml_laki = $pendidikan->jml_laki;
            $item->jml_perempuan = $pendidikan->jml_perempuan;
            return $item;
        });

        return $this->sendResponse($data, 'Data Dashboard Fetched Success');
    }
}
