<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use DB;
use App\Traits\General;
use App\Traits\Presensi;
use App\Models\SasaranKinerja;
use Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Carbon\Carbon;

class LaporanTppController extends BaseController
{
    use General;
    use Presensi;
    public function breadcumb(){
        return [
            [
                'label' => 'Laporan',
                'url' => '#'
            ],
            [
                'label' => 'TPP',
                'url' => '#'
            ],
        ];
    }

    public function index(){
        $module = $this->breadcumb();
        return view('laporan.tpp.index_pegawai',compact('module'));
    }

    public function index_opd(){
        $module = $this->breadcumb();
        $satuan_kerja = $this->infoSatuanKerja(Auth::user()->id_pegawai);
        $nama_satuan_kerja = $satuan_kerja->nama_satuan_kerja;
        $pegawai = array();
        $satuan_kerja_user = '';
        $role = hasRole();
        $query = DB::table('tb_pegawai')
        ->select('tb_pegawai.id', 'tb_pegawai.nama as text')
        ->leftJoin('tb_jabatan', 'tb_jabatan.id_pegawai', '=', 'tb_pegawai.id')
        ->where('tb_pegawai.status', '1');

        if ($role['role'] == '1') {
            $query->where('tb_pegawai.id_satuan_kerja', $satuan_kerja->id_satuan_kerja);
            $satuan_kerja_user = $satuan_kerja->id_satuan_kerja;
        } else {
            $query->where('tb_jabatan.id_unit_kerja', $satuan_kerja->id_unit_kerja);
        }

        $pegawai = $query->get();

        if ($role['role'] == '1') {
            return view('laporan.tpp.index',compact('module', 'pegawai','satuan_kerja_user','nama_satuan_kerja'));
        }else {
            return view('laporan.tpp.index_unit',compact('module', 'pegawai','satuan_kerja_user','nama_satuan_kerja'));
        }   
        
    }

    public function index_kabupaten(){
        $module = $this->breadcumb();
        $satuan_kerja = $this->option_satuan_kerja();
        return view('laporan.tpp.index_kabupaten',compact('module','satuan_kerja'));
    }

