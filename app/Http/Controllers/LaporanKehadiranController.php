<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Jobs\ProcessKehadiranExportJob;
use App\Models\KehadiranExportJob;
use App\Models\JamApel;
use App\Models\JamKerja;
use App\Models\Ramadan;
use App\Traits\General;
use App\Traits\Presensi;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            $username = Auth::user()->username;
            return view('laporan.kehadiran.index_opd', compact('module', 'pegawai', 'satuan_kerja_user', 'nama_satuan_kerja', 'username'));
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
        $tanggal_awal = date("Y-m-d", strtotime(date('Y-') . $bulan . '-01'));
        $tanggal_akhir = date("Y-m-d", strtotime(date('Y-') . $bulan . '-' . cal_days_in_month(CAL_GREGORIAN, $bulan, date('Y'))));

        $jabatan_req = request("status");
        $pegawai = request('pegawai') ? request('pegawai') : Auth::user()->id_pegawai;
        $pegawai_info = $this->findPegawai($pegawai, $jabatan_req);
        $data = $this->data_kehadiran_pegawai($pegawai, $tanggal_awal, $tanggal_akhir, $pegawai_info->waktu_masuk, $pegawai_info->waktu_keluar, $pegawai_info->tipe_pegawai, $pegawai_info->jumlah_shift);
        $type = request('type');

        if ($pegawai_info->tipe_pegawai == 'pegawai_administratif') {
            return $this->export_rekap_pegawai($data, $type, $pegawai_info, $tanggal_awal, $tanggal_akhir, $pegawai_info->tipe_pegawai);
        } else {
            return $this->export_rekap_pegawai_nakes($data, $type, $pegawai_info, $tanggal_awal, $tanggal_akhir);
        }
    }

    public function export_pegawai_bulan()
    {
        
        $satuan_kerja = request('satuan_kerja');
        $bulan = request('bulan');
        $tahun = session('tahun_penganggaran') ? session('tahun_penganggaran') : date('Y');
        // $tanggal_awal = date("Y-m-d", strtotime($tahun . '-' . $bulan . '-01'));
        // $tanggal_akhir = date("Y-m-d", strtotime($tahun . '-' . $bulan . '-' . cal_days_in_month(CAL_GREGORIAN, $bulan, date('Y'))));
        $periodePegawai = $this->getPeriodePegawaiDiSatuanKerja(request('pegawai'), $satuan_kerja, $tahun, $bulan);

        // CEK jika periode pegawai tidak ada
        if ($periodePegawai == null) {
            return back()->withErrors(['Periode pegawai tidak ditemukan pada satuan kerja tersebut']);
        }

        $jabatan_req = request("status");
        $pegawai = request('pegawai') ? request('pegawai') : Auth::user()->id_pegawai;
        // $pegawai_info = $this->findPegawai($pegawai, $jabatan_req);
        $pegawai_info = $this->findPegawaiByMutasi($pegawai, $satuan_kerja, $tahun);
        $data = $this->data_kehadiran_pegawai($pegawai, $periodePegawai['tanggal_awal'], $periodePegawai['tanggal_akhir'], $pegawai_info->waktu_masuk, $pegawai_info->waktu_keluar, $pegawai_info->tipe_pegawai, $pegawai_info->jumlah_shift);
        $type = request('type');
        if ($pegawai_info->tipe_pegawai == 'pegawai_administratif' || $pegawai_info->tipe_pegawai == 'tenaga_pendidik_non_guru') {
            return $this->export_rekap_pegawai($data, $type, $pegawai_info, $periodePegawai['tanggal_awal'], $periodePegawai['tanggal_akhir'], $pegawai_info->tipe_pegawai);
        } else {
            return $this->export_rekap_pegawai_nakes($data, $type, $pegawai_info, $periodePegawai['tanggal_awal'], $periodePegawai['tanggal_akhir']);
        }
    }

    public function export_rekap_pegawai($data, $type, $pegawai_info, $tanggal_awal, $tanggal_akhir, $tipe_pegawai)
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
        if ($tipe_pegawai == 'pegawai_administratif' || $tipe_pegawai == 'tenaga_pendidik_non_guru') {
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

        if ($tipe_pegawai == 'tenaga_pendidik' || $tipe_pegawai == 'tenaga_kesehatan_non_shift') {
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
        $sheet->setCellValue('A' . $cell, 'Potongan terlambat datang dan cepat pulang')->mergeCells('A' . $cell . ':B' . $cell);
        $sheet->setCellValue('C' . $cell, $data['jml_menit_terlambat_masuk_kerja'] + $data['jml_menit_terlambat_pulang_kerja']);
        $sheet->setCellValue('D' . $cell, 'Menit');
        $sheet->getRowDimension($cell)->setRowHeight(20);
        // $cell = $cell + 1;
        // $sheet->setCellValue('A' . $cell, 'Potongan masuk kerja')->mergeCells('A' . $cell . ':B' . $cell);
        // $sheet->setCellValue('C' . $cell, $data['potongan_masuk_kerja']);
        // $sheet->setCellValue('D' . $cell, '%');
        // $sheet->getRowDimension($cell)->setRowHeight(20);
        // $cell = $cell + 1;
        // $sheet->setCellValue('A' . $cell, 'Potongan pulang kerja')->mergeCells('A' . $cell . ':B' . $cell);
        // $sheet->setCellValue('C' . $cell, $data['potongan_pulang_kerja']);
        // $sheet->setCellValue('D' . $cell, '%');
        // $sheet->getRowDimension($cell)->setRowHeight(20);
        // $cell = $cell + 1;
        // $sheet->setCellValue('A' . $cell, 'Potongan apel')->mergeCells('A' . $cell . ':B' . $cell);
        // $sheet->setCellValue('C' . $cell, $data['potongan_apel']);
        // $sheet->setCellValue('D' . $cell, '%');
        // $sheet->getRowDimension($cell)->setRowHeight(20);
        // $cell = $cell + 1;
        // $sheet->setCellValue('A' . $cell, 'Total potongan')->mergeCells('A' . $cell . ':B' . $cell);
        // $sheet->setCellValue('C' . $cell, $data['jml_potongan_kehadiran_kerja']);
        // $sheet->setCellValue('D' . $cell, '%');
        // $sheet->getRowDimension($cell)->setRowHeight(25);
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

    public function data_kehadiran_pegawai_by_opd($satuan_kerja, $unit_kerja, $tanggal_awal, $tanggal_akhir, $status_kepegawaian, $tipe_pegawai)
    {
        $result = $this->calculateDataKehadiranPegawaiByOpdOptimized(
            $satuan_kerja,
            $unit_kerja,
            $tanggal_awal,
            $tanggal_akhir,
            $status_kepegawaian,
            $tipe_pegawai
        );

        return $result['data'];
    }

    public function export_opd()
    {
        $context = $this->resolveExportOpdContext(request());
        $result = $this->calculateDataKehadiranPegawaiByOpdOptimized(
            $context['satuan_kerja'],
            $context['unit_kerja'],
            $context['tanggal_awal'],
            $context['tanggal_akhir'],
            $context['status_kepegawaian'],
            $context['tipe_pegawai']
        );
        $startedRender = microtime(true);
        $response = $this->exportRekapBasedOnType($result['data'], $context['type'], $context, null);
        $renderSeconds = round(microtime(true) - $startedRender, 4);

        $this->logExportMetrics('export_opd', array_merge($result['metrics'], [
            'render_seconds' => $renderSeconds,
            'total_seconds' => round(microtime(true) - $result['metrics']['started_at'], 4),
        ]));

        return $response;
    }

    public function export_opd_bulan()
    {
        Log::info('=== EXPORT OPD BULAN ===');
        Log::info('Request params', request()->all());
        $context = $this->resolveExportOpdContext(request());
        $mode = request('mode', 'auto');
        $forceAsync = request('force_async') == '1';

        $startedLoadPegawai = microtime(true);
        $pegawaiRows = $this->loadPegawaiDataByOpd(
            $context['satuan_kerja'],
            $context['unit_kerja'],
            $context['tanggal_awal'],
            $context['status_kepegawaian'],
            $context['tipe_pegawai']
        );
        $loadPegawaiSeconds = microtime(true) - $startedLoadPegawai;

        $jumlahHari = Carbon::parse($context['tanggal_awal'])->diffInDays(Carbon::parse($context['tanggal_akhir'])) + 1;
        $estimatedWorkload = $pegawaiRows->count() * $jumlahHari;
        $shouldAsync = $forceAsync
            || $mode === 'async'
            || ($mode === 'auto' && $context['type'] === 'pdf' && $estimatedWorkload > 3000);

        if ($shouldAsync) {
            $exportJob = KehadiranExportJob::create([
                'user_id' => Auth::id(),
                'status' => 'queued',
                'type' => $context['type'],
                'payload' => $context,
                'estimated_workload' => $estimatedWorkload,
            ]);

            ProcessKehadiranExportJob::dispatch($exportJob->id);

            Log::info('kehadiran_export_queued', [
                'export_job_id' => $exportJob->id,
                'estimated_workload' => $estimatedWorkload,
                'load_pegawai_seconds' => round($loadPegawaiSeconds, 4),
                'mode' => $mode,
                'force_async' => $forceAsync,
            ]);

            $basePath = request()->is('laporan-opd/*') ? '/laporan-opd/kehadiran' : '/laporan/kehadiran';
            $statusUrl = url($basePath . '/export-status/' . $exportJob->id);
            $downloadUrl = url($basePath . '/export-download/' . $exportJob->id);

            if (!request()->expectsJson() && !request()->ajax()) {
                return response()->view('laporan.kehadiran.export_wait', [
                    'jobId' => $exportJob->id,
                    'statusUrl' => $statusUrl,
                    'downloadUrl' => $downloadUrl,
                    'estimatedWorkload' => $estimatedWorkload,
                ]);
            }

            return response()->json([
                'success' => true,
                'mode' => 'async',
                'job_id' => $exportJob->id,
                'status_url' => $statusUrl,
                'download_url' => $downloadUrl,
            ]);
        }

        $result = $this->calculateDataKehadiranPegawaiByOpdOptimized(
            $context['satuan_kerja'],
            $context['unit_kerja'],
            $context['tanggal_awal'],
            $context['tanggal_akhir'],
            $context['status_kepegawaian'],
            $context['tipe_pegawai'],
            $pegawaiRows
        );
        $startedRender = microtime(true);
        $response = $this->exportRekapBasedOnType($result['data'], $context['type'], $context, null);
        $renderSeconds = round(microtime(true) - $startedRender, 4);

        $this->logExportMetrics('export_opd_bulan', array_merge($result['metrics'], [
            'load_pegawai_seconds' => round($loadPegawaiSeconds, 4),
            'estimated_workload' => $estimatedWorkload,
            'mode' => 'sync',
            'render_seconds' => $renderSeconds,
            'total_seconds' => round(microtime(true) - $result['metrics']['started_at'], 4),
        ]));

        return $response;
    }

    public function export_status(int $id): JsonResponse
    {
        $job = KehadiranExportJob::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Data export tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'id' => $job->id,
            'status' => $job->status,
            'type' => $job->type,
            'estimated_workload' => $job->estimated_workload,
            'error_message' => $job->error_message,
            'download_url' => $job->status === 'done'
                ? url((request()->is('laporan-opd/*') ? '/laporan-opd/kehadiran' : '/laporan/kehadiran') . '/export-download/' . $job->id)
                : null,
            'started_at' => optional($job->started_at)->toDateTimeString(),
            'finished_at' => optional($job->finished_at)->toDateTimeString(),
        ]);
    }

    public function export_download(int $id)
    {
        $job = KehadiranExportJob::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$job) {
            abort(404, 'Data export tidak ditemukan');
        }

        if ($job->status !== 'done' || empty($job->result_path)) {
            return response()->json([
                'success' => false,
                'message' => 'File belum siap diunduh.',
            ], 409);
        }

        $absolutePath = storage_path('app/' . $job->result_path);
        if (!file_exists($absolutePath)) {
            return response()->json([
                'success' => false,
                'message' => 'File hasil export tidak ditemukan.',
            ], 404);
        }

        return response()->download($absolutePath);
    }

    public function runExportOpdBulanWithContext(array $context, ?string $savePath = null): ?string
    {
        $result = $this->calculateDataKehadiranPegawaiByOpdOptimized(
            $context['satuan_kerja'],
            $context['unit_kerja'],
            $context['tanggal_awal'],
            $context['tanggal_akhir'],
            $context['status_kepegawaian'],
            $context['tipe_pegawai']
        );
        $startedRender = microtime(true);
        $path = $this->exportRekapBasedOnType(
            $result['data'],
            $context['type'],
            $context,
            $savePath
        );
        $renderSeconds = round(microtime(true) - $startedRender, 4);

        $this->logExportMetrics('export_opd_bulan_async_worker', array_merge($result['metrics'], [
            'render_seconds' => $renderSeconds,
            'total_seconds' => round(microtime(true) - $result['metrics']['started_at'], 4),
        ]));

        return $path;
    }

    private function resolveExportOpdContext(Request $request): array
    {
        $bulan = (int) $request->get('bulan');
        $type = $request->get('type', 'pdf');
        $type = $type === 'excel' ? 'excel' : 'pdf';

        $tanggal_awal = date("Y-m-d", strtotime(date('Y-') . $bulan . '-01'));
        $tanggal_akhir = date("Y-m-d", strtotime(date('Y-') . $bulan . '-' . cal_days_in_month(CAL_GREGORIAN, $bulan, date('Y'))));

        if ($request->get('satuan_kerja')) {
            $satuan_kerja = $request->get('satuan_kerja');
            $nama_satuan_kerja = $request->get('nama_satuan_kerja');
            $unit_kerja = $request->get('id_unit_kerja');
            $nama_unit_kerja = $request->get('nama_unit_kerja');
        } else {
            $info = $this->infoSatuanKerja(Auth::user()->id_pegawai);
            $satuan_kerja = $info->id_satuan_kerja;
            $nama_satuan_kerja = $info->nama_satuan_kerja;
            $unit_kerja = $info->id_unit_kerja;
            $nama_unit_kerja = $info->nama_unit_kerja;
        }

        return [
            'bulan' => $bulan,
            'tanggal_awal' => $tanggal_awal,
            'tanggal_akhir' => $tanggal_akhir,
            'type' => $type,
            'satuan_kerja' => $satuan_kerja,
            'nama_satuan_kerja' => $nama_satuan_kerja,
            'unit_kerja' => $unit_kerja,
            'nama_unit_kerja' => $nama_unit_kerja,
            'status_kepegawaian' => $request->get('status_kepegawaian'),
            'tipe_pegawai' => $request->get('tipe_pegawai'),
        ];
    }

    private function exportRekapBasedOnType($data, string $type, array $context, ?string $savePath): ?string
    {
        if ($this->CheckOpd($context['unit_kerja']) && $context['tipe_pegawai'] == 'tenaga_pendidik' || $context['tipe_pegawai'] == 'tenaga_kesehatan_non_shift' || $context['tipe_pegawai'] == 'tenaga_kesehatan') {
            return $this->export_rekapitulasi_absen_guru(
                $data,
                $type,
                $context['bulan'],
                $context['nama_satuan_kerja'],
                $context['nama_unit_kerja'],
                $savePath
            );
        }

        return $this->export_rekapitulasi_absen(
            $data,
            $type,
            $context['bulan'],
            $context['nama_satuan_kerja'],
            $context['nama_unit_kerja'],
            $savePath
        );
    }

    private function logExportMetrics(string $channel, array $metrics): void
    {
        $payload = $metrics;
        unset($payload['started_at']);
        Log::info($channel, $payload);
    }

    private function loadPegawaiDataByOpd($satuan_kerja, $unit_kerja, $tanggal_awal, $status_kepegawaian, $tipe_pegawai)
    {
        $query_mutasi_check = DB::table('tb_pegawai')
            ->select(
                "tb_pegawai.id",
                "tb_pegawai.nama",
                'tb_pegawai.nip',
                'tb_unit_kerja.waktu_masuk',
                'tb_unit_kerja.waktu_keluar',
                'tb_pegawai.tipe_pegawai',
                'tb_unit_kerja.jumlah_shift',
                'tb_mutasi.tmt'
            )
            ->join('tb_mutasi', 'tb_mutasi.id_pegawai', '=', 'tb_pegawai.id')
            ->join('tb_jabatan', 'tb_jabatan.id', '=', 'tb_mutasi.id_jabatan_lama')
            ->join('tb_unit_kerja', 'tb_unit_kerja.id', '=', 'tb_jabatan.id_unit_kerja')
            ->where('tb_mutasi.id_satuan_kerja', $satuan_kerja)
            ->whereDate('tb_mutasi.tmt', '>=', $tanggal_awal);

        $query = DB::table('tb_pegawai')
            ->select(
                'tb_pegawai.id',
                'tb_pegawai.nama',
                'tb_pegawai.nip',
                'tb_unit_kerja.waktu_masuk',
                'tb_unit_kerja.waktu_keluar',
                'tb_pegawai.tipe_pegawai',
                'tb_unit_kerja.jumlah_shift',
                DB::raw('NULL as tmt')
            )
            ->join('tb_jabatan', 'tb_jabatan.id_pegawai', 'tb_pegawai.id')
            ->join('tb_master_jabatan', 'tb_jabatan.id_master_jabatan', '=', 'tb_master_jabatan.id')
            ->join('tb_unit_kerja', 'tb_jabatan.id_unit_kerja', '=', 'tb_unit_kerja.id')
            ->where('tb_pegawai.status', '1')
            ->orderBy('tb_master_jabatan.kelas_jabatan', 'DESC');

        $query->where("tb_jabatan.id_satuan_kerja", $satuan_kerja);
        if ($unit_kerja !== 'all') {
            $query->where('tb_jabatan.id_unit_kerja', $unit_kerja);
        }

        if (!is_null($status_kepegawaian)) {
            $query->where('status_kepegawaian', $status_kepegawaian);
        }

        if (!is_null($tipe_pegawai)) {
            $query->where('tipe_pegawai', $tipe_pegawai);
        }

        return $query->union($query_mutasi_check)->get();
    }

    private function calculateDataKehadiranPegawaiByOpdOptimized(
        $satuan_kerja,
        $unit_kerja,
        $tanggal_awal,
        $tanggal_akhir,
        $status_kepegawaian,
        $tipe_pegawai,
        $preloadedPegawaiRows = null
    ): array {
        $metrics = [
            'started_at' => microtime(true),
            'query_count' => 0,
            'load_pegawai_seconds' => 0,
            'load_absen_batch_seconds' => 0,
            'hitung_rekap_seconds' => 0,
        ];

        DB::flushQueryLog();
        DB::enableQueryLog();

        $startedLoadPegawai = microtime(true);
        $pegawaiRows = $preloadedPegawaiRows ?: $this->loadPegawaiDataByOpd(
            $satuan_kerja,
            $unit_kerja,
            $tanggal_awal,
            $status_kepegawaian,
            $tipe_pegawai
        );
        $metrics['load_pegawai_seconds'] = round(microtime(true) - $startedLoadPegawai, 4);

        $globalStart = Carbon::parse($tanggal_awal);
        $globalEnd = Carbon::parse($tanggal_akhir);

        foreach ($pegawaiRows as $item) {
            if (!is_null($item->tmt)) {
                $end = Carbon::parse($item->tmt)->subDay();
                if ($end->greaterThan($globalEnd)) {
                    $globalEnd = $end->copy();
                }
            }
        }

        $pegawaiIds = $pegawaiRows->pluck('id')->unique()->values()->all();
        $absenValidatedMap = [];
        $absenValidatedCount = [];
        $absenRawShiftMap = [];

        $startedLoadAbsen = microtime(true);
        if (!empty($pegawaiIds)) {
            $validatedRows = DB::table('tb_absen')
                ->select('id_pegawai', 'tanggal_absen', 'status', 'waktu_masuk', 'waktu_keluar', 'waktu_istirahat', 'waktu_masuk_istirahat', 'shift', 'status_masuk_istirahat')
                ->whereIn('id_pegawai', $pegawaiIds)
                ->where('validation', 1)
                ->whereBetween('tanggal_absen', [$tanggal_awal, $globalEnd->format('Y-m-d')])
                ->get();

            foreach ($validatedRows as $row) {
                $absenValidatedCount[$row->id_pegawai] = ($absenValidatedCount[$row->id_pegawai] ?? 0) + 1;
                $absenValidatedMap[$row->id_pegawai][$row->tanggal_absen] = [
                    'status' => $row->status,
                    'waktu_masuk' => $row->waktu_masuk,
                    'waktu_keluar' => $row->waktu_keluar,
                    'waktu_istirahat' => $row->waktu_istirahat,
                    'waktu_masuk_istirahat' => $row->waktu_masuk_istirahat,
                    'shift' => $row->shift,
                    'status_masuk_istirahat' => $row->status_masuk_istirahat,
                ];
            }

            $rawRows = DB::table('tb_absen')
                ->select('id_pegawai', 'tanggal_absen', 'shift')
                ->whereIn('id_pegawai', $pegawaiIds)
                ->whereBetween('tanggal_absen', [$globalStart->copy()->subDays(2)->format('Y-m-d'), $globalEnd->format('Y-m-d')])
                ->get();

            foreach ($rawRows as $row) {
                if (!isset($absenRawShiftMap[$row->id_pegawai][$row->tanggal_absen])) {
                    $absenRawShiftMap[$row->id_pegawai][$row->tanggal_absen] = ['shift' => $row->shift];
                }
            }
        }
        $metrics['load_absen_batch_seconds'] = round(microtime(true) - $startedLoadAbsen, 4);

        $cache = $this->preloadMasterCache($globalStart->format('Y-m-d'), $globalEnd->format('Y-m-d'));

        $startedHitung = microtime(true);
        $data = $pegawaiRows->map(function ($item) use ($tanggal_awal, $tanggal_akhir, $absenValidatedMap, $absenRawShiftMap, $cache) {
            if (!is_null($item->tmt)) {
                $tanggal_akhir_final = Carbon::parse($item->tmt)->subDay()->format('Y-m-d');
            } else {
                $tanggal_akhir_final = $tanggal_akhir;
            }

            $child = $this->calculatePegawaiKehadiranCached(
                (int) $item->id,
                $tanggal_awal,
                $tanggal_akhir_final,
                $item->waktu_masuk,
                $item->waktu_keluar,
                $item->tipe_pegawai,
                $item->jumlah_shift,
                $absenValidatedMap[(int) $item->id] ?? [],
                $absenRawShiftMap[(int) $item->id] ?? [],
                $cache,
                $absenValidatedCount[(int) $item->id] ?? 0
            );

            

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
        $metrics['hitung_rekap_seconds'] = round(microtime(true) - $startedHitung, 4);
        $metrics['query_count'] = count(DB::getQueryLog());
        DB::disableQueryLog();

        return ['data' => $data, 'metrics' => $metrics];
    }

    private function preloadMasterCache(string $tanggalAwal, string $tanggalAkhir): array
    {
        $liburIntervals = [
            'pegawai_administratif' => [],
            'tenaga_kesehatan_non_shift' => [],
            'tenaga_pendidik' => [],
        ];

        $liburRows = DB::table('tb_libur')
            ->select('tipe', 'tanggal_mulai', 'tanggal_selesai')
            ->where('tanggal_mulai', '<=', $tanggalAkhir)
            ->where('tanggal_selesai', '>=', $tanggalAwal)
            ->get();

        foreach ($liburRows as $row) {
            if (!isset($liburIntervals[$row->tipe])) {
                $liburIntervals[$row->tipe] = [];
            }
            $liburIntervals[$row->tipe][] = [$row->tanggal_mulai, $row->tanggal_selesai];
        }

        $ramadanIntervals = [];
        $ramadanRows = Ramadan::query()
            ->select('tanggal_mulai', 'tanggal_selesai')
            ->where('tanggal_mulai', '<=', $tanggalAkhir)
            ->where('tanggal_selesai', '>=', $tanggalAwal)
            ->get();
        foreach ($ramadanRows as $row) {
            $ramadanIntervals[] = [$row->tanggal_mulai, $row->tanggal_selesai];
        }

        $jamKerjaRows = JamKerja::query()
            ->select('tipe_pegawai', 'kategori', 'hari', 'shift', 'jumlah_shift', 'jam_masuk', 'jam_keluar')
            ->where('is_active', true)
            ->get();
        $jamKerjaMap = [];
        foreach ($jamKerjaRows as $row) {
            $jamKerjaMap[$row->tipe_pegawai . '|' . $row->kategori . '|' . $row->hari . '|' . ($row->shift ?? '-') . '|' . ($row->jumlah_shift ?? '-')] = $row;
        }

        $jamApelRows = JamApel::query()
            ->select('tipe_pegawai', 'jenis', 'shift', 'batas_akhir')
            ->where('is_active', true)
            ->get();
        $jamApelMap = [];
        foreach ($jamApelRows as $row) {
            $jamApelMap[$row->tipe_pegawai . '|' . $row->jenis . '|' . ($row->shift ?? '-')] = $row;
        }

        return [
            'libur_intervals' => $liburIntervals,
            'ramadan_intervals' => $ramadanIntervals,
            'jam_kerja_map' => $jamKerjaMap,
            'jam_apel_map' => $jamApelMap,
        ];
    }

    private function normalizeTipeLibur(string $tipePegawai): string
    {
        if ($tipePegawai === 'pegawai_administratif' || $tipePegawai === 'tenaga_kesehatan') {
            return 'pegawai_administratif';
        }
        if ($tipePegawai === 'tenaga_kesehatan_non_shift') {
            return 'tenaga_kesehatan_non_shift';
        }
        return 'tenaga_pendidik';
    }

    private function isDateInIntervals(string $tanggal, array $intervals): bool
    {
        foreach ($intervals as $interval) {
            if ($tanggal >= $interval[0] && $tanggal <= $interval[1]) {
                return true;
            }
        }
        return false;
    }

    private function isTanggalLiburCached(string $tanggal, string $tipePegawai, array $cache): bool
    {
        $normalized = $this->normalizeTipeLibur($tipePegawai);
        return $this->isDateInIntervals($tanggal, $cache['libur_intervals'][$normalized] ?? []);
    }

    private function isRamadanCached(string $tanggal, array $cache): bool
    {
        if ($this->isDateInIntervals($tanggal, $cache['ramadan_intervals'] ?? [])) {
            return true;
        }

        return $tanggal >= '2025-03-01' && $tanggal <= '2025-03-31';
    }

    private function getJamKerjaCached(string $tipePegawai, int $hari, ?string $shift, $jumlahShift, string $kategori, array $cache)
    {
        $key = $tipePegawai . '|' . $kategori . '|' . $hari . '|' . ($shift ?? '-') . '|' . ($jumlahShift ?? '-');
        return $cache['jam_kerja_map'][$key] ?? null;
    }

    private function getJamApelCached(string $tipePegawai, string $jenis, ?string $shift, array $cache)
    {
        $key = $tipePegawai . '|' . $jenis . '|' . ($shift ?? '-');
        return $cache['jam_apel_map'][$key] ?? null;
    }

    private function konvertWaktuCached($params, $waktu, $tanggal, $waktuDefaultAbsen, $tipePegawai, array $cache): int
    {
        $menit = 0;
        $dayOfWeek = (int) date('N', strtotime($tanggal));
        $kategori = $this->isRamadanCached($tanggal, $cache) ? 'ramadan' : 'reguler';
        $jamKerja = $this->getJamKerjaCached($tipePegawai, $dayOfWeek, null, null, $kategori, $cache);

        if ($waktu !== null) {
            if ($params === 'masuk') {
                if ($jamKerja) {
                    $waktuTetapAbsen = strtotime($jamKerja->jam_masuk);
                } else {
                    if (!$this->isRamadanCached($tanggal, $cache)) {
                        $waktuTetapAbsen = strtotime($waktuDefaultAbsen);
                    } else {
                        $waktuTetapAbsen = strtotime('08:00:00');
                    }

                    if (($tipePegawai == 'tenaga_pendidik' || $tipePegawai == 'tenaga_pendidik_non_guru')) {
                        if ($dayOfWeek == 1 || $dayOfWeek == 5) {
                            $waktuTetapAbsen = strtotime('07:00');
                        } elseif ($dayOfWeek == 2 || $dayOfWeek == 3 || $dayOfWeek == 4 || $dayOfWeek == 6) {
                            $waktuTetapAbsen = strtotime('07:30');
                        }
                    }
                }

                $diff = strtotime($waktu) - $waktuTetapAbsen;
            } else {
                if ($jamKerja) {
                    $waktuCheckout = $jamKerja->jam_keluar;
                } else {
                    if (!$this->isRamadanCached($tanggal, $cache)) {
                        $waktuCheckout = $waktuDefaultAbsen;
                    } else {
                        $waktuCheckout = '15:00:00';
                    }

                    if ($tipePegawai == 'pegawai_administratif' && Carbon::parse($tanggal)->dayOfWeek === Carbon::FRIDAY) {
                        $waktuCheckout = '15:30:00';
                    }

                    if ($tipePegawai == 'tenaga_pendidik') {
                        $waktuCheckout = '14:00:00';
                        if (Carbon::parse($tanggal)->dayOfWeek === Carbon::FRIDAY) {
                            $waktuCheckout = '11:30:00';
                        }
                        if ($this->isRamadanCached($tanggal, $cache)) {
                            $waktuCheckout = '13:30:00';
                            if (Carbon::parse($tanggal)->dayOfWeek === Carbon::FRIDAY) {
                                $waktuCheckout = '11:00:00';
                            }
                        }
                    }

                    if ($tipePegawai == 'tenaga_pendidik_non_guru') {
                        $waktuCheckout = '14:00:00';
                        if (Carbon::parse($tanggal)->dayOfWeek === Carbon::FRIDAY) {
                            $waktuCheckout = '11:30:00';
                        }
                        if ($this->isRamadanCached($tanggal, $cache)) {
                            $waktuCheckout = '13:30:00';
                            if (Carbon::parse($tanggal)->dayOfWeek === Carbon::FRIDAY) {
                                $waktuCheckout = '11:00:00';
                            }
                        }
                    }
                }

                $diff = strtotime($waktuCheckout) - strtotime($waktu);
            }

            if ($diff > 0) {
                $menit = (int) floor($diff / 60);
            }
        } else {
            $menit = 90;
        }

        return $menit;
    }

    private function konvertWaktuNakesCached($params, $waktu, $tanggal, $shift, $waktuTetap, $jumlahShift, $tipePegawai, array $cache): int
    {
        $menit = 0;
        $dayOfWeek = (int) date('N', strtotime($tanggal));
        $kategori = $this->isRamadanCached($tanggal, $cache) ? 'ramadan' : 'reguler';
        $jamKerja = $this->getJamKerjaCached($tipePegawai, $dayOfWeek, $shift, $jumlahShift, $kategori, $cache);

        $waktuAbsenDatang = '';
        $waktuAbsenPulang = '';

        if ($jamKerja) {
            $waktuAbsenDatang = $jamKerja->jam_masuk;
            $waktuAbsenPulang = $jamKerja->jam_keluar;

            $tanggalCarbon = Carbon::createFromFormat('Y-m-d', $tanggal);
            if ($tipePegawai == 'tenaga_kesehatan' && $shift == 'pagi' && $tanggalCarbon->isMonday()) {
                if ($params == 'masuk' && $jumlahShift == 3) {
                    $waktuAbsenDatang = $waktuTetap;
                }
            }
        } else {
            if ($tipePegawai == 'tenaga_kesehatan') {
                if ($shift == 'pagi') {
                    $waktuAbsenDatang = $jumlahShift == 3 ? '08:00:00' : '07:30:00';
                    $waktuAbsenPulang = $jumlahShift == 3 ? '14:00:00' : '17:00:00';
                } elseif ($shift == 'siang') {
                    $waktuAbsenDatang = '14:00:00';
                    $waktuAbsenPulang = '21:00:00';
                } else {
                    $waktuAbsenDatang = $jumlahShift == 3 ? '21:00:00' : '17:00:00';
                    $waktuAbsenPulang = $jumlahShift == 3 ? '08:00:00' : '07:30:00';
                }
            }
            if ($tipePegawai == 'tenaga_kesehatan_non_shift') {
                if ($dayOfWeek == 1 || $dayOfWeek == 5) {
                    $waktuAbsenDatang = '07:00:00';
                } elseif ($dayOfWeek == 2 || $dayOfWeek == 3 || $dayOfWeek == 4 || $dayOfWeek == 6) {
                    $waktuAbsenDatang = '07:30:00';
                }
                $waktuAbsenPulang = '14:00:00';
                if ($dayOfWeek == 5) {
                    $waktuAbsenPulang = '11:30:00';
                }
            }
        }

        if ($waktu !== null) {
            if ($params == 'masuk') {
                $diff = strtotime($waktu) - strtotime($waktuAbsenDatang);
            } else {
                $diff = strtotime($waktuAbsenPulang) - strtotime($waktu);
            }

            if ($diff > 0) {
                $menit = (int) floor($diff / 60);
            }
        } else {
            $menit = 90;
        }

        return $menit;
    }

    private function calculatePegawaiKehadiranCached(
        int $pegawai,
        string $tanggal_awal,
        string $tanggal_akhir,
        $waktu_tetap_masuk,
        $waktu_tetap_keluar,
        string $tipe_pegawai,
        $jumlah_shift,
        array $absenPerTanggal,
        array $rawShiftPerTanggal,
        array $cache,
        int $kehadiranKerjaCount
    ): array {
        $daftar_tanggal = [];
        $current_date = new Carbon($tanggal_awal);
        $jml_alfa = 0;
        $kmk_30 = $kmk_60 = $kmk_90 = $kmk_90_keatas = 0;
        $cpk_30 = $cpk_60 = $cpk_90 = $cpk_90_keatas = 0;
        $count_apel = $count_hadir = $count_sakit = $count_izin_cuti = $count_dinas_luar = $count_cuti = 0;
        $jml_tidak_apel = $jml_tidak_apel_hari_senin = $jml_tidak_hadir_berturut_turut = 0;

        while ($current_date->lte(Carbon::parse($tanggal_akhir))) {
            if ($tipe_pegawai == 'pegawai_administratif') {
                if ($current_date->dayOfWeek !== 6 && $current_date->dayOfWeek !== 0) {
                    if (!$this->isTanggalLiburCached($current_date->toDateString(), $tipe_pegawai, $cache)) {
                        $daftar_tanggal[] = $current_date->toDateString();
                    }
                }
            } elseif ($tipe_pegawai == 'tenaga_pendidik' || $tipe_pegawai == 'tenaga_pendidik_non_guru' || $tipe_pegawai == 'tenaga_kesehatan_non_shift') {
                if ($current_date->dayOfWeek !== 0) {
                    if (!$this->isTanggalLiburCached($current_date->toDateString(), $tipe_pegawai, $cache)) {
                        $daftar_tanggal[] = $current_date->toDateString();
                    }
                }
            } else {
                $daftar_tanggal[] = $current_date->toDateString();
            }
            $current_date->addDay();
        }

        $hasil_akhir = [];
        $hari_tidak_hadir_nakes = [];
        $jml_menit_terlambat_masuk_kerja = 0;
        $jml_menit_terlambat_pulang_kerja = 0;
        $selisih_waktu_masuk = 0;
        $selisih_waktu_pulang = 0;

        foreach ($daftar_tanggal as $tanggal) {
            if (isset($absenPerTanggal[$tanggal])) {
                $tanggalCarbon = Carbon::createFromFormat('Y-m-d', $tanggal);
                $absen = $absenPerTanggal[$tanggal];

                if ($tanggalCarbon->isMonday()) {
                    if ($absen['status'] !== 'apel' && $absen['status'] !== 'dinas luar' && $absen['status'] !== 'cuti' && $absen['status'] !== 'dinas luar' && $absen['status'] !== 'sakit') {
                        if ($tipe_pegawai == 'pegawai_administratif' && !$this->isRamadanCached($tanggalCarbon->toDateString(), $cache)) {
                            $jml_tidak_apel += 1;
                        } elseif ($tipe_pegawai == 'tenaga_kesehatan') {
                            if ($absen['shift'] == 'pagi' && !$this->isTanggalLiburCached($tanggalCarbon->toDateString(), $tipe_pegawai, $cache) && !$this->isRamadanCached($tanggalCarbon->toDateString(), $cache)) {
                                $jml_tidak_apel += 1;
                            }
                        }
                    }

                    if ($absen['status'] == 'apel') {
                        $count_apel += 1;
                    }
                }

                if (in_array($tanggalCarbon->format('l'), ['Tuesday', 'Wednesday', 'Thursday', 'Friday'])) {
                    if ($absen['status'] !== 'apel' && $absen['status'] !== 'dinas luar' && $absen['status'] !== 'cuti' && $absen['status'] !== 'dinas luar' && $absen['status'] !== 'sakit') {
                        if ($tipe_pegawai == 'pegawai_administratif' && !$this->isRamadanCached($tanggalCarbon->toDateString(), $cache)) {
                            $jml_tidak_apel_hari_senin += 1;
                        }
                    }
                }

                if ($absen['status'] == 'hadir' || $absen['status'] == 'apel') {
                    $count_hadir += 1;
                } elseif ($absen['status'] == 'sakit') {
                    $count_sakit += 1;
                } elseif ($absen['status'] == 'izin' || $absen['status'] == 'cuti') {
                    $count_izin_cuti += 1;
                }

                if ($absen['status'] == 'dinas luar') {
                    $count_dinas_luar += 1;
                }

                if ($absen['status'] == 'cuti') {
                    $count_cuti += 1;
                }

                if ($absen['status'] == 'apel' && $absen['waktu_masuk'] !== null) {
                    $shift_apel = ($tipe_pegawai == 'tenaga_kesehatan') ? $absen['shift'] : null;
                    $jamApel = $this->getJamApelCached($tipe_pegawai, 'reguler', $shift_apel, $cache);
                    if ($jamApel) {
                        $diff_apel = strtotime($absen['waktu_masuk']) - strtotime($jamApel->batas_akhir);
                        $selisih_waktu_masuk = ($diff_apel > 0) ? (int) floor($diff_apel / 60) : 0;
                    } else {
                        $selisih_waktu_masuk = 0;
                    }
                } elseif ($tipe_pegawai == 'pegawai_administratif' || $tipe_pegawai == 'tenaga_pendidik' || $tipe_pegawai == 'tenaga_pendidik_non_guru') {
                    $selisih_waktu_masuk = $this->konvertWaktuCached('masuk', $absen['waktu_masuk'], $tanggal, $waktu_tetap_masuk, $tipe_pegawai, $cache);
                } else {
                    $selisih_waktu_masuk = $this->konvertWaktuNakesCached('masuk', $absen['waktu_masuk'], $tanggal, $absen['shift'], $waktu_tetap_masuk, $jumlah_shift, $tipe_pegawai, $cache);
                }

                if ($tipe_pegawai == 'pegawai_administratif' || $tipe_pegawai == 'tenaga_pendidik' || $tipe_pegawai == 'tenaga_pendidik_non_guru') {
                    $selisih_waktu_masuk = $this->konvertWaktuCached('masuk', $absen['waktu_masuk'], $tanggal, $waktu_tetap_masuk, $tipe_pegawai, $cache);
                    $selisih_waktu_pulang = $this->konvertWaktuCached('keluar', $absen['waktu_keluar'], $tanggal, $waktu_tetap_keluar, $tipe_pegawai, $cache);
                } else {
                    $selisih_waktu_masuk = $this->konvertWaktuNakesCached('masuk', $absen['waktu_masuk'], $tanggal, $absen['shift'], $waktu_tetap_masuk, $jumlah_shift, $tipe_pegawai, $cache);
                    $selisih_waktu_pulang = $this->konvertWaktuNakesCached('keluar', $absen['waktu_keluar'], $tanggal, $absen['shift'], $waktu_tetap_keluar, $jumlah_shift, $tipe_pegawai, $cache);
                }

                if ($absen['waktu_masuk'] !== null) {
                    $jml_menit_terlambat_masuk_kerja += $selisih_waktu_masuk;
                }
                if ($tanggal !== date('Y-m-d')) {
                    $jml_menit_terlambat_pulang_kerja += $selisih_waktu_pulang;
                }

                if ($absen['status'] !== 'cuti' && $absen['status'] !== 'dinas luar' && $absen['status'] !== 'sakit') {
                    if ($selisih_waktu_masuk >= 1 && $selisih_waktu_masuk <= 30) {
                        $kmk_30 += 1;
                    } elseif ($selisih_waktu_masuk >= 31 && $selisih_waktu_masuk <= 60) {
                        $kmk_60 += 1;
                    } elseif ($selisih_waktu_masuk >= 61 && $selisih_waktu_masuk <= 90) {
                        $kmk_90 += 1;
                    } elseif ($selisih_waktu_masuk >= 91) {
                        $kmk_90_keatas += 1;
                    }

                    if ($selisih_waktu_pulang >= 1 && $selisih_waktu_pulang <= 30) {
                        $cpk_30 += 1;
                    } elseif ($selisih_waktu_pulang >= 31 && $selisih_waktu_pulang <= 60) {
                        $cpk_60 += 1;
                    } elseif ($selisih_waktu_pulang >= 61 && $selisih_waktu_pulang <= 90) {
                        $cpk_90 += 1;
                    } elseif ($selisih_waktu_pulang >= 91) {
                        $cpk_90_keatas += 1;
                    }
                }

                $waktu_pulang = $absen['waktu_keluar'];
                if ($waktu_pulang) {
                    $keterangan_pulang = $selisih_waktu_pulang > 0 ? 'Cepat ' . $selisih_waktu_pulang . ' menit' : 'Tepat waktu';
                } else {
                    if (Carbon::now()->greaterThan(Carbon::parse('22:00:00'))) {
                        $waktu_pulang = '14:00:00';
                        $keterangan_pulang = 'Cepat 90 menit';
                    } else {
                        $waktu_pulang = 'Belum Absen';
                        $keterangan_pulang = 'Belum Absen';
                    }
                }

                $hasil_akhir[] = [
                    'tanggal_absen' => $tanggal,
                    'status' => $absen['status'],
                    'waktu_masuk' => $absen['waktu_masuk'],
                    'waktu_keluar' => $waktu_pulang,
                    'waktu_istirahat' => $absen['waktu_istirahat'],
                    'waktu_masuk_istirahat' => $absen['waktu_masuk_istirahat'],
                    'keterangan_masuk' => $selisih_waktu_masuk > 0 ? 'Telat ' . $selisih_waktu_masuk . ' menit' : 'Tepat waktu',
                    'keterangan_pulang' => $keterangan_pulang,
                    'shift' => $absen['shift'],
                    'status_masuk_istirahat' => $absen['status_masuk_istirahat'],
                ];
            } else {
                $tanggalCarbon = Carbon::createFromFormat('Y-m-d', $tanggal);
                if ($tanggalCarbon->isWeekday() && !$tanggalCarbon->isTomorrow() && !$this->isTanggalLiburCached($tanggalCarbon->toDateString(), $tipe_pegawai, $cache)) {
                    $jml_tidak_hadir_berturut_turut += 1;
                } else {
                    $jml_tidak_hadir_berturut_turut = 0;
                }

                $status_ = 'Tanpa Keterangan';
                if (strtotime($tanggal) > strtotime(date('Y-m-d'))) {
                    $status_ = 'Belum absen';
                } else {
                    if ($tipe_pegawai == 'pegawai_administratif' || $tipe_pegawai == 'tenaga_pendidik' || $tipe_pegawai == 'tenaga_pendidik_non_guru') {
                        $jml_alfa += 1;
                    } else {
                        $mingguKe = $tanggalCarbon->weekOfMonth;
                        $tanggalSebelumnya = date('Y-m-d', strtotime($tanggal . ' -1 day'));
                        $tanggalSebelumnya2 = date('Y-m-d', strtotime($tanggal . ' -2 day'));
                        $check_last_day = $rawShiftPerTanggal[$tanggalSebelumnya] ?? null;
                        $check_last_day2 = $rawShiftPerTanggal[$tanggalSebelumnya2] ?? null;

                        if ($tipe_pegawai == 'tenaga_kesehatan') {
                            if (!is_null($check_last_day) && ($check_last_day['shift'] ?? null) == 'malam') {
                                $status_ = 'Lepas Jaga / Lepas Piket';
                            } elseif (!is_null($check_last_day2) && ($check_last_day2['shift'] ?? null) == 'malam') {
                                $status_ = 'Lepas Jaga / Lepas Piket';
                            } else {
                                $hari_tidak_hadir_nakes[] = ['tanggal' => $tanggal, 'minggu' => $mingguKe];
                                $status_ = '-';
                            }
                        }
                    }
                }

                $hasil_akhir[] = [
                    'tanggal_absen' => $tanggal,
                    'status' => $status_,
                    'waktu_masuk' => '-',
                    'waktu_keluar' => '-',
                    'waktu_istirahat' => '-',
                    'waktu_masuk_istirahat' => '-',
                    'keterangan_masuk' => '-',
                    'keterangan_pulang' => '-',
                    'shift' => '-',
                    'status_masuk_istirahat' => '-',
                ];
            }
        }

        $jumlah_alfa_nakes = 0;
        if ($tipe_pegawai == 'tenaga_kesehatan') {
            $jumlahHariMingguSama = array_count_values(array_column($hari_tidak_hadir_nakes, 'minggu'));
            foreach ($jumlahHariMingguSama as $minggu => $jumlah) {
                if ($jumlah > 1) {
                    $jumlah_alfa_nakes += $jumlah - 1;
                }
            }
            $jml_alfa = $jumlah_alfa_nakes;
        }

        $potongan_masuk_kerja = ($kmk_30 * 0.5) + ($kmk_60 * 1) + ($kmk_90 * 1.25) + ($kmk_90_keatas * 1.5);
        $potongan_pulang_kerja = ($cpk_30 * 0.5) + ($cpk_60 * 1) + ($cpk_90 * 1.25) + ($cpk_90_keatas * 1.5);
        $potongan_tanpa_keterangan = $jml_alfa * 3;
        $potongan_apel = ($jml_tidak_apel * 2) + ($jml_tidak_apel_hari_senin * 0.25);
        $jml_potongan_kehadiran_kerja = $potongan_tanpa_keterangan + $potongan_masuk_kerja + $potongan_pulang_kerja + $potongan_apel;

        return [
            'data' => $hasil_akhir,
            'jml_hari_kerja' => count($hasil_akhir),
            'kehadiran_kerja' => $kehadiranKerjaCount,
            'tanpa_keterangan' => $jml_alfa,
            'potongan_tanpa_keterangan' => $potongan_tanpa_keterangan,
            'potongan_masuk_kerja' => $potongan_masuk_kerja,
            'potongan_pulang_kerja' => $potongan_pulang_kerja,
            'potongan_apel' => $potongan_apel,
            'jml_potongan_kehadiran_kerja' => $jml_potongan_kehadiran_kerja,
            'jml_apel' => $count_apel,
            'jml_hadir' => $count_hadir,
            'jml_sakit' => $count_sakit,
            'jml_cuti' => $count_cuti,
            'jml_izin_cuti' => $count_izin_cuti,
            'jml_dinas_luar' => $count_dinas_luar,
            'kmk_30' => $kmk_30,
            'kmk_60' => $kmk_60,
            'kmk_90' => $kmk_90,
            'kmk_90_keatas' => $kmk_90_keatas,
            'cpk_30' => $cpk_30,
            'cpk_60' => $cpk_60,
            'cpk_90' => $cpk_90,
            'cpk_90_keatas' => $cpk_90_keatas,
            'jml_tidak_apel' => $jml_tidak_apel,
            'jml_tidak_apel_hari_senin' => $jml_tidak_apel_hari_senin,
            'jml_tidak_hadir_berturut_turut' => $jml_tidak_hadir_berturut_turut,
            'jml_menit_terlambat_masuk_kerja' => $jml_menit_terlambat_masuk_kerja,
            'jml_menit_terlambat_pulang_kerja' => $jml_menit_terlambat_pulang_kerja,
        ];
    }

    public function export_rekapitulasi_absen($data, $type, $tanggal_awal, $tanggal_akhir, $satuan_kerja, ?string $savePath = null)
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

            $total_ > 35 || $value->tanpa_keterangan > 3 ? $keterangan = 'TMS' : $keterangan = 'MS';
            $sheet->setCellValue('AH' . $cell, $value->jml_potongan_kehadiran_kerja);
            $sheet->setCellValue('AI' . $cell, $keterangan);

            if ($total_ > 35 || $value->tanpa_keterangan > 3) {
                $sheet->getStyle('AI' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('F44336');
            } else {
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
        $sheet->getStyle('AG8:AG' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('f7f702');

        $sheet->getStyle('A6:AI' . $cell)->applyFromArray($border);
        $sheet->getStyle('A:AI')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A:AI')->getAlignment()->setVertical('center');
        $sheet->getStyle('B7:B' . $cell)->getAlignment()->setHorizontal('rigth');
        $sheet->getStyle('A3:AI4')->getAlignment()->setHorizontal('rigth');

        $periode = $tanggal_awal . ' s/d ' . $tanggal_akhir;
        if ($type == 'excel') {
            $writer = new Xlsx($spreadsheet);
            if (is_null($savePath)) {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                $filename = "Laporan Absen {$satuan_kerja} {$periode}.xlsx";
                header("Content-Disposition: attachment;filename=\"$filename\"");
            }
        } else {
            $spreadsheet->getActiveSheet()->getHeaderFooter()
                ->setOddHeader('&C&H' . url()->current());
            $spreadsheet->getActiveSheet()->getHeaderFooter()
                ->setOddFooter('&L&B &RPage &P of &N');
            $class = \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf::class;
            \PhpOffice\PhpSpreadsheet\IOFactory::registerWriter('Pdf', $class);
            if (is_null($savePath)) {
                header('Content-Type: application/pdf');
                header('Cache-Control: max-age=0');
            }
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Pdf');
        }

        if (is_null($savePath)) {
            $writer->save('php://output');
            return null;
        }

        $writer->save($savePath);
        return $savePath;
    }

    public function export_rekapitulasi_absen_guru($data, $type, $tanggal_awal, $tanggal_akhir, $satuan_kerja, ?string $savePath = null)
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
            if (is_null($savePath)) {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                $filename = "Laporan Absen {$satuan_kerja} {$periode}.xlsx";
                header("Content-Disposition: attachment;filename=\"$filename\"");
            }
        } else {
            $spreadsheet->getActiveSheet()->getHeaderFooter()
                ->setOddHeader('&C&H' . url()->current());
            $spreadsheet->getActiveSheet()->getHeaderFooter()
                ->setOddFooter('&L&B &RPage &P of &N');
            $class = \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf::class;
            \PhpOffice\PhpSpreadsheet\IOFactory::registerWriter('Pdf', $class);
            if (is_null($savePath)) {
                header('Content-Type: application/pdf');
                header('Cache-Control: max-age=0');
            }
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Pdf');
        }

        if (is_null($savePath)) {
            $writer->save('php://output');
            return null;
        }

        $writer->save($savePath);
        return $savePath;
    }
}
