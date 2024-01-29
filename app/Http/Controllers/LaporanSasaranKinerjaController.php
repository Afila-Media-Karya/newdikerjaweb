<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\SasaranKinerja;
use DB;
use App\Traits\General;
use Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LaporanSasaranKinerjaController extends BaseController
{
    use General;
    public function breadcumb(){
        return [
            [
                'label' => 'Laporan',
                'url' => '#'
            ],
            [
                'label' => 'Sasaran Kinerja',
                'url' => '#'
            ],
        ];
    }

    public function index(){
        $module = $this->breadcumb();
        return view('laporan.sasaran_kinerja.index',compact('module'));
    }

    public function index_opd(){
        $module = $this->breadcumb();
        $satuan_kerja = $this->infoSatuanKerja(Auth::user()->id_pegawai);
        $satuan_kerja_user = '';
        $nama_satuan_kerja = '';
        $pegawai = array();
        $role = hasRole();
        $query = DB::table('tb_pegawai')
        ->select('tb_pegawai.id','tb_pegawai.nama as text')
        ->leftJoin('tb_jabatan','tb_jabatan.id_pegawai','=','tb_pegawai.id')
        ->where('tb_pegawai.status','1');

        if ($role['role'] == '1') {
            $query->where('tb_pegawai.id_satuan_kerja',$satuan_kerja->id_satuan_kerja);
            $satuan_kerja_user = $satuan_kerja->id_satuan_kerja;
            $nama_satuan_kerja = $satuan_kerja->nama_satuan_kerja;
        }else {
            $query->where('tb_jabatan.id_unit_kerja',$satuan_kerja->id_unit_kerja);
        }
        
        $pegawai = $query->get();

        if ($role['role'] == '1') {
            return view('laporan.sasaran_kinerja.index_opd',compact('module','pegawai','satuan_kerja_user','nama_satuan_kerja'));
        }else {
            return view('laporan.sasaran_kinerja.index_unit',compact('module','pegawai'));
        }

    }

    public function index_kabupaten(){
        $module = $this->breadcumb();
        $satuan_kerja = $this->option_satuan_kerja();
        return view('laporan.sasaran_kinerja.index_kabupaten',compact('module','satuan_kerja'));
    }

    public function data_skp_kepala($pegawai_params){
        $result = array();
        $jabatan = $this->checkJabatanDefinitif($pegawai_params);

        $skp_utama = DB::table('tb_skp')
            ->select('tb_skp.id', 'tb_skp.jenis', 'tb_skp.rencana')
            ->orderBy('tb_skp.jenis', 'ASC')
            ->where('tb_skp.jenis','utama')
            ->where('tb_skp.id_jabatan',$jabatan->id_jabatan)
            ->where('tahun',session('tahun_penganggaran'))
            ->get();


        $skp_tambahan = DB::table('tb_skp')
            ->select('tb_skp.id', 'tb_skp.jenis', 'tb_skp.rencana')
            ->orderBy('tb_skp.jenis', 'ASC')
            ->where('tb_skp.jenis','tambahan')
            ->where('tb_skp.id_jabatan',$jabatan->id_jabatan)
            ->where('tahun',session('tahun_penganggaran'))
            ->get();

        $skp_utama = $skp_utama->map(function ($item) {
            $item->aspek_skp = DB::table('tb_aspek_skp')->select('iki','aspek_skp','target','satuan','realisasi')->where('id_skp',$item->id)->get();
            return $item;
        });

        $skp_tambahan = $skp_tambahan->map(function ($item) {
            $item->aspek_skp = DB::table('tb_aspek_skp')->select('iki','aspek_skp','target','satuan','realisasi')->where('id_skp',$item->id)->get();
            return $item;
        });

        $result['skp']['utama'] = $skp_utama;
        $result['skp']['tambahan'] = $skp_tambahan;
        return $result;
    }

    public function data_skp_pegawai($pegawai_params){
        $result = array();
        $jabatan = $this->checkJabatanDefinitif($pegawai_params);

        $skp_utama = DB::table('tb_skp as skp_pegawai')
            ->leftJoin('tb_skp as skp_atasan','skp_pegawai.id_skp_atasan','=','skp_atasan.id')
            ->select('skp_pegawai.id', 'skp_pegawai.jenis', 'skp_pegawai.rencana','skp_pegawai.id_skp_atasan','skp_atasan.rencana as rencana_kerja_atasan')
            ->orderBy('skp_pegawai.jenis', 'ASC')
            ->orderBy('skp_pegawai.id_skp_atasan','ASC')
            ->where('skp_pegawai.jenis','utama')
            ->where('skp_pegawai.id_jabatan',$jabatan->id_jabatan)
            ->where('skp_pegawai.tahun',session('tahun_penganggaran'))
            ->get();

        $skp_tambahan = DB::table('tb_skp as skp_pegawai')
            ->leftJoin('tb_skp as skp_atasan','skp_pegawai.id_skp_atasan','=','skp_atasan.id')
            ->select('skp_pegawai.id', 'skp_pegawai.jenis', 'skp_pegawai.rencana','skp_pegawai.id_skp_atasan','skp_atasan.rencana as rencana_kerja_atasan')
            ->orderBy('skp_pegawai.jenis', 'ASC')
            ->orderBy('skp_pegawai.id_skp_atasan','ASC')
            ->where('skp_pegawai.jenis','tambahan')
            ->where('skp_pegawai.id_jabatan',$jabatan->id_jabatan)
            ->where('skp_pegawai.tahun',session('tahun_penganggaran'))
            ->get();

        $skp_utama = $skp_utama->map(function ($item) {
            $item->aspek_skp = DB::table('tb_aspek_skp')->select('iki','aspek_skp','target','satuan','realisasi')->where('id_skp',$item->id)->get();
            return $item;
        });

        $skp_tambahan = $skp_tambahan->map(function ($item) {
            $item->aspek_skp = DB::table('tb_aspek_skp')->select('iki','aspek_skp','target','satuan','realisasi')->where('id_skp',$item->id)->get();
            return $item;
        });

        $result['skp']['utama'] = $skp_utama;
        $result['skp']['tambahan'] = $skp_tambahan;
        return $result;
    }

    public function count_skp($id_jabatan){
        return DB::table('tb_skp')
        ->selectRaw('COUNT(CASE WHEN jenis = "utama" THEN 1 END) as jumlah_utama, COUNT(CASE WHEN jenis = "tambahan" THEN 1 END) as jumlah_tambahan')
        ->where('id_jabatan', $id_jabatan)
        ->first();
    }

    public function data_skp_by_satuan_kerja2($satuan_kerja){
        
        $result = array();

        $query = DB::table('tb_pegawai')
            ->join('tb_jabatan', 'tb_jabatan.id_pegawai', '=', 'tb_pegawai.id')
            ->join('tb_master_jabatan', 'tb_master_jabatan.id', '=', 'tb_jabatan.id_master_jabatan')
            ->select('tb_pegawai.id', 'tb_pegawai.nama', 'tb_pegawai.nip', 'tb_pegawai.golongan', 'tb_jabatan.id as id_jabatan', 'tb_master_jabatan.nama_jabatan','tb_master_jabatan.level_jabatan')
            ->orderBy('tb_master_jabatan.kelas_jabatan','DESC');

           if ($satuan_kerja !== null) {
            $query->where('tb_pegawai.id_satuan_kerja', $satuan_kerja);
           } 

          $result = $query->get();

       
        $result = $result->map(function ($item) {
             $item->skp_utama = DB::table('tb_skp')
                                ->select('id','rencana')
                                ->where('jenis', 'utama')
                                ->where('id_jabatan', $item->id_jabatan)
                                ->get();

                        $item->skp_utama->each(function ($skpItem)  {
                            $skpItem->aspek_skp = DB::table('tb_aspek_skp')
                            ->select('iki', 'aspek_skp', 'target', 'satuan', 'realisasi', 'id_skp')
                            ->where('tahun', session('tahun_penganggaran'))
                            ->where('id_skp',$skpItem->id)
                            ->get();
                        });

            $item->skp_tambahan = DB::table('tb_skp')
                                ->select('id','rencana')
                                ->where('jenis', 'tambahan')
                                ->where('id_jabatan', $item->id_jabatan)
                                ->get();

            $item->skp_tambahan->each(function ($skpItem)  {
                            $skpItem->aspek_skp = DB::table('tb_aspek_skp')
                            ->select('iki', 'aspek_skp', 'target', 'satuan', 'realisasi', 'id_skp')
                            ->where('tahun', session('tahun_penganggaran'))
                            ->where('id_skp',$skpItem->id)
                            ->get();
                        });


            return $item;
        });

        return $result;
    }

    public function data_skp_by_satuan_kerja($satuan_kerja,$unit_kerja){
        $result = array();

        $query = DB::table('tb_pegawai')
            ->join('tb_jabatan', 'tb_jabatan.id_pegawai', '=', 'tb_pegawai.id')
            ->join('tb_master_jabatan', 'tb_master_jabatan.id', '=', 'tb_jabatan.id_master_jabatan')
            ->select('tb_pegawai.id', 'tb_pegawai.nama', 'tb_pegawai.nip', 'tb_pegawai.golongan', 'tb_jabatan.id as id_jabatan', 'tb_master_jabatan.nama_jabatan','tb_master_jabatan.level_jabatan','tb_jabatan.status as status_jabatan')
            ->orderBy('tb_master_jabatan.kelas_jabatan','DESC');

        $role = hasRole();

        if ($role['guard'] == 'web') {
            // if ($role['role'] == '1') {
            //     if ($satuan_kerja !== null) {
            //         $query->where('tb_pegawai.id_satuan_kerja', $satuan_kerja);
            //     }
            // }else {
            //     $query->where('tb_jabatan.id_unit_kerja', $unit_kerja);
            // }    
            $query->where('tb_pegawai.id_satuan_kerja', $satuan_kerja);
            if ($unit_kerja !== 'all') {
                $query->where('tb_jabatan.id_unit_kerja', $unit_kerja); 
            } 
        }

        if (hasRole()['guard'] == 'administrator') {
            $query->where('tb_pegawai.id_satuan_kerja', $satuan_kerja);
            if ($unit_kerja !== 'all') {
                $query->where('tb_jabatan.id_unit_kerja', $unit_kerja); 
            }
        }
        

        $result = $query->get();

       
        $result = $result->map(function ($item) {
             $item->skp_utama = DB::table('tb_skp')
                                ->select('id','rencana')
                                ->where('jenis', 'utama')
                                ->where('id_jabatan', $item->id_jabatan)
                                ->get();

                        $item->skp_utama->each(function ($skpItem)  {
                            $skpItem->aspek_skp = DB::table('tb_aspek_skp')
                            ->select('iki', 'aspek_skp', 'target', 'satuan', 'realisasi', 'id_skp')
                            ->where('tahun', session('tahun_penganggaran'))
                            ->where('id_skp',$skpItem->id)
                            ->get();
                        });

            $item->skp_tambahan = DB::table('tb_skp')
                                ->select('id','rencana')
                                ->where('jenis', 'tambahan')
                                ->where('id_jabatan', $item->id_jabatan)
                                ->get();

            $item->skp_tambahan->each(function ($skpItem)  {
                            $skpItem->aspek_skp = DB::table('tb_aspek_skp')
                            ->select('iki', 'aspek_skp', 'target', 'satuan', 'realisasi', 'id_skp')
                            ->where('tahun', session('tahun_penganggaran'))
                            ->where('id_skp',$skpItem->id)
                            ->get();
                        });


            return $item;
        });

        return $result;
    }

    public function export_opd(){
        $satuan_kerja = '';
        $nama_satuan_kerja = '';
        $unit_kerja = '';
        $nama_unit_kerja = '';
        if (request('satuan_kerja')) {
            $satuan_kerja = request('satuan_kerja');
            $nama_satuan_kerja = request('nama_satuan_kerja');
            $unit_kerja = request('id_unit_kerja');
            $nama_unit_kerja = request('nama_unit_kerja');
        }else{
            $satuan_kerja = $this->infoSatuanKerja(Auth::user()->id_pegawai)->id_satuan_kerja;
            $nama_satuan_kerja = $this->infoSatuanKerja(Auth::user()->id_pegawai)->nama_satuan_kerja;
            $unit_kerja = $this->infoSatuanKerja(Auth::user()->id_pegawai)->id_unit_kerja;
            $nama_unit_kerja = $this->infoSatuanKerja(Auth::user()->id_pegawai)->nama_unit_kerja;
        }
        $data = $this->data_skp_by_satuan_kerja($satuan_kerja,$unit_kerja);
        $type = request('type');
        return $this->export_rekapitulasi_nilai_skp($data, $type,$nama_satuan_kerja,$nama_unit_kerja);
    }


    public function export_rekapitulasi_nilai_skp($data, $type,$satuan_kerja,$nama_unit_kerja){
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator('BKPSDM BULUKUMBA')
            ->setLastModifiedBy('BKPSDM BULUKUMBA')
            ->setTitle('Laporan Rekapitulasi SKP Satuan Kerja')
            ->setSubject('Laporan Rekapitulasi SKP Satuan Kerja')
            ->setDescription('Laporan Rekapitulasi SKP Satuan Kerja')
            ->setKeywords('pdf php')
            ->setCategory('Laporan Rekapitulasi SKP Satuan Kerja');
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);

        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_FOLIO);

        $sheet->getRowDimension(1)->setRowHeight(17);
        $sheet->getRowDimension(2)->setRowHeight(17);
        $sheet->getRowDimension(3)->setRowHeight(7);
        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
        $spreadsheet->getDefaultStyle()->getFont()->setSize(10);
        $spreadsheet->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);
        $spreadsheet->getActiveSheet()->getPageSetup()->setVerticalCentered(false);

        // //Margin PDF
        $spreadsheet->getActiveSheet()->getPageMargins()->setTop(0.3);
        $spreadsheet->getActiveSheet()->getPageMargins()->setRight(0.3);
        $spreadsheet->getActiveSheet()->getPageMargins()->setLeft(0.5);
        $spreadsheet->getActiveSheet()->getPageMargins()->setBottom(0.3);

        $perangka_daerah = '';
        if ($satuan_kerja == $nama_unit_kerja || $nama_unit_kerja == 'Semua') {
            $perangka_daerah = $satuan_kerja;
        } else {
            $perangka_daerah = $satuan_kerja . ' - ' . $nama_unit_kerja;
        }


        $sheet->setCellValue('A1', 'REKAPITULASI CAPAIAN PRODUKTIVITAS KERJA (SKP)')->mergeCells('A1:L1');
        $sheet->setCellValue('A2', 'PERANGKAT DAERAH : '. strtoupper($perangka_daerah))->mergeCells('A2:C2');
        // $sheet->setCellValue('C2', ': ' . $perangka_daerah)->mergeCells('C2:K2');
        
        $tahun = ""  . session('tahun_penganggaran') ."";
        $periode = date("01", strtotime($tahun)) . ' s/d ' . date("t", strtotime($tahun)) . ' ' . strftime('%B %Y', mktime(0, 0, 0, date('m') + 1, 0, (int)session('tahun_penganggaran')));

        $sheet->setCellValue('A3', 'PERIODE PENILAIAN : ' . strtoupper($periode))->mergeCells('A3:C3');
        // $sheet->setCellValue('C3', ': ' . $periode)->mergeCells('C3:K3');
        
        $sheet->setCellValue('A4', 'No')->mergeCells('A4:A4');
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->setCellValue('B4', 'Nama / NIP');
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->setCellValue('C4', 'Pangkat Golongan');
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->setCellValue('D4', 'Nama Jabatan');
        $sheet->getColumnDimension('D')->setWidth(50);
        $sheet->setCellValue('E4', 'Nilai SKP');
        $sheet->getColumnDimension('E')->setWidth(10);
        $sheet->getStyle('A4:E4')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('E1F5FE');

        $cell = 4;
        $total_nilai = 0;
        $total_utama = 0;
        foreach ($data as $key => $value) {
            $cell++;
            $nilai_utama = 0;
            $nilai_tambahan = 0;
            
            $sheet->setCellValue('A' . $cell, $key + 1);
            $sheet->setCellValue('B' . $cell, $value->nama . ' / ' . $value->nip);
            $sheet->setCellValue('C' . $cell, $value->golongan);

            $jabatan = $value->nama_jabatan;
            if ($value->status_jabatan !== 'definitif') {
                $jabatan = strtoupper($value->status_jabatan).' '.$value->nama_jabatan;
            }

            $sheet->setCellValue('D' . $cell, $jabatan);

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
                    $nilai_utama = $jumlah_data > 0 ?  round($sum_nilai_iki / $jumlah_data, 1) : 0;
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
            $total_nilai = round($nilai_utama + $nilai_tambahan, 1);
            $sheet->setCellValue('E' . $cell, $total_nilai);
        }
        $sheet->getStyle('A1')->getFont()->setSize(12);
        $sheet->getStyle('A:E')->getAlignment()->setWrapText(true);
        $sheet->getStyle('A4:E4')->getFont()->setBold(true);
        $sheet->getStyle('A4:E4')->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('A5:A' . (count($data) + $cell))->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('B5:B' . (count($data) + $cell))->getAlignment()->setVertical('center');
        $sheet->getStyle('C5:C' . (count($data) + $cell))->getAlignment()->setVertical('center');
        $sheet->getStyle('D5:D' . (count($data) + $cell))->getAlignment()->setVertical('center');
        $sheet->getStyle('E5:E' . (count($data) + $cell))->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('A1')->getAlignment()->setVertical('center')->setHorizontal('center');

        // return $data;

        $border = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '0000000'],
                ],
            ],
        ];

        // $sheet->getStyle('A4:L' . $cell)->applyFromArray($border);
        $sheet->getStyle('A4:E' . $cell)->applyFromArray($border);

        if ($type == 'excel') {
            // Untuk download 
            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Laporan SKP ' .$satuan_kerja. '.xlsx"');
        } else {
            $spreadsheet->getActiveSheet()->getHeaderFooter()
                ->setOddHeader('&C&H' . url()->current());
            $spreadsheet->getActiveSheet()->getHeaderFooter()
                ->setOddFooter('&L&B &RPage &P of &N');
            $class = \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf::class;
            \PhpOffice\PhpSpreadsheet\IOFactory::registerWriter('Pdf', $class);
            header('Content-Type: application/pdf');
            header('Cache-Control: max-age=0');
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Pdf');
        }

        $writer->save('php://output');
    }

    public function export_pegawai(){
        $sasaran_kinerja = request('sasaran_kinerja');
        $type = request('type');

        $pegawai_params = request('pegawai') ? request('pegawai') : Auth::user()->id_pegawai;

        $pegawai = $this->findPegawai($pegawai_params);
        $checkJabatan = $this->checkJabatanDefinitif($pegawai_params);
        $data = array();

        if ($checkJabatan) {
            $atasan = $this->findAtasan($pegawai_params);
            if ($atasan) {
                $level = '';
                $checkJabatan->level_jabatan == 1 || $checkJabatan->level_jabatan == 2 ? $level = 'kepala' : $level = 'pegawai';
                if ($level == 'kepala') {
                    $data = $this->data_skp_kepala($pegawai_params);
                    return $sasaran_kinerja == 'Target Kinerja' ? $this->exportSkpKepala($data,$type,$pegawai,$atasan) : $this->exportRealisasiKepala($data,$type,$pegawai,$atasan);
                        
                    
                }else{
                    $data = $this->data_skp_pegawai($pegawai_params);
                    return $sasaran_kinerja == 'Target Kinerja' ? $this->exportSkpPegawai($data,$type,$pegawai,$atasan) : $this->exportRealisasiPegawai($data,$type,$pegawai,$atasan);
                }
            }else{
               return redirect()->back()->withErrors(['error' => 'Belum bisa membuka laporan, pegawai belum ada atasan langsung']); 
            }
            
        }else{
            return redirect()->back()->withErrors(['error' => 'Belum bisa membuka laporan, pegawai tersebut belum mempunyai jabatan']);
        }
    }

    public function exportSkpKepala($data, $type, $pegawai, $atasan){
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator('BKPSDM BULUKUMBA')
            ->setLastModifiedBy('BKPSDM BULUKUMBA')
            ->setTitle('Laporan SKP Kepala')
            ->setSubject('Laporan SKP Kepala')
            ->setDescription('Laporan SKP Kepala')
            ->setKeywords('pdf php')
            ->setCategory('LAPORAN SKP Kepala');
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_FOLIO);

        $sheet->getRowDimension(1)->setRowHeight(17);
        $sheet->getRowDimension(2)->setRowHeight(17);
        $sheet->getRowDimension(3)->setRowHeight(7);
        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
        $spreadsheet->getDefaultStyle()->getFont()->setSize(10);
        $spreadsheet->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);
        $spreadsheet->getActiveSheet()->getPageSetup()->setVerticalCentered(false);

        // //Margin PDF
        $spreadsheet->getActiveSheet()->getPageMargins()->setTop(0.3);
        $spreadsheet->getActiveSheet()->getPageMargins()->setRight(0.3);
        $spreadsheet->getActiveSheet()->getPageMargins()->setLeft(0.5);
        $spreadsheet->getActiveSheet()->getPageMargins()->setBottom(0.3);

        $sheet->setCellValue('A1', 'SASARAN KINERJA PEGAWAI (SKP)')->mergeCells('A1:F1');
        $sheet->setCellValue('A2', 'PEJABAT PIMPINAN TINGGI DAN PIMPINAN UNIT KERJA MANDIRI')->mergeCells('A2:F2');

        $sheet->setCellValue('A4', 'PERIODE PENILAIAN')->mergeCells('A4:F4');
        $sheet->setCellValue('A5', $pegawai->nama_satuan_kerja)->mergeCells('A5:C5');

        $tahun = ""  . session('tahun_penganggaran') . "";
       $periode = "Tahun " . session('tahun_penganggaran');
        $sheet->setCellValue('D5', $periode)->mergeCells('D5:F5');

        $sheet->setCellValue('A6', 'PEGAWAI YANG DINILAI')->mergeCells('A6:C6');
        $sheet->setCellValue('D6', 'PEJABAT PENILAI PEKERJA')->mergeCells('D6:F6');

        $sheet->setCellValue('A7', 'Nama')->mergeCells('A7:B7');
        $sheet->setCellValue('C7', $pegawai->nama)->mergeCells('C7:C7');
        $sheet->setCellValue('A8', 'NIP')->mergeCells('A8:B8');
        $sheet->setCellValue('C8', "" . $pegawai->nip)->mergeCells('C8:C8');
        $sheet->setCellValue('A9', 'Pangkat / Gol Ruang')->mergeCells('A9:B9');
        $sheet->setCellValue('C9', $pegawai->golongan)->mergeCells('C9:C9');
        $sheet->setCellValue('A10', 'Jabatan')->mergeCells('A10:B10');

        $jabatan_pegawai = $pegawai->nama_jabatan;

        if ($pegawai->status_jabatan !== 'definitif') {
            $jabatan_pegawai = $pegawai->status_jabatan.' '.$pegawai->nama_jabatan;
        }

        $sheet->setCellValue('C10', $jabatan_pegawai)->mergeCells('C10:C10');
        $sheet->setCellValue('A11', 'Unit kerja')->mergeCells('A11:B11');
        $sheet->setCellValue('C11', $pegawai->nama_unit_kerja)->mergeCells('C11:C11');

        $sheet->setCellValue('D7', 'Nama');
        if ($atasan !== "") {
            $sheet->setCellValue('E7', $atasan->nama)->mergeCells('E7:F7');
        } else {
            $sheet->setCellValue('E7', '-')->mergeCells('E7:F7');
        }
        $sheet->setCellValue('D8', 'NIP');
        if ($atasan !== "") {
            $sheet->setCellValue('E8', "" . $atasan->nip)->mergeCells('E8:F8');
        } else {
            $sheet->setCellValue('E8', '-')->mergeCells('E8:F8');
        }
        $sheet->setCellValue('D9', 'Pangkat / Gol Ruang');
        if ($atasan !== "") {
            $sheet->setCellValue('E9', $atasan->golongan)->mergeCells('E9:F9');
        } else {
            $sheet->setCellValue('E9', '-')->mergeCells('E9:F9');
        }
        $sheet->setCellValue('D10', 'Jabatan');
        if ($atasan !== "") {

            $jabatan_atasan = $atasan->nama_jabatan;

            if ($atasan->status_jabatan !== 'definitif') {
                $jabatan_atasan = $atasan->status_jabatan.' '.$atasan->nama_jabatan;
            }

            $sheet->setCellValue('E10', $jabatan_atasan)->mergeCells('E10:F10');
        } else {
            $sheet->setCellValue('E10', '-')->mergeCells('E10:F10');
        }
        $sheet->setCellValue('D11', 'Unit kerja');
        if ($atasan !== "") {
            $sheet->setCellValue('E11', $atasan->nama_unit_kerja)->mergeCells('E11:F11');
        } else {
            $sheet->setCellValue('E11', '-')->mergeCells('E11:F11');
        }

        $sheet->setCellValue('A12', 'No')->mergeCells('A12:A12');
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->setCellValue('B12', 'RENCANA KINERJA')->mergeCells('B12:C12');
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(50);
        $sheet->setCellValue('D12', 'INDIKATOR KINERJA INDIVIDU')->mergeCells('D12:E12');
        $sheet->getColumnDimension('E')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(40);
        $sheet->setCellValue('F12', 'TARGET')->mergeCells('F12:F12');
        $sheet->getColumnDimension('F')->setWidth(20);

        $sheet->getStyle('A1:A2')->getFont()->setSize(12);
        $sheet->getStyle('A:F')->getAlignment()->setWrapText(true);
        $sheet->getStyle('A6:F12')->getFont()->setBold(true);
        $sheet->getStyle('A4:F11')->getAlignment()->setVertical('center')->setHorizontal('left');
        $sheet->getStyle('D4:F5')->getAlignment()->setVertical('center')->setHorizontal('right');
        $sheet->getStyle('A6:F6')->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('A1:A2')->getAlignment()->setVertical('center')->setHorizontal('center');


        $cell = 13;
        if (count($data['skp']['utama'])) {
            $sheet->setCellValue('B' . $cell, 'A. KINERJA UTAMA')->mergeCells('B' . $cell . ':F' . $cell);
            $sheet->getStyle('B' . $cell . ':F' . $cell)->getFont()->setBold(true);
            $cell++;
            foreach ($data['skp']['utama'] as $index => $value) {

                // print rencana_kerja
                $sheet->setCellValue('A' . $cell, $index + 1)->mergeCells('A' . $cell . ':A' . ($cell + count($value->aspek_skp) - 1));
                $sheet->setCellValue('B' . $cell, $value->rencana)->mergeCells('B' . $cell . ':C' . ($cell + count($value->aspek_skp) - 1));

                foreach ($value->aspek_skp as $k => $v) {
                    $sheet->setCellValue('D' . $cell, $v->iki)->mergeCells('D' . $cell . ':E' . $cell);
                    $sheet->setCellValue('F' . $cell, $v->target . ' ' . $v->satuan);
                    $cell++;
                }
            }
        } else {
            $sheet->setCellValue('B' . $cell, 'A. KINERJA UTAMA')->mergeCells('B' . $cell . ':F' . $cell);
            $sheet->getStyle('B' . $cell . ':F' . $cell)->getFont()->setBold(true);
            $cell++;
            $sheet->setCellValue('A' . $cell, 1);
            $sheet->setCellValue('B' . $cell, '-')->mergeCells('B' . $cell . ':C' . $cell);
            $sheet->setCellValue('D' . $cell, '-')->mergeCells('D' . $cell . ':E' . $cell);
            $sheet->setCellValue('F' . $cell, '-');
            $cell++;
        }

        // TAMBAHAN
        if (count($data['skp']['tambahan']) > 0) {
            $sheet->setCellValue('B' . $cell, 'B. KINERJA TAMBAHAN')->mergeCells('B' . $cell . ':F' . $cell);
            $sheet->getStyle('B' . $cell . ':F' . $cell)->getFont()->setBold(true);
            $cell++;
            foreach ($data['skp']['tambahan'] as $index => $value) {
                $sheet->setCellValue('A' . $cell, $index + 1);
                $sheet->setCellValue('B' . $cell, $value->rencana)->mergeCells('B' . $cell . ':C' . $cell);
                foreach ($value->aspek_skp as $k => $v) {
                    $sheet->setCellValue('D' . $cell, $v->iki)->mergeCells('D' . $cell . ':E' . $cell);
                    $sheet->setCellValue('F' . $cell, $v->target . ' ' . $v->satuan);
                    $cell++;
                }
            }
        } else {
            $sheet->setCellValue('B' . $cell, 'B. KINERJA TAMBAHAN')->mergeCells('B' . $cell . ':F' . $cell);
            $sheet->getStyle('B' . $cell . ':F' . $cell)->getFont()->setBold(true);
            $cell++;
            $sheet->setCellValue('A' . $cell, 1);
            $sheet->setCellValue('B' . $cell, '-')->mergeCells('B' . $cell . ':C' . $cell);
            $sheet->setCellValue('D' . $cell, '-')->mergeCells('D' . $cell . ':E' . $cell);
            $sheet->setCellValue('F' . $cell, '-');
        }

        $sheet->getStyle('A12:F' . $cell)->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('B13:E' . $cell)->getAlignment()->setVertical('center')->setHorizontal('left');

        $border = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '0000000'],
                ],
            ],
        ];

        $sheet->getStyle('A6:F' . $cell)->applyFromArray($border);

        $cell++;
        $sheet->setCellValue('B' . $cell, '')->mergeCells('B' . $cell . ':F' . $cell);

        $tgl_cetak = date("t", strtotime($tahun)) . ' ' . strftime('%B %Y', mktime(0, 0, 0, date("n") + 1, 0, (int)session('tahun_penganggaran')));

        $sheet->setCellValue('E' . ++$cell, 'BULUKUMBA, ' . $tgl_cetak)->mergeCells('E' . $cell . ':F' . $cell);
        $sheet->getStyle('E' . $cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('E' . ++$cell, 'Pejabat Penilai Kinerja')->mergeCells('E' . $cell . ':F' . $cell);
        $sheet->getStyle('E' . $cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $cell = $cell + 3;
        $sheet->setCellValue('E' . ++$cell, $atasan->nama)->mergeCells('E' . $cell . ':F' . $cell);
        $sheet->getStyle('E' . $cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('E' . ++$cell, $atasan->nip)->mergeCells('E' . $cell . ':F' . $cell);
        $sheet->getStyle('E' . $cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        if ($type == 'excel') {
            // Untuk download 
            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Laporan SKP ' . $pegawai->nama . '.xlsx"');
        } else {
            $spreadsheet->getActiveSheet()->getHeaderFooter()
                ->setOddHeader('&C&H' . url()->current());
            $spreadsheet->getActiveSheet()->getHeaderFooter()
                ->setOddFooter('&L&B &RPage &P of &N');
            $class = \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf::class;
            \PhpOffice\PhpSpreadsheet\IOFactory::registerWriter('Pdf', $class);
            header('Content-Type: application/pdf');
            header('Cache-Control: max-age=0');
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Pdf');
        }

        $writer->save('php://output');
    }

    public function exportRealisasiKepala($data,$type, $pegawai, $atasan){
        $spreadsheet = new Spreadsheet();

        $spreadsheet->getProperties()->setCreator('BKPSDM BULUKUMBA')
            ->setLastModifiedBy('BKPSDM BULUKUMBA')
            ->setTitle('Laporan Penilaian SKP Kepala')
            ->setSubject('Laporan Penilaian SKP Kepala')
            ->setDescription('Laporan Penilaian SKP Kepala')
            ->setKeywords('pdf php')
            ->setCategory('LAPORAN Penilaian SKP KEPALA');
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_FOLIO);
        $sheet->getRowDimension(1)->setRowHeight(17);
        $sheet->getRowDimension(2)->setRowHeight(17);
        $sheet->getRowDimension(3)->setRowHeight(7);
        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
        $spreadsheet->getDefaultStyle()->getFont()->setSize(10);
        $spreadsheet->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);
        $spreadsheet->getActiveSheet()->getPageSetup()->setVerticalCentered(true);

        // //Margin PDF
        $spreadsheet->getActiveSheet()->getPageMargins()->setTop(0.3);
        $spreadsheet->getActiveSheet()->getPageMargins()->setRight(0.3);
        $spreadsheet->getActiveSheet()->getPageMargins()->setLeft(0.5);
        $spreadsheet->getActiveSheet()->getPageMargins()->setBottom(0.3);

        $sheet->setCellValue('A1', 'PENILAIAN SASARAN KINERJA PEGAWAI (SKP)')->mergeCells('A1:J1');
        $sheet->setCellValue('A2', 'PEJABAT PIMPINAN TINGGI DAN PIMPINAN UNIT KERJA MANDIRI')->mergeCells('A2:J2');

        $sheet->setCellValue('A4', 'PERIODE PENILAIAN')->mergeCells('A4:J4');
        $sheet->setCellValue('A5', $pegawai->nama_satuan_kerja)->mergeCells('A5:C5');

        $tahun = ""  . session('tahun_penganggaran') ."";
         $periode = "Tahun " . session('tahun_penganggaran');
        $sheet->setCellValue('D5', $periode)->mergeCells('D5:J5');

        $sheet->setCellValue('A6', 'PEGAWAI YANG DINILAI')->mergeCells('A6:C6');
        $sheet->setCellValue('D6', 'PEJABAT PENILAI PEKERJA')->mergeCells('D6:J6');


        $sheet->setCellValue('A7', 'Nama')->mergeCells('A7:B7');
        $sheet->setCellValue('C7', $pegawai->nama)->mergeCells('C7:C7');
        $sheet->setCellValue('A8', 'NIP')->mergeCells('A8:B8');
        $sheet->setCellValue('C8', "'" . $pegawai->nip)->mergeCells('C8:C8');
        $sheet->setCellValue('A9', 'Pangkat / Gol Ruang')->mergeCells('A9:B9');
        $sheet->setCellValue('C9', $pegawai->golongan)->mergeCells('C9:C9');
        $sheet->setCellValue('A10', 'Jabatan')->mergeCells('A10:B10');

        $jabatan_pegawai = $pegawai->nama_jabatan;

        if ($pegawai->status_jabatan !== 'definitif') {
            $jabatan_pegawai = $pegawai->status_jabatan.' '.$pegawai->nama_jabatan;
        }

        $sheet->setCellValue('C10', $jabatan_pegawai)->mergeCells('C10:C10');
        $sheet->setCellValue('A11', 'Unit kerja')->mergeCells('A11:B11');
        $sheet->setCellValue('C11', $pegawai->nama_unit_kerja)->mergeCells('C11:C11');


        $sheet->setCellValue('D7', 'Nama')->mergeCells('D7:E7');
        if ($atasan != "") {
            $sheet->setCellValue('F7', $atasan->nama)->mergeCells('F7:J7');
        } else {
            $sheet->setCellValue('F7', '-')->mergeCells('F7:J7');
        }
        $sheet->setCellValue('D8', 'NIP')->mergeCells('D8:E8');
        if ($atasan != "") {
            $sheet->setCellValue('F8', "'" . $atasan->nip)->mergeCells('F8:J8');
        } else {
            $sheet->setCellValue('F8', '-')->mergeCells('F8:J8');
        }
        $sheet->setCellValue('D9', 'Pangkat / Gol Ruang')->mergeCells('D9:E9');
        if ($atasan != "") {
            $sheet->setCellValue('F9', $atasan->golongan)->mergeCells('F9:J9');
        } else {
            $sheet->setCellValue('F9', '-')->mergeCells('F9:J9');
        }
        $sheet->setCellValue('D10', 'Jabatan')->mergeCells('D10:E10');
        if ($atasan != "") {
            $jabatan_atasan = $atasan->nama_jabatan;

            if ($atasan->status_jabatan !== 'definitif') {
                $jabatan_atasan = $atasan->status_jabatan.' '.$atasan->nama_jabatan;
            }
            $sheet->setCellValue('F10', $jabatan_atasan)->mergeCells('F10:J10');
        } else {
            $sheet->setCellValue('F10', '-')->mergeCells('F10:J10');
        }
        $sheet->setCellValue('D11', 'Unit kerja')->mergeCells('D11:E11');
        if ($atasan != "") {
            $sheet->setCellValue('F11', $atasan->nama_unit_kerja)->mergeCells('F11:J11');
        } else {
            $sheet->setCellValue('F11', '-')->mergeCells('F11:J11');
        }

        $sheet->setCellValue('A12', 'No.')->mergeCells('A12:A12');
        $sheet->getColumnDimension('A')->setWidth(7);
        $sheet->setCellValue('B12', 'Rencana Kinerja')->mergeCells('B12:B12');
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->setCellValue('C12', 'Indikator Kinerja Individu')->mergeCells('C12:C12');
        $sheet->getColumnDimension('C')->setWidth(50);
        $sheet->setCellValue('D12', 'Target')->mergeCells('D12:D12');
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->setCellValue('E12', 'Realisasi')->mergeCells('E12:E12');
        $sheet->getColumnDimension('E')->setWidth(12);
        $sheet->setCellValue('F12', 'Single Rate')->mergeCells('F12:F12');
        $sheet->getColumnDimension('F')->setWidth(12);
        $sheet->setCellValue('G12', 'Capaian IKI')->mergeCells('G12:G12');
        $sheet->getColumnDimension('G')->setWidth(12);
        $sheet->setCellValue('H12', 'Kategori Capaian')->mergeCells('H12:H12');
        $sheet->getColumnDimension('H')->setWidth(12);
        $sheet->setCellValue('I12', 'Nilai Capaian IKI')->mergeCells('I12:I12');
        $sheet->getColumnDimension('I')->setWidth(12);
        $sheet->setCellValue('J12', 'Nilai Timbang')->mergeCells('J12:J12');
        $sheet->getColumnDimension('J')->setWidth(12);

        $sheet->getStyle('A1:J2')->getFont()->setSize(12);
        $sheet->getStyle('A:J')->getAlignment()->setWrapText(true);
        $sheet->getStyle('A6:J12')->getFont()->setBold(true);
        $sheet->getStyle('A4:J11')->getAlignment()->setVertical('center')->setHorizontal('left');
        $sheet->getStyle('D4:J5')->getAlignment()->setVertical('center')->setHorizontal('right');
        $sheet->getStyle('A6:J6')->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('A1:A2')->getAlignment()->setVertical('center')->setHorizontal('center');

        $cell = 13;
        $nilai_utama = 0;
        $nilai_tambahan = 0;
        // //UTAMA
        if (count($data['skp']['utama'])) {
            $sheet->setCellValue('B' . $cell, 'A. KINERJA UTAMA')->mergeCells('B' . $cell . ':J' . $cell);
            $sheet->getStyle('B' . $cell . ':J' . $cell)->getFont()->setBold(true);
            $cell++;
            $jumlah_data = 0;
            $sum_nilai_iki = 0;
            foreach ($data['skp']['utama'] as $index => $value) {
                $sheet->getStyle('A' . $cell)->getAlignment()->setVertical('top')->setHorizontal('center');
                $sheet->setCellValue('A' . $cell, $index + 1)->mergeCells('A' . $cell . ':A' . ($cell + count($value->aspek_skp) - 1));
                $sheet->setCellValue('B' . $cell, $value->rencana)->mergeCells('B' . $cell . ':B' . ($cell + count($value->aspek_skp) - 1));

                foreach ($value->aspek_skp as $k => $v) {
                    $sheet->setCellValue('C' . $cell, $v->iki);
                    $kategori_ = '';
                    $sheet->setCellValue('D' . $cell, $v->target . ' ' . $v->satuan);
                    $sheet->setCellValue('E' . $cell, $v->realisasi . ' ' . $v->satuan);
                    $single_rate = $v->target > 0 ? ($v->realisasi / $v->target) * 100 : 0;

                    $sheet->setCellValue('F' . $cell, round($single_rate, 0) . ' %');
                    if ($single_rate > 110) {
                        $sheet->setCellValue('G' . $cell, '110 %');
                        $sheet->setCellValue('H' . $cell, 'Sangat Baik');
                        $nilai_iki = 110 + ((120 - 110) / (110 - 101)) * (110 - 101);
                        $sheet->setCellValue('I' . $cell, round($nilai_iki, 1));
                    } elseif ($single_rate >= 101 && $single_rate <= 110) {
                        $sheet->setCellValue('G' . $cell, round($single_rate, 0) . ' %');
                        $sheet->setCellValue('H' . $cell, 'Sangat Baik');
                        $nilai_iki = 110 + ((120 - 110) / (110 - 101)) * ($single_rate - 101);
                        $sheet->setCellValue('I' . $cell, round($nilai_iki, 1));
                    } elseif ($single_rate == 100) {
                        $sheet->setCellValue('G' . $cell, round($single_rate, 0) . ' %');
                        $sheet->setCellValue('H' . $cell, 'Baik');
                        $nilai_iki = 109;
                        $sheet->setCellValue('I' . $cell, round($nilai_iki, 1));
                    } elseif ($single_rate >= 80 && $single_rate <= 99) {
                        $sheet->setCellValue('G' . $cell, round($single_rate, 0) . ' %');
                        $sheet->setCellValue('H' . $cell, 'Cukup');
                        $nilai_iki = 70 + ((89 - 70) / (99 - 80)) * ($single_rate - 80);
                        $sheet->setCellValue('I' . $cell, round($nilai_iki, 1));
                    } elseif ($single_rate >= 60 && $single_rate <= 79) {
                        $sheet->setCellValue('G' . $cell, round($single_rate, 0) . ' %');
                        $sheet->setCellValue('H' . $cell, 'Kurang');
                        $nilai_iki = 50 + ((69 - 50) / (79 - 60)) * ($single_rate - 60);
                        $sheet->setCellValue('I' . $cell, round($nilai_iki, 1));
                    } elseif ($single_rate >= 0 && $single_rate <= 79) {
                        $sheet->setCellValue('G' . $cell, round($single_rate, 0) . ' %');
                        $sheet->setCellValue('H' . $cell, 'Sangat Kurang');
                        $nilai_iki = (49 / 59) * $single_rate;
                        $sheet->setCellValue('I' . $cell, round($nilai_iki, 1));
                    }
                    $sum_nilai_iki += $nilai_iki;
                    $jumlah_data++;


                    $sheet->setCellValue('J' . ($cell - $jumlah_data - 1), '');
                    $cell++;
                }
            }

            $sheet->getStyle('B' . $cell . ':I' . $cell)->getAlignment()->setVertical('top')->setHorizontal('right');
            $sheet->getStyle('J' . $cell)->getAlignment()->setVertical('top')->setHorizontal('center');
            $sheet->getStyle('B' . $cell . ':J' . $cell)->getFont()->setBold(true);
            // $cell++;
            $sheet->setCellValue('B' . $cell, 'NILAI KINERJA UTAMA')->mergeCells('B' . $cell . ':I' . $cell);
            $sheet->setCellValue('J' . $cell, $nilai_utama = round($sum_nilai_iki / $jumlah_data, 1));
            $cell++;
        } else {
            $sheet->setCellValue('B' . $cell, 'A. KINERJA UTAMA')->mergeCells('B' . $cell . ':J' . $cell);
            $sheet->getStyle('B' . $cell . ':J' . $cell)->getFont()->setBold(true);
            $cell++;
            $sheet->getStyle('A' . $cell)->getAlignment()->setVertical('top')->setHorizontal('center');
            $sheet->setCellValue('A' . $cell, 1);
            $sheet->setCellValue('B' . $cell, '-');
            $sheet->setCellValue('C' . $cell, '-');
            $sheet->setCellValue('D' . $cell, '-');
            $sheet->setCellValue('E' . $cell, '-');
            $sheet->setCellValue('G' . $cell, '-');
            $sheet->setCellValue('H' . $cell, '-');
            $sheet->setCellValue('I' . $cell, '-');
            $sheet->setCellValue('J' . $cell, '-')->mergeCells('J' . $cell . ':J' . $cell);
            $cell++;

            $sheet->getStyle('B' . $cell . ':I' . $cell)->getAlignment()->setVertical('top')->setHorizontal('right');
            $sheet->getStyle('J' . $cell)->getAlignment()->setVertical('top')->setHorizontal('center');
            $sheet->getStyle('B' . $cell . ':J' . $cell)->getFont()->setBold(true);
            $sheet->setCellValue('B' . $cell, 'NILAI KINERJA UTAMA')->mergeCells('B' . $cell . ':I' . $cell);
            $sheet->setCellValue('J' . $cell, 0);
            $cell++;
        }

        // //TAMBAHAN
        if (count($data['skp']['tambahan'])) {
            // dd($data['skp']['tambahan']);
            $sheet->setCellValue('B' . $cell, 'B. KINERJA TAMBAHAN')->mergeCells('B' . $cell . ':J' . $cell);
            $sheet->getStyle('B' . $cell . ':J' . $cell)->getFont()->setBold(true);
            $cell++;
            $total_tambahan = 0;
            foreach ($data['skp']['tambahan'] as $index => $value) {
                $sheet->setCellValue('A' . $cell, $index + 1);
                $sheet->setCellValue('B' . $cell, $value->rencana);

                foreach ($value->aspek_skp as $k => $v) {
                    $sheet->setCellValue('C' . $cell, $v->iki);
                    $kategori_ = '';
                    $sheet->setCellValue('D' . $cell, $v->target . ' ' . $v->satuan);
                    $sheet->setCellValue('E' . $cell, $v->realisasi . ' ' . $v->satuan);
                    $single_rate = ($v->realisasi / $v->target) * 100;
                    $sheet->setCellValue('F' . $cell, round($single_rate, 0) . ' %');
  
                    if ($single_rate > 110) {
                        $sheet->setCellValue('G' . $cell, '110');
                        $sheet->setCellValue('H' . $cell, 'Sangat Baik');
                        $nilai_iki = 110 + ((120 - 110) / (110 - 101)) * (110 - 101);
                        $sheet->setCellValue('I' . $cell, round($nilai_iki, 1));
                    } elseif ($single_rate >= 101 && $single_rate <= 110) {
                        $sheet->setCellValue('G' . $cell, round($single_rate, 0) . ' %');
                        $sheet->setCellValue('H' . $cell, 'Sangat Baik');
                        $nilai_iki = 110 + ((120 - 110) / (110 - 101)) * ($single_rate - 101);
                        $sheet->setCellValue('I' . $cell, round($nilai_iki, 1));
                    } elseif ($single_rate == 100) {
                        $sheet->setCellValue('G' . $cell, round($single_rate, 0) . ' %');
                        $sheet->setCellValue('H' . $cell, 'Baik');
                        $nilai_iki = 109;
                        $sheet->setCellValue('I' . $cell, round($nilai_iki, 1));
                    } elseif ($single_rate >= 80 && $single_rate <= 99) {
                        $sheet->setCellValue('G' . $cell, round($single_rate, 0) . ' %');
                        $sheet->setCellValue('H' . $cell, 'Cukup');
                        $nilai_iki = 70 + ((89 - 70) / (99 - 80)) * ($single_rate - 80);
                        $sheet->setCellValue('I' . $cell, round($nilai_iki, 1));
                    } elseif ($single_rate >= 60 && $single_rate <= 79) {
                        $sheet->setCellValue('G' . $cell, round($single_rate, 0) . ' %');
                        $sheet->setCellValue('H' . $cell, 'Kurang');
                        $nilai_iki = 50 + ((69 - 50) / (79 - 60)) * ($single_rate - 60);
                        $sheet->setCellValue('I' . $cell, round($nilai_iki, 1));
                    } elseif ($single_rate >= 0 && $single_rate <= 79) {
                        $sheet->setCellValue('G' . $cell, round($single_rate, 0) . ' %');
                        $sheet->setCellValue('H' . $cell, 'Sangat Kurang');
                        $nilai_iki = (49 / 59) * $single_rate;
                        $sheet->setCellValue('I' . $cell, round($nilai_iki, 1));
                    }

                    if ($nilai_iki > 110) {
                        $sheet->setCellValue('J' . $cell, '2.4');
                        $total_tambahan += 2.4;
                    } elseif ($nilai_iki >= 101 && $nilai_iki <= 110) {
                        $sheet->setCellValue('J' . $cell, '1.6');
                        $total_tambahan += 1.6;
                    } elseif ($nilai_iki == 100) {
                        $sheet->setCellValue('J' . $cell, '1.0');
                        $total_tambahan += 1.0;
                    } elseif ($nilai_iki >= 80 && $nilai_iki <= 99) {
                        $sheet->setCellValue('J' . $cell, '0.5');
                        $total_tambahan += 0.5;
                    } elseif ($nilai_iki >= 60 && $nilai_iki <= 79) {
                        $sheet->setCellValue('J' . $cell, '0.3');
                        $total_tambahan += 0.3;
                    } elseif ($nilai_iki >= 0 && $nilai_iki <= 79) {
                        $sheet->setCellValue('J' . $cell, '0.1');
                        $total_tambahan += 0.1;
                    }
                        
                    
                    $cell++;
                }
            }
            $sheet->getStyle('B' . $cell . ':J' . ($cell + 1))->getAlignment()->setVertical('top')->setHorizontal('center');
            $sheet->getStyle('I' . ($cell + 1))->getAlignment()->setVertical('top')->setHorizontal('right');
            $sheet->getStyle('B' . $cell . ':J' . ($cell + 1))->getFont()->setBold(true);
            $sheet->setCellValue('B' . $cell, 'NILAI KINERJA TAMBAHAN')->mergeCells('B' . $cell . ':I' . $cell);
            $sheet->setCellValue('J' . $cell, $nilai_tambahan = $total_tambahan);
            $cell++;
        } else {
            $sheet->setCellValue('B' . $cell, 'B. KINERJA TAMBAHAN')->mergeCells('B' . $cell . ':J' . $cell);
            $sheet->getStyle('B' . $cell . ':J' . $cell)->getFont()->setBold(true);
            $cell++;
            $sheet->getStyle('A' . $cell)->getAlignment()->setVertical('top')->setHorizontal('center');
            $sheet->setCellValue('A' . $cell, 1);
            $sheet->setCellValue('B' . $cell, '-');
            $sheet->setCellValue('C' . $cell, '-');
            $sheet->setCellValue('D' . $cell, '-');
            $sheet->setCellValue('E' . $cell, '-');
            $sheet->setCellValue('G' . $cell, '-');
            $sheet->setCellValue('H' . $cell, '-');
            $sheet->setCellValue('I' . $cell, '-');
            $sheet->setCellValue('J' . $cell, '-')->mergeCells('J' . $cell . ':J' . $cell);
            $cell++;

            $sheet->getStyle('B' . $cell . ':I' . $cell)->getAlignment()->setVertical('top')->setHorizontal('right');
            $sheet->getStyle('J' . $cell)->getAlignment()->setVertical('top')->setHorizontal('center');
            $sheet->getStyle('B' . $cell . ':J' . $cell)->getFont()->setBold(true);
            $sheet->setCellValue('B' . $cell, 'NILAI KINERJA TAMBAHAN')->mergeCells('B' . $cell . ':I' . $cell);
            $sheet->setCellValue('J' . $cell, 0);
            $cell++;
        }

        $sheet->getStyle('B' . $cell . ':I' . $cell)->getAlignment()->setVertical('top')->setHorizontal('right');
        $sheet->getStyle('J' . $cell)->getAlignment()->setVertical('top')->setHorizontal('center');
        $sheet->getStyle('B' . $cell . ':J' . $cell)->getFont()->setBold(true);
        $sheet->setCellValue('B' . $cell, 'NILAI SKP')->mergeCells('B' . $cell . ':I' . $cell);
        $sheet->setCellValue('J' . $cell, $nilai_utama + $nilai_tambahan);
        $cell++;

        $sheet->getStyle('A12:J' . $cell)->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('B13:C' . $cell)->getAlignment()->setVertical('center')->setHorizontal('left');


        $border = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '0000000'],
                ],
            ],
        ];

        $sheet->getStyle('A6:J' . $cell)->applyFromArray($border);

        $cell++;
        $sheet->setCellValue('B' . $cell, '')->mergeCells('B' . $cell . ':K' . $cell);

        $tgl_cetak = date("t", strtotime($tahun)) . ' ' . strftime('%B %Y', mktime(0, 0, 0, date("n") + 1, 0, (int)session('tahun_penganggaran')));

        $sheet->setCellValue('H' . ++$cell, 'BULUKUMBA, ' . $tgl_cetak)->mergeCells('H' . $cell . ':K' . $cell);
        $sheet->getStyle('H' . $cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('H' . ++$cell, 'Pejabat Penilai Kinerja')->mergeCells('H' . $cell . ':K' . $cell);
        $sheet->getStyle('H' . $cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $cell = $cell + 3;
        $sheet->setCellValue('H' . ++$cell, $atasan->nama)->mergeCells('H' . $cell . ':K' . $cell);
        $sheet->getStyle('H' . $cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('H' . ++$cell, $atasan->nip)->mergeCells('H' . $cell . ':K' . $cell);
        $sheet->getStyle('H' . $cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);


        if ($type == 'excel') {
            // Untuk download 
            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Laporan Penilaian SKP ' . $pegawai->nama . '.xlsx"');
        } else {
            $spreadsheet->getActiveSheet()->getHeaderFooter()
                ->setOddHeader('&C&H' . url()->current());
            $spreadsheet->getActiveSheet()->getHeaderFooter()
                ->setOddFooter('&L&B &RPage &P of &N');
            $class = \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf::class;
            \PhpOffice\PhpSpreadsheet\IOFactory::registerWriter('Pdf', $class);
            header('Content-Type: application/pdf');
            header('Cache-Control: max-age=0');
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Pdf');
        }

        $writer->save('php://output');
    }

    public function exportSkpPegawai($data, $type, $pegawai, $atasan){
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator('BKPSDM BULUKUMBA')
            ->setLastModifiedBy('BKPSDM BULUKUMBA')
            ->setTitle('Laporan SKP ')
            ->setSubject('Laporan SKP ')
            ->setDescription('Laporan SKP ')
            ->setKeywords('pdf php')
            ->setCategory('LAPORAN SKP');
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_FOLIO);
        $sheet->getRowDimension(1)->setRowHeight(17);
        $sheet->getRowDimension(2)->setRowHeight(17);
        $sheet->getRowDimension(3)->setRowHeight(17);
        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
        $spreadsheet->getDefaultStyle()->getFont()->setSize(10);
        $spreadsheet->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);
        $spreadsheet->getActiveSheet()->getPageSetup()->setVerticalCentered(false);

        // //Margin PDF
        $spreadsheet->getActiveSheet()->getPageMargins()->setTop(0.3);
        $spreadsheet->getActiveSheet()->getPageMargins()->setRight(0.3);
        $spreadsheet->getActiveSheet()->getPageMargins()->setLeft(0.5);
        $spreadsheet->getActiveSheet()->getPageMargins()->setBottom(0.3);

        $sheet->getStyle('A:L')->getAlignment()->setWrapText(true);
        $sheet->getStyle('A:L')->getAlignment()->setVertical('top')->setHorizontal('left');

        $sheet->setCellValue('A1', 'SASARAN KINERJA PEGAWAI (SKP)')->mergeCells('A1:H1');
        $sheet->setCellValue('A2', 'PEJABAT ADMINISTRATOR PENGAWAS DAN FUNGSIONAL')->mergeCells('A2:H2');

        $sheet->setCellValue('A4', 'PERIODE PENILAIAN')->mergeCells('A4:H4');
        $sheet->setCellValue('A5', $pegawai->nama_satuan_kerja)->mergeCells('A5:D5');

        $tahun = ""  . session('tahun_penganggaran') ."";
            $periode = "Tahun " . session('tahun_penganggaran');
        $sheet->setCellValue('E5', $periode)->mergeCells('E5:H5');

        $sheet->setCellValue('A6', 'PEGAWAI YANG DINILAI')->mergeCells('A6:D6');
        $sheet->setCellValue('E6', 'PEJABAT PENILAI PEKERJA')->mergeCells('E6:H6');
        $sheet->getStyle('A6:E6')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('E1F5FE');

        $sheet->setCellValue('A7', 'Nama')->mergeCells('A7:B7');
        $sheet->setCellValue('C7', $pegawai->nama)->mergeCells('C7:D7');
        $sheet->setCellValue('A8', 'NIP')->mergeCells('A8:B8');
        $sheet->setCellValue('C8', "" . $pegawai->nip)->mergeCells('C8:D8');
        $sheet->setCellValue('A9', 'Pangkat / Gol Ruang')->mergeCells('A9:B9');
        $sheet->setCellValue('C9', $pegawai->golongan)->mergeCells('C9:D9');
        $sheet->setCellValue('A10', 'Jabatan')->mergeCells('A10:B10');

        $jabatan_pegawai = $pegawai->nama_jabatan;

        if ($pegawai->status_jabatan !== 'definitif') {
            $jabatan_pegawai = $pegawai->status_jabatan.' '.$pegawai->nama_jabatan;
        }

        $sheet->setCellValue('C10', $jabatan_pegawai)->mergeCells('C10:D10');
        $sheet->setCellValue('A11', 'Unit kerja')->mergeCells('A11:B11');
        $sheet->setCellValue('C11', $pegawai->nama_unit_kerja)->mergeCells('C11:D11');


        $sheet->setCellValue('E7', 'Nama')->mergeCells('E7:F7');
        if ($atasan != "") {
            $sheet->setCellValue('G7', $atasan->nama)->mergeCells('G7:H7');
        } else {
            $sheet->setCellValue('G7', '-')->mergeCells('E7:H7');
        }
        $sheet->setCellValue('E8', 'NIP')->mergeCells('E8:F8');
        if ($atasan != "") {
            $sheet->setCellValue('G8', "" . $atasan->nip)->mergeCells('G8:H8');
        } else {
            $sheet->setCellValue('G8', '-')->mergeCells('G8:H8');
        }
        $sheet->setCellValue('E9', 'Pangkat / Gol Ruang')->mergeCells('E9:F9');
        if ($atasan != "") {
            $sheet->setCellValue('G9', $atasan->golongan)->mergeCells('G9:H9');
        } else {
            $sheet->setCellValue('G9', '-')->mergeCells('G9:H9');
        }
        $sheet->setCellValue('E10', 'Jabatan')->mergeCells('E10:F10');
        if ($atasan != "") {
            $jabatan_atasan = $atasan->nama_jabatan;

            if ($atasan->status_jabatan !== 'definitif') {
                $jabatan_atasan = $atasan->status_jabatan.' '.$atasan->nama_jabatan;
            }
            $sheet->setCellValue('G10', $jabatan_atasan)->mergeCells('G10:H10');
        } else {
            $sheet->setCellValue('G10', '-')->mergeCells('G10:H10');
        }
        $sheet->setCellValue('E11', 'Unit kerja')->mergeCells('E11:F11');
        if ($atasan != "") {
            $sheet->setCellValue('G11', $atasan->nama_unit_kerja)->mergeCells('G11:H11');
        } else {
            $sheet->setCellValue('G11', '-')->mergeCells('G11:H11');
        }


        $sheet->setCellValue('A12', 'No')->mergeCells('A12:A12');
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->setCellValue('B12', 'RENCANA KINERJA ATASAN LANGSUNG')->mergeCells('B12:C12');
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->setCellValue('D12', 'RENCANA KINERJA')->mergeCells('D12:D12');
        $sheet->getColumnDimension('D')->setWidth(45);


        $sheet->setCellValue('E12', 'ASPEK')->mergeCells('E12:E12');
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->setCellValue('F12', 'INDIKATOR KINERJA INDIVIDU')->mergeCells('F12:G12');
        $sheet->getColumnDimension('F')->setWidth(5);
        $sheet->getColumnDimension('G')->setWidth(30);
        $sheet->setCellValue('H12', 'TARGET')->mergeCells('H12:H12');
        $sheet->getColumnDimension('H')->setWidth(25);
        $sheet->getStyle('A12:H12')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('E1F5FE');

        $cell = 13;
        $number = 1;
        //UTAMA
        $previousRencanaKerjaAtasan = null;
        $nomor = 1;
        if (isset($data['skp']['utama'])) {
            $sheet->setCellValue('B' . $cell, 'A. KINERJA UTAMA')->mergeCells('B' . $cell . ':H' . $cell);
            $sheet->getStyle('B' . $cell . ':F' . $cell)->getFont()->setBold(true);
            $sheet->getStyle('A' . $cell . ':H' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('E1F5FE');
            $cell++;
            foreach ($data['skp']['utama'] as $index => $value) {

                $rencanaKerjaAtasan = $value->id_skp_atasan;
                
                if ($rencanaKerjaAtasan === $previousRencanaKerjaAtasan) {
                    $sheet->setCellValue('A' . $cell, '')->mergeCells('A' . $cell . ':A' . ($cell + count($value->aspek_skp) - 1));
                    $sheet->setCellValue('B' . $cell, '')->mergeCells('B' . $cell . ':C' . ($cell + count($value->aspek_skp) - 1));
                } else {
                        
                    $sheet->setCellValue('A' . $cell, $nomor ++)->mergeCells('A' . $cell . ':A' . ($cell + count($value->aspek_skp) - 1));
                    $sheet->setCellValue('B' . $cell, $value->rencana_kerja_atasan)->mergeCells('B' . $cell . ':C' . ($cell + count($value->aspek_skp) - 1));
                }
                
                $sheet->setCellValue('D' . $cell, $value->rencana)->mergeCells('D' . $cell . ':D' . ($cell + count($value->aspek_skp) - 1));

                foreach ($value->aspek_skp as $k => $v) {
                    $sheet->setCellValue('E' . $cell, $v->aspek_skp);
                    $sheet->setCellValue('F' . $cell, $v->iki)->mergeCells('F' . $cell . ':G' . $cell);
                    $sheet->setCellValue('H' . $cell, $v->target . ' ' . $v->satuan);
                    $cell++;
                }
                $previousRencanaKerjaAtasan = $rencanaKerjaAtasan;

            }
        } else {
            $sheet->setCellValue('B' . $cell, 'A. KINERJA UTAMA')->mergeCells('B' . $cell . ':H' . $cell);
            $sheet->getStyle('B' . $cell . ':H' . $cell)->getFont()->setBold(true);
            $sheet->getStyle('A' . $cell . ':H' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('E1F5FE');
            $cell++;
            $sheet->setCellValue('B' . $cell, '-')->mergeCells('B' . $cell . ':C' . $cell);
            $sheet->setCellValue('D' . $cell, '-')->mergeCells('D' . $cell . ':D' . ($cell + 2));
            $sheet->getStyle('E' . $cell . ':E' . $cell)->getAlignment()->setVertical('top')->setHorizontal('center');
            $sheet->setCellValue('E' . $cell, '-');
            $sheet->setCellValue('F' . $cell, '-')->mergeCells('F' . $cell . ':G' . $cell);
            $sheet->getStyle('H' . $cell . ':H' . $cell)->getAlignment()->setVertical('top')->setHorizontal('center');
            $sheet->setCellValue('H' . $cell, '-');
            $sheet->setCellValue('A' . $cell, 1)->mergeCells('A' . $cell . ':A' . ($cell - 1));
            $cell++;
        }


        // // TAMBAHAN
        $previousRencanaKerjaAtasanTambahan = null;
        if (isset($data['skp']['tambahan'])) {
            $sheet->setCellValue('B' . $cell, 'B. KINERJA TAMBAHAN')->mergeCells('B' . $cell . ':H' . $cell);
            $sheet->getStyle('B' . $cell . ':H' . $cell)->getFont()->setBold(true);
            $sheet->getStyle('A' . $cell . ':H' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('E1F5FE');
            $cell++;
            foreach ($data['skp']['tambahan'] as $keyy => $values) {
                $rencanaKerjaAtasanTambahan = $value->id_skp_atasan;

                if ($rencanaKerjaAtasanTambahan === $previousRencanaKerjaAtasanTambahan) {
                    $sheet->setCellValue('A' . $cell, '')->mergeCells('A' . $cell . ':A' . ($cell + count($value->aspek_skp) - 1));
                    $sheet->setCellValue('B' . $cell, '')->mergeCells('B' . $cell . ':C' . ($cell + count($value->aspek_skp) - 1));
                } else {
                    $sheet->setCellValue('A' . $cell, $nomor++)->mergeCells('A' . $cell . ':A' . ($cell + count($value->aspek_skp) - 1));
                    $sheet->setCellValue('B' . $cell, $value->rencana_kerja_atasan)->mergeCells('B' . $cell . ':C' . ($cell + count($value->aspek_skp) - 1));
                }

                $sheet->setCellValue('D' . $cell, $value->rencana)->mergeCells('D' . $cell . ':D' . ($cell + count($value->aspek_skp) - 1));

                foreach ($value->aspek_skp as $k => $v) {
                    $sheet->setCellValue('E' . $cell, $v->aspek_skp);
                    $sheet->setCellValue('F' . $cell, $v->iki)->mergeCells('F' . $cell . ':G' . $cell);
                    $sheet->setCellValue('H' . $cell, $v->target . ' ' . $v->satuan);
                    $cell++;
                }
                $previousRencanaKerjaAtasanTambahan = $rencanaKerjaAtasanTambahan;

            }
        } else {
            $sheet->setCellValue('B' . $cell, 'B. KINERJA TAMBAHAN')->mergeCells('B' . $cell . ':H' . $cell);
            $sheet->getStyle('B' . $cell . ':H' . $cell)->getFont()->setBold(true);
            $sheet->getStyle('A' . $cell . ':H' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('E1F5FE');
            $cell++;
            $sheet->setCellValue('B' . $cell, '-')->mergeCells('B' . $cell . ':C' . $cell);
            $sheet->setCellValue('D' . $cell, '-')->mergeCells('D' . $cell . ':D' . ($cell));
            $sheet->getStyle('E' . $cell . ':E' . $cell)->getAlignment()->setVertical('top')->setHorizontal('center');
            $sheet->setCellValue('E' . $cell, '-');
            $sheet->setCellValue('F' . $cell, '-')->mergeCells('F' . $cell . ':G' . $cell);
            $sheet->getStyle('H' . $cell . ':H' . $cell)->getAlignment()->setVertical('top')->setHorizontal('center');
            $sheet->setCellValue('H' . $cell, '-');
            $sheet->setCellValue('A' . $cell, 1)->mergeCells('A' . $cell . ':A' . ($cell - 1));
        }


        $border = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '0000000'],
                ],
            ],
        ];

        $sheet->getStyle('A6:H' . $cell)->applyFromArray($border);

        $cell++;
        $sheet->setCellValue('B' . $cell, '')->mergeCells('B' . $cell . ':F' . $cell);

        $tgl_cetak = date("t", strtotime($tahun)) . ' ' . strftime('%B %Y', mktime(0, 0, 0, date("n") + 1, 0, (int)session('tahun_penganggaran')));

        $sheet->setCellValue('G' . ++$cell, 'BULUKUMBA, ' . $tgl_cetak)->mergeCells('G' . $cell . ':K' . $cell);
        $sheet->getStyle('G' . $cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('G' . ++$cell, 'Pejabat Penilai Kinerja')->mergeCells('G' . $cell . ':K' . $cell);
        $sheet->getStyle('G' . $cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $cell = $cell + 3;
        $sheet->setCellValue('G' . ++$cell, $atasan->nama)->mergeCells('G' . $cell . ':K' . $cell);
        $sheet->getStyle('G' . $cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('G' . ++$cell, $atasan->nip)->mergeCells('G' . $cell . ':K' . $cell);
        $sheet->getStyle('G' . $cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle('A1:H2')->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('A1:H2')->getFont()->setSize(12);
        $sheet->getStyle('A6:H6')->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('A6:H6')->getFont()->setBold(true);
        $sheet->getStyle('A7:H7')->getFont()->setBold(true);
        $sheet->getStyle('A12:H12')->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('A12:H12')->getFont()->setBold(true);
        $sheet->getStyle('E4:H5')->getAlignment()->setVertical('center')->setHorizontal('right');


        if ($type == 'excel') {
            // Untuk download 
            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Laporan SKP ' . $pegawai->nama . '.xlsx"');
        } else {
            $spreadsheet->getActiveSheet()->getHeaderFooter()
                ->setOddHeader('&C&H' . url()->current());
            $spreadsheet->getActiveSheet()->getHeaderFooter()
                ->setOddFooter('&L&B &RPage &P of &N');
            $class = \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf::class;
            \PhpOffice\PhpSpreadsheet\IOFactory::registerWriter('Pdf', $class);
            header('Content-Type: application/pdf');
            header('Cache-Control: max-age=0');
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Pdf');
        }

        $writer->save('php://output');
    }

    public function exportRealisasiPegawai($data,$type,$pegawai,$atasan){
   
        $spreadsheet = new Spreadsheet();

        $spreadsheet->getProperties()->setCreator('BKPSDM BULUKUMBA')
            ->setLastModifiedBy('BKPSDM BULUKUMBA')
            ->setTitle('Laporan Penilaian SKP')
            ->setSubject('Laporan Penilaian SKP')
            ->setDescription('Laporan Penilaian SKP')
            ->setKeywords('pdf php')
            ->setCategory('LAPORAN PENILAIAN SKP');
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_FOLIO);
        $sheet->getRowDimension(1)->setRowHeight(17);
        $sheet->getRowDimension(2)->setRowHeight(17);
        $sheet->getRowDimension(3)->setRowHeight(17);
        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
        $spreadsheet->getDefaultStyle()->getFont()->setSize(10);
        $spreadsheet->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);
        $spreadsheet->getActiveSheet()->getPageSetup()->setVerticalCentered(false);

        // //Margin PDF
        $spreadsheet->getActiveSheet()->getPageMargins()->setTop(0.3);
        $spreadsheet->getActiveSheet()->getPageMargins()->setRight(0.3);
        $spreadsheet->getActiveSheet()->getPageMargins()->setLeft(0.5);
        $spreadsheet->getActiveSheet()->getPageMargins()->setBottom(0.3);

        $sheet->getStyle('A:L')->getAlignment()->setWrapText(true);
        $sheet->getStyle('A:L')->getAlignment()->setVertical('top')->setHorizontal('left');

        $sheet->setCellValue('A1', 'PENILAIAN SASARAN KINERJA PEGAWAI (SKP)')->mergeCells('A1:L1');
        $sheet->setCellValue('A2', 'PEJABAT ADMINISTRATOR PENGAWAS & FUNGSIONAL')->mergeCells('A2:L2');

        $sheet->setCellValue('F4', 'PERIODE PENILAIAN')->mergeCells('F4:L4');
        $sheet->setCellValue('A5', $pegawai->nama_satuan_kerja)->mergeCells('A5:E5');

        $tahun = ""  . session('tahun_penganggaran')  . "";
        $periode = "Tahun " . session('tahun_penganggaran');
        $sheet->setCellValue('F5', $periode)->mergeCells('F5:L5');

        $sheet->setCellValue('A6', 'PEGAWAI YANG DINILAI')->mergeCells('A6:E6');
        $sheet->setCellValue('F6', 'PEJABAT PENILAI PEKERJA')->mergeCells('F6:L6');
        $sheet->getStyle('A6:F6')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('E1F5FE');

        $sheet->setCellValue('A7', 'Nama')->mergeCells('A7:B7');
        $sheet->setCellValue('C7', $pegawai->nama)->mergeCells('C7:E7');
        $sheet->setCellValue('A8', 'NIP')->mergeCells('A8:B8');
        $sheet->setCellValue('C8', "'" . $pegawai->nip)->mergeCells('C8:E8');
        $sheet->setCellValue('A9', 'Pangkat / Gol Ruang')->mergeCells('A9:B9');
        $sheet->setCellValue('C9', $pegawai->golongan)->mergeCells('C9:E9');
        $sheet->setCellValue('A10', 'Jabatan')->mergeCells('A10:B10');
        $sheet->setCellValue('C10', $pegawai->nama_jabatan)->mergeCells('C10:E10');
        $sheet->setCellValue('A11', 'Unit kerja')->mergeCells('A11:B11');
        $sheet->setCellValue('C11', $pegawai->nama_unit_kerja)->mergeCells('C11:E11');

        // return $data;
        $sheet->setCellValue('F7', 'Nama')->mergeCells('F7:G7');
        if ($atasan != "") {
            $sheet->setCellValue('H7', $atasan->nama)->mergeCells('H7:L7');
        } else {
            $sheet->setCellValue('H7', '-')->mergeCells('H7:L7');
        }
        $sheet->setCellValue('F8', 'NIP')->mergeCells('F8:G8');
        if ($atasan != "") {
            $sheet->setCellValue('H8', "'" . $atasan->nip)->mergeCells('H8:L8');
        } else {
            $sheet->setCellValue('H8', '-')->mergeCells('H8:L8');
        }
        $sheet->setCellValue('F9', 'Pangkat / Gol Ruang')->mergeCells('F9:G9');
        if ($atasan != "") {
            $sheet->setCellValue('H9', $atasan->golongan)->mergeCells('H9:L9');
        } else {
            $sheet->setCellValue('H9', '-')->mergeCells('H9:L9');
        }
        $sheet->setCellValue('F10', 'Jabatan')->mergeCells('F10:G10');
        if ($atasan != "") {
            $sheet->setCellValue('H10', $atasan->nama_jabatan)->mergeCells('H10:L10');
        } else {
            $sheet->setCellValue('H10', '-')->mergeCells('H10:L10');
        }
        $sheet->setCellValue('F11', 'Unit kerja')->mergeCells('F11:G11');
        if ($atasan != "") {
            $sheet->setCellValue('H11', $atasan->nama_unit_kerja)->mergeCells('H11:L11');
        } else {
            $sheet->setCellValue('H11', '-')->mergeCells('H11:L11');
        }


        $sheet->setCellValue('A12', 'No')->mergeCells('A12:A13');
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->setCellValue('B12', 'Rencana Kinerja Atasan Langsung')->mergeCells('B12:B13');
        $sheet->getColumnDimension('B')->setWidth(18);
        $sheet->setCellValue('C12', 'Rencana Kinerja')->mergeCells('C12:C13');
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->setCellValue('D12', 'Aspek')->mergeCells('D12:D13');
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->setCellValue('E12', 'Indikator Kinerja Individu')->mergeCells('E12:E13');
        $sheet->getColumnDimension('E')->setWidth(40);

        $sheet->setCellValue('F12', 'Target')->mergeCells('F12:F13');
        $sheet->getColumnDimension('F')->setWidth(17);
        $sheet->setCellValue('G12', 'Realisasi')->mergeCells('G12:G13');
        $sheet->getColumnDimension('G')->setWidth(17);
        $sheet->setCellValue('H12', 'Capaian IKI')->mergeCells('H12:H13');
        $sheet->getColumnDimension('H')->setWidth(12);
        $sheet->setCellValue('I12', 'Kategori Capaian IKI')->mergeCells('I12:I13');
        $sheet->getColumnDimension('I')->setWidth(12);
        $sheet->setCellValue('J12', 'Capaian Rencana Kinerja')->mergeCells('J12:K12');
        $sheet->setCellValue('J13', 'Kategori');
        $sheet->getColumnDimension('J')->setWidth(12);
        $sheet->setCellValue('K13', 'Nilai');
        $sheet->getColumnDimension('K')->setWidth(12);
        $sheet->setCellValue('L12', 'Nilai Timbang')->mergeCells('L12:L13');
        $sheet->getColumnDimension('L')->setWidth(12);

        $sheet->getStyle('A12:L13')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('E1F5FE');

        $cell = 14;
        $nilai_utama = 0;
        $nilai_tambahan = 0;

        //UTAMA ATASAN
        $previousRencanaKerjaAtasan = null;
        $nomor = 1;
        if (isset($data['skp']['utama'])) {
            $sheet->setCellValue('B' . $cell, 'A. KINERJA UTAMA')->mergeCells('B' . $cell . ':K' . $cell);
            $sheet->getStyle('A' . $cell . ':K' . $cell)->getFont()->setBold(true);
            $sheet->getStyle('A' . $cell . ':L' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('E1F5FE');
             $cell++;
            $total_utama = 0;
            $data_utama = 0;
            $index_data = 0;

            foreach ($data['skp']['utama'] as $index => $value) {
                $data_utama++;
                $rencanaKerjaAtasan = $value->id_skp_atasan;
                
                if ($rencanaKerjaAtasan === $previousRencanaKerjaAtasan) {
                    $sheet->setCellValue('A' . $cell, '')->mergeCells('A' . $cell . ':A' . ($cell + count($value->aspek_skp) - 1));
                    $sheet->setCellValue('B' . $cell, '')->mergeCells('B' . $cell . ':B' . ($cell + count($value->aspek_skp) - 1));
                } else {
                    $sheet->setCellValue('A' . $cell, $nomor ++)->mergeCells('A' . $cell . ':A' . ($cell + count($value->aspek_skp) - 1));
                    $sheet->setCellValue('B' . $cell, $value->rencana_kerja_atasan)->mergeCells('B' . $cell . ':B' . ($cell + count($value->aspek_skp) - 1));
                }
                
                $sheet->setCellValue('C' . $cell, $value->rencana)->mergeCells('C' . $cell . ':C' . ($cell + count($value->aspek_skp) - 1));
                $sum_capaian = 0;
                foreach ($value->aspek_skp as $k => $v) {
                    $sheet->setCellValue('D' . $cell, $v->aspek_skp);
                    $sheet->setCellValue('E' . $cell, $v->iki);
                    $sheet->setCellValue('G' . $cell, $v->target . ' ' . $v->satuan);
                    $kategori_ = '';

                    $sheet->setCellValue('F' . $cell, $v->target . ' ' . $v->satuan);
                    $sheet->setCellValue('G' . $cell, $v->realisasi . ' ' . $v->satuan);
                    $capaian_iki = ($v->realisasi / $v->target) * 100;

                    if ($capaian_iki > 100) {
                        $sheet->setCellValue('H' . $cell, round($capaian_iki, 0) . ' %');
                        $sheet->setCellValue('I' . $cell, 'Sangat Baik');
                        $nilai_iki = 16;
                    } elseif ($capaian_iki == 100) {
                        $sheet->setCellValue('H' . $cell, round($capaian_iki, 0) . ' %');
                        $sheet->setCellValue('I' . $cell, 'Baik');
                        $nilai_iki = 13;
                    } elseif ($capaian_iki >= 80 && $capaian_iki <= 99) {
                        $sheet->setCellValue('H' . $cell, round($capaian_iki, 0) . ' %');
                        $sheet->setCellValue('I' . $cell, 'Cukup');
                        $nilai_iki = 8;
                    } elseif ($capaian_iki >= 60 && $capaian_iki <= 79) {
                        $sheet->setCellValue('H' . $cell, round($capaian_iki, 0) . ' %');
                        $sheet->setCellValue('I' . $cell, 'Kurang');
                        $nilai_iki = 3;
                    } elseif ($capaian_iki >= 0 && $capaian_iki <= 59) {
                        $sheet->setCellValue('H' . $cell, round($capaian_iki, 0) . ' %');
                        $sheet->setCellValue('I' . $cell, 'Sangat Kurang');
                        $nilai_iki = 1;
                    }
                    $sum_capaian += $nilai_iki;

                    $cell++;
                }
                if ($sum_capaian > 42) {
                    $sheet->setCellValue('J' . ($cell - 3), 'Sangat Baik')->mergeCells('J' . ($cell - 3) . ':J' . ($cell - 1));
                    $sheet->setCellValue('K' . ($cell - 3), '120 %')->mergeCells('K' . ($cell - 3) . ':K' . ($cell - 1));
                    $sheet->setCellValue('L' . ($cell - 3), '120.0')->mergeCells('L' . ($cell - 3) . ':L' . ($cell - 1));
                    $total_utama += 120;
                } elseif ($sum_capaian >= 34) {
                    $sheet->setCellValue('J' . ($cell - 3), 'Baik')->mergeCells('J' . ($cell - 3) . ':J' . ($cell - 1));
                    $sheet->setCellValue('K' . ($cell - 3), '100 %')->mergeCells('K' . ($cell - 3) . ':K' . ($cell - 1));
                    $sheet->setCellValue('L' . ($cell - 3), '100')->mergeCells('L' . ($cell - 3) . ':L' . ($cell - 1));
                    $total_utama += 100;
                } elseif ($sum_capaian >= 19) {
                    $sheet->setCellValue('J' . ($cell - 3), 'Cukup')->mergeCells('J' . ($cell - 3) . ':J' . ($cell - 1));
                    $sheet->setCellValue('K' . ($cell - 3), '80 %')->mergeCells('K' . ($cell - 3) . ':K' . ($cell - 1));
                    $sheet->setCellValue('L' . ($cell - 3), '80.0')->mergeCells('L' . ($cell - 3) . ':L' . ($cell - 1));
                    $total_utama += 80;
                } elseif ($sum_capaian >= 7) {
                    $sheet->setCellValue('J' . ($cell - 3), 'Kurang')->mergeCells('J' . ($cell - 3) . ':J' . ($cell - 1));
                    $sheet->setCellValue('K' . ($cell - 3), '60 %')->mergeCells('K' . ($cell - 3) . ':K' . ($cell - 1));
                    $sheet->setCellValue('L' . ($cell - 3), '60.0')->mergeCells('L' . ($cell - 3) . ':L' . ($cell - 1));
                    $total_utama += 60;
                } elseif ($sum_capaian >= 3) {
                    $sheet->setCellValue('J' . ($cell - 3), 'Sangat Kurang')->mergeCells('J' . ($cell - 3) . ':J' . ($cell - 1));
                    $sheet->setCellValue('K' . ($cell - 3), '25 %')->mergeCells('K' . ($cell - 3) . ':K' . ($cell - 1));
                    $sheet->setCellValue('L' . ($cell - 3), '25.0')->mergeCells('L' . ($cell - 3) . ':L' . ($cell - 1));
                    $total_utama += 25;
                } elseif ($sum_capaian >= 0) {
                    $sheet->setCellValue('J' . ($cell - 3), 'Sangat Kurang')->mergeCells('J' . ($cell - 3) . ':J' . ($cell - 1));
                    $sheet->setCellValue('K' . ($cell - 3), '25 %')->mergeCells('K' . ($cell - 3) . ':K' . ($cell - 1));
                    $sheet->setCellValue('L' . ($cell - 3), '25.0')->mergeCells('L' . ($cell - 3) . ':L' . ($cell - 1));
                    $total_utama += 25;
                }

                    $sheet->setCellValue('A' . ($cell - 3), $index_data)->mergeCells('A' . ($cell - 3) . ':A' . ($cell - 1));
                    if (!$index == 0)
                        $sheet->setCellValue('B' . ($cell - 3), '')->mergeCells('B' . ($cell - 3) . ':B' . ($cell - 1));
                $previousRencanaKerjaAtasan = $rencanaKerjaAtasan;

                

            }
           
            $nilai_utama = $data_utama > 0 ? round($total_utama / $data_utama, 1) : 0;
            $sheet->getStyle('B' . $cell . ':K' . $cell)->getAlignment()->setVertical('top')->setHorizontal('right');
            $sheet->getStyle('L' . $cell)->getAlignment()->setVertical('top')->setHorizontal('center');
            $sheet->getStyle('B' . $cell . ':L' . $cell)->getFont()->setBold(true);
            $sheet->setCellValue('B' . $cell, 'NILAI KINERJA UTAMA')->mergeCells('B' . $cell . ':K' . $cell);
            $sheet->setCellValue('L' . $cell, $nilai_utama);
            $cell++;
        } else {
            $nilai_utama = 0;
            $sheet->setCellValue('B' . $cell, 'A. KINERJA UTAMA')->mergeCells('B' . $cell . ':K' . $cell);
            $sheet->getStyle('A' . $cell . ':K' . $cell)->getFont()->setBold(true);
            $sheet->getStyle('A' . $cell . ':L' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('E1F5FE');
            $cell++;
            $sheet->setCellValue('A' . $cell, 1);
            $sheet->setCellValue('B' . $cell, '-');
            $sheet->setCellValue('C' . $cell, '-')->mergeCells('C' . $cell . ':C' . $cell);

            $sheet->getStyle('D' . $cell . ':D' . $cell)->getAlignment()->setVertical('top')->setHorizontal('center');
            $sheet->getStyle('F' . $cell . ':L' . $cell)->getAlignment()->setVertical('top')->setHorizontal('center');
            $sheet->setCellValue('D' . $cell, '-');
            $sheet->setCellValue('E' . $cell, '-');

            $sheet->setCellValue('F' . $cell, '-');
            $sheet->setCellValue('G' . $cell, '-');

            $sheet->setCellValue('H' . $cell, '-');
            $sheet->setCellValue('I' . $cell, '-');

            $sheet->setCellValue('J' . $cell, '-')->mergeCells('J' . $cell . ':J' . $cell);
            $sheet->setCellValue('K' . $cell, '-')->mergeCells('K' . $cell . ':K' . $cell);
            $sheet->setCellValue('L' . $cell, '-')->mergeCells('L' . $cell . ':L' . $cell);

            $cell++;
            $sheet->getStyle('B' . $cell . ':K' . $cell)->getAlignment()->setVertical('top')->setHorizontal('right');
            $sheet->getStyle('L' . $cell)->getAlignment()->setVertical('top')->setHorizontal('center');
            $sheet->getStyle('B' . $cell . ':L' . $cell)->getFont()->setBold(true);
            $sheet->setCellValue('B' . $cell, 'NILAI KINERJA UTAMA')->mergeCells('B' . $cell . ':K' . $cell);
            $sheet->setCellValue('L' . $cell, 0);
            $cell++;
        }

        $previousRencanaKerjaAtasanTambahan = null;
        if (isset($data['skp']['tambahan'])) {
          
            $sheet->setCellValue('B' . $cell, 'B. KINERJA TAMBAHAN')->mergeCells('B' . $cell . ':K' . $cell);
            $sheet->getStyle('B' . $cell . ':K' . $cell)->getFont()->setBold(true);
            $sheet->getStyle('A' . $cell . ':L' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('E1F5FE');
            $cell++;
            $total_tambahan = 0;
            foreach ($data['skp']['tambahan'] as $keyy => $value) {
                $rencanaKerjaAtasanTambahan = $value->id_skp_atasan;


                if ($rencanaKerjaAtasanTambahan === $previousRencanaKerjaAtasanTambahan) {
                    $sheet->setCellValue('A' . $cell, '')->mergeCells('A' . $cell . ':A' . ($cell + count($value->aspek_skp) - 1));
                    $sheet->setCellValue('B' . $cell, '')->mergeCells('B' . $cell . ':B' . ($cell + count($value->aspek_skp) - 1));
                } else {
                    $sheet->setCellValue('A' . $cell, $nomor ++)->mergeCells('A' . $cell . ':A' . ($cell + count($value->aspek_skp) - 1));
                    $sheet->setCellValue('B' . $cell, $value->rencana_kerja_atasan)->mergeCells('B' . $cell . ':B' . ($cell + count($value->aspek_skp) - 1));
                }

                $sheet->setCellValue('C' . $cell, $value->rencana)->mergeCells('C' . $cell . ':C' . ($cell + count($value->aspek_skp) - 1));
                
                foreach ($value->aspek_skp as $k => $v) {
                    $sheet->setCellValue('D' . $cell, $v->aspek_skp);
                    $sheet->setCellValue('E' . $cell, $v->iki);
                    $sheet->setCellValue('G' . $cell, $v->target . ' ' . $v->satuan);
                    $kategori_ = '';

                    $sheet->setCellValue('F' . $cell, $v->target . ' ' . $v->satuan);
                    $sheet->setCellValue('G' . $cell, $v->realisasi . ' ' . $v->satuan);
                    $capaian_iki = ($v->realisasi / $v->target) * 100;

                    if ($capaian_iki >= 101) {
                        $sheet->setCellValue('H' . $cell, round($capaian_iki, 0) . ' %');
                        $sheet->setCellValue('I' . $cell, 'Sangat Baik');
                        $nilai_iki = 16;
                    } elseif ($capaian_iki == 100) {
                        $sheet->setCellValue('H' . $cell, round($capaian_iki, 0) . ' %');
                        $sheet->setCellValue('I' . $cell, 'Baik');
                        $nilai_iki = 13;
                    } elseif ($capaian_iki >= 80 && $capaian_iki <= 99) {
                        $sheet->setCellValue('H' . $cell, round($capaian_iki, 0) . ' %');
                        $sheet->setCellValue('I' . $cell, 'Cukup');
                        $nilai_iki = 8;
                    } elseif ($capaian_iki >= 60 && $capaian_iki <= 79) {
                        $sheet->setCellValue('H' . $cell, round($capaian_iki, 0) . ' %');
                        $sheet->setCellValue('I' . $cell, 'Kurang');
                        $nilai_iki = 3;
                    } elseif ($capaian_iki >= 0 && $capaian_iki <= 79) {
                        $sheet->setCellValue('H' . $cell, round($capaian_iki, 0) . ' %');
                        $sheet->setCellValue('I' . $cell, 'Sangat Kurang');
                        $nilai_iki = 1;
                    }
                    $sum_capaian += $nilai_iki;

                    $cell++;
                }
                $previousRencanaKerjaAtasanTambahan = $rencanaKerjaAtasanTambahan;

                if ($sum_capaian >= 42) {
                    $sheet->setCellValue('J' . ($cell - 3), 'Sangat Baik')->mergeCells('J' . ($cell - 3) . ':J' . ($cell - 1));
                    $sheet->setCellValue('K' . ($cell - 3), '120')->mergeCells('K' . ($cell - 3) . ':K' . ($cell - 1));
                    $sheet->setCellValue('L' . ($cell - 3), '2.4')->mergeCells('L' . ($cell - 3) . ':L' . ($cell - 1));
                    $total_tambahan += 2.4;
                } elseif ($sum_capaian >= 34) {
                    $sheet->setCellValue('J' . ($cell - 3), 'Baik')->mergeCells('J' . ($cell - 3) . ':J' . ($cell - 1));
                    $sheet->setCellValue('K' . ($cell - 3), '100')->mergeCells('K' . ($cell - 3) . ':K' . ($cell - 1));
                    $sheet->setCellValue('L' . ($cell - 3), '1.6')->mergeCells('L' . ($cell - 3) . ':L' . ($cell - 1));
                    $total_tambahan += 1.6;
                } elseif ($sum_capaian >= 19) {
                    $sheet->setCellValue('J' . ($cell - 3), 'Cukup')->mergeCells('J' . ($cell - 3) . ':J' . ($cell - 1));
                    $sheet->setCellValue('K' . ($cell - 3), '80')->mergeCells('K' . ($cell - 3) . ':K' . ($cell - 1));
                    $sheet->setCellValue('L' . ($cell - 3), '1')->mergeCells('L' . ($cell - 3) . ':L' . ($cell - 1));
                    $total_tambahan += 1;
                } elseif ($sum_capaian >= 7) {
                    $sheet->setCellValue('J' . ($cell - 3), 'Kurang')->mergeCells('J' . ($cell - 3) . ':J' . ($cell - 1));
                    $sheet->setCellValue('K' . ($cell - 3), '60 %')->mergeCells('K' . ($cell - 3) . ':K' . ($cell - 1));
                    $sheet->setCellValue('L' . ($cell - 3), '0.5')->mergeCells('L' . ($cell - 3) . ':L' . ($cell - 1));
                    $total_tambahan += 0.5;
                } elseif ($sum_capaian >= 3) {
                    $sheet->setCellValue('J' . ($cell - 3), 'Sangat Kurang')->mergeCells('J' . ($cell - 3) . ':J' . ($cell - 1));
                    $sheet->setCellValue('K' . ($cell - 3), '25')->mergeCells('K' . ($cell - 3) . ':K' . ($cell - 1));
                    $sheet->setCellValue('L' . ($cell - 3), '0.1')->mergeCells('L' . ($cell - 3) . ':L' . ($cell - 1));
                    $total_tambahan += 0.1;
                } elseif ($sum_capaian >= 0) {
                    $sheet->setCellValue('J' . ($cell - 3), 'Sangat Kurang')->mergeCells('J' . ($cell - 3) . ':J' . ($cell - 1));
                    $sheet->setCellValue('K' . ($cell - 3), '25')->mergeCells('K' . ($cell - 3) . ':K' . ($cell - 1));
                    $sheet->setCellValue('L' . ($cell - 3), '0.1')->mergeCells('L' . ($cell - 3) . ':L' . ($cell - 1));
                    $total_tambahan += 0.1;
                }

                 $sheet->setCellValue('A' . ($cell - 3), $k + 1)->mergeCells('A' . ($cell - 3) . ':A' . ($cell - 1));
                    if (!$k == 0)
                        $sheet->setCellValue('B' . ($cell - 3), '')->mergeCells('B' . ($cell - 3) . ':B' . ($cell - 1));
                    $cell++;

            }
            $sheet->getStyle('F' . $cell . ':L' . ($cell + 1))->getAlignment()->setVertical('top')->setHorizontal('center');
            $sheet->getStyle('B' . $cell . ':K' . ($cell + 1))->getAlignment()->setVertical('top')->setHorizontal('right');
            $sheet->getStyle('B' . $cell . ':L' . ($cell + 1))->getFont()->setBold(true);
            $sheet->setCellValue('B' . $cell, 'NILAI KINERJA TAMBAHAN')->mergeCells('B' . $cell . ':K' . $cell);
            $sheet->setCellValue('L' . $cell, $nilai_tambahan = $total_tambahan);
        } else {

            $sheet->setCellValue('B' . $cell, 'B. KINERJA TAMBAHAN')->mergeCells('B' . $cell . ':K' . $cell);
            $sheet->getStyle('A' . $cell . ':K' . $cell)->getFont()->setBold(true);
            $sheet->getStyle('A' . $cell . ':L' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('E1F5FE');
            $cell++;
            $sheet->setCellValue('A' . $cell, 1);
            $sheet->setCellValue('B' . $cell, '-');
            $sheet->setCellValue('C' . $cell, '-')->mergeCells('C' . $cell . ':C' . $cell);

            $sheet->getStyle('D' . $cell . ':D' . $cell)->getAlignment()->setVertical('top')->setHorizontal('center');
            $sheet->getStyle('F' . $cell . ':L' . $cell)->getAlignment()->setVertical('top')->setHorizontal('center');
            $sheet->setCellValue('D' . $cell, '-');
            $sheet->setCellValue('E' . $cell, '-');

            $sheet->setCellValue('F' . $cell, '-');
            $sheet->setCellValue('G' . $cell, '-');

            $sheet->setCellValue('H' . $cell, '-');
            $sheet->setCellValue('I' . $cell, '-');

            $sheet->setCellValue('J' . $cell, '-')->mergeCells('J' . $cell . ':J' . $cell);
            $sheet->setCellValue('K' . $cell, '-')->mergeCells('K' . $cell . ':K' . $cell);
            $sheet->setCellValue('L' . $cell, '-')->mergeCells('L' . $cell . ':L' . $cell);

            $cell++;
            $sheet->getStyle('F' . $cell . ':L' . ($cell + 1))->getAlignment()->setVertical('top')->setHorizontal('center');
            $sheet->getStyle('B' . $cell . ':K' . ($cell + 1))->getAlignment()->setVertical('top')->setHorizontal('right');
            $sheet->getStyle('B' . $cell . ':L' . ($cell + 1))->getFont()->setBold(true);
            $sheet->setCellValue('B' . $cell, 'NILAI KINERJA TAMBAHAN')->mergeCells('B' . $cell . ':K' . $cell);
            $sheet->setCellValue('L' . $cell, 0);
        }

        $cell++;
        $sheet->getStyle('F' . $cell . ':L' . ($cell + 1))->getAlignment()->setVertical('top')->setHorizontal('center');
        $sheet->getStyle('B' . $cell . ':K' . ($cell + 1))->getAlignment()->setVertical('top')->setHorizontal('right');
        $sheet->getStyle('B' . $cell . ':L' . ($cell + 1))->getFont()->setBold(true);
        $sheet->setCellValue('B' . $cell, 'NILAI SKP')->mergeCells('B' . $cell . ':K' . $cell);
        $sheet->setCellValue('L' . $cell, $nilai_utama + $nilai_tambahan);


        $border = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '0000000'],
                ],
            ],
        ];

        $sheet->getStyle('A6:L' . $cell)->applyFromArray($border);
        $cell++;
        $sheet->setCellValue('B' . $cell, '
        ')->mergeCells('B' . $cell . ':K' . $cell);

        $tgl_cetak = date("t", strtotime($tahun)) . ' ' . strftime('%B %Y', mktime(0, 0, 0, date("n") + 1, 0, (int)session('tahun_penganggaran')));

        $sheet->setCellValue('H' . ++$cell, 'BULUKUMBA, ' . $tgl_cetak)->mergeCells('H' . $cell . ':K' . $cell);
        $sheet->getStyle('H' . $cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('H' . ++$cell, 'Pejabat Penilai Kinerja')->mergeCells('H' . $cell . ':K' . $cell);
        $sheet->getStyle('H' . $cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $cell = $cell + 3;
        $sheet->setCellValue('H' . ++$cell, $atasan->nama)->mergeCells('H' . $cell . ':K' . $cell);
        $sheet->getStyle('H' . $cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('H' . ++$cell, $atasan->nip)->mergeCells('H' . $cell . ':K' . $cell);
        $sheet->getStyle('H' . $cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);



        $sheet->getStyle('A1:L2')->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('A1:L2')->getFont()->setSize(12);
        $sheet->getStyle('A6:L6')->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('A6:L6')->getFont()->setBold(true);
        $sheet->getStyle('A7:L7')->getFont()->setBold(true);
        $sheet->getStyle('A12:L13')->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('A12:L13')->getFont()->setBold(true);
        $sheet->getStyle('F4:H5')->getAlignment()->setVertical('center')->setHorizontal('right');





        if ($type == 'excel') {
            // Untuk download 
            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Laporan Penilaian SKP ' . $pegawai->nama . '.xlsx"');
        } else {
            $spreadsheet->getActiveSheet()->getHeaderFooter()
                ->setOddHeader('&C&H' . url()->current());
            $spreadsheet->getActiveSheet()->getHeaderFooter()
                ->setOddFooter('&L&B &RPage &P of &N');
            $class = \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf::class;
            \PhpOffice\PhpSpreadsheet\IOFactory::registerWriter('Pdf', $class);
            header('Content-Type: application/pdf');
            header('Cache-Control: max-age=0');
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Pdf');
        }

        $writer->save('php://output');
    }
}
