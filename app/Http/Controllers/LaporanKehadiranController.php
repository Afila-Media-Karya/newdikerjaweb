<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use DB;
use App\Traits\General;
use App\Traits\Presensi;
use Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Carbon\Carbon;

class LaporanKehadiranController extends Controller
{
    use General;
    use Presensi;

    public function breadcumb()
    {
        return [
            [
                'label' => 'Laporan',
                'url' => '#'
            ],
            [
                'label' => 'Kehadiran',
                'url' => '#'
            ],
        ];
    }

    public function index()
    {
        $module = $this->breadcumb();
        return view('laporan.kehadiran.index', compact('module'));
    }

    public function index_opd()
    {
        $module = $this->breadcumb();
        $satuan_kerja = $this->infoSatuanKerja(Auth::user()->id_pegawai);
        // dd($satuan_kerja);
        $pegawai = array();
        $role = hasRole();
        $satuan_kerja_user = '';
        $nama_satuan_kerja = '';
        $query = DB::table('tb_pegawai')
            ->select('tb_pegawai.id', 'tb_pegawai.nama as text')
            ->leftJoin('tb_jabatan', 'tb_jabatan.id_pegawai', '=', 'tb_pegawai.id')
            ->where('tb_pegawai.status', '1');

        if ($role['role'] == '1') {
            $query->where('tb_pegawai.id_satuan_kerja', $satuan_kerja->id_satuan_kerja);
            $satuan_kerja_user = $satuan_kerja->id_satuan_kerja;
            $nama_satuan_kerja = $satuan_kerja->nama_satuan_kerja;
        } else {
            $query->where('tb_jabatan.id_unit_kerja', $satuan_kerja->id_unit_kerja);
        }

        $pegawai = $query->get();

        if ($role['role'] == '1') {
            return view('laporan.kehadiran.index_opd', compact('module', 'pegawai','satuan_kerja_user','nama_satuan_kerja'));
        } else {
            return view('laporan.kehadiran.index_unit', compact('module', 'pegawai'));
        }

        
    }

    public function index_kabupaten()
    {
        $module = $this->breadcumb();
        $satuan_kerja = $this->option_satuan_kerja();
        return view('laporan.kehadiran.index_kabupaten', compact('module', 'satuan_kerja'));
    }

    public function export_pegawai()
    {
        $bulan = request('bulan');
        $tanggal_awal = date("Y-m-d", strtotime(date('Y-'). $bulan . '-01'));
        $tanggal_akhir = date("Y-m-d", strtotime(date('Y-'). $bulan . '-' . cal_days_in_month(CAL_GREGORIAN, $bulan, date('Y'))));

        $jabatan_req = request("status");
        $pegawai = request('pegawai') ? request('pegawai') : Auth::user()->id_pegawai;
        $pegawai_info = $this->findPegawai($pegawai, $jabatan_req);
        $data = $this->data_kehadiran_pegawai($pegawai, $tanggal_awal, $tanggal_akhir,$pegawai_info->waktu_masuk,$pegawai_info->waktu_keluar,$pegawai_info->tipe_pegawai);
        $type = request('type');
        
        if ($pegawai_info->tipe_pegawai == 'pegawai_administratif') {
            return $this->export_rekap_pegawai($data, $type, $pegawai_info, $tanggal_awal, $tanggal_akhir);
        }else {
            return $this->export_rekap_pegawai_nakes($data, $type, $pegawai_info, $tanggal_awal, $tanggal_akhir);
        }
    }

    public function export_pegawai_bulan(){
        $bulan = request('bulan');
        $tanggal_awal = date("Y-m-d", strtotime(session('tahun_penganggaran').'-' . $bulan . '-01'));
        $tanggal_akhir = date("Y-m-d", strtotime(session('tahun_penganggaran').'-' . $bulan . '-' . cal_days_in_month(CAL_GREGORIAN, $bulan, date('Y'))));

        $jabatan_req = request("status");
        $pegawai = request('pegawai') ? request('pegawai') : Auth::user()->id_pegawai;
        $pegawai_info = $this->findPegawai($pegawai, $jabatan_req);
        $data = $this->data_kehadiran_pegawai($pegawai, $tanggal_awal, $tanggal_akhir,$pegawai_info->waktu_masuk,$pegawai_info->waktu_keluar,$pegawai_info->tipe_pegawai);
        $type = request('type');
        if ($pegawai_info->tipe_pegawai == 'pegawai_administratif') {
            return $this->export_rekap_pegawai($data, $type, $pegawai_info, $tanggal_awal, $tanggal_akhir);
        }else {
            return $this->export_rekap_pegawai_nakes($data, $type, $pegawai_info, $tanggal_awal, $tanggal_akhir);
        }

        
    }

