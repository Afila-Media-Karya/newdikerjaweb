<?php

namespace App\Http\Controllers\jabatan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\Jabatan;
use DB;
use App\Traits\General;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class JabatanPltController extends BaseController
{
    use General;
    public function breadcumb(){
        return [
            [
                'label' => 'Jabatan',
                'url' => '#'
            ],
            [
                'label' => 'Jabatan PLT',
                'url' => '#'
            ],
        ];
    }

    public function datatable(){
        $data = array();
        $satuan_kerja = intval(request('satuan_kerja'));

        $query = DB::table('tb_jabatan')->join('tb_pegawai','tb_jabatan.id_pegawai','=','tb_pegawai.id')->join('tb_master_jabatan as jabatan1','tb_jabatan.id_master_jabatan','=','jabatan1.id')->join('tb_master_jabatan as jabatan2', 'jabatan1.id_parent', '=', 'jabatan2.id')->join('tb_satuan_kerja','tb_jabatan.id_satuan_kerja','=','tb_satuan_kerja.id')->select('tb_jabatan.id','tb_jabatan.uuid','tb_pegawai.uuid as pegawai_uuid','jabatan1.nama_jabatan as jabatan','jabatan2.nama_jabatan as atasan_langsung','tb_jabatan.status','tb_satuan_kerja.nama_satuan_kerja','tb_pegawai.nama as pejabat')->orderBy('jabatan2.id_parent','DESC')->where('tb_jabatan.status','plt');
        
        if ($satuan_kerja > 0) {
            $query->where('tb_jabatan.id_satuan_kerja',$satuan_kerja);
        }

        $data = $query->get();

        return $this->sendResponse($data, 'Jabatan Fetched Success');
    }

    public function index(){
        $module = $this->breadcumb();
        $satuan_kerja_user = '';
        if(hasRole()['guard'] == 'web' && hasRole()['role'] == '1'){
            $satuan_kerja_user = $this->infoSatuanKerja(hasRole()['id_pegawai']);
        }
        return view('jabatan.jabatanplt',compact('module','satuan_kerja_user'));
    }

    public function detail($params){

        $module =  [
            [
                'label' => 'Jabatan',
                'url' => '#'
            ],
            [
                'label' => 'Jabatan PLT',
                'url' => '/pegawai/pegawai-masuk'
            ],
            [
                'label' => 'Detail Jabatan PLT',
                'url' => '#'
            ],
        ];

        $jabatan_definitif = '-';
        $jabatan_plt = '-';
        $data = DB::table('tb_pegawai')->where('tb_pegawai.uuid',$params)->first();
        if ($data) {
            // dd($data);
            $jabatan = DB::table('tb_jabatan')
            ->join('tb_master_jabatan','tb_jabatan.id_master_jabatan','=','tb_master_jabatan.id')
            ->select(
                'tb_master_jabatan.nama_jabatan',
                'tb_jabatan.status')->where('tb_jabatan.id_pegawai',$data->id)->get();
            // dd($jabatan);
            foreach ($jabatan as $key => $value) {
                $value->status == 'definitif'? $jabatan_definitif = $value->nama_jabatan : $jabatan_plt = $value->nama_jabatan;
            }

            $data->jabatan_definitif = $jabatan_definitif;
            $data->jabatan_plt = $jabatan_plt;

        }else{
            return redirect()->back()->withErrors(['error' => 'Belum bisa membuka detail jabatan plt, pejabat belum ada!']); 
        }

        return view('jabatan.detailplt',compact('data','module'));
    }

    public function cetak(){
        
        $data = array();
        $satuan_kerja = intval(request('satuan_kerja'));

        $query = DB::table('tb_jabatan')->join('tb_pegawai','tb_jabatan.id_pegawai','=','tb_pegawai.id')->join('tb_master_jabatan as jabatan1','tb_jabatan.id_master_jabatan','=','jabatan1.id')->join('tb_master_jabatan as jabatan2', 'jabatan1.id_parent', '=', 'jabatan2.id')->join('tb_satuan_kerja','tb_jabatan.id_satuan_kerja','=','tb_satuan_kerja.id')->select('tb_jabatan.id','tb_jabatan.uuid','tb_pegawai.uuid as pegawai_uuid','jabatan1.nama_jabatan as jabatan','jabatan2.nama_jabatan as atasan_langsung','tb_jabatan.status','tb_satuan_kerja.nama_satuan_kerja','tb_pegawai.nama as pejabat')->orderBy('jabatan2.id_parent','DESC')->where('tb_jabatan.status','plt');
        
        if ($satuan_kerja > 0) {
            $query->where('tb_jabatan.id_satuan_kerja',$satuan_kerja);
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

        $sheet->setCellValue('A1', 'DAFTAR JABATAN PLT')->mergeCells('A1:E1');
        $sheet->setCellValue('A2', 'PEMERINTAH KABUPATEN BULUKUMBA')->mergeCells('A2:E2');

        $sheet->getStyle('A5:E5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('E1F5FE');

        $sheet->setCellValue('A5', 'No');
        $sheet->setCellValue('B5', 'JABATAN')->getColumnDimension('B')->setWidth(35);
        $sheet->setCellValue('C5', 'PEJABAT')->getColumnDimension('C')->setWidth(35);
        $sheet->setCellValue('D5', 'ATASAN LANGSUNG')->getColumnDimension('D')->setWidth(35);
        $sheet->setCellValue('E5', 'SATUAN KERJA')->getColumnDimension('E')->setWidth(35);

        $cell = 6;
        $no = 1;
        foreach ($data as $key => $value) {
            $sheet->setCellValue('A' . $cell, $no++);
            $sheet->setCellValue('B' . $cell, $value->jabatan);
            $sheet->setCellValue('C' . $cell, $value->pejabat);
            $sheet->setCellValue('D' . $cell, $value->atasan_langsung);
            $sheet->setCellValue('E' . $cell, $value->nama_satuan_kerja);
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
        $sheet->getStyle('A5:E'.$cell)->applyFromArray($border_row);

        $sheet->getStyle('A:E')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A:E')->getAlignment()->setVertical('center');
        // $sheet->getStyle('B5:B' . $cell)->getAlignment()->setHorizontal('rigth');
        // $sheet->getStyle('C5:C' . $cell)->getAlignment()->setHorizontal('rigth');
        //$sheet->getStyle('A3:G')->getAlignment()->setHorizontal('rigth');
        

        $cell++;

        $spreadsheet->getActiveSheet()->getHeaderFooter()->setOddHeader('&C&H' . url()->current());
        $spreadsheet->getActiveSheet()->getHeaderFooter()->setOddFooter('&L&B &RPage &P of &N');
        $class = \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf::class;
        \PhpOffice\PhpSpreadsheet\IOFactory::registerWriter('Pdf', $class);
        header('Content-Type: application/pdf');
        header('Cache-Control: max-age=0');
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Pdf');

        $writer->save('php://output');
    }
}
