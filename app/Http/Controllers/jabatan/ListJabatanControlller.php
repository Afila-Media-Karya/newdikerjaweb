<?php

namespace App\Http\Controllers\jabatan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Requests\JabatanRequest;
use App\Models\Jabatan;
use DB;
use App\Traits\General;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ListJabatanControlller extends BaseController
{
    use General;
    public function breadcumb()
    {
        return [
            [
                'label' => 'Jabatan',
                'url' => '#'
            ],
            [
                'label' => 'List Jabatan',
                'url' => '#'
            ],
        ];
    }

    public function datatable()
    {
        $data = array();
        $satuan_kerja = intval(request('satuan_kerja'));

        $query = DB::table('tb_jabatan as tb_jabatan1')
            ->leftJoin('tb_master_jabatan as tb_master_jabatan1', 'tb_jabatan1.id_master_jabatan', '=', 'tb_master_jabatan1.id')
            ->leftJoin('tb_jabatan as tb_jabatan2', 'tb_jabatan1.id_parent', '=', 'tb_jabatan2.id')
            ->leftJoin('tb_pegawai', 'tb_jabatan1.id_pegawai', '=', 'tb_pegawai.id')
            ->leftJoin('tb_master_jabatan as tb_master_jabatan2', 'tb_jabatan2.id_master_jabatan', '=', 'tb_master_jabatan2.id')
            ->select('tb_jabatan1.id', 'tb_jabatan1.uuid', 'tb_pegawai.nama as pejabat', 'tb_master_jabatan1.nama_jabatan as jabatan', 'tb_master_jabatan2.nama_jabatan as atasan_langsung', 'tb_jabatan1.status', 'tb_master_jabatan1.level_jabatan')
            ->orderBy('tb_jabatan2.id_parent', 'DESC')
            ->whereNotNull('tb_jabatan1.id_parent')
            ->orderBy('tb_master_jabatan1.kelas_jabatan', 'ASC');

        if ($satuan_kerja > 0) {
            $query->where('tb_jabatan1.id_unit_kerja', $satuan_kerja);
        }
        
        $data = $query->get();

        return $this->sendResponse($data, 'Jabatan Fetched Success');
    }

    public function index()
    {
        $module = $this->breadcumb();
        $satuan_kerja = $this->option_satuan_kerja();
        $unit_kerja = $this->option_unit_kerja();
        $satuan_kerja_user = '';

        if(hasRole()['guard'] == 'web'){
            $satuan_kerja_user = $this->infoSatuanKerja(hasRole()['id_pegawai'])->id_unit_kerja;
        }

        if (hasRole()['guard'] == 'administrator') {
            return view('jabatan.jabatan.index',compact('module','satuan_kerja','satuan_kerja_user','unit_kerja'));
        }elseif (hasRole()['guard'] == 'web' && hasRole()['role'] == '1') {
            $satuan_kerja_user = $this->infoSatuanKerja(hasRole()['id_pegawai'])->id_satuan_kerja;
            $unit_kerja = $this->option_by_unit_kerja($satuan_kerja_user);

            return view('jabatan.jabatan.indexopd',compact('module','satuan_kerja','satuan_kerja_user','unit_kerja'));
        }else{
            $unit_kerja = $this->infoSatuanKerja(hasRole()['id_pegawai'])->id_unit_kerja;
            $lokasi = DB::table('tb_lokasi')->where('id_unit_kerja',$unit_kerja)->first()->id;
            $pegawai = $this->option_pegawaiBy_unit_kerja(null,$unit_kerja);
            return view('jabatan.jabatan.indexunit',compact('module','satuan_kerja','satuan_kerja_user','unit_kerja','lokasi','pegawai'));
        }
    }

    public function cetak()
    {
        $data = array();
        $satuan_kerja = intval(request('satuan_kerja'));
        $type = request('type');

        $query = DB::table('tb_jabatan as tb_jabatan1')
            ->leftJoin('tb_satuan_kerja', 'tb_jabatan1.id_satuan_kerja', '=', 'tb_satuan_kerja.id')
            ->leftJoin('tb_master_jabatan as tb_master_jabatan1', 'tb_jabatan1.id_master_jabatan', '=', 'tb_master_jabatan1.id')
            ->leftJoin('tb_jabatan as tb_jabatan2', 'tb_jabatan1.id_parent', '=', 'tb_jabatan2.id')
            ->leftJoin('tb_pegawai', 'tb_jabatan1.id_pegawai', '=', 'tb_pegawai.id')
            ->leftJoin('tb_master_jabatan as tb_master_jabatan2', 'tb_jabatan2.id_master_jabatan', '=', 'tb_master_jabatan2.id')
            ->select('tb_jabatan1.id', 'tb_jabatan1.uuid','tb_pegawai.nama as nama', 'tb_pegawai.nip as nip' , 'tb_master_jabatan1.nama_jabatan as jabatan', 'tb_master_jabatan2.nama_jabatan as atasan_langsung', 'tb_jabatan1.status', 'tb_master_jabatan1.level_jabatan', 'tb_satuan_kerja.nama_satuan_kerja')
            ->whereNotNull('tb_jabatan1.id_parent')
            ->orderBy('tb_satuan_kerja.kode_satuan_kerja', 'ASC')
            ->orderBy('tb_master_jabatan1.kelas_jabatan', 'DESC');

        if (hasRole()['guard'] == 'web' && hasRole()['role'] == '3') {
            $query->where('tb_jabatan1.id_unit_kerja', $satuan_kerja);
        } else {
            if ($satuan_kerja > 0) {
                $query->where('tb_jabatan1.id_satuan_kerja', $satuan_kerja);
            }
        }

        $data = $query->get();


        $spreadsheet = new Spreadsheet();

        $spreadsheet->getProperties()->setCreator('BKPSDM BULUKUMBA')
            ->setLastModifiedBy('BKPSDM BULUKUMBA')
            ->setTitle('Laporan Rekapitulasi Kinerja')
            ->setSubject('Laporan Rekapitulasi Kinerja')
            ->setDescription('Laporan Rekapitulasi Kinerja')
            ->setKeywords('pdf php')
            ->setCategory('Laporan Rekapitulasi Kinerja');
        $sheet = $spreadsheet->getActiveSheet();
        //  $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_FOLIO);

        $sheet->getRowDimension(1)->setRowHeight(17);
        $sheet->getRowDimension(2)->setRowHeight(17);
        $sheet->getRowDimension(3)->setRowHeight(7);
        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
        $spreadsheet->getDefaultStyle()->getFont()->setSize(10);
        $spreadsheet->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);
        $spreadsheet->getActiveSheet()->getPageSetup()->setVerticalCentered(false);

        // Margin PDF
        $spreadsheet->getActiveSheet()->getPageMargins()->setTop(0.3);
        $spreadsheet->getActiveSheet()->getPageMargins()->setRight(0.3);
        $spreadsheet->getActiveSheet()->getPageMargins()->setLeft(0.5);
        $spreadsheet->getActiveSheet()->getPageMargins()->setBottom(0.3);

        // Load a logo image
        $spreadsheet->getActiveSheet()->mergeCells('A1:F1');
        $logoPath = 'admin/assets/media/logos/logo_sm.png';
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Paid');
        $drawing->setDescription('Paid');
        $drawing->setPath($logoPath);
        $drawing->setCoordinates('A1');
        $drawing->setHeight(70);
        $drawing->setWorksheet($spreadsheet->getActiveSheet());

        $sheet->setCellValue('A2', 'DAFTAR JABATAN')->mergeCells('A2:F2');
        $sheet->setCellValue('A3', 'PEMERINTAH KABUPATEN BULUKUMBA')->mergeCells('A3:F3');
        $sheet->setCellValue('A4', ' ')->mergeCells('A4:F4');

        $sheet->setCellValue('A5', 'NO');
        $sheet->setCellValue('B5', 'NAMA')->getColumnDimension('B')->setWidth(40);
        $sheet->setCellValue('C5', 'NIP');
        $sheet->setCellValue('D5', 'JABATAN')->getColumnDimension('D')->setWidth(40);
        $sheet->setCellValue('E5', 'ATASAN LANGSUNG')->getColumnDimension('E')->setWidth(40);
        $sheet->setCellValue('F5', 'SATUAN KERJA')->getColumnDimension('F')->setWidth(30);
        $sheet->getStyle('A5:F5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('E1F5FE');

        $spreadsheet->getActiveSheet()->getRowDimension('5')->setRowHeight(25);
        $sheet->getStyle('A:D')->getAlignment()->setWrapText(true);
        $sheet->getStyle('A1:D5')->getFont()->setBold(true);

        $cell = $cell_head = 6;
        $no = 1;
        foreach ($data as $key => $value) {
            $sheet->setCellValue('A' . $cell, $no++);
            $sheet->setCellValue('B' . $cell, $value->nama);
            $sheet->setCellValue('C' . $cell, $value->nip);
            $sheet->setCellValue('D' . $cell, $value->jabatan);
            $sheet->setCellValue('E' . $cell, $value->atasan_langsung);
            $sheet->setCellValue('F' . $cell, $value->nama_satuan_kerja);
            $cell++;
        }

        $border_row = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '0000000'],
                ],
            ],
        ];
        $sheet->getStyle('A5:F' . $cell)->applyFromArray($border_row);
        $sheet->getStyle('A1:F' . $cell)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1:F' . $cell)->getAlignment()->setVertical('center');
        $sheet->getStyle('B6:B' . $cell)->getAlignment()->setHorizontal('rigth');
        $sheet->getStyle('D6:E' . $cell)->getAlignment()->setHorizontal('rigth');
        // $sheet->getStyle('C5:C' . $cell)->getAlignment()->setHorizontal('rigth');
        // $sheet->getStyle('A3:G')->getAlignment()->setHorizontal('rigth');


        $cell++;

        if ($type == 'excel') {
            // Untuk download 
            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Daftar Laporan TPP"' . strtoupper('tes')  . ' BULAN ' . strtoupper('tes') . ' .xlsx"');
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

    public function carijumlahkelasjabatan($satuan_kerja){

        $result = DB::table('tb_jabatan')
        ->join('tb_pegawai', 'tb_jabatan.id_pegawai', '=', 'tb_pegawai.id')
        ->join('tb_master_jabatan', 'tb_jabatan.id_master_jabatan', '=', 'tb_master_jabatan.id')
        ->where('tb_jabatan.id_satuan_kerja', $satuan_kerja)
        ->select(
            DB::raw('COUNT(CASE WHEN tb_master_jabatan.kelas_jabatan = 15 THEN 1 END) AS kelas_15'),
            DB::raw('COUNT(CASE WHEN tb_master_jabatan.kelas_jabatan = 14 THEN 1 END) AS kelas_14'),
            DB::raw('COUNT(CASE WHEN tb_master_jabatan.kelas_jabatan = 13 THEN 1 END) AS kelas_13'),
            DB::raw('COUNT(CASE WHEN tb_master_jabatan.kelas_jabatan = 12 THEN 1 END) AS kelas_12'),
            DB::raw('COUNT(CASE WHEN tb_master_jabatan.kelas_jabatan = 11 THEN 1 END) AS kelas_11'),
            DB::raw('COUNT(CASE WHEN tb_master_jabatan.kelas_jabatan = 10 THEN 1 END) AS kelas_10'),
            DB::raw('COUNT(CASE WHEN tb_master_jabatan.kelas_jabatan = 9 THEN 1 END) AS kelas_9'),
            DB::raw('COUNT(CASE WHEN tb_master_jabatan.kelas_jabatan = 8 THEN 1 END) AS kelas_8'),
            DB::raw('COUNT(CASE WHEN tb_master_jabatan.kelas_jabatan = 7 THEN 1 END) AS kelas_7'),
            DB::raw('COUNT(CASE WHEN tb_master_jabatan.kelas_jabatan = 6 THEN 1 END) AS kelas_6'),
            DB::raw('COUNT(CASE WHEN tb_master_jabatan.kelas_jabatan = 5 THEN 1 END) AS kelas_5'),
            DB::raw('COUNT(CASE WHEN tb_master_jabatan.kelas_jabatan = 4 THEN 1 END) AS kelas_4'),
            DB::raw('COUNT(CASE WHEN tb_master_jabatan.kelas_jabatan = 3 THEN 1 END) AS kelas_3'),
            DB::raw('COUNT(CASE WHEN tb_master_jabatan.kelas_jabatan = 2 THEN 1 END) AS kelas_2'),
            DB::raw('COUNT(CASE WHEN tb_master_jabatan.kelas_jabatan = 1 THEN 1 END) AS kelas_1')
        )
        ->first(); // Menggunakan first() karena kita hanya mengharapkan satu baris hasil

         return $result;       
    }

    public function cetakKelasJabatan(){

        $data = DB::table('tb_satuan_kerja')->select('id as id_satuan_kerja','inisial_satuan_kerja')->get();

        $data = $data->map(function ($item) {
            $nilai = $this->carijumlahkelasjabatan($item->id_satuan_kerja);
            $item->kelas_15 = $nilai->kelas_15;
            $item->kelas_14 = $nilai->kelas_14;
            $item->kelas_13 = $nilai->kelas_13;
            $item->kelas_12 = $nilai->kelas_12;
            $item->kelas_11 = $nilai->kelas_11;
            $item->kelas_10 = $nilai->kelas_10;
            $item->kelas_9 = $nilai->kelas_9;
            $item->kelas_8 = $nilai->kelas_8;
            $item->kelas_7 = $nilai->kelas_7;
            $item->kelas_6 = $nilai->kelas_6;
            $item->kelas_5 = $nilai->kelas_5;
            $item->kelas_4 = $nilai->kelas_4;
            $item->kelas_3 = $nilai->kelas_3;
            $item->kelas_2 = $nilai->kelas_2;
            $item->kelas_1 = $nilai->kelas_1;
            $item->jml_kelas = $nilai->kelas_1 + $nilai->kelas_2 + $nilai->kelas_3 + $nilai->kelas_4 + $nilai->kelas_5 + $nilai->kelas_6 + $nilai->kelas_7 + $nilai->kelas_8 + $nilai->kelas_9 + $nilai->kelas_10 + $nilai->kelas_11 + $nilai->kelas_12 + $nilai->kelas_13 + $nilai->kelas_14 + $nilai->kelas_15;
            return $item;
        });

        $spreadsheet = new Spreadsheet();

        $spreadsheet->getProperties()->setCreator('BKPSDM BULUKUMBA')
            ->setLastModifiedBy('BKPSDM BULUKUMBA')
            ->setTitle('Laporan Rekap Kelas Jabatan')
            ->setSubject('Laporan Rekap Kelas Jabatan')
            ->setDescription('Laporan Rekap Kelas Jabatan')
            ->setKeywords('pdf php')
            ->setCategory('Laporan Rekap Kelas Jabatan');
        $sheet = $spreadsheet->getActiveSheet();
        //  $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

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

        // Load a logo image
        $spreadsheet->getActiveSheet()->mergeCells('A1:R1');
        $logoPath = 'admin/assets/media/logos/logo_sm.png';
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Paid');
        $drawing->setDescription('Paid');
        $drawing->setPath($logoPath);
        $drawing->setCoordinates('A1');
        $drawing->setHeight(70);
        $drawing->setWorksheet($spreadsheet->getActiveSheet());

        $sheet->setCellValue('A2', 'REKAP JABATAN PER KELAS')->mergeCells('A2:R2');
        $sheet->setCellValue('A3', 'PEGAWAI NEGERI SIPIL DI LINGKUP PEMERINTAH KABUPATEN BULUKUMBA')->mergeCells('A3:R3');
        $sheet->setCellValue('A4', ' ')->mergeCells('A4:F4');

         $sheet->setCellValue('A5', 'NO')->mergeCells('A5:A6');
        $sheet->setCellValue('B5', 'NAMA OPD')->mergeCells('B5:B6')->getColumnDimension('B')->setWidth(30);
        $sheet->setCellValue('C5', 'KELAS JABATAN')->mergeCells('C5:Q5');
        $sheet->setCellValue('R5', 'JUMLAH');


        $sheet->setCellValue('C6', '1');
        $sheet->setCellValue('D6', '2');
        $sheet->setCellValue('E6', '3');
        $sheet->setCellValue('F6', '4');
        $sheet->setCellValue('G6', '5');
        $sheet->setCellValue('H6', '6');
        $sheet->setCellValue('I6', '7');
        $sheet->setCellValue('J6', '8');
        $sheet->setCellValue('K6', '9');
        $sheet->setCellValue('L6', '10');
        $sheet->setCellValue('M6', '11');
        $sheet->setCellValue('N6', '12');
        $sheet->setCellValue('O6', '13');
        $sheet->setCellValue('P6', '14');
        $sheet->setCellValue('Q6', '15');
        $sheet->setCellValue('R6', '16');

        $sheet->getStyle('A5:R5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('E1F5FE');

        $spreadsheet->getActiveSheet()->getRowDimension('5')->setRowHeight(25);
        $sheet->getStyle('A:D')->getAlignment()->setWrapText(true);
        $sheet->getStyle('A1:D5')->getFont()->setBold(true);

        $cell = $cell_head = 7;
        $no = 1;
       
        $count_kelas_1 = 0;
        $count_kelas_2 = 0;
        $count_kelas_3 = 0;
        $count_kelas_4 = 0;
        $count_kelas_5 = 0;
        $count_kelas_6 = 0;
        $count_kelas_7 = 0;
        $count_kelas_8 = 0;
        $count_kelas_9 = 0;
        $count_kelas_10 = 0;
        $count_kelas_11 = 0;
        $count_kelas_12 = 0;
        $count_kelas_13 = 0;
        $count_kelas_14 = 0;
        $count_kelas_15 = 0;
        $count_jml = 0;


        foreach ($data as $key => $value) {
            $sheet->setCellValue('A' . $cell, $no++);
            $sheet->setCellValue('B' . $cell, $value->inisial_satuan_kerja);
            $sheet->setCellValue('C' . $cell, $value->kelas_1);
            $sheet->setCellValue('D' . $cell, $value->kelas_2);
            $sheet->setCellValue('E' . $cell, $value->kelas_3);
            $sheet->setCellValue('F' . $cell, $value->kelas_4);
            $sheet->setCellValue('G' . $cell, $value->kelas_5);
            $sheet->setCellValue('H' . $cell, $value->kelas_6);
            $sheet->setCellValue('I' . $cell, $value->kelas_7);
            $sheet->setCellValue('J' . $cell, $value->kelas_8);
            $sheet->setCellValue('K' . $cell, $value->kelas_9);
            $sheet->setCellValue('L' . $cell, $value->kelas_10);
            $sheet->setCellValue('M' . $cell, $value->kelas_11);
            $sheet->setCellValue('N' . $cell, $value->kelas_12);
            $sheet->setCellValue('O' . $cell, $value->kelas_13);
            $sheet->setCellValue('P' . $cell, $value->kelas_14);
            $sheet->setCellValue('Q' . $cell, $value->kelas_15);
            $sheet->setCellValue('R' . $cell, $value->jml_kelas);

            $count_kelas_1 += $value->kelas_1;
            $count_kelas_2 += $value->kelas_2;
            $count_kelas_3 += $value->kelas_3;
            $count_kelas_4 += $value->kelas_4;
            $count_kelas_5 += $value->kelas_5;
            $count_kelas_6 += $value->kelas_6;
            $count_kelas_7 += $value->kelas_7;
            $count_kelas_8 += $value->kelas_8;
            $count_kelas_9 += $value->kelas_9;
            $count_kelas_10 += $value->kelas_10;
            $count_kelas_11 += $value->kelas_11;
            $count_kelas_12 += $value->kelas_12;
            $count_kelas_13 += $value->kelas_13;
            $count_kelas_14 += $value->kelas_14;
            $count_kelas_15 += $value->kelas_15;
            $count_jml += $value->jml_kelas;
            
            
            $sheet->getStyle('C'.$cell.':F'.$cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('FFF3CC');
            $sheet->getStyle('G'.$cell.':J'.$cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('C8C8C8');
            $sheet->getStyle('K'.$cell.':Q'.$cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('E2EEDA');
            $cell++;
        }

        

        $sheet->setCellValue('A' . $cell, 'Jumlah')->mergeCells('A'.$cell.':B'.$cell);
        $sheet->setCellValue('C' . $cell, $count_kelas_1);
        $sheet->setCellValue('D' . $cell, $count_kelas_2);
        $sheet->setCellValue('E' . $cell, $count_kelas_3);
        $sheet->setCellValue('F' . $cell, $count_kelas_4);
        $sheet->setCellValue('G' . $cell, $count_kelas_5);
        $sheet->setCellValue('H' . $cell, $count_kelas_6);
        $sheet->setCellValue('I' . $cell, $count_kelas_7);
        $sheet->setCellValue('J' . $cell, $count_kelas_8);
        $sheet->setCellValue('K' . $cell, $count_kelas_9);
        $sheet->setCellValue('L' . $cell, $count_kelas_10);
        $sheet->setCellValue('M' . $cell, $count_kelas_11);
        $sheet->setCellValue('N' . $cell, $count_kelas_12);
        $sheet->setCellValue('O' . $cell, $count_kelas_13);
        $sheet->setCellValue('P' . $cell, $count_kelas_14);
        $sheet->setCellValue('Q' . $cell, $count_kelas_15);
        $sheet->setCellValue('R' . $cell, $count_jml);

        $border_row = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '0000000'],
                ],
            ],
        ];
        $sheet->getStyle('A5:R' . $cell)->applyFromArray($border_row);
        $sheet->getStyle('A1:R' . $cell)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1:R' . $cell)->getAlignment()->setVertical('center');
        // $sheet->getStyle('B6:B' . $cell)->getAlignment()->setHorizontal('rigth');
        // $sheet->getStyle('D6:E' . $cell)->getAlignment()->setHorizontal('rigth');


        $cell++;

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

        $writer->save('php://output');
    }

    public function detail($params)
    {
        $module =  [
            [
                'label' => 'Jabatan',
                'url' => '#'
            ],
            [
                'label' => 'List Jabatan',
                'url' => '#'
            ],
            [
                'label' => 'Detail Jabatan',
                'url' => '#'
            ],
        ];

        $data = DB::table('tb_jabatan as jabatan1')
            ->leftJoin('tb_master_jabatan as masterjabatan1', 'jabatan1.id_master_jabatan', '=', 'masterjabatan1.id')
            ->leftJoin('tb_jabatan as jabatan2', 'jabatan1.id_parent', '=', 'jabatan2.id')
            ->leftJoin('tb_master_jabatan as masterjabatan2', 'jabatan2.id_master_jabatan', '=', 'masterjabatan2.id')
            ->leftJoin('tb_pegawai', 'jabatan1.id_pegawai', '=', 'tb_pegawai.id')
            ->select('tb_pegawai.id', 'tb_pegawai.uuid', 'tb_pegawai.nip', 'tb_pegawai.nama', 'tb_pegawai.tempat_lahir', 'tb_pegawai.tanggal_lahir', 'tb_pegawai.jenis_kelamin', 'tb_pegawai.agama', 'tb_pegawai.status_perkawinan', 'tb_pegawai.tmt_pegawai', 'tb_pegawai.golongan', 'tb_pegawai.tmt_golongan', 'tb_pegawai.tmt_jabatan', 'tb_pegawai.pendidikan', 'tb_pegawai.pendidikan_lulus', 'tb_pegawai.pendidikan_struktural', 'tb_pegawai.pendidikan_struktural_lulus', 'tb_pegawai.id_satuan_kerja', 'masterjabatan1.nama_jabatan', 'masterjabatan2.nama_jabatan as atasan_langsung')
            ->orderBy('jabatan1.id_parent', 'DESC')
            ->orderBy('masterjabatan1.level_jabatan', 'DESC')
            ->where('jabatan1.uuid', $params)
            ->first();

        return view('jabatan.detailjabatan', compact('data', 'module'));
    }

    public function store(JabatanRequest $request)
    {
        $data = array();
        try {
            $promise = true;
            $pegawai_val = $request->id_pegawai;
            if ($pegawai_val !== '-') {
                $check_jabatan = $this->checkJabatanDefinitif($request->id_pegawai);
                if ($request->status !== 'plt') {
                    if ($check_jabatan) {
                        if ($check_jabatan->status === 'definitif') {
                            $promise = false;
                            return $this->sendError($check_jabatan->nama . ' mengisi jabatan ' . $check_jabatan->nama_jabatan . ' dan status ' . $check_jabatan->status, 'Maaf tidak bisa mengatur jabatan!', 200);
                        } else {
                            $promise = true;
                        }
                    }
                }
            }

            if ($pegawai_val === '-') {
                $pegawai_val = null;
            }

            $check_level = DB::table('tb_master_jabatan')->select('level_jabatan')->where('id', $request->id_master_jabatan)->first();

            $jabatanDuplicate = $this->checkJabatanDuplicate($request->id_master_jabatan);
            if ($check_level->level_jabatan !== 8 && $check_level->level_jabatan !== 7) {
                if ($jabatanDuplicate) {
                    if ($jabatanDuplicate->id_pegawai !== null) {
                        return $this->sendError('Jabatan tersebut sudah terisi', 'Maaf tidak bisa mengatur jabatan!', 200);
                    }
                }
            }

            if ($promise) {
                if ($jabatanDuplicate && $check_level->level_jabatan !== 8 && $check_level->level_jabatan !== 7) {
                    $data = Jabatan::where('uuid', $jabatanDuplicate->uuid)->first();
                    $data->id_pegawai = $pegawai_val;
                    $data->id_master_jabatan = $request->id_master_jabatan;
                    $data->id_satuan_kerja = $request->id_satuan_kerja;
                    $data->id_unit_kerja = $request->id_unit_kerja;
                    $data->status = $request->status;
                    $data->pagu_tpp = intval(str_replace(['Rp ', '.'], '', $request->pagu_tpp));  
                    $data->pembayaran = $request->pembayaran;
                    $data->target_waktu = $request->target_waktu;
                    $data->save();
                } else {
                    $data = new Jabatan();
                    $data->id_pegawai = $pegawai_val;
                    $data->id_master_jabatan = $request->id_master_jabatan;
                    $data->id_satuan_kerja = $request->id_satuan_kerja;
                    $data->id_unit_kerja = $request->id_unit_kerja;
                    $data->id_lokasi_apel = $request->id_lokasi_apel;
                    $data->id_lokasi_kerja = $request->id_lokasi_kerja;
                    $data->id_parent = $request->id_parent;
                    $data->status = $request->status;
                    $data->pagu_tpp = intval(str_replace(['Rp ', '.'], '', $request->pagu_tpp));  
                    $data->pembayaran = $request->pembayaran;
                    $data->target_waktu = $request->target_waktu;
                    $data->save();
                }
            }
        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Jabatan Added success');
    }

    public function update(JabatanRequest $request, $params)
    {
        $data = array();
        try {
            $pegawai_val = $request->id_pegawai;
            $check_jabatan = $this->checkJabatanDefinitif($request->id_pegawai);
            $data = Jabatan::where('uuid', $params)->first();

            if ($check_jabatan !== null) {
                if ($check_jabatan->status === 'definitif' && $request->status === 'definitif') {
                    if ($data->id_pegawai !== intval($request->id_pegawai)) {
                        return $this->sendError($check_jabatan->nama . ' mengisi jabatan ' . $check_jabatan->nama_jabatan . ' dan status ' . $check_jabatan->status . ', anda tidak bisa memilih jabatan definitif!', 'Gagal', 200);
                    }
   
                }

                if ($check_jabatan->status === 'definitif' && $request->status === 'plt') {

                    $checkMasterInJabatan = DB::table('tb_jabatan')->join('tb_pegawai', 'tb_jabatan.id_pegawai', 'tb_pegawai.id')->join('tb_master_jabatan', 'tb_jabatan.id_master_jabatan', '=', 'tb_master_jabatan.id')->select('tb_jabatan.id', 'tb_pegawai.nama', 'tb_master_jabatan.nama_jabatan', 'tb_jabatan.status')->where('tb_jabatan.id_master_jabatan', $request->id_master_jabatan)->first();

                    if ($checkMasterInJabatan) {
                        return $this->sendError('Jabatan ' . $checkMasterInJabatan->nama_jabatan . ' sudah disi oleh ' . $checkMasterInJabatan->nama . ' dengan status ' . $checkMasterInJabatan->status, 'Gagal', 200);
                    }
                }
            }

            if ($pegawai_val === '-') {
                $pegawai_val = null;
            }

            if (isset($data->id_satuan_kerja) || isset($data->id_master_jabatan)) {

                $data->id_pegawai = $pegawai_val;
                if (isset($request->id_master_jabatan)) {
                    $data->id_master_jabatan = $request->id_master_jabatan;
                }
                if (isset($request->id_unit_kerja)) {
                    $data->id_unit_kerja = $request->id_unit_kerja;
                }
                if (isset($request->id_lokasi_apel)) {
                    $data->id_lokasi_apel = $request->id_lokasi_apel;
                }
                if (isset($request->id_lokasi_kerja)) {
                    $data->id_lokasi_kerja = $request->id_lokasi_kerja;
                }
                if (isset($request->id_parent)) {
                    $data->id_parent = $request->id_parent;
                }
                $data->status = $request->status;
                $data->pembayaran = $request->pembayaran;
                if (isset($request->pagu_tpp)) {
                    $data->pagu_tpp = intval(str_replace(['Rp ', '.'], '', $request->pagu_tpp));
                }
                $data->target_waktu = $request->target_waktu;  
                $data->save();
            } else {
                if ($request->type == 'administrator') {
         
                    $data->id_pegawai = $pegawai_val;
                    if (isset($data->id_master_jabatan)) {
                        $data->id_master_jabatan = $request->id_master_jabatan;
                    }
                    $data->id_satuan_kerja = $request->id_satuan_kerja;
                    $data->id_unit_kerja = $request->id_unit_kerja;
                    $data->id_lokasi_apel = $request->id_lokasi_apel;
                    $data->id_lokasi_kerja = $request->id_lokasi_kerja;
                    $data->id_parent = $request->id_parent;
                    $data->status = $request->status;
                    if (isset($request->pagu_tpp)) {
                        $data->pagu_tpp = intval(str_replace(['Rp ', '.'], '', $request->pagu_tpp));
                    }
                    $data->pembayaran = $request->pembayaran;
                    $data->target_waktu = $request->target_waktu;
                    $data->save();
                } else {
                   
                    if (isset($data->id_master_jabatan)) {
                        $data->id_master_jabatan = $request->id_master_jabatan;
                    }
                    $data->id_pegawai = $pegawai_val;
                    $data->id_parent = $request->id_parent;
                    $data->status = $request->status;
                    if (isset($request->pagu_tpp)) {
                        $data->pagu_tpp = intval(str_replace(['Rp ', '.'], '', $request->pagu_tpp));
                    }
                    $data->pembayaran = $request->pembayaran;
                    $data->target_waktu = $request->target_waktu;
                    $data->save();
                }
            }
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Jabatan Update success');
    }

    public function show($params)
    {
        $data = array();
        try {
            $data = Jabatan::where('uuid', $params)->first();
            $data->status_jabatan = $data->status;
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Jabatan Update success');
    }

    public function delete(Request $request, $params)
    {
        $data = array();
        try {
            // $data =  DB::table('tb_jabatan')->where('uuid', $params)->delete();
            $data = Jabatan::where('uuid', $params)->first();
            $data->id_pegawai = null;
            $data->save();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Jabatan Delete success');
    }
}
