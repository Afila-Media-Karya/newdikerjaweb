<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\PerangkatDaerah;
use DB;
use App\Traits\General;
use App\Models\SasaranKinerja;
use App\Models\UnitKerja;
use Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Carbon\Carbon;

class LaporanListPegawaiController extends BaseController
{
    use General;
    public function export()
    {
        $satuan_kerja = request('satuan_kerja');
        $unit_kerja = request('unit_kerja');
        $jenis_kelamin = request('jenis_kelamin');
        $agama = request('agama');
        $pendidikan = request('pendidikan');
        $golongan = request('golongan');
        $jenis_jabatan = request('jenis_jabatan');
        $status_kepegawaian = request('status_kepegawaian');
        $tipe_pegawai = request('tipe_pegawai');
        $eselon = request('eselon');
        $type = request('type');
        $role = hasRole();

        $data = array();
        $query = DB::table('tb_pegawai')
            ->LeftJoin('tb_satuan_kerja', 'tb_pegawai.id_satuan_kerja', 'tb_satuan_kerja.id')
            ->LeftJoin('tb_jabatan', 'tb_jabatan.id_pegawai', 'tb_pegawai.id')
            ->LeftJoin('tb_master_jabatan', 'tb_jabatan.id_master_jabatan', 'tb_master_jabatan.id')
            ->select(
                'tb_pegawai.id',
                'tb_pegawai.uuid',
                'tb_pegawai.nip',
                'tb_pegawai.golongan',
                'tb_pegawai.nama',
                'tb_pegawai.jenis_kelamin',
                'tb_pegawai.status',
                'tb_pegawai.agama',
                'tb_pegawai.pendidikan',
                'tb_master_jabatan.nama_jabatan',
                'tb_master_jabatan.jenis_jabatan',
                'tb_satuan_kerja.nama_satuan_kerja',
                'tb_master_jabatan.level_jabatan',
                'tb_pegawai.id_satuan_kerja'
            )
            ->where('tb_pegawai.status', '1')
            ->orderBy('tb_master_jabatan.kelas_jabatan', 'DESC');

        $dinas = 'KABUPATEN ENREKANG';
        if (!is_null($satuan_kerja) && $satuan_kerja !== 'semua') {
            $query->where('tb_pegawai.id_satuan_kerja', $satuan_kerja);
            $dinas = PerangkatDaerah::where('id', $satuan_kerja)->first();
            $dinas = $dinas ? strtoupper($dinas->nama_satuan_kerja) . ' KABUPATEN ENREKANG' : '';
        }

        if (!is_null($unit_kerja) && $unit_kerja !== 'semua') {
            $query->where('tb_jabatan.id_unit_kerja', $unit_kerja);
        }

        if (!is_null($jenis_kelamin) && $jenis_kelamin !== 'semua') {
            $query->where('tb_pegawai.jenis_kelamin', $jenis_kelamin);
        }

        if (!is_null($agama) && $agama !== 'semua') {
            $query->where('tb_pegawai.agama', $satuan_kerja);
        }

        if (!is_null($pendidikan) && $pendidikan !== 'semua') {
            $query->where('tb_pegawai.pendidikan', $pendidikan);
        }

        if (!is_null($golongan) && $golongan !== 'semua') {
            $query->where('tb_pegawai.golongan', $golongan);
        }

        if (!is_null($jenis_jabatan) && $jenis_jabatan !== 'semua') {
            $query->where('tb_master_jabatan.jenis_jabatan', $jenis_jabatan);
        }

        if (!is_null($status_kepegawaian) && $status_kepegawaian !== 'semua') {
            $query->where('tb_pegawai.status_kepegawaian', $status_kepegawaian);
        }

        if (!is_null($tipe_pegawai) && $tipe_pegawai !== 'semua') {
            $query->where('tb_pegawai.tipe_pegawai', $tipe_pegawai);
        }

        if (!is_null($eselon) && $eselon !== 'semua') {
            $query->where('tb_pegawai.eselon', $eselon);
        }

        if ($role['guard'] == 'web') {
            $satuan_kerja = $this->infoSatuanKerja(Auth::user()->id_pegawai);
            $query->where('tb_pegawai.id_satuan_kerja', $satuan_kerja->id_satuan_kerja);

            // if ($role['role'] == '3') {
                $query->where('tb_jabatan.id_unit_kerja', $satuan_kerja->id_unit_kerja);
            // }
        }

        $data = $query->get();

        return $this->export_list_pegawai($data, $type, $dinas);
    }

