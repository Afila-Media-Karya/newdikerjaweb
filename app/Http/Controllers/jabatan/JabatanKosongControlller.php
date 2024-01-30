<?php

namespace App\Http\Controllers\jabatan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Requests\JabatanKosongRequest;
use App\Models\Jabatan;
use DB;
use App\Traits\General;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class JabatanKosongControlller extends BaseController
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
                'label' => 'Jabatan Kosong',
                'url' => '#'
            ],
        ];
    }

    public function datatable()
    {
        $data = array();
        $satuan_kerja = intval(request('satuan_kerja'));

        $query = DB::table('tb_jabatan as tb_jabatan1')
            ->leftJoin('tb_satuan_kerja', 'tb_jabatan1.id_satuan_kerja', '=', 'tb_satuan_kerja.id')
            ->leftJoin('tb_master_jabatan as tb_master_jabatan1', 'tb_jabatan1.id_master_jabatan', '=', 'tb_master_jabatan1.id')
            ->leftJoin('tb_jabatan as tb_jabatan2', 'tb_jabatan1.id_parent', '=', 'tb_jabatan2.id')
            ->leftJoin('tb_pegawai', 'tb_jabatan1.id_pegawai', '=', 'tb_pegawai.id')
            ->leftJoin('tb_master_jabatan as tb_master_jabatan2', 'tb_jabatan2.id_master_jabatan', '=', 'tb_master_jabatan2.id')
            ->select('tb_jabatan1.id', 'tb_jabatan1.uuid', 'tb_master_jabatan1.nama_jabatan as jabatan', 'tb_master_jabatan2.nama_jabatan as atasan_langsung', 'tb_satuan_kerja.nama_satuan_kerja','tb_jabatan1.status as status_jabatan')
            ->orderBy('tb_jabatan2.id_parent', 'DESC')
            ->orderBy('tb_master_jabatan1.kelas_jabatan', 'ASC')
            ->whereNotNull('tb_jabatan1.id_parent')
            ->whereNull('tb_jabatan1.id_pegawai');

        if (hasRole()['guard'] == 'web' && hasRole()['role'] == '3') {
            $query->where('tb_jabatan1.id_unit_kerja', $satuan_kerja);
        } else {
            if ($satuan_kerja > 0) {
                $query->where('tb_jabatan1.id_satuan_kerja', $satuan_kerja);
            }
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
        if (hasRole()['guard'] == 'web') {
            if (hasRole()['role'] == '1') {
                $satuan_kerja_user = $this->infoSatuanKerja(hasRole()['id_pegawai'])->id_satuan_kerja;
            } else {
                $satuan_kerja_user = $this->infoSatuanKerja(hasRole()['id_pegawai'])->id_unit_kerja;
            }
        }
        return view('jabatan.jabatankosong',compact('module','satuan_kerja','satuan_kerja_user','unit_kerja'));
    }

    public function cetak()
    {
        $data = array();
        $satuan_kerja = intval(request('satuan_kerja'));
        $type = request('type');

        // $query = DB::table('tb_jabatan')
        // ->join('tb_master_jabatan as jabatan1','tb_jabatan.id_master_jabatan','=','jabatan1.id')
        // ->leftJoin('tb_master_jabatan as jabatan2', 'jabatan1.id_parent', '=', 'jabatan2.id')
        // ->join('tb_satuan_kerja','tb_jabatan.id_satuan_kerja','=','tb_satuan_kerja.id')
        // ->select('tb_jabatan.id','tb_jabatan.uuid','jabatan1.nama_jabatan as jabatan','jabatan2.nama_jabatan as atasan_langsung','tb_jabatan.status','tb_satuan_kerja.nama_satuan_kerja')
        // ->orderBy('tb_satuan_kerja.nama_satuan_kerja','DESC')
        // ->orderBy('jabatan2.id_parent','DESC')
        // ->whereNull('tb_jabatan.id_pegawai');

        $query = DB::table('tb_jabatan as tb_jabatan1')
            ->leftJoin('tb_satuan_kerja', 'tb_jabatan1.id_satuan_kerja', '=', 'tb_satuan_kerja.id')
            ->leftJoin('tb_master_jabatan as tb_master_jabatan1', 'tb_jabatan1.id_master_jabatan', '=', 'tb_master_jabatan1.id')
            ->leftJoin('tb_jabatan as tb_jabatan2', 'tb_jabatan1.id_parent', '=', 'tb_jabatan2.id')
            ->leftJoin('tb_pegawai', 'tb_jabatan1.id_pegawai', '=', 'tb_pegawai.id')
            ->leftJoin('tb_master_jabatan as tb_master_jabatan2', 'tb_jabatan2.id_master_jabatan', '=', 'tb_master_jabatan2.id')
            ->select('tb_jabatan1.id', 'tb_jabatan1.uuid', 'tb_master_jabatan1.nama_jabatan as jabatan', 'tb_master_jabatan2.nama_jabatan as atasan_langsung', 'tb_satuan_kerja.nama_satuan_kerja', 'tb_jabatan1.status')
            // ->orderBy('tb_jabatan2.id_parent', 'DESC')
            // ->orderBy('tb_master_jabatan1.kelas_jabatan', 'ASC')
            ->orderBy('tb_satuan_kerja.kode_satuan_kerja', 'ASC')
            ->orderBy('tb_master_jabatan1.kelas_jabatan', 'DESC')
            ->whereNull('tb_jabatan1.id_pegawai');

        // if ($satuan_kerja > 0) {
        //     $query->where('tb_jabatan.id_satuan_kerja',$satuan_kerja);
        // }

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

        // //Margin PDF
        $spreadsheet->getActiveSheet()->getPageMargins()->setTop(0.3);
        $spreadsheet->getActiveSheet()->getPageMargins()->setRight(0.3);
        $spreadsheet->getActiveSheet()->getPageMargins()->setLeft(0.5);
        $spreadsheet->getActiveSheet()->getPageMargins()->setBottom(0.3);

        // Load a logo image
        $spreadsheet->getActiveSheet()->mergeCells('A1:D1');
        $logoPath = 'admin/assets/media/logos/BULUKUMBA.png';
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Paid');
        $drawing->setDescription('Paid');
        $drawing->setPath($logoPath);
        $drawing->setCoordinates('A1');
        $drawing->setHeight(70);
        $drawing->setWorksheet($spreadsheet->getActiveSheet());

        $sheet->setCellValue('A2', 'DAFTAR JABATAN KOSONG')->mergeCells('A2:D2');
        $sheet->setCellValue('A3', 'PEMERINTAH KABUPATEN BULUKUMBA')->mergeCells('A3:D3');
        $sheet->setCellValue('A4', ' ')->mergeCells('A4:D4');

        $sheet->setCellValue('A5', 'NO');
        $sheet->setCellValue('B5', 'JABATAN')->getColumnDimension('B')->setWidth(40);
        $sheet->setCellValue('C5', 'ATASAN LANGSUNG')->getColumnDimension('C')->setWidth(50);
        $sheet->setCellValue('D5', 'SATUAN KERJA')->getColumnDimension('D')->setWidth(50);
        $sheet->getStyle('A5:D5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('E1F5FE');

        $spreadsheet->getActiveSheet()->getRowDimension('5')->setRowHeight(25);
        $sheet->getStyle('A:D')->getAlignment()->setWrapText(true);
        $sheet->getStyle('A1:D5')->getFont()->setBold(true);

        $cell = $cell_head = 6;
        $no = 1;
        foreach ($data as $key => $value) {
            $sheet->setCellValue('A' . $cell, $no++);
            $sheet->setCellValue('B' . $cell, $value->jabatan);
            $sheet->setCellValue('C' . $cell, $value->atasan_langsung);
            $sheet->setCellValue('D' . $cell, $value->nama_satuan_kerja);
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
        $sheet->getStyle('A5:D' . $cell)->applyFromArray($border_row);
        $sheet->getStyle('A1:D' . $cell)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1:D' . $cell)->getAlignment()->setVertical('center');
        $sheet->getStyle('B6:C' . $cell)->getAlignment()->setHorizontal('rigth');
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

    public function update(JabatanKosongRequest $request, $params)
    {
        $data = array();
        try {

            $promise = true;
            $check_jabatan = $this->checkJabatanDefinitif($request->id_pegawai);
            // dd($check_jabatan);
            $data = Jabatan::where('uuid', $params)->first();

            if ($request->status_jabatan !== 'plt') {
                if ($data->status !== 'plt') {
                    if ($check_jabatan) {
                        return $this->sendError($check_jabatan->nama . ' mengisi jabatan ' . $check_jabatan->nama_jabatan, 'Maaf tidak bisa mengatur jabatan!', 200);
                    }
                }
            }
            
            $data->id_pegawai = $request->id_pegawai;
            $data->status = $request->status_jabatan;
            $data->save();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Jabatan Update success');
    }

    public function show($params)
    {
        $data = array();
        try {
            $data = DB::table('tb_jabatan as tb_jabatan1')
                ->leftJoin('tb_jabatan as tb_jabatan2', 'tb_jabatan1.id_parent', '=', 'tb_jabatan2.id')
                ->join('tb_master_jabatan as masterjabatan1', 'tb_jabatan1.id_master_jabatan', '=', 'masterjabatan1.id')
                ->join('tb_master_jabatan as masterjabatan2', 'tb_jabatan2.id_master_jabatan', '=', 'masterjabatan2.id')
                // ->leftJoin('tb_master_jabatan as jabatan2', 'jabatan1.id_parent', '=', 'jabatan2.id')

                ->join('tb_satuan_kerja', 'tb_jabatan1.id_satuan_kerja', '=', 'tb_satuan_kerja.id')
                ->select('tb_jabatan1.id', 'tb_jabatan1.uuid', 'masterjabatan1.nama_jabatan as jabatan', 'masterjabatan2.nama_jabatan as atasan_langsung', 'tb_jabatan1.status', 'tb_jabatan1.id_satuan_kerja', 'tb_satuan_kerja.nama_satuan_kerja', 'tb_jabatan1.id_unit_kerja')
                ->where('tb_jabatan1.uuid', $params)
                ->first();
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

    public function option($params)
    {
        $data = array();
        try {
            $data = $this->optionJabatanKosong($params);
        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'option jabatan success');
    }
}
