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
            return $this->export_rekap_pegawai($data, $type, $pegawai_info, $tanggal_awal, $tanggal_akhir,$pegawai_info->tipe_pegawai);
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
        if ($pegawai_info->tipe_pegawai == 'pegawai_administratif' || $pegawai_info->tipe_pegawai == 'tenaga_pendidik') {
            return $this->export_rekap_pegawai($data, $type, $pegawai_info, $tanggal_awal, $tanggal_akhir,$pegawai_info->tipe_pegawai);
        }else {
            return $this->export_rekap_pegawai_nakes($data, $type, $pegawai_info, $tanggal_awal, $tanggal_akhir);
        }
    }

    public function export_rekap_pegawai($data, $type, $pegawai_info, $tanggal_awal, $tanggal_akhir,$tipe_pegawai)
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

        $sheet->setCellValue('A1', 'Laporan Rekapitulasi Absen Pegawai')->mergeCells('A1:I1');
        $sheet->setCellValue('A2', '' . $pegawai_info->nama_unit_kerja)->mergeCells('A2:I2');
        // $sheet->setCellValue('A3', $pegawai_info->nama . ' / ' . $pegawai_info->nip)->mergeCells('A3:G3');
        $sheet->getStyle('A1:I4')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1:I4')->getFont()->setSize(14);

        $sheet->setCellValue('A7', ' ')->mergeCells('A10:I10');

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
        
        $sheet->setCellValue('F11', 'Istirahat')->mergeCells('F11:G11');
        $sheet->setCellValue('F12', 'Waktu');
        $sheet->getColumnDimension('F')->setWidth(25);
        $sheet->setCellValue('G12', 'Keterangan');
        $sheet->getColumnDimension('G')->setWidth(25);

        $sheet->setCellValue('H11', 'Pulang')->mergeCells('H11:I11');
        $sheet->setCellValue('H12', 'Waktu');
        $sheet->getColumnDimension('H')->setWidth(25);
        $sheet->setCellValue('I12', 'Keterangan');
        $sheet->getColumnDimension('I')->setWidth(25);


        $sheet->setCellValue('B13', 'Nama')->mergeCells('B11:B12');
        $sheet->setCellValue('C11', 'Status Absen')->mergeCells('C11:C12');

        $sheet->getStyle('A:I')->getAlignment()->setWrapText(true);
        $sheet->getStyle('A1:I12')->getFont()->setBold(true);
        $sheet->getRowDimension(11)->setRowHeight(30);
        $sheet->getRowDimension(12)->setRowHeight(30);

        $sheet->getStyle('A11:I12')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('E1F5FE');


        $cell = 13;

        foreach ($data['data'] as $index => $value) {
            $sheet->getRowDimension($cell)->setRowHeight(30);
            $sheet->setCellValue('A' . $cell, $index + 1);
            $sheet->setCellValue('B' . $cell, date('d/m/y', strtotime($value['tanggal_absen'])));
            $sheet->setCellValue('C' . $cell, ucfirst($value['status']));
            $sheet->setCellValue('D' . $cell, $value['waktu_masuk']);
            $sheet->setCellValue('E' . $cell, $value['keterangan_masuk']);
            $sheet->setCellValue('F' . $cell, $value['status_masuk_istirahat']);
            $sheet->setCellValue('G' . $cell, $value['waktu_masuk_istirahat']);
            $sheet->setCellValue('H' . $cell, $value['waktu_keluar']);
            $sheet->setCellValue('I' . $cell, $value['keterangan_pulang']);
            $cell++;
        }


        $sheet->getStyle('A5:I9')->getFont()->setSize(12);

        $border = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '0000000'],
                ],
            ],
        ];


        $sheet->getStyle('A11:I' . $cell)->applyFromArray($border);
        $sheet->getStyle('A11:I' . $cell)->getAlignment()->setVertical('center')->setHorizontal('center');

        $cell++;
        $sheet->setCellValue('A' . $cell, ' ')->mergeCells('A' . $cell . ':I' . $cell);
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
        if ($tipe_pegawai == 'pegawai_administratif') {
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
        }

        if ($tipe_pegawai == 'tenaga_pendidik') {
            $cell = $cell + 1;
            $sheet->setCellValue('A' . $cell, 'Jumlah Menit Terlambat Datang')->mergeCells('A' . $cell . ':B' . $cell);
            $sheet->setCellValue('C' . $cell, $data['jml_menit_terlambat_masuk_kerja']);
            $sheet->setCellValue('D' . $cell, 'Menit');
            $sheet->getRowDimension($cell)->setRowHeight(20);
            $cell = $cell + 1;
            $sheet->setCellValue('A' . $cell, 'Jumlah Menit Cepat Pulang')->mergeCells('A' . $cell . ':B' . $cell);
            $sheet->setCellValue('C' . $cell, $data['jml_menit_terlambat_pulang_kerja']);
            $sheet->setCellValue('D' . $cell, 'Menit');
            $sheet->getRowDimension($cell)->setRowHeight(20);
            $cell = $cell + 1;
            $sheet->setCellValue('A' . $cell, 'Jumlah Total Menit Terlambat Datang dan Cepat Pulang')->mergeCells('A' . $cell . ':B' . $cell);
            $sheet->setCellValue('C' . $cell, $data['jml_menit_terlambat_masuk_kerja'] + $data['jml_menit_terlambat_pulang_kerja']);
            $sheet->setCellValue('D' . $cell, 'Menit');
            $sheet->getRowDimension($cell)->setRowHeight(20);
        }

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
            $item->jml_apel = $child['jml_apel'];
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
            $item->jml_tidak_apel_hari_senin = $child['jml_tidak_apel_hari_senin'];
            $item->jml_tidak_hadir_berturut_turut = $child['jml_tidak_hadir_berturut_turut'];
            $item->jml_menit_terlambat_masuk_kerja = $child['jml_menit_terlambat_masuk_kerja'];
            $item->jml_menit_terlambat_pulang_kerja = $child['jml_menit_terlambat_pulang_kerja'];
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
        $type = request('type');

        if ($this->CheckOpd($unit_kerja)) {
            return $this->export_rekapitulasi_absen_guru($data, $type, $bulan, $nama_satuan_kerja, $nama_unit_kerja);
            
        }else {
            return $this->export_rekapitulasi_absen($data, $type, $bulan, $nama_satuan_kerja, $nama_unit_kerja);
        }

        
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
        $sheet->mergeCells('A1:AI1');

        $sheet->setCellValue('A3', 'PERANGKAT DAERAH');
        $sheet->mergeCells('A3:B3');
        $sheet->setCellValue('C3', ': ');
        $sheet->setCellValue('D3', $satuan_kerja)->mergeCells('D3:AF3');

        $sheet->setCellValue('A4', 'PERIODE');
        $sheet->mergeCells('A4:B4');
        $sheet->setCellValue('C4', ':');
        $sheet->setCellValue('D4', $tanggal_awal . ' s/d ' . $tanggal_akhir)->mergeCells('D4:AF4');

        $sheet->setCellValue('A5', ' ');

        
        
        $sheet->getStyle('A1:AI1')->getFont()->setSize(16);
        $sheet->getStyle('A3:AI4')->getFont()->setSize(12);

        $sheet->getColumnDimension('B')->setWidth(35);
       

        $sheet->getStyle('A6:AI10')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('D9D9D9');

        // konten
        $sheet->setCellValue('A6', 'No')->mergeCells('A6:A11');
        $sheet->setCellValue('B6', 'NAMA/NIP')->mergeCells('B6:B11');
        $sheet->setCellValue('C6', 'JML HARI KERJA')->mergeCells('C6:C11');
        $sheet->setCellValue('D7', 'HADIR')->mergeCells('D7:D11');
        $sheet->setCellValue('E7', 'SAKIT')->mergeCells('E7:E11');
        $sheet->setCellValue('F7', 'CUTI')->mergeCells('F7:F11');
        $sheet->setCellValue('G7', 'DINAS LUAR')->mergeCells('G7:G11');


        $sheet->setCellValue('D6', 'KEHADIRAN KERJA')->mergeCells('D6:AI6');
       
        $sheet->setCellValue('H7', 'TANPA KETERANGAN')->mergeCells('H7:J8');
         $sheet->setCellValue('H9', 'JUMLAH KEHADIRAN KERJA')->mergeCells('H9:H11');
        $sheet->setCellValue('I9', 'JUMLAH HARI TANPA KETERANGAN')->mergeCells('I9:I11');
        $sheet->setCellValue('J9', 'TOTAL POTONGAN (%)')->mergeCells('J9:J11');

        $sheet->setCellValue('K7', 'KEHADIRAN TERLAMBAT, CEPAT PULANG, DAN TIDAK APEL')->mergeCells('K7:AG7');

        $sheet->setCellValue('K8', 'KETERLAMBATAN MASUK KERJA')->mergeCells('K8:S8');
        $sheet->setCellValue('K9', 'WAKTU TMK (MENIT)')->mergeCells('K9:R9');
        $sheet->setCellValue('S9', 'TOTAL POTONGAN (%)')->mergeCells('S9:S11');

        // batas
        $sheet->setCellValue('T8', 'CEPAT PULANG KERJA')->mergeCells('T8:AB8');
        $sheet->setCellValue('T9', 'WAKTU CPK (MENIT)')->mergeCells('T9:AA9');
        $sheet->setCellValue('AB9', 'TOTAL POTONGAN (%)')->mergeCells('AB9:AB11');

        $sheet->setCellValue('AC8', 'APEL / UPACARA')->mergeCells('AC8:AF8');
        $sheet->setCellValue('AC9', 'JUMLAH TIDAK HADIR APEL/ UPACARA')->mergeCells('AC9:AC11');
        $sheet->setCellValue('AD9', 'TOTAL POTONGAN (%)')->mergeCells('AD9:AD11');
        $sheet->setCellValue('AE9', 'JUMLAH TIDAK HADIR APEL (SELASA - JUMAT)')->mergeCells('AE9:AE11');
        $sheet->setCellValue('AF9', 'TOTAL POTONGAN (%)')->mergeCells('AF9:AF11');

        $sheet->setCellValue('AG8', 'TOTAL')->mergeCells('AG8:AG11');

        $sheet->setCellValue('K10', '1-30' . PHP_EOL . 'M')->mergeCells('K10:K11');
        $sheet->setCellValue('L10', 'JML' . PHP_EOL . 'POT')->mergeCells('L10:L11');
        $sheet->setCellValue('M10', '31-60' . PHP_EOL . 'M')->mergeCells('M10:M11');
        $sheet->setCellValue('N10', 'JML' . PHP_EOL . 'POT')->mergeCells('N10:N11');
        $sheet->setCellValue('O10', '60-90' . PHP_EOL . 'M')->mergeCells('O10:O11');
        $sheet->setCellValue('P10', 'JML' . PHP_EOL . 'POT')->mergeCells('P10:P11');
        $sheet->setCellValue('Q10', '91' . PHP_EOL . 'Keatas')->mergeCells('Q10:Q11');
        $sheet->setCellValue('R10', 'JML' . PHP_EOL . 'POT')->mergeCells('R10:R11');

        $sheet->setCellValue('T10', '1-30' . PHP_EOL . 'M')->mergeCells('T10:T11');
        $sheet->setCellValue('U10', 'JML' . PHP_EOL . 'POT')->mergeCells('U10:U11');
        $sheet->setCellValue('V10', '31-60' . PHP_EOL . 'M')->mergeCells('V10:V11');
        $sheet->setCellValue('W10', 'JML' . PHP_EOL . 'POT')->mergeCells('W10:W11');
        $sheet->setCellValue('X10', '60-90' . PHP_EOL . 'M')->mergeCells('X10:X11');
        $sheet->setCellValue('Y10', 'JML' . PHP_EOL . 'POT')->mergeCells('Y10:Y11');
        $sheet->setCellValue('Z10', '91' . PHP_EOL . 'Keatas')->mergeCells('Z10:Z11');
        $sheet->setCellValue('AA10', 'JML' . PHP_EOL . 'POT')->mergeCells('AA10:AA11');

        $sheet->setCellValue('AH7', 'JUMLAH POTONGAN KEHADIRAN KERJA')->mergeCells('AH7:AH11');
        $sheet->setCellValue('AI7', 'KETERANGAN')->mergeCells('AI7:AI11');

        $cell = 12;

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
            $sheet->setCellValue('AD' . $cell, ($value->jml_tidak_apel * 2));
            $sheet->setCellValue('AE' . $cell, $value->jml_tidak_apel_hari_senin);
            $sheet->setCellValue('AF' . $cell, ($value->jml_tidak_apel_hari_senin * 0.25));
            $total_ = $value->potongan_masuk_kerja + $value->potongan_pulang_kerja + ($value->jml_tidak_apel * 2) + ($value->jml_tidak_apel_hari_senin * 0.25);
            $sheet->setCellValue('AG' . $cell, $total_);
            
            $keterangan = '';

            $value->tanpa_keterangan > 3 ? $keterangan = 'TMS' : $keterangan = 'MS';
            $sheet->setCellValue('AH' . $cell, $value->jml_potongan_kehadiran_kerja);
            $sheet->setCellValue('AI' . $cell, $keterangan);

            if ($value->tanpa_keterangan > 3) {
                $sheet->getStyle('AI' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('F44336'); 
             }else{
                $sheet->getStyle('AI' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('00E676');
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
        $sheet->getStyle('AC7:AF' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('BCCBE2');
        $sheet->getStyle('AH7:AH' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('4EAD5A');

        $sheet->getStyle('K7:AG7')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('f7f702');
        $sheet->getStyle('AG8:AG'.$cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('f7f702');
        
        $sheet->getStyle('A6:AI' . $cell)->applyFromArray($border);
        $sheet->getStyle('A:AI')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A:AI')->getAlignment()->setVertical('center');
        $sheet->getStyle('B7:B' . $cell)->getAlignment()->setHorizontal('rigth');
        $sheet->getStyle('A3:AI4')->getAlignment()->setHorizontal('rigth');

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

    public function export_rekapitulasi_absen_guru($data, $type,$tanggal_awal,$tanggal_akhir,$satuan_kerja){

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
        $sheet->mergeCells('A1:M1');

        $sheet->setCellValue('A3', 'PERANGKAT DAERAH');
        $sheet->mergeCells('A3:B3');
        $sheet->setCellValue('C3', ': ');
        $sheet->setCellValue('D3', $satuan_kerja)->mergeCells('D3:G3');

        $sheet->setCellValue('A4', 'PERIODE');
        $sheet->mergeCells('A4:B4');
        $sheet->setCellValue('C4', ':');
        $sheet->setCellValue('D4', strtoupper(konvertBulan($tanggal_awal)))->mergeCells('D4:G4');

        $sheet->setCellValue('A5', ' ');

        
        
        $sheet->getStyle('A1:M1')->getFont()->setSize(16);
        $sheet->getStyle('A3:M4')->getFont()->setSize(12);

        $sheet->getColumnDimension('B')->setWidth(35);
       

        $sheet->getStyle('A6:M10')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('D9D9D9');

        // konten
        $sheet->setCellValue('A6', 'No')->mergeCells('A6:A10');
        $sheet->setCellValue('B6', 'NAMA/NIP')->mergeCells('B6:B10');
        $sheet->setCellValue('C6', 'JML HARI KERJA')->mergeCells('C6:C10');

        $sheet->setCellValue('D6', 'KEHADIRAN KERJA')->mergeCells('D6:I6');

        $sheet->setCellValue('D7', 'APEL')->mergeCells('D7:D10');
        $sheet->setCellValue('E7', 'HADIR')->mergeCells('E7:E10');
        $sheet->setCellValue('F7', 'SAKIT')->mergeCells('F7:F10');
        $sheet->setCellValue('G7', 'CUTI')->mergeCells('G7:G10');
        $sheet->setCellValue('H7', 'DINAS LUAR')->mergeCells('H7:H10');
        $sheet->setCellValue('I7', 'TANPA KETERANGAN')->mergeCells('I7:I10');
        $sheet->setCellValue('J6', 'JUMLAH KEHADIRAN KERJA')->mergeCells('J6:J10');

        $sheet->setCellValue('K6', 'KETERANGAN KEHADIRAN KERJA')->mergeCells('K6:M6');
        $sheet->setCellValue('K7', 'JUMLAH MENIT KETERLAMBATAN MASUK KERJA')->mergeCells('K7:K10');
        $sheet->getColumnDimension('K')->setWidth(20);
        $sheet->setCellValue('L7', 'JUMLAH MENIT CEPAT PULANG KERJA')->mergeCells('L7:L10');
        $sheet->getColumnDimension('L')->setWidth(20);
        $sheet->setCellValue('M7', 'TOTAL (MENIT)')->mergeCells('M7:M10');

        $cell = 11;

        foreach ($data as $key => $value) {
            $sheet->setCellValue('A' . $cell, $key + 1);
            $sheet->setCellValue('B' . $cell, $value->nama . ' ' . PHP_EOL . ' ' . $value->nip);
            $sheet->setCellValue('C' . $cell, $value->jml_hari_kerja);
            $sheet->setCellValue('D' . $cell, $value->jml_apel);
            $sheet->setCellValue('E' . $cell, $value->jml_hadir);
            $sheet->setCellValue('F' . $cell, $value->jml_sakit);
            $sheet->setCellValue('G' . $cell, $value->jml_izin_cuti);
            $sheet->setCellValue('H' . $cell, $value->jml_dinas_luar);
            $sheet->setCellValue('I' . $cell, $value->tanpa_keterangan);
            $sheet->setCellValue('J' . $cell, ($value->jml_hadir + $value->jml_dinas_luar));
            $sheet->setCellValue('K' . $cell, $value->jml_menit_terlambat_masuk_kerja);
            $sheet->setCellValue('L' . $cell, $value->jml_menit_terlambat_pulang_kerja);
            $sheet->setCellValue('M' . $cell, ($value->jml_menit_terlambat_masuk_kerja + $value->jml_menit_terlambat_pulang_kerja));
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

        $sheet->getStyle('D7:H' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('ECF1DF');
        $sheet->getStyle('I7:I' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('E6B8B7');
        $sheet->getStyle('K7:M' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('F5D6B7');
        $sheet->getStyle('J7:J' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('D9D9D9');
        // $sheet->getStyle('T7:AB' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('F9EADB');
        // $sheet->getStyle('AC7:AF' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('BCCBE2');
        // $sheet->getStyle('AH7:AH' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('4EAD5A');

        // $sheet->getStyle('K7:AG7')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('f7f702');
        // $sheet->getStyle('AG8:AG'.$cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('f7f702');
        
        $sheet->getStyle('A6:M' . $cell)->applyFromArray($border);
        $sheet->getStyle('A:M')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A:M')->getAlignment()->setVertical('center');

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