    public function export_rekap_pegawai($data, $type, $pegawai_info, $tanggal_awal, $tanggal_akhir)
    {
        $spreadsheet = new Spreadsheet();

        $spreadsheet->getProperties()->setCreator('BKPSDM ENREKANG')
            ->setLastModifiedBy('BKPSDM ENREKANG')
            ->setTitle('Laporan Rekapitulasi Absen Pegawai')
            ->setSubject('Laporan Rekapitulasi Absen Pegawai')
            ->setDescription('Laporan Rekapitulasi Absen Pegawai')
            ->setKeywords('pdf php')
            ->setCategory('LAPORAN ABSEN');
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_FOLIO);
        $sheet->getRowDimension(1)->setRowHeight(20);
        $sheet->getRowDimension(2)->setRowHeight(20);
        $sheet->getRowDimension(3)->setRowHeight(20);


        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
        $spreadsheet->getDefaultStyle()->getFont()->setSize(12);
        $spreadsheet->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);
        $spreadsheet->getActiveSheet()->getPageSetup()->setVerticalCentered(false);

        // //Margin PDF
        $spreadsheet->getActiveSheet()->getPageMargins()->setTop(0.3);
        $spreadsheet->getActiveSheet()->getPageMargins()->setRight(0.3);
        $spreadsheet->getActiveSheet()->getPageMargins()->setLeft(0.5);
        $spreadsheet->getActiveSheet()->getPageMargins()->setBottom(0.3);

        $sheet->setCellValue('A1', 'Laporan Rekapitulasi Absen Pegawai')->mergeCells('A1:G1');
        $sheet->setCellValue('A2', '' . $pegawai_info->nama_unit_kerja)->mergeCells('A2:G2');
        // $sheet->setCellValue('A3', $pegawai_info->nama . ' / ' . $pegawai_info->nip)->mergeCells('A3:G3');
        $sheet->getStyle('A1:G4')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1:G4')->getFont()->setSize(14);

        $sheet->setCellValue('A7', ' ')->mergeCells('A10:G10');

        $sheet->setCellValue('A8', 'Nama')->mergeCells('A8' . ':B8');
        $sheet->setCellValue('C8', ': ' . $pegawai_info->nama)->mergeCells('C8' . ':G8');
        $sheet->setCellValue('A9', 'NIP')->mergeCells('A8' . ':B8');
        $sheet->setCellValue('C9', ': ' . $pegawai_info->nip)->mergeCells('C9' . ':G9');

        // $sheet->setCellValue('A10', ' ')->mergeCells('A10:G10');

        $sheet->setCellValue('A11', 'No')->mergeCells('A11:A12');
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->setCellValue('B11', 'Tanggal')->mergeCells('B11:B12');
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->setCellValue('C11', 'Status Absen')->mergeCells('C11:C12');
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->setCellValue('D11', 'Datang')->mergeCells('D11:E11');
        $sheet->setCellValue('D12', 'Waktu');
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->setCellValue('E12', 'Keterangan');
        $sheet->getColumnDimension('E')->setWidth(25);
        $sheet->setCellValue('F11', 'Pulang')->mergeCells('F11:G11');
        $sheet->setCellValue('F12', 'Waktu');
        $sheet->getColumnDimension('F')->setWidth(25);
        $sheet->setCellValue('G12', 'Keterangan');
        $sheet->getColumnDimension('G')->setWidth(25);

        $sheet->setCellValue('B13', 'Nama')->mergeCells('B11:B12');
        $sheet->setCellValue('C11', 'Status Absen')->mergeCells('C11:C12');

        $sheet->getStyle('A:G')->getAlignment()->setWrapText(true);
        $sheet->getStyle('A1:G12')->getFont()->setBold(true);
        $sheet->getRowDimension(11)->setRowHeight(30);
        $sheet->getRowDimension(12)->setRowHeight(30);

        $sheet->getStyle('A11:G12')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('E1F5FE');


        $cell = 13;

        foreach ($data['data'] as $index => $value) {
            $sheet->getRowDimension($cell)->setRowHeight(30);
            $sheet->setCellValue('A' . $cell, $index + 1);
            $sheet->setCellValue('B' . $cell, date('d/m/y', strtotime($value['tanggal_absen'])));
            $sheet->setCellValue('C' . $cell, ucfirst($value['status']));
            $sheet->setCellValue('D' . $cell, $value['waktu_masuk']);
            $sheet->setCellValue('E' . $cell, $value['keterangan_masuk']);
            $sheet->setCellValue('F' . $cell, $value['waktu_keluar']);
            $sheet->setCellValue('G' . $cell, $value['keterangan_pulang']);
            $cell++;
        }


        $sheet->getStyle('A5:G9')->getFont()->setSize(12);

        $border = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '0000000'],
                ],
            ],
        ];


        $sheet->getStyle('A11:G' . $cell)->applyFromArray($border);
        $sheet->getStyle('A11:G' . $cell)->getAlignment()->setVertical('center')->setHorizontal('center');

        $cell++;
        $sheet->setCellValue('A' . $cell, ' ')->mergeCells('A' . $cell . ':G' . $cell);
        $cell++;

        $cell_str = $cell;
        $sheet->setCellValue('A' . $cell, 'Keterangan')->mergeCells('A' . $cell . ':B' . $cell);
        $sheet->setCellValue('C' . $cell, 'Volume');
        $sheet->setCellValue('D' . $cell, 'Satuan');

        $sheet->getRowDimension($cell)->setRowHeight(25);
        $sheet->getStyle('A' . $cell . ':D' . $cell)->getFont()->setBold(true);
        $sheet->getStyle('A' . $cell . ':D' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('E1F5FE');

        $cell = $cell + 1;
        $sheet->setCellValue('A' . $cell, 'Jumlah hari kerja')->mergeCells('A' . $cell . ':B' . $cell);
        $sheet->setCellValue('C' . $cell, $data['jml_hari_kerja']);
        $sheet->setCellValue('D' . $cell, 'Hari');
        $sheet->getRowDimension($cell)->setRowHeight(20);
        $cell = $cell + 1;
        $sheet->setCellValue('A' . $cell, 'Kehadiran kerja')->mergeCells('A' . $cell . ':B' . $cell);
        $sheet->setCellValue('C' . $cell, $data['kehadiran_kerja']);
        $sheet->setCellValue('D' . $cell, 'Hari');
        $sheet->getRowDimension($cell)->setRowHeight(20);
        $cell = $cell + 1;
        $sheet->setCellValue('A' . $cell, 'Tanpa keterangan')->mergeCells('A' . $cell . ':B' . $cell);
        $sheet->setCellValue('C' . $cell, $data['tanpa_keterangan']);
        $sheet->setCellValue('D' . $cell, 'Hari');
        $sheet->getRowDimension($cell)->setRowHeight(20);
        $cell = $cell + 1;
        $sheet->setCellValue('A' . $cell, 'Potongan tanpa keterangan')->mergeCells('A' . $cell . ':B' . $cell);
        $sheet->setCellValue('C' . $cell, $data['potongan_tanpa_keterangan']);
        $sheet->setCellValue('D' . $cell, '%');
        $sheet->getRowDimension($cell)->setRowHeight(20);
        $cell = $cell + 1;
        $sheet->setCellValue('A' . $cell, 'Potongan masuk kerja')->mergeCells('A' . $cell . ':B' . $cell);
        $sheet->setCellValue('C' . $cell, $data['potongan_masuk_kerja']);
        $sheet->setCellValue('D' . $cell, '%');
        $sheet->getRowDimension($cell)->setRowHeight(20);
        $cell = $cell + 1;
        $sheet->setCellValue('A' . $cell, 'Potongan pulang kerja')->mergeCells('A' . $cell . ':B' . $cell);
        $sheet->setCellValue('C' . $cell, $data['potongan_pulang_kerja']);
        $sheet->setCellValue('D' . $cell, '%');
        $sheet->getRowDimension($cell)->setRowHeight(20);
        $cell = $cell + 1;
        $sheet->setCellValue('A' . $cell, 'Potongan apel')->mergeCells('A' . $cell . ':B' . $cell);
        $sheet->setCellValue('C' . $cell, $data['potongan_apel']);
        $sheet->setCellValue('D' . $cell, '%');
        $sheet->getRowDimension($cell)->setRowHeight(20);
        $cell = $cell + 1;
        $sheet->setCellValue('A' . $cell, 'Total potongan')->mergeCells('A' . $cell . ':B' . $cell);
        $sheet->setCellValue('C' . $cell, $data['jml_potongan_kehadiran_kerja']);
        $sheet->setCellValue('D' . $cell, '%');
        $sheet->getRowDimension($cell)->setRowHeight(25);
        $sheet->getStyle('A' . $cell . ':D' . $cell)->getFont()->setBold(true);
        $sheet->getStyle('A' . $cell . ':D' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('E1F5FE');

        $sheet->getStyle('A' . $cell_str . ':D' . $cell)->applyFromArray($border);
        $sheet->getStyle('A' . $cell_str . ':D' . $cell)->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('A' . $cell_str + 1 . ':A' . $cell)->getAlignment()->setHorizontal('left');


        if ($type == 'excel') {
            // Untuk download 
            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

            $periode = $tanggal_awal . ' s/d ' . $tanggal_akhir;
            $filename = "Laporan Absen {$pegawai_info->nama}_$pegawai_info->nip {$periode}.xlsx";
            header("Content-Disposition: attachment;filename=\"$filename\"");
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

    public function export_rekap_pegawai_nakes($data, $type, $pegawai_info, $tanggal_awal, $tanggal_akhir)
    {
        $spreadsheet = new Spreadsheet();

        $spreadsheet->getProperties()->setCreator('BKPSDM ENREKANG')
            ->setLastModifiedBy('BKPSDM ENREKANG')
            ->setTitle('Laporan Rekapitulasi Absen Pegawai')
            ->setSubject('Laporan Rekapitulasi Absen Pegawai')
            ->setDescription('Laporan Rekapitulasi Absen Pegawai')
            ->setKeywords('pdf php')
            ->setCategory('LAPORAN ABSEN');
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_FOLIO);
        $sheet->getRowDimension(1)->setRowHeight(20);
        $sheet->getRowDimension(2)->setRowHeight(20);
        $sheet->getRowDimension(3)->setRowHeight(20);


        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
        $spreadsheet->getDefaultStyle()->getFont()->setSize(12);
        $spreadsheet->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);
        $spreadsheet->getActiveSheet()->getPageSetup()->setVerticalCentered(false);

        // //Margin PDF
        $spreadsheet->getActiveSheet()->getPageMargins()->setTop(0.3);
        $spreadsheet->getActiveSheet()->getPageMargins()->setRight(0.3);
        $spreadsheet->getActiveSheet()->getPageMargins()->setLeft(0.5);
        $spreadsheet->getActiveSheet()->getPageMargins()->setBottom(0.3);

        $sheet->setCellValue('A1', 'Laporan Rekapitulasi Absen Pegawai')->mergeCells('A1:H1');
        $sheet->setCellValue('A2', '' . $pegawai_info->nama_unit_kerja)->mergeCells('A2:H2');
        // $sheet->setCellValue('A3', $pegawai_info->nama . ' / ' . $pegawai_info->nip)->mergeCells('A3:G3');
        $sheet->getStyle('A1:H4')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1:H4')->getFont()->setSize(14);

        $sheet->setCellValue('A7', ' ')->mergeCells('A10:G10');

        $sheet->setCellValue('A8', 'Nama')->mergeCells('A8' . ':B8');
        $sheet->setCellValue('C8', ': ' . $pegawai_info->nama)->mergeCells('C8' . ':G8');
        $sheet->setCellValue('A9', 'NIP')->mergeCells('A8' . ':B8');
        $sheet->setCellValue('C9', ': ' . $pegawai_info->nip)->mergeCells('C9' . ':G9');

        // $sheet->setCellValue('A10', ' ')->mergeCells('A10:G10');

        $sheet->setCellValue('A11', 'No')->mergeCells('A11:A12');
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->setCellValue('B11', 'Tanggal')->mergeCells('B11:B12');
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->setCellValue('C11', 'Status Absen')->mergeCells('C11:C12');
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->setCellValue('D11', 'Shift')->mergeCells('D11:D12');
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->setCellValue('E11', 'Datang')->mergeCells('E11:F11');
        $sheet->setCellValue('E12', 'Waktu');
        $sheet->getColumnDimension('E')->setWidth(25);
        $sheet->setCellValue('F12', 'Keterangan');
        $sheet->getColumnDimension('F')->setWidth(25);
        $sheet->setCellValue('G11', 'Pulang')->mergeCells('G11:H11');
        $sheet->setCellValue('G12', 'Waktu');
        $sheet->getColumnDimension('G')->setWidth(25);
        $sheet->getColumnDimension('H')->setWidth(25);
        $sheet->setCellValue('H12', 'Keterangan');
        $sheet->getColumnDimension('H')->setWidth(25);

        $sheet->setCellValue('B13', 'Nama')->mergeCells('B11:B12');
        $sheet->setCellValue('C11', 'Status Absen')->mergeCells('C11:C12');

        $sheet->getStyle('A:H')->getAlignment()->setWrapText(true);
        $sheet->getStyle('A1:H12')->getFont()->setBold(true);
        $sheet->getRowDimension(11)->setRowHeight(30);
        $sheet->getRowDimension(12)->setRowHeight(30);

        $sheet->getStyle('A11:H12')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('E1F5FE');


        $cell = 13;

        foreach ($data['data'] as $index => $value) {
            $sheet->getRowDimension($cell)->setRowHeight(30);
            $sheet->setCellValue('A' . $cell, $index + 1);
            $sheet->setCellValue('B' . $cell, date('d/m/y', strtotime($value['tanggal_absen'])));
            $sheet->setCellValue('C' . $cell, ucfirst($value['status']));
            $sheet->setCellValue('D' . $cell, $value['shift']);
            $sheet->setCellValue('E' . $cell, $value['waktu_masuk']);
            $sheet->setCellValue('F' . $cell, $value['keterangan_masuk']);
            $sheet->setCellValue('G' . $cell, $value['waktu_keluar']);
            $sheet->setCellValue('H' . $cell, $value['keterangan_pulang']);
            $cell++;
        }


        $sheet->getStyle('A5:H9')->getFont()->setSize(12);

        $border = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '0000000'],
                ],
            ],
        ];


        $sheet->getStyle('A11:H' . $cell)->applyFromArray($border);
        $sheet->getStyle('A11:H' . $cell)->getAlignment()->setVertical('center')->setHorizontal('center');

        $cell++;
        $sheet->setCellValue('A' . $cell, ' ')->mergeCells('A' . $cell . ':G' . $cell);
        $cell++;

        $cell_str = $cell;
        $sheet->setCellValue('A' . $cell, 'Keterangan')->mergeCells('A' . $cell . ':B' . $cell);
        $sheet->setCellValue('C' . $cell, 'Volume');
        $sheet->setCellValue('D' . $cell, 'Satuan');

        $sheet->getRowDimension($cell)->setRowHeight(25);
        $sheet->getStyle('A' . $cell . ':D' . $cell)->getFont()->setBold(true);
        $sheet->getStyle('A' . $cell . ':D' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('E1F5FE');

        $cell = $cell + 1;
        $sheet->setCellValue('A' . $cell, 'Jumlah hari kerja')->mergeCells('A' . $cell . ':B' . $cell);
        $sheet->setCellValue('C' . $cell, $data['jml_hari_kerja']);
        $sheet->setCellValue('D' . $cell, 'Hari');
        $sheet->getRowDimension($cell)->setRowHeight(20);
        $cell = $cell + 1;
        $sheet->setCellValue('A' . $cell, 'Kehadiran kerja')->mergeCells('A' . $cell . ':B' . $cell);
        $sheet->setCellValue('C' . $cell, $data['kehadiran_kerja']);
        $sheet->setCellValue('D' . $cell, 'Hari');
        $sheet->getRowDimension($cell)->setRowHeight(20);
        $cell = $cell + 1;
        $sheet->setCellValue('A' . $cell, 'Tanpa keterangan')->mergeCells('A' . $cell . ':B' . $cell);
        $sheet->setCellValue('C' . $cell, $data['tanpa_keterangan']);
        $sheet->setCellValue('D' . $cell, 'Hari');
        $sheet->getRowDimension($cell)->setRowHeight(20);
        $cell = $cell + 1;
        $sheet->setCellValue('A' . $cell, 'Potongan tanpa keterangan')->mergeCells('A' . $cell . ':B' . $cell);
        $sheet->setCellValue('C' . $cell, $data['potongan_tanpa_keterangan']);
        $sheet->setCellValue('D' . $cell, '%');
        $sheet->getRowDimension($cell)->setRowHeight(20);
        $cell = $cell + 1;
        $sheet->setCellValue('A' . $cell, 'Potongan masuk kerja')->mergeCells('A' . $cell . ':B' . $cell);
        $sheet->setCellValue('C' . $cell, $data['potongan_masuk_kerja']);
        $sheet->setCellValue('D' . $cell, '%');
        $sheet->getRowDimension($cell)->setRowHeight(20);
        $cell = $cell + 1;
        $sheet->setCellValue('A' . $cell, 'Potongan pulang kerja')->mergeCells('A' . $cell . ':B' . $cell);
        $sheet->setCellValue('C' . $cell, $data['potongan_pulang_kerja']);
        $sheet->setCellValue('D' . $cell, '%');
        $sheet->getRowDimension($cell)->setRowHeight(20);
        $cell = $cell + 1;
        $sheet->setCellValue('A' . $cell, 'Potongan apel')->mergeCells('A' . $cell . ':B' . $cell);
        $sheet->setCellValue('C' . $cell, $data['potongan_apel']);
        $sheet->setCellValue('D' . $cell, '%');
        $sheet->getRowDimension($cell)->setRowHeight(20);
        $cell = $cell + 1;
        $sheet->setCellValue('A' . $cell, 'Total potongan')->mergeCells('A' . $cell . ':B' . $cell);
        $sheet->setCellValue('C' . $cell, $data['jml_potongan_kehadiran_kerja']);
        $sheet->setCellValue('D' . $cell, '%');
        $sheet->getRowDimension($cell)->setRowHeight(25);
        $sheet->getStyle('A' . $cell . ':D' . $cell)->getFont()->setBold(true);
        $sheet->getStyle('A' . $cell . ':D' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('E1F5FE');

        $sheet->getStyle('A' . $cell_str . ':D' . $cell)->applyFromArray($border);
        $sheet->getStyle('A' . $cell_str . ':D' . $cell)->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('A' . $cell_str + 1 . ':A' . $cell)->getAlignment()->setHorizontal('left');


        if ($type == 'excel') {
            // Untuk download 
            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

            $periode = $tanggal_awal . ' s/d ' . $tanggal_akhir;
            $filename = "Laporan Absen {$pegawai_info->nama}_$pegawai_info->nip {$periode}.xlsx";
            header("Content-Disposition: attachment;filename=\"$filename\"");
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

    public function data_kehadiran_pegawai_by_opd($satuan_kerja, $unit_kerja, $tanggal_awal, $tanggal_akhir)
    {
        $data = array();
        $query = DB::table('tb_pegawai')
            ->select('tb_pegawai.id', 'tb_pegawai.nama', 'tb_pegawai.nip','tb_unit_kerja.waktu_masuk','tb_unit_kerja.waktu_keluar','tb_pegawai.tipe_pegawai')
            ->join('tb_jabatan', 'tb_jabatan.id_pegawai', 'tb_pegawai.id')
            ->join('tb_master_jabatan', 'tb_jabatan.id_master_jabatan', '=', 'tb_master_jabatan.id')
            ->join('tb_unit_kerja','tb_jabatan.id_unit_kerja','=','tb_unit_kerja.id')
            ->where('tb_pegawai.status', '1')
            ->orderBy('tb_master_jabatan.kelas_jabatan', 'DESC');

        $role = hasRole();

        if ($role['guard'] == 'web') {
            $query->where("tb_jabatan.id_satuan_kerja",$satuan_kerja);
            if ($unit_kerja !== 'all') {
                $query->where('tb_jabatan.id_unit_kerja', $unit_kerja);    
            }
        }

        if (hasRole()['guard'] == 'administrator') {
            $query->where("tb_jabatan.id_satuan_kerja",$satuan_kerja);
            if ($unit_kerja !== 'all') {
                $query->where('tb_jabatan.id_unit_kerja', $unit_kerja);    
            }
        }

        $data = $query->get();

        $data = $data->map(function ($item) use ($tanggal_awal, $tanggal_akhir) {
            $child = $this->data_kehadiran_pegawai($item->id, $tanggal_awal, $tanggal_akhir,$item->waktu_masuk, $item->waktu_keluar, $item->tipe_pegawai);
            $item->jml_hari_kerja = $child['jml_hari_kerja'];
            $item->kehadiran_kerja = $child['kehadiran_kerja'];
            $item->tanpa_keterangan = $child['tanpa_keterangan'];
            $item->potongan_tanpa_keterangan = $child['potongan_tanpa_keterangan'];
            $item->potongan_masuk_kerja = $child['potongan_masuk_kerja'];
            $item->potongan_pulang_kerja = $child['potongan_pulang_kerja'];
            $item->potongan_apel = $child['potongan_apel'];
            $item->jml_potongan_kehadiran_kerja = $child['jml_potongan_kehadiran_kerja'];
            $item->jml_hadir = $child['jml_hadir'];
            $item->jml_sakit = $child['jml_sakit'];
            $item->jml_izin_cuti = $child['jml_izin_cuti'];
            $item->jml_dinas_luar = $child['jml_dinas_luar'];
            $item->kmk_30 = $child['kmk_30'];
            $item->kmk_30_keatas = $child['kmk_30_keatas'];
            $item->cpk_30 = $child['cpk_30'];
            $item->cpk_30_keatas = $child['cpk_30_keatas'];
            $item->jml_tidak_apel = $child['jml_tidak_apel'];
            $item->jml_tidak_hadir_berturut_turut = $child['jml_tidak_hadir_berturut_turut'];
            return $item;
        });

        return $data;
    }

    public function export_opd()
    {
        $satuan_kerja = '';
        $nama_satuan_kerja = '';
        $unit_kerja = '';
        $nama_unit_kerja = '';
        $bulan = request('bulan');
        $tanggal_awal = date("Y-m-d", strtotime(date('Y-') . $bulan . '-01'));
        $tanggal_akhir = date("Y-m-d", strtotime(date('Y-') . $bulan . '-'. cal_days_in_month(CAL_GREGORIAN, $bulan, date('Y'))));
        if (request('satuan_kerja')) {
            $satuan_kerja = request('satuan_kerja');
            $nama_satuan_kerja = request('nama_satuan_kerja');
            $unit_kerja = request('id_unit_kerja');
            $nama_unit_kerja = request('nama_unit_kerja');
        } else {
            $satuan_kerja = $this->infoSatuanKerja(Auth::user()->id_pegawai)->id_satuan_kerja;
            $nama_satuan_kerja = $this->infoSatuanKerja(Auth::user()->id_pegawai)->nama_satuan_kerja;
            $unit_kerja = $this->infoSatuanKerja(Auth::user()->id_pegawai)->id_unit_kerja;
            $nama_unit_kerja = $this->infoSatuanKerja(Auth::user()->id_pegawai)->nama_unit_kerja;
        }

        $data = $this->data_kehadiran_pegawai_by_opd($satuan_kerja, $unit_kerja, $tanggal_awal, $tanggal_akhir);
        $type = request('type');
        return $this->export_rekapitulasi_absen($data, $type, $bulan, $nama_satuan_kerja, $nama_unit_kerja);
    }

    public function export_opd_bulan(){
        $satuan_kerja = '';
        $nama_satuan_kerja = '';
        $unit_kerja = '';
        $nama_unit_kerja = '';
        
        $bulan = request('bulan');

        // $tanggalAwal = Carbon::create(session('tahun_penganggaran'), $bulan, 1)->startOfMonth();
        // $tanggalAkhir = Carbon::create(session('tahun_penganggaran'), $bulan, 1)->endOfMonth();

        $tanggal_awal = date("Y-m-d", strtotime(date('Y-') . $bulan . '-01'));
        $tanggal_akhir = date("Y-m-d", strtotime(date('Y-') . $bulan . '-' . cal_days_in_month(CAL_GREGORIAN, $bulan, date('Y'))));


        if (request('satuan_kerja')) {
            $satuan_kerja = request('satuan_kerja');
            $nama_satuan_kerja = request('nama_satuan_kerja');
            $unit_kerja = request('id_unit_kerja');
            $nama_unit_kerja = request('nama_unit_kerja');
        } else {
            $satuan_kerja = $this->infoSatuanKerja(Auth::user()->id_pegawai)->id_satuan_kerja;
            $nama_satuan_kerja = $this->infoSatuanKerja(Auth::user()->id_pegawai)->nama_satuan_kerja;
            $unit_kerja = $this->infoSatuanKerja(Auth::user()->id_pegawai)->id_unit_kerja;
            $nama_unit_kerja = $this->infoSatuanKerja(Auth::user()->id_pegawai)->nama_unit_kerja;
        }

        $data = $this->data_kehadiran_pegawai_by_opd($satuan_kerja, $unit_kerja, $tanggal_awal, $tanggal_akhir);
        // return $data;
        $type = request('type');
        return $this->export_rekapitulasi_absen($data, $type, $bulan, $nama_satuan_kerja, $nama_unit_kerja);
    }

    public function export_rekapitulasi_absen($data, $type, $bulan, $satuan_kerja, $nama_unit_kerja)
    {
        $spreadsheet = new Spreadsheet();

        $spreadsheet->getProperties()->setCreator('BKPSDM ENREKANG')
            ->setLastModifiedBy('BKPSDM ENREKANG')
            ->setTitle('Laporan Rekapitulasi Absen Pegawai')
            ->setSubject('Laporan Rekapitulasi Absen Pegawai')
            ->setDescription('Laporan Rekapitulasi Absen Pegawai')
            ->setKeywords('pdf php')
            ->setCategory('LAPORAN ABSEN');
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_FOLIO);
        $sheet->getRowDimension(3)->setRowHeight(17);
        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
        $spreadsheet->getDefaultStyle()->getFont()->setSize(10);
        $spreadsheet->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);
        $spreadsheet->getActiveSheet()->getPageSetup()->setVerticalCentered(false);

        $spreadsheet->getActiveSheet()->getPageMargins()->setTop(0.3);
        $spreadsheet->getActiveSheet()->getPageMargins()->setRight(0.3);
        $spreadsheet->getActiveSheet()->getPageMargins()->setLeft(0.5);
        $spreadsheet->getActiveSheet()->getPageMargins()->setBottom(0.3);

        $sheet->setCellValue('A1', 'REKAPITULASI CAPAIAN DISIPLIN / KEHADIRAN KERJA');
        $sheet->mergeCells('A1:AF1');

        $perangka_daerah = '';
        if ($satuan_kerja == $nama_unit_kerja || $nama_unit_kerja == 'Semua') {
            $perangka_daerah = $satuan_kerja;
        } else {
            $perangka_daerah = $satuan_kerja . ' - ' . $nama_unit_kerja;
        }

        $sheet->setCellValue('A3', 'PERANGKAT DAERAH');
        $sheet->mergeCells('A3:B3');
        $sheet->setCellValue('C3', ': ');
        $sheet->setCellValue('D3', $perangka_daerah)->mergeCells('D3:AF3');

        $sheet->setCellValue('A4', 'PERIODE');
        $sheet->mergeCells('A4:B4');
        $sheet->setCellValue('C4', ':');
        $bulan = date('F', strtotime(date('Y-') . $bulan . date('-d')));
        $sheet->setCellValue('D4', $bulan)->mergeCells('D4:AF4');

        $sheet->setCellValue('A5', ' ');



        $sheet->getStyle('A1:W1')->getFont()->setSize(16);
        $sheet->getStyle('A3:W4')->getFont()->setSize(12);

        $sheet->getColumnDimension('B')->setWidth(35);


        // $sheet->getStyle('A6:W10')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('D9D9D9');

        // konten
        $sheet->setCellValue('A6', 'No')->mergeCells('A6:A10');
        $sheet->setCellValue('B6', 'NAMA/NIP')->mergeCells('B6:B10');
        $sheet->setCellValue('C6', 'JML HARI KERJA')->mergeCells('C6:C10');
        $sheet->setCellValue('D7', 'HADIR')->mergeCells('D7:D10');
        $sheet->setCellValue('E7', 'DINAS LUAR')->mergeCells('E7:E10');
        $sheet->setCellValue('F7', 'IZIN/CUTI')->mergeCells('F7:F10');
        $sheet->setCellValue('G7', 'SAKIT')->mergeCells('G7:G10');


        $sheet->setCellValue('D6', 'KEHADIRAN KERJA')->mergeCells('D6:I6');


        $sheet->setCellValue('H7', 'TANPA KETERANGAN')->mergeCells('H7:H10');
        $sheet->setCellValue('I7', 'TOTAL POTONGAN (%)')->mergeCells('I7:I10');

        $sheet->setCellValue('J6', 'TERLAMBAT DATANG KERJA')->mergeCells('J6:N7');
        $sheet->setCellValue('N8', 'TOTAL POTONGAN (%)')->mergeCells('N8:N10');

        $sheet->setCellValue('O6', 'CEPAT PULANG KERJA')->mergeCells('O6:S7');
        $sheet->setCellValue('S8', 'TOTAL POTONGAN (%)')->mergeCells('S8:S10');

        $sheet->setCellValue('J8', '1-30' . PHP_EOL . 'MENIT' . PHP_EOL . '(kali)')->mergeCells('J8:J10');
        $sheet->setCellValue('K8', 'JML' . PHP_EOL . 'POT' . PHP_EOL . '(%)')->mergeCells('K8:K10');
        $sheet->setCellValue('L8', '31 ~' . PHP_EOL . 'MENIT' . PHP_EOL . '(kali)')->mergeCells('L8:L10');
        $sheet->setCellValue('M8', 'JML' . PHP_EOL . 'POT' . PHP_EOL . '(%)')->mergeCells('M8:M10');

        $sheet->setCellValue('O8', '1-30' . PHP_EOL . 'MENIT' . PHP_EOL . '(kali)')->mergeCells('O8:O10');
        $sheet->setCellValue('P8', 'JML' . PHP_EOL . 'POT' . PHP_EOL . '(%)')->mergeCells('P8:P10');
        $sheet->setCellValue('Q8', '31 ~' . PHP_EOL . 'MENIT' . PHP_EOL . '(kali)')->mergeCells('Q8:Q10');
        $sheet->setCellValue('R8', 'JML' . PHP_EOL . 'POT' . PHP_EOL . '(%)')->mergeCells('R8:R10');

        $sheet->setCellValue('T6', 'APEL / UPACARA')->mergeCells('T6:U7');
        $sheet->setCellValue('T8', 'JUMLAH TIDAK HADIR APEL/ UPACARA')->mergeCells('T8:T10');
        $sheet->setCellValue('U8', 'TOTAL POTONGAN (%)')->mergeCells('U8:U10');

        $sheet->setCellValue('V6', 'JUMLAH POTONGAN KEHADIRAN KERJA')->mergeCells('V6:V10');
        $sheet->setCellValue('W6', 'KETERANGAN')->mergeCells('W6:W10');
        $spreadsheet->getActiveSheet()->getRowDimension(6)->setRowHeight(25);

        $cell = 11;

        foreach ($data as $key => $value) {
            $sheet->setCellValue('A' . $cell, $key + 1);
            $sheet->setCellValue('B' . $cell, $value->nama . ' ' . PHP_EOL . ' ' . $value->nip);
            $sheet->setCellValue('C' . $cell, $value->jml_hari_kerja);
            $sheet->setCellValue('D' . $cell, $value->jml_hadir);
            $sheet->setCellValue('E' . $cell, $value->jml_dinas_luar);
            $sheet->setCellValue('F' . $cell, $value->jml_izin_cuti);
            $sheet->setCellValue('G' . $cell, $value->jml_sakit);
            $sheet->setCellValue('H' . $cell, $value->tanpa_keterangan);
            $sheet->setCellValue('I' . $cell, $value->potongan_tanpa_keterangan);
            $sheet->setCellValue('J' . $cell, $value->kmk_30);
            $sheet->setCellValue('K' . $cell, $value->kmk_30 * 0.5);
            $sheet->setCellValue('L' . $cell, $value->kmk_30_keatas);
            $sheet->setCellValue('M' . $cell, $value->kmk_30_keatas * 1);
            $sheet->setCellValue('N' . $cell, $value->potongan_masuk_kerja);
            $sheet->setCellValue('O' . $cell, $value->cpk_30);
            $sheet->setCellValue('P' . $cell, $value->cpk_30 * 0.5);
            $sheet->setCellValue('Q' . $cell, $value->cpk_30_keatas);
            $sheet->setCellValue('R' . $cell, $value->cpk_30_keatas * 1);
            $sheet->setCellValue('S' . $cell, $value->potongan_pulang_kerja);
            $sheet->setCellValue('T' . $cell, $value->jml_tidak_apel);
            $sheet->setCellValue('U' . $cell, $value->potongan_apel);
            $keterangan = '';

            

            $value->tanpa_keterangan >= 11 && $value->jml_tidak_hadir_berturut_turut >= 5 ? $keterangan = 'TMS' : $keterangan = 'MS';
            $sheet->setCellValue('V' . $cell, $value->jml_potongan_kehadiran_kerja);
            $sheet->setCellValue('W' . $cell, $keterangan);

            if ($keterangan == 'TMS') {
                $sheet->getStyle('W' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('F44336');
            } else {
                $sheet->getStyle('W' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('00E676');
            }
            $cell++;
        }


        $border = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '0000000'],
                ],
            ],
        ];

        $sheet->getStyle('D8:E' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('ECF1E0');
        $sheet->getStyle('F8:I' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('DFBAB8');
        // $sheet->getStyle('J8:N' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('D9D9D9');
        $sheet->getStyle('J8:N' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('F5D6B7');
        $sheet->getStyle('O8:S' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('F9EADB');
        $sheet->getStyle('T8:U' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('BCCBE2');
        // $sheet->getStyle('AE7:W' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('4EAD5A');

        $sheet->getStyle('A6:W' . $cell)->applyFromArray($border);
        $sheet->getStyle('A:W')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A:W')->getAlignment()->setVertical('center');
        $sheet->getStyle('B7:B' . $cell)->getAlignment()->setHorizontal('rigth');
        $sheet->getStyle('A3:W4')->getAlignment()->setHorizontal('rigth');

        $periode = date('F', strtotime(date('Y-') . $bulan . date('-d')));
        if ($type == 'excel') {
            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

            $periode = date('F', strtotime(date('Y-') . $bulan . date('-d')));
            $filename = "Laporan Absen {$satuan_kerja} {$periode}.xlsx";
            header("Content-Disposition: attachment;filename=\"$filename\"");
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