    public function data_tpp_satuan_kerja($satuan_kerja,$unit_kerja, $bulan){

        $tahun = date('Y'); 
        $tanggal_awal = date("$tahun-$bulan-01");
        $tanggal_akhir = date("Y-m-t", strtotime($tanggal_awal));
        $data = array();

        $query = DB::table('tb_pegawai')
        ->selectRaw('
            tb_pegawai.id,
            tb_pegawai.nama,
            tb_pegawai.nip,
            tb_pegawai.golongan,
            tb_pegawai.tipe_pegawai,
            tb_master_jabatan.nama_jabatan,
            tb_master_jabatan.target_waktu,
            tb_master_jabatan.kelas_jabatan,
            tb_master_jabatan.pagu_tpp,
            tb_master_jabatan.jenis_jabatan,
            tb_master_jabatan.level_jabatan,
            tb_jabatan.pembayaran,
            tb_jabatan.status as status_jabatan,
            tb_unit_kerja.waktu_masuk,
            tb_unit_kerja.waktu_keluar,
            (SELECT SUM(waktu) FROM tb_aktivitas WHERE tb_aktivitas.id_pegawai = tb_pegawai.id AND MONTH(tanggal) = ? LIMIT 1) as capaian_waktu', [$bulan])
        ->join('tb_jabatan', 'tb_jabatan.id_pegawai', 'tb_pegawai.id')
        ->join('tb_master_jabatan', 'tb_jabatan.id_master_jabatan', '=', 'tb_master_jabatan.id')
        ->join('tb_satuan_kerja', 'tb_pegawai.id_satuan_kerja', '=', 'tb_satuan_kerja.id')
        ->join('tb_unit_kerja','tb_jabatan.id_unit_kerja','=','tb_unit_kerja.id')
        //->where('tb_satuan_kerja.id', $satuan_kerja)
        ->where('tb_pegawai.status','1')
        ->orderBy('tb_master_jabatan.kelas_jabatan','DESC')
        ->groupBy('tb_pegawai.id', 'tb_pegawai.nama', 'tb_pegawai.nip', 'tb_pegawai.golongan', 'tb_master_jabatan.nama_jabatan', 'tb_master_jabatan.target_waktu','tb_master_jabatan.kelas_jabatan','tb_master_jabatan.pagu_tpp','tb_master_jabatan.jenis_jabatan','tb_master_jabatan.level_jabatan','tb_jabatan.pembayaran','tb_jabatan.status','tb_unit_kerja.waktu_masuk','tb_unit_kerja.waktu_keluar');
        
        $role = hasRole();

        if ($role['guard'] == 'web') {
            // dd('tes');

            $query->where('tb_satuan_kerja.id', $satuan_kerja);
            if ($unit_kerja !== 'all') {
                 $query->where('tb_jabatan.id_unit_kerja', $unit_kerja);    
             }
            // if ($role['role'] == '1') {
            //     if ($satuan_kerja !== null) {
            //         $query->where('tb_satuan_kerja.id', $satuan_kerja);
            //     }
            // }else {
            //     $query->where('tb_jabatan.id_unit_kerja', $unit_kerja);
            // }     
        }

        if (hasRole()['guard'] == 'administrator') {
            // dd('tes2');
            $query->where('tb_satuan_kerja.id', $satuan_kerja);
            if ($unit_kerja !== 'all') {
                 $query->where('tb_jabatan.id_unit_kerja', $unit_kerja);    
             }
        }
        
        $data = $query->get();

        $data = $data->map(function ($item) use ($tanggal_awal,$tanggal_akhir) {
            $child = $this->data_kehadiran_pegawai($item->id,$tanggal_awal,$tanggal_akhir,$item->waktu_masuk,$item->waktu_keluar,$item->tipe_pegawai);
            $item->jml_potongan_kehadiran_kerja = $child['jml_potongan_kehadiran_kerja'];
            $item->tanpa_keterangan = $child['tanpa_keterangan'];
            $item->jml_tidak_hadir_berturut_turut = $child['jml_tidak_hadir_berturut_turut'];
            return $item;
        });

        return $data;
    }

    public function export(){
        $satuan_kerja = '';
        $nama_satuan_kerja = '';
        $unit_kerja = '';
        $nama_unit_kerja = '';
        $bulan = request('bulan');
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

        // dd($satuan_kerja.' | '.$unit_kerja.' | '.$bulan);

        $data = $this->data_tpp_satuan_kerja($satuan_kerja,$unit_kerja, $bulan);
        $type = request('type');
        // return $data;
        return $this->export_tpp($data, $type,$nama_satuan_kerja,$nama_unit_kerja,$bulan);
    }

    public function export_tpp($data, $type,$nama_satuan_kerja,$nama_unit_kerja,$bulan){
        $spreadsheet = new Spreadsheet();

        $spreadsheet->getProperties()->setCreator('BKPSDM BULUKUMBA')
            ->setLastModifiedBy('BKPSDM BULUKUMBA')
            ->setTitle('Laporan Pembayaran TPP')
            ->setSubject('Laporan Pembayaran TPP')
            ->setDescription('Laporan Pembayaran TPP')
            ->setKeywords('pdf php')
            ->setCategory('LAPORAN Pembayaran TPP');
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

        $tahun = ""  . session('tahun_penganggaran') . "-" . $bulan . "";
        if ($bulan != '0') {

            $periode = date("01", strtotime($tahun)) . ' ' . strftime('%B', mktime(0, 0, 0, $bulan + 1, 0)) . ' s/d ' . date("t", strtotime($tahun)) . ' ' . strftime('%B %Y', mktime(0, 0, 0, $bulan + 1, 0, (int)session('tahun_penganggaran')));
        } else {
            $periode = "Tahun " . session('tahun_penganggaran');
        }

        $sheet->setCellValue('A1', 'LAPORAN PEMBAYARAN TAMBAHAN PENGAHASILAN PEGAWAI')->mergeCells('A1:S1');
        //$sheet->setCellValue('A2', 'OPD ' . strtoupper($data['satuan_kerja']))->mergeCells('A2:U2');
        $sheet->setCellValue('A2', strtoupper($nama_satuan_kerja))->mergeCells('A2:S2');
        $row_bulan = 3;
        if ($nama_unit_kerja !== 'Semua' && $nama_satuan_kerja !== $nama_unit_kerja) {
            $sheet->setCellValue('A3',strtoupper($nama_unit_kerja))->mergeCells('A3:S3');
            $row_bulan = 4;
        }
        $sheet->setCellValue('A'.$row_bulan, 'BULAN '.strtoupper(konvertBulan($bulan)))->mergeCells('A'.$row_bulan.':S'.$row_bulan);

        $sheet->getStyle('A6:S9')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('E1F5FE');


        $sheet->setCellValue('A6', 'NO.')->mergeCells('A6:A8');
        $sheet->setCellValue('B6', 'NAMA & NIP')->mergeCells('B6:B8');
        $sheet->setCellValue('C6', 'GOL.')->mergeCells('C6:C8');
        $sheet->setCellValue('D6', 'JABATAN')->mergeCells('D6:D8');
        $sheet->getColumnDimension('D')->setWidth(35);
        $sheet->setCellValue('E6', 'JENIS JABATAN SESUAI PERBUB TPP')->mergeCells('E6:E8');
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->setCellValue('F6', 'KELAS JABATAN')->mergeCells('F6:F8');
        $sheet->setCellValue('G6', 'PAGU TPP')->mergeCells('G6:G8');
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->setCellValue('H6', 'BESARAN TPP')->mergeCells('H6:N6');
        $sheet->setCellValue('H7', 'KINERJA')->mergeCells('H7:J7');
        $sheet->setCellValue('K7', 'KEHADIRAN')->mergeCells('K7:N7');
        $sheet->setCellValue('H8', 'KINERJA MAKS' . PHP_EOL .'(Rp)');
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->setCellValue('I8', 'CAPAIAN KINERJA ' . PHP_EOL .'(%)');
        $sheet->setCellValue('J8', 'NILAI KINERJA ' . PHP_EOL .'(Rp)');
        $sheet->getColumnDimension('J')->setWidth(20);
        $sheet->setCellValue('K8', 'KEHADIRAN MAKS ' . PHP_EOL .'(Rp)');
        $sheet->getColumnDimension('K')->setWidth(20);
        $sheet->setCellValue('L8', 'POTONGAN KEHADIRAN' . PHP_EOL .'(%)');
        $sheet->setCellValue('M8', 'POTONGAN KEHADIRAN' . PHP_EOL .'(Rp)');
         $sheet->getColumnDimension('M')->setWidth(20);
        $sheet->setCellValue('N8', 'NILAI KEHADIRAN ' . PHP_EOL .'(Rp)');
        $sheet->getColumnDimension('N')->setWidth(20);
        $sheet->setCellValue('O6', 'TPP BRUTO')->mergeCells('O6:O8');
        $sheet->getColumnDimension('O')->setWidth(20);
        $sheet->setCellValue('P6', 'PPH PSL 21')->mergeCells('P6:P8');
        $sheet->getColumnDimension('P')->setWidth(20);
        $sheet->setCellValue('Q6', 'TPP NETTO')->mergeCells('Q6:Q8');
        $sheet->getColumnDimension('Q')->setWidth(20);
        $sheet->setCellValue('R6', 'NO. REK')->mergeCells('R6:R8');
        $sheet->getColumnDimension('R')->setWidth(20);
        $sheet->setCellValue('S6', 'KETERANGAN')->mergeCells('S6:S8');

            $sheet->setCellValue('A9', 'A');
            $sheet->setCellValue('B9', 'B');
            $sheet->setCellValue('C9', 'C');
            $sheet->setCellValue('D9', 'D');
            $sheet->setCellValue('E9', 'E');
            $sheet->setCellValue('F9', 'F');
            $sheet->setCellValue('G9', 'G');
            $sheet->setCellValue('H9', 'H');
            $sheet->setCellValue('I9', 'I');
            $sheet->setCellValue('J9', 'J');
            $sheet->setCellValue('K9', 'K');
            $sheet->setCellValue('L9', 'L');
            $sheet->setCellValue('M9', 'M');
            $sheet->setCellValue('N9', 'N');
            $sheet->setCellValue('O9', 'O');
            $sheet->setCellValue('P9', 'P');
            $sheet->setCellValue('Q9', 'Q');
            $sheet->setCellValue('R9', 'R');
            $sheet->setCellValue('S9', 'S');

        $sheet->getColumnDimension('B')->setWidth(35);

        $sheet->getStyle('A1:A4')->getFont()->setSize(12);
        $sheet->getStyle('A:V')->getAlignment()->setWrapText(true);
        $sheet->getStyle('A1:A4')->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('A6:V9')->getFont()->setBold(true);
        $sheet->getStyle('A6:V9')->getAlignment()->setVertical('center')->setHorizontal('center');

        $cell = $cell_head = 10;
        $jmlPaguTpp = 0;
        $jmlNilaiKinerja = 0;
        $jmlNilaiKehadiran = 0;
        $jmlBpjs = 0;
        $jmlTppBruto = 0;
        $jmlPphPsl = 0;
        $jmlTppNetto = 0;
        $jmlBrutoSpm = 0;
        $jmlIuran = 0;
        $nilai_kinerja = 0;
        $target_nilai = 0;

        $capaian_prod = 0;
        $target_prod = 0;
        $nilaiKinerja = 0;
        $nilai_kinerja = 0;
        $keterangan = '';
        $kelas_jabatan = '';
        $golongan = '';
 
        foreach ($data as $key => $value) {

            

            $value->golongan !== null ? $golongan = explode("/",$value->golongan)[1] : $golongan = '-';

            $value->target_waktu !== null ? $target_nilai = $value->target_waktu : $target_nilai = 0;

            $target_nilai > 0 ? $nilai_kinerja = ( intval($value->capaian_waktu) / $target_nilai ) * 100 : $nilai_kinerja = 0;

            if ($nilai_kinerja > 100) {
                $nilai_kinerja = 100;
            }

            $value->tanpa_keterangan >= 11 || $value->jml_tidak_hadir_berturut_turut == 5 || $nilai_kinerja <= 0 ? $keterangan = 'TMS' : $keterangan = 'MS';

            $nilaiPaguTpp = $value->pagu_tpp * $value->pembayaran / 100;

            $sheet->setCellValue('A' . $cell, $key + 1);
            $sheet->setCellValue('B' . $cell, $value->nama . PHP_EOL . "" . $value->nip);
            $sheet->setCellValue('C' . $cell, $golongan);

            $jabatan_pegawai = $value->nama_jabatan;
            if ($value->status_jabatan !== 'definitif') {
                $jabatan_pegawai = strtoupper($value->status_jabatan).' '.$value->nama_jabatan;
            }

            $sheet->setCellValue('D' . $cell, $jabatan_pegawai);

            $jenis_jabatan = '';
            
            if ($value->level_jabatan > 0 && $value->level_jabatan <= 2) {
                $jenis_jabatan = 'JPT';
            }elseif ($value->level_jabatan > 2 && $value->level_jabatan <= 4) {
                $jenis_jabatan = 'Administrator';
            }elseif ($value->level_jabatan > 4 && $value->level_jabatan <= 6) {
                $jenis_jabatan = 'Pengawas';
            }elseif ($value->level_jabatan == 7) {
                $jenis_jabatan = 'Fungsional';
            }elseif ($value->level_jabatan == 8) {
                $jenis_jabatan = 'Pelaksana';
            }
            else {
                $jenis_jabatan = '-';
            }

            $sheet->setCellValue('E' . $cell, $jenis_jabatan);
            // kelas jabatan
            $value->kelas_jabatan !== null ? $kelas_jabatan = $value->kelas_jabatan : $kelas_jabatan = '-';
            $sheet->setCellValue('F' . $cell, $kelas_jabatan);
            $sheet->setCellValue('G' . $cell, number_format($nilaiPaguTpp));

            $pembagi_nilai_kinerja = 0;
            $pembagi_nilai_kehadiran = 0;

            if ($value->kelas_jabatan > 0 && $value->kelas_jabatan <= 10) {
                $pembagi_nilai_kinerja = 30;
                $pembagi_nilai_kehadiran = 70;
            }elseif ($value->kelas_jabatan > 10 && $value->kelas_jabatan <= 12) {
                $pembagi_nilai_kinerja = 50;
                $pembagi_nilai_kehadiran = 50;
            }else{
                $pembagi_nilai_kinerja = 70;
                $pembagi_nilai_kehadiran = 30;
            }

            $jumlahKehadiran = 0;
            $tppBruto = 0;
                $bpjs=0;
                $iuran=0;
                $brutoSpm=0;
                $pphPsl = 0;
                $tppNetto = 0;

            if ($keterangan !== 'TMS') {
                
                 $nilai_kinerja_rp = $nilaiPaguTpp* $pembagi_nilai_kinerja/100; 

            $sheet->setCellValue('H' . $cell, number_format($nilai_kinerja_rp));
            $sheet->setCellValue('I' . $cell, round($nilai_kinerja,2));
            
            $nilaiKinerja = $nilai_kinerja * $nilai_kinerja_rp / 100;
            $sheet->setCellValue('J' . $cell, number_format($nilaiKinerja));

            $persentaseKehadiran = $pembagi_nilai_kehadiran * $nilaiPaguTpp / 100;
            $sheet->setCellValue('K' . $cell, number_format($persentaseKehadiran));
            $sheet->setCellValue('L' . $cell, $value->jml_potongan_kehadiran_kerja);

            $nilaiKehadiran = $persentaseKehadiran * $value->jml_potongan_kehadiran_kerja / 100;
            $sheet->setCellValue('M' . $cell, number_format($nilaiKehadiran));

            $jumlahKehadiran = $persentaseKehadiran - $nilaiKehadiran;
            $sheet->setCellValue('N' . $cell, number_format($jumlahKehadiran));

            $bpjs = 1 * $nilaiPaguTpp / 100;
            
            $tppBruto = 0;
            $iuran = 4 * $nilaiPaguTpp / 100;
            if ($keterangan === 'TMS') {
                $tppBruto = 0;
                $bpjs=0;
                $iuran=0;
                $brutoSpm=0;
            }else{
                $tppBruto = $nilaiKinerja + $jumlahKehadiran;
                $brutoSpm = $nilaiKinerja + $jumlahKehadiran + $iuran;
            }

            $sheet->setCellValue('O' . $cell, number_format($tppBruto) );
            
            if (strstr( $golongan, 'IV' )) {
                $pphPsl = 15 * $tppBruto / 100;
            }elseif (strstr( $golongan, 'III' )) {
                    $pphPsl = 5 * $tppBruto / 100;
            }else{
                $pphPsl = 0;
            }

            $sheet->setCellValue('P' . $cell, number_format($pphPsl));
          
            // $sheet->setCellValue('O' . $cell, number_format($bpjs));
            // $sheet->setCellValue('Q' . $cell, number_format($pphPsl) );

            

            $tppNetto = $tppBruto - $pphPsl;
            $sheet->setCellValue('Q' . $cell, number_format($tppNetto));
            
            // $sheet->setCellValue('S' . $cell, number_format($brutoSpm));
            $sheet->setCellValue('R' . $cell, '-');
            // $sheet->setCellValue('U' . $cell, number_format($iuran));

            }

           
            $sheet->setCellValue('S'.$cell, $keterangan);
            if ($keterangan === 'TMS') {
                $sheet->getStyle('S' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('F44336');
                
             }else{
                $sheet->getStyle('S' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('00E676');
             }
            $jmlPaguTpp += $nilaiPaguTpp;
            $jmlNilaiKinerja += $nilaiKinerja;
            $jmlNilaiKehadiran += $jumlahKehadiran;
            $jmlBpjs += $bpjs;
            $jmlTppBruto += $tppBruto;
            $jmlPphPsl += $pphPsl;
            $jmlTppNetto += $tppNetto;
            $jmlBrutoSpm += $brutoSpm;
            $jmlIuran += $iuran;

            $cell++;
        }

        $sheet->setCellValue('A' . $cell, "JUMLAH")->mergeCells('A' . $cell . ':F' . $cell);
        $sheet->setCellValue('G' . $cell, number_format($jmlPaguTpp));
        $sheet->setCellValue('J' . $cell, number_format($jmlNilaiKinerja));
        $sheet->setCellValue('N' . $cell, number_format($jmlNilaiKehadiran));
        // $sheet->setCellValue('O' . $cell, number_format($jmlBpjs));
        $sheet->setCellValue('O' . $cell, number_format($jmlTppBruto));
        $sheet->setCellValue('P' . $cell, number_format($jmlPphPsl));
        $sheet->setCellValue('Q' . $cell, number_format($jmlTppNetto));
        // $sheet->setCellValue('S' . $cell, number_format($jmlBrutoSpm));
        // $sheet->setCellValue('U' . $cell, number_format($jmlIuran));
        $spreadsheet->getActiveSheet()->getRowDimension($cell)->setRowHeight(25);

        $sheet->getStyle('G9:G' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('B2DFDB');
        $sheet->getStyle('H9:J' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('FFECB3');
        $sheet->getStyle('K9:N' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('FFF9C4');
        $sheet->getStyle('O9:O' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('FFCDD2');
        $sheet->getStyle('Q9:Q' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('00E676');
        // $sheet->getStyle('S9:S' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('B3E5FC');


        $sheet->getStyle('A10:U' . $cell)->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('B10:B' . $cell)->getAlignment()->setVertical('center')->setHorizontal('left');
        $sheet->getStyle('D10:D' . $cell)->getAlignment()->setVertical('center')->setHorizontal('left');
        $sheet->getStyle('G10:G' . $cell)->getAlignment()->setVertical('center')->setHorizontal('right');
        $sheet->getStyle('H10:H' . $cell)->getAlignment()->setVertical('center')->setHorizontal('right');
        $sheet->getStyle('J10:K' . $cell)->getAlignment()->setVertical('center')->setHorizontal('right');
        $sheet->getStyle('M10:U' . $cell)->getAlignment()->setVertical('center')->setHorizontal('right');
        $sheet->getStyle('T10:T' . $cell)->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('V10:V' . $cell)->getAlignment()->setVertical('center')->setHorizontal('center');
        
        $sheet->getStyle('A' . $cell . ':S' . $cell)->getFont()->setBold(true);

        $border = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '0000000'],
                ]
            ],
        ];

        $sheet->getStyle('A6:S' . $cell)->applyFromArray($border);

        $cell++;
        $sheet->setCellValue('B' . $cell, '')->mergeCells('B' . $cell . ':U' . $cell);

        $tgl_cetak = date("t", strtotime($tahun)) . ' ' . strftime('%B %Y', mktime(0, 0, 0, $bulan + 1, 0, (int)session('tahun_penganggaran')));

        // $sheet->setCellValue('S' . ++$cell, 'BULUKUMBA, ' . $tgl_cetak)->mergeCells('S' . $cell . ':V' . $cell);
        // $sheet->getStyle('A' . $cell . ':V' . $cell)->getAlignment()->setVertical('center')->setHorizontal('center');

        // $cell = $cell + 1;
        // $sheet->setCellValue('A' . $cell, 'KEPALA OPD')->mergeCells('A' . $cell . ':C' . $cell);
        // $sheet->setCellValue('I' . $cell, 'BENDAHARA PENGELUARAN')->mergeCells('I' . $cell . ':N' . $cell);
        // $sheet->setCellValue('S' . $cell, 'NAMA PEMBUAT DAFTAR')->mergeCells('S' . $cell . ':V' . $cell);
        // $sheet->getStyle('A' . $cell . ':V' . $cell)->getAlignment()->setVertical('center')->setHorizontal('center');


        // $cell = $cell + 5;
        // $sheet->setCellValue('A' . $cell, 'NAMA KEPALA OPD')->mergeCells('A' . $cell . ':C' . $cell);
        // $sheet->setCellValue('I' . $cell, 'NAMA BENDAHARA')->mergeCells('I' . $cell . ':N' . $cell);
        // $sheet->setCellValue('S' . $cell, 'NAMA PEMBUAT DAFTAR')->mergeCells('S' . $cell . ':V' . $cell);
        // $sheet->getStyle('A' . $cell . ':V' . $cell)->getAlignment()->setVertical('center')->setHorizontal('center');
        // $sheet->getStyle('A' . $cell . ':V' . $cell)->getFont()->setUnderline(true);;

        // $cell++;
        // $sheet->setCellValue('A' . $cell, 'GOLONGAN JABATAN')->mergeCells('A' . $cell . ':C' . $cell);
        // $sheet->getStyle('A' . $cell . ':V' . $cell)->getAlignment()->setVertical('center')->setHorizontal('center');
        
        // $cell++;
        // $sheet->setCellValue('A' . $cell, 'NIP')->mergeCells('A' . $cell . ':C' . $cell);
        // $sheet->getStyle('A' . $cell . ':V' . $cell)->getAlignment()->setVertical('center')->setHorizontal('center');

        if ($type == 'excel') {
            // Untuk download 
            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

            $bulan_tmt = strtoupper(konvertBulan($bulan));
            $satuan_kerja_tmt = strtoupper($nama_satuan_kerja);
            $filename = "LAPORAN TPP {$satuan_kerja_tmt} BULAN {$bulan_tmt}.xlsx";
            header("Content-Disposition: attachment;filename=\"$filename\"");
        } else {
            $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, $cell_head - 1);
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

    public function data_tpp_pegawai($pegawai,$bulan){
        $tahun = date('Y'); 
        $tanggal_awal = date("$tahun-$bulan-01");
        $tanggal_akhir = date("Y-m-t", strtotime($tanggal_awal));

        $data = DB::table('tb_pegawai')
        ->selectRaw('
            tb_pegawai.id,
            tb_pegawai.nama,
            tb_pegawai.nip,
            tb_pegawai.golongan,
            tb_pegawai.tipe_pegawai,
            tb_master_jabatan.nama_jabatan,
            tb_master_jabatan.target_waktu,
            tb_master_jabatan.kelas_jabatan,
            tb_master_jabatan.pagu_tpp,
            tb_master_jabatan.jenis_jabatan,
            tb_master_jabatan.level_jabatan,
            tb_jabatan.pembayaran,
            tb_unit_kerja.waktu_masuk,
            tb_unit_kerja.waktu_keluar,
            (SELECT SUM(waktu) FROM tb_aktivitas WHERE tb_aktivitas.id_pegawai = tb_pegawai.id AND MONTH(tanggal) = ? LIMIT 1) as capaian_waktu', [$bulan])
        ->join('tb_jabatan', 'tb_jabatan.id_pegawai', 'tb_pegawai.id')
        ->join('tb_master_jabatan', 'tb_jabatan.id_master_jabatan', '=', 'tb_master_jabatan.id')
        ->join('tb_satuan_kerja', 'tb_pegawai.id_satuan_kerja', '=', 'tb_satuan_kerja.id')
        ->join('tb_unit_kerja','tb_jabatan.id_unit_kerja','=','tb_unit_kerja.id')
        ->where('tb_pegawai.id', $pegawai)
        ->groupBy('tb_pegawai.id', 'tb_pegawai.nama', 'tb_pegawai.nip', 'tb_pegawai.golongan', 'tb_master_jabatan.nama_jabatan', 'tb_master_jabatan.target_waktu','tb_master_jabatan.kelas_jabatan','tb_master_jabatan.pagu_tpp','tb_master_jabatan.jenis_jabatan','tb_master_jabatan.level_jabatan','tb_jabatan.pembayaran','tb_unit_kerja.waktu_masuk','tb_unit_kerja.waktu_keluar')
        ->first();

        $child = $this->data_kehadiran_pegawai($data->id,$tanggal_awal,$tanggal_akhir,$data->waktu_masuk,$data->waktu_keluar,$data->tipe_pegawai);
        $data->jml_potongan_kehadiran_kerja = $child['jml_potongan_kehadiran_kerja'];
        $data->tanpa_keterangan = $child['tanpa_keterangan'];
        $data->jml_hari_kerja = $child['jml_hari_kerja'];
        $data->jml_hadir = $child['jml_hadir'];
        $data->jml_sakit = $child['jml_sakit'];
        $data->jml_cuti = $child['jml_cuti'];
        $data->jml_dinas_luar = $child['jml_dinas_luar'];
        $data->jml_tidak_apel = $child['jml_tidak_apel'];

        $jmlPaguTpp = 0;
        $jmlNilaiKinerja = 0;
        $jmlNilaiKehadiran = 0;
        $jmlBpjs = 0;
        $jmlTppBruto = 0;
        $jmlPphPsl = 0;
        $jmlTppNetto = 0;
        $jmlBrutoSpm = 0;
        $jmlIuran = 0;
        $nilai_kinerja = 0;
        $target_nilai = 0;

        $capaian_prod = 0;
        $target_prod = 0;
        $nilaiKinerja = 0;
        $nilai_kinerja = 0;
        $keterangan = '';
        $kelas_jabatan = '';
        $golongan = '';


            $data->golongan !== null ? $golongan = explode("/",$data->golongan)[1] : $golongan = '-';
            $data->target_waktu !== null ? $target_nilai = $data->target_waktu : $target_nilai = 0;
            // if ($data->kelas_jabatan == 1 || $data->kelas_jabatan == 3 || $data->kelas_jabatan == 15) {
            //     $nilai_kinerja = 100;
            // }else{
            //     $target_nilai > 0 ? $nilai_kinerja = ( intval($data->capaian_waktu) / $target_nilai ) * 100 : $nilai_kinerja = 0;
            // }

            $target_nilai > 0 ? $nilai_kinerja = ( intval($data->capaian_waktu) / $target_nilai ) * 100 : $nilai_kinerja = 0;

            if ($nilai_kinerja > 100) {
                $nilai_kinerja = 100;
            }

            $pembagi_nilai_kinerja = 0;
            $pembagi_nilai_kehadiran = 0;

            if ($data->kelas_jabatan > 0 && $data->kelas_jabatan <= 10) {
                $pembagi_nilai_kinerja = 30;
                $pembagi_nilai_kehadiran = 70;
            }elseif ($data->kelas_jabatan > 10 && $data->kelas_jabatan <= 12) {
                $pembagi_nilai_kinerja = 50;
                $pembagi_nilai_kehadiran = 50;
            }else{
                $pembagi_nilai_kinerja = 70;
                $pembagi_nilai_kehadiran = 30;
            }

            $nilaiPaguTpp = $data->pagu_tpp * $data->pembayaran / 100;

            $nilai_kinerja_rp = $nilaiPaguTpp* $pembagi_nilai_kinerja/100; 
            $nilaiKinerja = $nilai_kinerja * $nilai_kinerja_rp / 100;
            $persentaseKehadiran = $pembagi_nilai_kehadiran * $nilaiPaguTpp / 100;
            $nilaiKehadiran = $persentaseKehadiran * $data->jml_potongan_kehadiran_kerja / 100;
            $jumlahKehadiran = $persentaseKehadiran - $nilaiKehadiran;
            $bpjs = 1 * $nilaiPaguTpp / 100;
            $data->tanpa_keterangan > 3  ? $keterangan = 'TMS'  : $keterangan = 'MS';
            $tppBruto = 0;
            $iuran = 4 * $nilaiPaguTpp / 100;
            if ($keterangan === 'TMS') {
                $tppBruto = 0;
                $bpjs=0;
                $iuran=0;
                $brutoSpm=0;
            }else{
                $tppBruto = $nilaiKinerja + $jumlahKehadiran;
                $brutoSpm = $nilaiKinerja + $jumlahKehadiran + $iuran;
            }

            $perkalian_pph = 0;
            if (strstr( $golongan, 'IV' )) {
                $perkalian_pph = 15;
                $pphPsl = 15 * $tppBruto / 100;
                
            }elseif (strstr( $golongan, 'III' )) {
                $perkalian_pph = 5;
                $pphPsl = $perkalian_pph * $tppBruto / 100;
            }else{
                $pphPsl = 0;
            }
            $tppNetto = $tppBruto - $pphPsl;
            
            $tpp_bulan_ini = ($persentaseKehadiran - $nilaiKehadiran) + $nilaiKinerja;
            $potongan_jkn_pph_tmt = $pphPsl + $bpjs; 

        return [
            'kinerja_maks' => 'Rp. '.number_format($nilai_kinerja_rp),
            'persen_kinerja_maks' => round($nilai_kinerja,2),
            'kehadiran_maks' => 'Rp. '.number_format($persentaseKehadiran),
            'persen_kehadiran_maks' => $data->jml_potongan_kehadiran_kerja,
            'potongan_kinerja' => 'Rp. '.number_format($nilaiPaguTpp * (100 - $nilai_kinerja) / 100),
            'persen_potongan_kinerja' => round((100 - $nilai_kinerja),2),
            'potongan_kehadiran' => 'Rp. '.number_format($nilaiKehadiran),
            'persentase_potongan_kehadiran' => $data->jml_potongan_kehadiran_kerja,
            'bpjs' => $bpjs,
            'pphPsl' => number_format($pphPsl),
            'nilai_bruto' => 'Rp '.number_format($tppBruto),
            'tppNetto' => number_format($tppNetto),
            'brutoSpm' => number_format($brutoSpm),
            'nilaiPaguTpp' => 'Rp. '.number_format($nilaiPaguTpp),
            'iuran' => 'Rp. '.number_format($nilaiPaguTpp * 4 / 100),
            'jml_hari_kerja' => $data->jml_hari_kerja,
            'jml_hadir' => $data->jml_hadir,
            'jml_sakit' => $data->jml_sakit,
            'jml_cuti' => $data->jml_cuti,
            'jml_dinas_luar' => $data->jml_dinas_luar,
            'jml_tidak_apel' => $data->jml_tidak_apel,
            'tanpa_keterangan' => $data->tanpa_keterangan,
            'pembagi_nilai_kehadiran' => $pembagi_nilai_kehadiran,
            'pembagi_nilai_kinerja' => $pembagi_nilai_kinerja,
            'capaian' => number_format($nilaiKinerja),
            'jumlahKehadiran' => 'Rp '.number_format($jumlahKehadiran),
            'potongan_jkn_pph' => $pphPsl + $bpjs,
            'total_tpp_bruto' => $nilaiKinerja + $jumlahKehadiran,
            'tpp_bulan_ini' => 'Rp. '.number_format($tpp_bulan_ini),
             'potongan_jkn_pph_tmt' => 'Rp. '.number_format($potongan_jkn_pph_tmt),
             'perkalian_pph' => $perkalian_pph
        ];
    }

    public function export_pegawai(){
        // dd('tes');
        $type = request('type');
        $pegawai_params = request('pegawai') ? request('pegawai') : Auth::user()->id_pegawai;
        $bulan = request('bulan');
        $jabatan_req = request("status");

        
        // dd($jabatan_req);
        $pegawai = $this->findPegawai($pegawai_params, $jabatan_req);
        $checkJabatan = $this->checkJabatanDefinitif($pegawai_params, $jabatan_req);


        $data = array();
        if ($checkJabatan) {
            $atasan = $this->findAtasan($pegawai_params);
            // dd($atasan);
            // if ($atasan) {
                $data = $this->data_tpp_pegawai($pegawai_params, $bulan);
                // return $data;
                // if (request('is_opd')) {
                //     return $this->export_tpp_pegawai_opd($data,$type,$pegawai,$atasan,$bulan);
                // }  else {
                    
                // }

                return $this->export_tpp_pegawai($data,$type,$pegawai,$atasan,$bulan);

            // }else{
            //    return redirect()->back()->withErrors(['error' => 'Belum bisa membuka laporan']); 
            // }
            
        }else{
            return redirect()->back()->withErrors(['error' => 'Belum bisa membuka laporan, pegawai tersebut belum mempunyai jabatan']);
        }
    }

    public function export_tpp_pegawai($data,$type, $pegawai, $atasan, $bulan){
         $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator('BKPSDM BULUKUMBA')
            ->setLastModifiedBy('BKPSDM BULUKUMBA')
            ->setTitle('Laporan Pembayaran TPP')
            ->setSubject('Laporan Pembayaran TPP')
            ->setDescription('Laporan Pembayaran TPP')
            ->setKeywords('pdf php')
            ->setCategory('LAPORAN Pembayaran TPP');
        $sheet = $spreadsheet->getActiveSheet();
        // $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

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

        $sheet->setCellValue('A1', 'LAPORAN KINERJA PEGAWAI (AKTIVITAS)')->mergeCells('A1:J1');
        $sheet->setCellValue('A2', strtoupper(konvertBulan($bulan)))->mergeCells('A2:J2');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

        $spreadsheet->getActiveSheet()->getStyle('A1:F4')->getFont()->setBold(true);


        $sheet->setCellValue('A4', 'PEGAWAI YANG DINILAI')->mergeCells('A4:D4');
        $sheet->setCellValue('E4', 'PEJABAT PENILAI')->mergeCells('E4:H4');
        $sheet->getStyle('A4:H4')->getAlignment()->setHorizontal('center');
        
        $sheet->setCellValue('A5', ' Nama')->mergeCells('A5:B5');
        $sheet->setCellValue('C5', ' '.$pegawai->nama)->mergeCells('C5:D5');

        $sheet->setCellValue('E5', ' Nama')->mergeCells('E5:F5');
        $sheet->setCellValue('G5', ' '.($atasan !== null ? $atasan->nama : '-'))->mergeCells('G5:H5');

        $sheet->setCellValue('A6', ' NIP')->mergeCells('A6:B6');
        $sheet->setCellValue('C6', " " .$pegawai->nip)->mergeCells('C6:D6');

        $sheet->setCellValue('E6', ' NIP')->mergeCells('E6:F6');
        $sheet->setCellValue('G6', " ".($atasan !== null ?  $atasan->nip : '-'))->mergeCells('G6:H6');

        $golongan_pegawai = '';
        $golongan_atasan = '';

        $pegawai->golongan !== null ? $golongan_pegawai = $pegawai->golongan : $golongan_pegawai = '-';
        $atasan->golongan !== null ? $golongan_atasan = $atasan->golongan : $golongan_atasan = '-';

        $sheet->setCellValue('A7', ' Pangkat / Gol Ruang')->mergeCells('A7:B7');
        $sheet->setCellValue('C7', $golongan_pegawai)->mergeCells('C7:D7');

        $sheet->setCellValue('E7', ' Pangkat / Gol Ruang')->mergeCells('E7:F7');
        $sheet->setCellValue('G7', ' '.$golongan_atasan)->mergeCells('G7:H7');

        $sheet->setCellValue('A8', ' Jabatan')->mergeCells('A8:B8');
        $sheet->setCellValue('C8', ' '.$pegawai->nama_jabatan)->mergeCells('C8:D8');

        $sheet->setCellValue('E8', ' Jabatan')->mergeCells('E8:F8');
        $sheet->setCellValue('G8', ' '.($atasan !== null ?  $atasan->nama_jabatan : '-'))->mergeCells('G8:H8');

        $sheet->setCellValue('A9', ' Unit kerja')->mergeCells('A9:B9');
        $sheet->setCellValue('C9', ' '.($pegawai !== null ? $pegawai->nama_unit_kerja : '-'))->mergeCells('C9:D9');

        $sheet->setCellValue('E9', ' Unit kerja')->mergeCells('E9:F9');
        $sheet->setCellValue('G9', ' '.($atasan !== null ? $atasan->nama_unit_kerja : '-'))->mergeCells('G9:H9');

        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(30);

        $border_header = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '0000000'],
                ],
            ],
        ];

        $sheet->getStyle('A4:H9')->applyFromArray($border_header);
        $sheet->setCellValue('A10', ' ');

        $sheet->setCellValue('A11', 'No');
        $sheet->setCellValue('B11', 'Uraian')->mergeCells('B11:F11');
         $sheet->setCellValue('G11', 'Persentase');
          $sheet->setCellValue('H11', 'Total');

          $sheet->getStyle('A11:H11')->getAlignment()->setHorizontal('center');
          $spreadsheet->getActiveSheet()->getStyle('A11:H11')->getFont()->setBold(true);
        

        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);

        $cell = 12;

         $sheet->setCellValue('A12', 'A');
         $sheet->setCellValue('B12', 'PAGU TPP')->mergeCells('B12:F12');
         $sheet->setCellValue('H12', $data['nilaiPaguTpp']);

         $sheet->getStyle('A12:H12')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('D3DFE2');
         $spreadsheet->getActiveSheet()->getStyle('A12:H12')->getFont()->setBold(true);

         $sheet->setCellValue('A13', '1');
         $sheet->setCellValue('B13', 'Kinerja Maks')->mergeCells('B13:F13');
         $sheet->setCellValue('G13', $data['persen_kinerja_maks'].' %');
         $sheet->setCellValue('H13', $data['kinerja_maks']);

         $sheet->setCellValue('A14', '2');
         $sheet->setCellValue('B14', 'Kehadiran Maks')->mergeCells('B14:F14');
         $sheet->setCellValue('G14', $data['persen_kehadiran_maks'].' %');
         $sheet->setCellValue('H14', $data['kehadiran_maks']);

          $sheet->setCellValue('A15', 'B');
         $sheet->setCellValue('B15', 'Potongan')->mergeCells('B15:F15');
         $spreadsheet->getActiveSheet()->getStyle('A15:H15')->getFont()->setBold(true);

         $sheet->setCellValue('A16', '1');
         $sheet->setCellValue('B16', 'Potongan Kehadiran')->mergeCells('B16:F16');
         $sheet->setCellValue('G16', $data['pembagi_nilai_kehadiran'].' %');
         $sheet->setCellValue('H16', $data['potongan_kehadiran']);

         $sheet->setCellValue('A17', '2');
         $sheet->setCellValue('B17', 'Potongan Kinerja')->mergeCells('B17:F17');
         $sheet->setCellValue('G17', $data['persen_potongan_kinerja'].' %');
         $sheet->setCellValue('H17', $data['potongan_kinerja']);

        //  $sheet->setCellValue('A17', '3');
        //  $sheet->setCellValue('B17', 'BPJS')->mergeCells('B17:F17');
        //  $sheet->setCellValue('G17', '1%');
        //  $sheet->setCellValue('H17', $data['bpjs']);

         $sheet->setCellValue('A18', 'C');
         $sheet->setCellValue('B18', 'Nilai Bruto')->mergeCells('B18:F18');
         $sheet->setCellValue('H18', $data['nilai_bruto']);

         $sheet->getStyle('A18:H18')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('EDCDCC');
         $spreadsheet->getActiveSheet()->getStyle('A18:H18')->getFont()->setBold(true);

         $sheet->setCellValue('A19', '4');
         $sheet->setCellValue('B19', 'PPH21')->mergeCells('B19:F19');
         $sheet->setCellValue('G19', $data['perkalian_pph'].'%');
         $sheet->setCellValue('H19', $data['pphPsl']);

        // $sheet->setCellValue('A22', 'E');
        //  $sheet->setCellValue('B22', 'Nilai Bruto SPM')->mergeCells('B22:F22');
        //  $sheet->setCellValue('H22', $data['brutoSpm']);
        //  $sheet->getStyle('A22:H22')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('CCD9F5');
        //  $spreadsheet->getActiveSheet()->getStyle('A22:H22')->getFont()->setBold(true);

         $sheet->setCellValue('A20', 'E');
         $sheet->setCellValue('B20', 'TPP Netto')->mergeCells('B20:F20');
         $sheet->setCellValue('H20', $data['tppNetto']);

         $sheet->getStyle('A20:H20')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('DDEAD5');
         $spreadsheet->getActiveSheet()->getStyle('A20:H20')->getFont()->setBold(true);

        //  $sheet->setCellValue('A21', '5');
        //  $sheet->setCellValue('B21', 'Iuran (Dibayarkan oleh Pemda)')->mergeCells('B21:F21');
        //  $sheet->setCellValue('G21', '4%');
        //  $sheet->setCellValue('H21', $data['iuran']);

         $sheet->getStyle('A12:A22')->getAlignment()->setHorizontal('center');
         $sheet->getStyle('H12:H22')->getAlignment()->setHorizontal('right');
        //  $sheet->getStyle('A18')->getAlignment()->setHorizontal('center');
        //  $sheet->getStyle('A20')->getAlignment()->setHorizontal('center');
        //  $sheet->getStyle('A22')->getAlignment()->setHorizontal('center');

        $border_row = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '0000000'],
                ],
            ],
        ];

        $sheet->getStyle('A11:H20')->applyFromArray($border_row);


        if ($type == 'excel') {
         
            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Laporan TPP '. $pegawai->nama .'".xlsx');
            
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
    
    public function export_tpp_pegawai_opd($data,$type, $pegawai, $atasan, $bulan){
         $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator('BKPSDM BULUKUMBA')
            ->setLastModifiedBy('BKPSDM BULUKUMBA')
            ->setTitle('Laporan Pembayaran TPP')
            ->setSubject('Laporan Pembayaran TPP')
            ->setDescription('Laporan Pembayaran TPP')
            ->setKeywords('pdf php')
            ->setCategory('LAPORAN Pembayaran TPP');
        $sheet = $spreadsheet->getActiveSheet();
        // $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

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

        $sheet->setCellValue('A1', 'RINCIAN PEMBAYARAN TPP')->mergeCells('A1:C1');
        $sheet->setCellValue('A2', strtoupper(konvertBulan($bulan)) . ' ' . date('Y'))->mergeCells('A2:C2');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

        $sheet->setCellValue('A3', ' ');

        $sheet->setCellValue('A4', 'Nama');
        $sheet->setCellValue('B4', ': ' . $pegawai->nama);
        $sheet->setCellValue('A5', 'NIP');
        $sheet->setCellValue('B5', ': ' . $pegawai->nip);

        $sheet->setCellValue('A6', 'No');
        $sheet->setCellValue('B6', 'Uraian');
        $sheet->setCellValue('C6', 'Jumlah');
        $spreadsheet->getActiveSheet()->getRowDimension('6')->setRowHeight(20);
        $sheet->getStyle('A6:C6')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('D9D9D9');

        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(60);
        $sheet->getColumnDimension('C')->setWidth(40);

        $sheet->getStyle('A6:C6')->getAlignment()->setHorizontal('center');
        $spreadsheet->getActiveSheet()->getStyle('A1:C6')->getFont()->setBold(true);
        
        $cell = 7;

        // Pagu TPP 
        $sheet->setCellValue('A' . $cell, 'A');
        $sheet->setCellValue('B' . $cell, 'Pagu TPP');
        $sheet->setCellValue('C' . $cell, $data['nilaiPaguTpp']);

         $sheet->getStyle('A'. $cell.':C'. $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('D9EAD3');
         $spreadsheet->getActiveSheet()->getStyle('A' . $cell . ':C' . $cell)->getFont()->setBold(true);

         $cell+=1;
         $sheet->setCellValue('A' . $cell, '');
         $sheet->setCellValue('B' . $cell, 'Kinerja Maks (' . $data['pembagi_nilai_kinerja'] . '%)');
         $sheet->setCellValue('C' . $cell, $data['kinerja_maks']);
         
         $cell+=1;
         $sheet->setCellValue('A' . $cell, '');
         $sheet->setCellValue('B' . $cell, 'Capaian (' . $data['persen_kehadiran_maks'] . '%)');
         $sheet->setCellValue('C' . $cell,'Rp. '. $data['capaian']);
         
        //  Total Kinerja
         $cell+=1;
         $sheet->setCellValue('A' . $cell, 'B');
         $sheet->setCellValue('B' . $cell, 'Total Kinerja');
         $sheet->setCellValue('C' . $cell, 'Rp. '. $data['capaian']);

         $sheet->getStyle('A'. $cell.':C'. $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('D9EAD3');
         $spreadsheet->getActiveSheet()->getStyle('A' . $cell . ':C' . $cell)->getFont()->setBold(true);

         $cell+=1;
         $sheet->setCellValue('A' . $cell, '');
         $sheet->setCellValue('B' . $cell, 'Kehadiran Maks (' . $data['pembagi_nilai_kehadiran'] . '%)');
         $sheet->setCellValue('C' . $cell, $data['kehadiran_maks']);
         
         $cell+=1;
         $sheet->setCellValue('A' . $cell, '');
         $sheet->setCellValue('B' . $cell, 'Potongan (' . $data['persen_kehadiran_maks'] . '%)');
         $sheet->setCellValue('C' . $cell, $data['potongan_kehadiran']);
         
         $cell+=1;
         $sheet->setCellValue('A' . $cell, 'C');
         $sheet->setCellValue('B' . $cell, 'Total Kehadiran');
         $sheet->setCellValue('C' . $cell, $data['jumlahKehadiran']);

         $sheet->getStyle('A'. $cell.':C'. $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('D9EAD3');
         $spreadsheet->getActiveSheet()->getStyle('A' . $cell . ':C' . $cell)->getFont()->setBold(true);

         $cell+=1;
         $sheet->setCellValue('A' . $cell, 'D');
         $sheet->setCellValue('B' . $cell, 'Total TPP Bruto');
         $sheet->setCellValue('C' . $cell,'Rp '.number_format($data['total_tpp_bruto']));

         $sheet->getStyle('A'. $cell.':C'. $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('D9EAD3');
         $spreadsheet->getActiveSheet()->getStyle('A' . $cell . ':C' . $cell)->getFont()->setBold(true);

         $cell+=1;
         $sheet->setCellValue('A' . $cell, ' ');

         $cell+=1;
         $sheet->setCellValue('A' . $cell, 'E');
         $sheet->setCellValue('B' . $cell, 'JKN dan PPH 21');
         $sheet->setCellValue('C' . $cell, 'Rp '.number_format($data['potongan_jkn_pph']));

         $sheet->getStyle('A'. $cell.':C'. $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('D9EAD3');
         $spreadsheet->getActiveSheet()->getStyle('A' . $cell . ':C' . $cell)->getFont()->setBold(true);
        
         $cell+=1;
         $sheet->setCellValue('A' . $cell, '');
         $sheet->setCellValue('B' . $cell, 'BPJS (' . 1 . '%)');
         $sheet->setCellValue('C' . $cell, 'Rp '.number_format($data['bpjs']));

         $cell+=1;
         $sheet->setCellValue('A' . $cell, 'F');
         $sheet->setCellValue('B' . $cell, 'TPP Bruto');
         $sheet->setCellValue('C' . $cell, $data['nilai_bruto']);

         $sheet->getStyle('A'. $cell.':C'. $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('D9EAD3');
         $spreadsheet->getActiveSheet()->getStyle('A' . $cell . ':C' . $cell)->getFont()->setBold(true);

         $cell+=1;
         $sheet->setCellValue('A' . $cell, '');
         $sheet->setCellValue('B' . $cell, 'PPH21 (' . 15 . '%)');
         $sheet->setCellValue('C' . $cell, $data['pphPsl']);

         $cell+=1;
         $sheet->setCellValue('A' . $cell, 'G');
         $sheet->setCellValue('B' . $cell, 'Total TPP Netto');
         $sheet->setCellValue('C' . $cell, $data['tppNetto']);

         $sheet->getStyle('A'. $cell.':C'. $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('D9EAD3');
         $spreadsheet->getActiveSheet()->getStyle('A' . $cell . ':C' . $cell)->getFont()->setBold(true);

         $cell+=1;
         $sheet->setCellValue('A' . $cell, '');
         $sheet->setCellValue('B' . $cell, 'Iuran BPJS Dibayarkan oleh Pemda (' . 4 . '%)');
         $sheet->setCellValue('C' . $cell, $data['iuran']);

         $cell+=1;
         $sheet->setCellValue('A' . $cell, 'H');
         $sheet->setCellValue('B' . $cell, 'Nilai Bruto SPM');
         $sheet->setCellValue('C' . $cell, $data['brutoSpm']);

         $sheet->getStyle('A'. $cell.':C'. $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('D9EAD3');
         $spreadsheet->getActiveSheet()->getStyle('A' . $cell . ':C' . $cell)->getFont()->setBold(true);

         $sheet->getStyle('A6:C' . $cell)->getAlignment()->setVertical('center')->setHorizontal('center');
         $sheet->getStyle('B7:B' . $cell)->getAlignment()->setHorizontal('left');
         $sheet->getStyle('C7:C' . $cell)->getAlignment()->setHorizontal('right');

        $border = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '0000000'],
                ],
            ],
        ];

        $sheet->getStyle('A6:C'.$cell)->applyFromArray($border);


        if ($type == 'excel') {
         
            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Laporan TPP '. $pegawai->nama .'".xlsx');
            
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
