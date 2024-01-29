<?php

namespace App\Http\Controllers\review;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\SasaranKinerja;
use DB;
use App\Traits\General;

class RealisasiReviewController extends BaseController
{
    use General;

    public function breadcumb(){
        return [
            [
                'label' => 'Review',
                'url' => '#'
            ],
            [
                'label' => 'Realisasi',
                'url' => '#'
            ],
        ];
    }

     public function index(){
        $module = $this->breadcumb();
        return view('review.realisasi.index',compact('module'));
    }

    public function nilai_skp($id_jabatan, $level_jabatan){
        // dd($id_jabatan);

        $nilai_utama = 0;
        $nilai_tambahan = 0;

        $skp_utama = DB::table('tb_skp')
                                ->select('id','rencana')
                                ->where('jenis', 'utama')
                                ->where('id_jabatan', $id_jabatan)
                                ->get();

        $skp_utama->each(function ($skpItem)  {
            $skpItem->aspek_skp = DB::table('tb_aspek_skp')
            ->select('iki', 'aspek_skp', 'target', 'satuan', 'realisasi', 'id_skp')
            ->where('tahun', session('tahun_penganggaran'))
            ->where('id_skp',$skpItem->id)
            ->get();
        });

        // dd($skp_utama);

        $skp_tambahan = DB::table('tb_skp')
                                ->select('id','rencana')
                                ->where('jenis', 'tambahan')
                                ->where('id_jabatan', $id_jabatan)
                                ->get();

        $skp_tambahan->each(function ($skpItem)  {
            $skpItem->aspek_skp = DB::table('tb_aspek_skp')
            ->select('iki', 'aspek_skp', 'target', 'satuan', 'realisasi', 'id_skp')
            ->where('tahun', session('tahun_penganggaran'))
            ->where('id_skp',$skpItem->id)
            ->get();
        });

        if ($level_jabatan == 1 || $level_jabatan == 2) {
            if (count($skp_utama)) {
                    $jumlah_data = 0;
                    $sum_nilai_iki = 0;
                    foreach ($skp_utama as $index => $val) {

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
                    $nilai_utama = round($sum_nilai_iki / $jumlah_data, 1);
                } else {
                    $nilai_utama = 0;
                }

                if (count($skp_tambahan)) {
                    $total_tambahan = 0;
                    foreach ($skp_tambahan as $index => $value) {
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
            if (count($skp_utama) > 0) {
                    
                    $total_utama = 0;
                    $data_utama = 0;
                    foreach ($skp_utama as $index => $val) {
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

                

                if (count($skp_tambahan) > 0) {
                    
                        $total_tambahan = 0;
                        foreach ($skp_tambahan as $keyy => $val) {
                            
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

        $total_nilai = round($nilai_utama + $nilai_tambahan, 1);

        return $total_nilai;

    }

    public function datatable(){
        $jabatan = $this->checkJabatanDefinitif(hasRole()['id_pegawai']);
        $data = DB::table("tb_jabatan")
        ->join('tb_master_jabatan', 'tb_jabatan.id_master_jabatan', '=', 'tb_master_jabatan.id')
        ->join('tb_pegawai', 'tb_jabatan.id_pegawai', '=', 'tb_pegawai.id')
        ->select('tb_pegawai.nama', 'tb_pegawai.nip', 'tb_master_jabatan.nama_jabatan','tb_jabatan.id as id_jabatan','tb_master_jabatan.level_jabatan')
        ->where('tb_jabatan.id_parent', $jabatan->id_jabatan)
        ->where('tb_jabatan.status',session('session_jabatan'))
        ->groupBy('tb_pegawai.nama', 'tb_pegawai.nip', 'tb_master_jabatan.nama_jabatan','tb_jabatan.id')
        ->get();

        $data = $data->map(function ($item) {
            $item->nilai_skp = $this->nilai_skp($item->id_jabatan, $item->level_jabatan);
            return $item;
        });
        return $this->sendResponse($data, 'Review Realisasi SKp Fetched Success');
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
                'label' => 'Realisasi',
                'url' => '#'
            ],
            [
                'label' => 'Review Realisasi',
                'url' => '#'
            ],
        ];

        if ($level > 2) {
            return view('review.realisasi.review_skp_pegawai',compact('module','jabatan','level'));
        }

        return view('review.realisasi.review_skp_kepala',compact('module','jabatan','level'));
    }

    public function postReviewSkp(Request $request){
    
        $data = array();
        $tes = [];
        try {
        $result = [];

        foreach ($request->id_skp as $key => $value) {
            if (!isset($result[$value])) {
                $result[$value] = $key;
            }
        }

        $result = array_flip($result);
        
        // dd($result);

        foreach ($result as $i => $k) {            
            $data = SasaranKinerja::where('id', $k)->first();
             $data->validation = $request->validation[$i];
            $data->keterangan = $request->keterangan[$i];
            $data->save();
        }

        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Review SKP Added success');

    }
}
