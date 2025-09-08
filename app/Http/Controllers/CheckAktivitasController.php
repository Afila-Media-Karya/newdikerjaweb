<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CheckAktivitasController extends BaseController
{
    public function breadcumb(){
        return [
            [
                'label' => 'Check Aktivitas',
                'url' => '#'
            ]
        ];
    }

    public function index(){
        $module = $this->breadcumb();
        return view('admin_kabupaten.check_aktivitas.index',compact('module'));
    }

    public function datatable(){
        $data = array();
       
            // Ambil nilai bulan dari request, defaultnya ke bulan saat ini jika tidak ada
        $bulan = request('bulan'); 
        
        // Ambil nilai tahun dari request, defaultnya ke tahun saat ini
        $tahun = session('tahun_penganggaran');

        // Buat tanggal awal dan tanggal akhir berdasarkan bulan dan tahun
        $tanggalMulai = date('Y-m-01', strtotime($tahun . '-' . $bulan));
        $tanggalSelesai = date('Y-m-t', strtotime($tahun . '-' . $bulan));

        $data = DB::table('tb_aktivitas')
            ->select(
                'tb_pegawai.nama',
                'tb_pegawai.nip',
                'tb_satuan_kerja.nama_satuan_kerja',
                DB::raw('DATE(tanggal) AS Tanggal'),
                DB::raw('DATE(tb_aktivitas.created_at) AS "Tanggal Input"'),
                DB::raw('DATEDIFF(tb_aktivitas.created_at, tanggal) AS "selisih_hari"'),
                'tb_aktivitas.aktivitas',
                'tb_aktivitas.keterangan',
                'waktu'
            )
            ->join('tb_pegawai', 'tb_aktivitas.id_pegawai', '=', 'tb_pegawai.id')
            ->join('tb_satuan_kerja', 'tb_pegawai.id_satuan_kerja', '=', 'tb_satuan_kerja.id')
            ->whereRaw('DATEDIFF(tb_aktivitas.created_at, tanggal) > 5')
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai])
            ->orderBy('tb_pegawai.id', 'asc')
            ->orderBy('tanggal', 'asc')
            ->get();

        return $this->sendResponse($data, 'Check Aktivitas Fetched Success');
    }

    public function backup(){
        $bulan = request('bulan');

        $tahun = session('tahun_penganggaran');

        // Buat tanggal awal dan tanggal akhir berdasarkan bulan dan tahun
        $tanggalMulai = date('Y-m-01', strtotime($tahun . '-' . $bulan));
        $tanggalSelesai = date('Y-m-t', strtotime($tahun . '-' . $bulan));

        $data = DB::table('tb_aktivitas')
            ->select(
                'tb_pegawai.nama',
                'tb_pegawai.nip',
                'tb_satuan_kerja.nama_satuan_kerja',
                DB::raw('DATE(tanggal) AS tanggal'),
                DB::raw('DATE(tb_aktivitas.created_at) AS "tanggal_input"'),
                DB::raw('DATEDIFF(tb_aktivitas.created_at, tanggal) AS "selisih_hari"'),
                'tb_aktivitas.aktivitas',
                'tb_aktivitas.keterangan',
                'waktu'
            )
            ->join('tb_pegawai', 'tb_aktivitas.id_pegawai', '=', 'tb_pegawai.id')
            ->join('tb_satuan_kerja', 'tb_pegawai.id_satuan_kerja', '=', 'tb_satuan_kerja.id')
            ->whereRaw('DATEDIFF(tb_aktivitas.created_at, tanggal) > 5')
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai])
            ->orderBy('tb_pegawai.id', 'asc')
            ->orderBy('tanggal', 'asc')
            ->get();

        
        $spreadsheet = new Spreadsheet();

        $spreadsheet->getProperties()->setCreator('BKPSDM BULUKUMBA')
            ->setLastModifiedBy('BKPSDM BULUKUMBA')
            ->setTitle('Laporan Aktivitas lewat 5 Hari')
            ->setSubject('Laporan Aktivitas lewat 5 Hari')
            ->setDescription('Laporan Aktivitas lewat 5 Hari')
            ->setKeywords('pdf php')
            ->setCategory('Laporan Aktivitas lewat 5 Hari');
        $sheet = $spreadsheet->getActiveSheet();
         $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        // $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_FOLIO);

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

        // $perangka_daerah = '';
        // if ($satuan_kerja == $nama_unit_kerja || $nama_unit_kerja == 'Semua') {
        //     $perangka_daerah = $satuan_kerja;
        // } else {
        //     $perangka_daerah = $satuan_kerja . ' - ' . $nama_unit_kerja;
        // }

        $sheet->setCellValue('A1', 'REKAPITULASI AKTIVITAS YANG LEWAT 5 HARI')->mergeCells('A1:H1');
        $row_bulan = 2;
        $sheet->setCellValue('A' . $row_bulan, 'BULAN ' . strtoupper(konvertBulan($bulan)))->mergeCells('A' . $row_bulan . ':H' . $row_bulan);

        $sheet->getStyle('A6:I6')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('E1F5FE');

        $sheet->setCellValue('A6', 'No');
        $sheet->setCellValue('B6', 'Nama / NIP')->getColumnDimension('B')->setWidth(20);
        $sheet->setCellValue('C6', 'Satuan Kerja')->getColumnDimension('C')->setWidth(20);
        $sheet->setCellValue('D6', 'Tanggal')->getColumnDimension('D')->setWidth(20);
        $sheet->setCellValue('E6', 'Tanggal Input')->getColumnDimension('E')->setWidth(20);
        $sheet->setCellValue('F6', 'Selisih Hari')->getColumnDimension('F')->setWidth(20);
        $sheet->setCellValue('G6', 'Akitivitas')->getColumnDimension('G')->setWidth(20);
        $sheet->setCellValue('H6', 'Keterangan')->getColumnDimension('H')->setWidth(20);
        $sheet->setCellValue('I6', 'Waktu')->getColumnDimension('I')->setWidth(20);

        $cell = 7;
        $no = 1;
        $golongan = '';
        $keterangan = '';
        $target_nilai = 0;
        $pegawai_ttd = array();
        foreach ($data as $key => $value) {

            $sheet->setCellValue('A' . $cell, $no++);
            $sheet->setCellValue('B' . $cell, $value->nama . PHP_EOL . $value->nip);
            $sheet->setCellValue('C' . $cell, $value->nama_satuan_kerja);
            $sheet->setCellValue('D' . $cell, $value->tanggal);
            $sheet->setCellValue('E' . $cell, $value->tanggal_input);
            $sheet->setCellValue('F' . $cell, $value->selisih_hari);
            $sheet->setCellValue('G' . $cell, $value->aktivitas);
            $sheet->setCellValue('H' . $cell, $value->keterangan);
            $sheet->setCellValue('I' . $cell, $value->waktu);
            

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
        $sheet->getStyle('A6:I' . $cell)->applyFromArray($border_row);

        // $sheet->getStyle('E7:E' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('FFECB3');
        // $sheet->getStyle('F7:F' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('FFF9D4');
        // $sheet->getStyle('G7:G' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('00E676');


        $sheet->getStyle('A:H')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A:H')->getAlignment()->setVertical('center');
        $sheet->getStyle('B7:B' . $cell)->getAlignment()->setHorizontal('rigth');
        $sheet->getStyle('C7:D' . $cell)->getAlignment()->setHorizontal('rigth');
        //$sheet->getStyle('A3:H')->getAlignment()->setHorizontal('rigth');


        $cell++;


        // if ($type == 'excel') {
        //     // Untuk download 
            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $bulan_tmt = strtoupper(konvertBulan($bulan));
            $filename = "LAPORAN Aktivitas lewat 5 hari BULAN {$bulan_tmt}.xlsx";
            header("Content-Disposition: attachment;filename=\"$filename\"");
        // } else {
            // $spreadsheet->getActiveSheet()->getHeaderFooter()
            //     ->setOddHeader('&C&H' . url()->current());
            // $spreadsheet->getActiveSheet()->getHeaderFooter()
            //     ->setOddFooter('&L&B &RPage &P of &N');
            // $class = \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf::class;
            // \PhpOffice\PhpSpreadsheet\IOFactory::registerWriter('Pdf', $class);
            // header('Content-Type: application/pdf');
            // header('Cache-Control: max-age=0');
            // $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Pdf');
        // }

        $writer->save('php://output');
    }

    public function proses(Request $request){
        $data = $request->all();
        // Proses data sesuai kebutuhan
        // Misalnya, simpan ke database atau lakukan validasi

        // Contoh respons sukses
        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil diproses',
            'data' => $data
        ]);
    }

    public function delete(){

        $bulan = request('bulan');

        $tahun = session('tahun_penganggaran');

        // Buat tanggal awal dan tanggal akhir berdasarkan bulan dan tahun
        $tanggalMulai = date('Y-m-01', strtotime($tahun . '-' . $bulan));
        $tanggalSelesai = date('Y-m-t', strtotime($tahun . '-' . $bulan));

        DB::table('tb_aktivitas')
        ->join('tb_pegawai', 'tb_aktivitas.id_pegawai', '=', 'tb_pegawai.id')
        ->whereRaw('DATEDIFF(tb_aktivitas.created_at, tb_aktivitas.tanggal) > 5')
        ->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai])
        ->delete();

    return $this->sendResponse([], 'Hari Libur Delete success');
    }
}
