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
        $tahun = session('tahun_penganggaran') ? session('tahun_penganggaran') : date('Y');

        $tanggal_awal = date("Y-m-d", strtotime($tahun.'-' . $bulan . '-01'));
        $tanggal_akhir = date("Y-m-d", strtotime($tahun.'-' . $bulan . '-' . cal_days_in_month(CAL_GREGORIAN, $bulan, date('Y'))));

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

        $spreadsheet->getProperties()->setCreator('BKPSDM BULUKUMBA')
            ->setLastModifiedBy('BKPSDM BULUKUMBA')
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

        $spreadsheet->getProperties()->setCreator('BKPSDM BULUKUMBA')
            ->setLastModifiedBy('BKPSDM BULUKUMBA')
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
            $item->jml_cuti = $child['jml_cuti'];
            $item->jml_izin_cuti = $child['jml_izin_cuti'];
            $item->jml_dinas_luar = $child['jml_dinas_luar'];
            $item->kmk_30 = $child['kmk_30'];
            $item->kmk_60 = $child['kmk_60'];
            $item->kmk_90 = $child['kmk_90'];
            $item->kmk_90_keatas = $child['kmk_90_keatas'];
            $item->cpk_30 = $child['cpk_30'];
            $item->cpk_60 = $child['cpk_60'];
            $item->cpk_90 = $child['cpk_90'];
            $item->cpk_90_keatas = $child['cpk_90_keatas'];
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

   public function export_rekapitulasi_absen($data, $type,$tanggal_awal,$tanggal_akhir,$satuan_kerja){
        $spreadsheet = new Spreadsheet();

        $spreadsheet->getProperties()->setCreator('BKPSDM BULUKUMBA')
            ->setLastModifiedBy('BKPSDM BULUKUMBA')
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

        $sheet->setCellValue('A3', 'PERANGKAT DAERAH');
        $sheet->mergeCells('A3:B3');
        $sheet->setCellValue('C3', ': ');
        $sheet->setCellValue('D3', $satuan_kerja)->mergeCells('D3:AF3');

        $sheet->setCellValue('A4', 'PERIODE');
        $sheet->mergeCells('A4:B4');
        $sheet->setCellValue('C4', ':');
        $sheet->setCellValue('D4', $tanggal_awal . ' s/d ' . $tanggal_akhir)->mergeCells('D4:AF4');

        $sheet->setCellValue('A5', ' ');

        
        
        $sheet->getStyle('A1:AF1')->getFont()->setSize(16);
        $sheet->getStyle('A3:AF4')->getFont()->setSize(12);

        $sheet->getColumnDimension('B')->setWidth(35);
       

        $sheet->getStyle('A6:AF10')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('D9D9D9');

        // konten
        $sheet->setCellValue('A6', 'No')->mergeCells('A6:A10');
        $sheet->setCellValue('B6', 'NAMA/NIP')->mergeCells('B6:B10');
        $sheet->setCellValue('C6', 'JML HARI KERJA')->mergeCells('C6:C10');
        $sheet->setCellValue('D7', 'HADIR')->mergeCells('D7:D10');
        $sheet->setCellValue('E7', 'SAKIT')->mergeCells('E7:E10');
        $sheet->setCellValue('F7', 'CUTI')->mergeCells('F7:F10');
        $sheet->setCellValue('G7', 'DINAS LUAR')->mergeCells('G7:G10');


        $sheet->setCellValue('D6', 'KEHADIRAN KERJA')->mergeCells('D6:AF6');
        $sheet->setCellValue('AE7', 'JUMLAH POTONGAN KEHADIRAN KERJA')->mergeCells('AE7:AE10');
        $sheet->setCellValue('AF7', 'KETERANGAN')->mergeCells('AF7:AF10');

       
        $sheet->setCellValue('H7', 'TANPA KETERANGAN')->mergeCells('H7:J7');
         $sheet->setCellValue('H8', 'JUMLAH KEHADIRAN KERJA')->mergeCells('H8:H10');
        $sheet->setCellValue('I8', 'JUMLAH HARI TANPA KETERANGAN')->mergeCells('I8:I10');
        $sheet->setCellValue('J8', 'TOTAL POTONGAN (%)')->mergeCells('J8:J10');

        $sheet->setCellValue('K7', 'KETERLAMBATAN MASUK KERJA')->mergeCells('K7:S7');
        $sheet->setCellValue('K8', 'WAKTU TMK (MENIT)')->mergeCells('K8:R8');
        $sheet->setCellValue('S8', 'TOTAL POTONGAN (%)')->mergeCells('S8:S10');

        $sheet->setCellValue('T7', 'CEPAT PULANG KERJA')->mergeCells('T7:AB7');
        $sheet->setCellValue('T8', 'WAKTU CPK (MENIT)')->mergeCells('T8:AA8');
        $sheet->setCellValue('AB8', 'TOTAL POTONGAN (%)')->mergeCells('AB8:AB10');

        $sheet->setCellValue('AC7', 'APEL / UPACARA')->mergeCells('AC7:AD7');
        $sheet->setCellValue('AC8', 'JUMLAH TIDAK HADIR APEL/ UPACARA')->mergeCells('AC8:AC10');
        $sheet->setCellValue('AD8', 'TOTAL POTONGAN (%)')->mergeCells('AD8:AD10');

    

        $sheet->setCellValue('K9', '1-30' . PHP_EOL . 'M')->mergeCells('K9:K10');
        $sheet->setCellValue('L9', 'JML' . PHP_EOL . 'POT')->mergeCells('L9:L10');
        $sheet->setCellValue('M9', '31-60' . PHP_EOL . 'M')->mergeCells('M9:M10');
        $sheet->setCellValue('N9', 'JML' . PHP_EOL . 'POT')->mergeCells('N9:N10');
        $sheet->setCellValue('O9', '60-90' . PHP_EOL . 'M')->mergeCells('O9:O10');
        $sheet->setCellValue('P9', 'JML' . PHP_EOL . 'POT')->mergeCells('P9:P10');
        $sheet->setCellValue('Q9', '91' . PHP_EOL . 'Keatas')->mergeCells('Q9:Q10');
        $sheet->setCellValue('R9', 'JML' . PHP_EOL . 'POT')->mergeCells('R9:R10');

        $sheet->setCellValue('T9', '1-30' . PHP_EOL . 'M')->mergeCells('T9:T10');
        $sheet->setCellValue('U9', 'JML' . PHP_EOL . 'POT')->mergeCells('U9:U10');
        $sheet->setCellValue('V9', '31-60' . PHP_EOL . 'M')->mergeCells('V9:V10');
        $sheet->setCellValue('W9', 'JML' . PHP_EOL . 'POT')->mergeCells('W9:W10');
        $sheet->setCellValue('X9', '60-90' . PHP_EOL . 'M')->mergeCells('X9:X10');
        $sheet->setCellValue('Y9', 'JML' . PHP_EOL . 'POT')->mergeCells('Y9:Y10');
        $sheet->setCellValue('Z9', '91' . PHP_EOL . 'Keatas')->mergeCells('Z9:Z10');
        $sheet->setCellValue('AA9', 'JML' . PHP_EOL . 'POT')->mergeCells('AA9:AA10');

        $cell = 11;

        foreach ($data as $key => $value) {
            $sheet->setCellValue('A' . $cell, $key + 1);
            $sheet->setCellValue('B' . $cell, $value->nama . ' ' . PHP_EOL . ' ' . $value->nip);
            $sheet->setCellValue('C' . $cell, $value->jml_hari_kerja);
            $sheet->setCellValue('D' . $cell, $value->jml_hadir);
            $sheet->setCellValue('E' . $cell, $value->jml_sakit);
            $sheet->setCellValue('F' . $cell, $value->jml_cuti);
            $sheet->setCellValue('G' . $cell, $value->jml_dinas_luar);
            $sheet->setCellValue('H' . $cell, ($value->jml_hadir + $value->jml_dinas_luar));
            $sheet->setCellValue('I' . $cell, $value->tanpa_keterangan);
            $sheet->setCellValue('J' . $cell, $value->tanpa_keterangan * 3);
            $sheet->setCellValue('K' . $cell, $value->kmk_30);
            $sheet->setCellValue('L' . $cell, $value->kmk_30 * 0.5);
            $sheet->setCellValue('M' . $cell, $value->kmk_60);
            $sheet->setCellValue('N' . $cell, $value->kmk_60 * 1);
            $sheet->setCellValue('O' . $cell, $value->kmk_90);
            $sheet->setCellValue('P' . $cell, $value->kmk_90 * 1.25);
            $sheet->setCellValue('Q' . $cell, $value->kmk_90_keatas);
            $sheet->setCellValue('R' . $cell, $value->kmk_90_keatas * 1.5);
            $sheet->setCellValue('S' . $cell, $value->potongan_masuk_kerja);

            $sheet->setCellValue('T' . $cell, $value->cpk_30);
            $sheet->setCellValue('U' . $cell, $value->cpk_30 * 0.5);
            $sheet->setCellValue('V' . $cell, $value->cpk_60);
            $sheet->setCellValue('W' . $cell, $value->cpk_60 * 1);
            $sheet->setCellValue('X' . $cell, $value->cpk_90);
            $sheet->setCellValue('Y' . $cell, $value->cpk_90 * 1.25);
            $sheet->setCellValue('Z' . $cell, $value->cpk_90_keatas);
            $sheet->setCellValue('AA' . $cell, $value->cpk_90_keatas * 1.5);

            $sheet->setCellValue('AB' . $cell, $value->potongan_pulang_kerja);
            $sheet->setCellValue('AC' . $cell, $value->jml_tidak_apel);
            $sheet->setCellValue('AD' . $cell, $value->potongan_apel);
            
            $keterangan = '';

            $value->tanpa_keterangan > 3 ? $keterangan = 'TMS' : $keterangan = 'MS';
            $sheet->setCellValue('AE' . $cell, $value->jml_potongan_kehadiran_kerja);
            $sheet->setCellValue('AF' . $cell, $keterangan);

            if ($value->tanpa_keterangan > 3) {
                $sheet->getStyle('AF' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('F44336'); 
             }else{
                $sheet->getStyle('AF' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('00E676');
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

        $sheet->getStyle('D7:G' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('ECF1E0');
        $sheet->getStyle('H7:J' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('DFBAB8');
        $sheet->getStyle('I7:I' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('D9D9D9');
        $sheet->getStyle('K7:S' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('F5D6B7');
        $sheet->getStyle('T7:AB' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('F9EADB');
        $sheet->getStyle('AC7:AD' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('BCCBE2');
        $sheet->getStyle('AE7:AE' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('4EAD5A');

        $sheet->getStyle('A6:AF' . $cell)->applyFromArray($border);
        $sheet->getStyle('A:AF')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A:AF')->getAlignment()->setVertical('center');
        $sheet->getStyle('B7:B' . $cell)->getAlignment()->setHorizontal('rigth');
        $sheet->getStyle('A3:AF4')->getAlignment()->setHorizontal('rigth');

        $periode = $tanggal_awal . ' s/d ' . $tanggal_akhir;
        if ($type == 'excel') {
            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

            $periode = $tanggal_awal . ' s/d ' . $tanggal_akhir;
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