    public function export_list_pegawai($data, $type, $dinas)
    {
        // dd($data);
        $spreadsheet = new Spreadsheet();

        $spreadsheet->getProperties()->setCreator('BKPSDM ENREKANG')
            ->setLastModifiedBy('BKPSDM ENREKANG')
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

        // //Margin PDF
        $spreadsheet->getActiveSheet()->getPageMargins()->setTop(0.3);
        $spreadsheet->getActiveSheet()->getPageMargins()->setRight(0.3);
        $spreadsheet->getActiveSheet()->getPageMargins()->setLeft(0.5);
        $spreadsheet->getActiveSheet()->getPageMargins()->setBottom(0.3);

        // Load a logo image
        $spreadsheet->getActiveSheet()->mergeCells('A1:I1');
        $logoPath = 'admin/assets/media/logos/enrekang.png';
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Paid');
        $drawing->setDescription('Paid');
        $drawing->setPath($logoPath);
        $drawing->setCoordinates('A1');
        $drawing->setHeight(70);
        $drawing->setWorksheet($spreadsheet->getActiveSheet());

        $sheet->setCellValue('A2', 'DAFTAR PEGAWAI')->mergeCells('A2:I2');
        $sheet->setCellValue('A3', strtoupper($dinas))->mergeCells('A3:I3');
        $sheet->setCellValue('A4', ' ')->mergeCells('A4:I4');
        
        $sheet->setCellValue('A5', 'No');
        $sheet->setCellValue('B5', 'Nama / NIP')->getColumnDimension('B')->setWidth(40);
        $sheet->setCellValue('C5', 'Nama Jabatan')->getColumnDimension('C')->setWidth(30);
        $sheet->setCellValue('D5', 'Jenis Jabatan')->getColumnDimension('D')->setWidth(30);
        $sheet->setCellValue('E5', 'Jenis Kelamin')->getColumnDimension('E')->setWidth(15);
        $sheet->setCellValue('F5', 'Agama')->getColumnDimension('F')->setWidth(15);
        $sheet->setCellValue('G5', 'Pendidikan')->getColumnDimension('G')->setWidth(20);
        $sheet->setCellValue('H5', 'Golongan')->getColumnDimension('H')->setWidth(20);
        $sheet->setCellValue('I5', 'Satuan Kerja')->getColumnDimension('I')->setWidth(35);
        $sheet->getStyle('A5:I5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('E1F5FE');

        $spreadsheet->getActiveSheet()->getRowDimension('5')->setRowHeight(25);
        $sheet->getStyle('A:I')->getAlignment()->setWrapText(true);
        $sheet->getStyle('A1:I5')->getFont()->setBold(true);

        $cell = $cell_head = 6;
        $no = 1;
        $golongan = '';
        foreach ($data as $key => $value) {
            $value->golongan !== null ? $golongan = $value->golongan : $golongan = '';
            $sheet->setCellValue('A' . $cell, $no++);
            $sheet->setCellValue('B' . $cell, $value->nama . PHP_EOL . $value->nip);
            $sheet->setCellValue('C' . $cell, $value->nama_jabatan);
            $sheet->setCellValue('D' . $cell, $value->jenis_jabatan);
            $sheet->setCellValue('E' . $cell, $value->jenis_kelamin);
            $sheet->setCellValue('F' . $cell, $value->agama);
            $sheet->setCellValue('G' . $cell, $value->pendidikan);
            $sheet->setCellValue('H' . $cell, $golongan);
            $sheet->setCellValue('I' . $cell, $value->nama_satuan_kerja);
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
        $sheet->getStyle('A5:I' . $cell)->applyFromArray($border_row);
        $sheet->getStyle('A1:I' . $cell)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1:I' . $cell)->getAlignment()->setVertical('center');
        $sheet->getStyle('B6:C' . $cell)->getAlignment()->setHorizontal('rigth');


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
}
