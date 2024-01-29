<?php

namespace App\Http\Controllers\master_jabatan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Requests\MasterJabatanRequest;
use App\Models\MasterJabatan;
use DB;
use App\Traits\General;
use Barryvdh\DomPDF\Facade\Pdf;

class MasterJabatanController extends BaseController
{
    use General;

    public function optionJabatan(){
        $data = array();
        try {
            $satuan_kerja = request('satuan_kerja');
            $type = request('type');
            $data = $this->option_jabatan_all($satuan_kerja, $type);
        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'option jabatan success');
    }

    public function optionKelompokJabatan(){
        $data = array();
        try {
            $jenis_jabatan = request('jenis_jabatan');
            $level = request('level');
            $data = $this->option_kelompok_jabatan($jenis_jabatan,$level);
        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'option Kelompok jabatan success');
    }

    public function optionAtasanLangsung(){
        $kelas = request('kelas');
        $satuan_kerja = request('satuan_kerja');
         $data = array();
        try {
            $data = $this->option_atasan_langsung($kelas, $satuan_kerja);
        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'option atasan langsung success');
    }

    public function breadcumb(){
        return [
            [
                'label' => 'Master Jabatan',
                'url' => '#'
            ],
            [
                'label' => 'Master Jabatan',
                'url' => '#'
            ],
        ];
    }

    public function datatable(){
        $data = array();

        $satuan_kerja = intval(request('satuan_kerja'));

        $query = DB::table('tb_master_jabatan as jabatan1')
        ->leftJoin('tb_satuan_kerja','jabatan1.id_satuan_kerja','=','tb_satuan_kerja.id')
        ->select('jabatan1.id', 'jabatan1.uuid', 'jabatan1.nama_jabatan', 'jabatan1.jenis_jabatan', 'jabatan1.kelas_jabatan','tb_satuan_kerja.nama_satuan_kerja')
        ->where('jabatan1.level_jabatan','>',0)
        ->orderBy('jabatan1.kelas_jabatan','ASC');

        if ($satuan_kerja > 0) {
            $query->where('jabatan1.id_satuan_kerja',$satuan_kerja);
        }

         $data = $query->get();

        return $this->sendResponse($data, 'Data Jabatan Fetched Success');
    }

    public function index(){
        $module = $this->breadcumb();
        $jenis_jabatan = $this->option_jenis_jabatan();
        $satuan_kerja = $this->option_satuan_kerja();

        return view('admin_kabupaten.master_jabatan.masterjabatan',compact('module','satuan_kerja','jenis_jabatan'));
    }

    public function store(MasterJabatanRequest $request){
        $jabatan = array();
        // dd($request->all());
        try {
            // if (!$this->checkMasterJabatan($request)) {
                $jabatan = new MasterJabatan();
                $jabatan->nama_struktur = $request->nama_struktur;   
                $jabatan->nama_jabatan = $request->nama_jabatan;   
                $jabatan->jenis_jabatan = $request->jenis_jabatan;   
                $jabatan->pagu_tpp = intval(str_replace(['Rp ', '.'], '', $request->pagu_tpp));   
                $jabatan->id_satuan_kerja = $request->id_satuan_kerja;   
                $jabatan->level_jabatan = $request->level_jabatan;   
                $jabatan->kelas_jabatan = $request->kelas_jabatan;   
                $jabatan->id_kelompok_jabatan = $request->id_kelompok_jabatan;   
                $jabatan->target_waktu = 6750;
                $jabatan->status = 1;    
                $jabatan->save(); 
            // }else{
            //     return $this->sendError('Data sudah ada !', 'Gagal', 200);
            // }
                
        
        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($jabatan, 'Master Jabatan Added success');
    }

    public function update(MasterJabatanRequest $request, $params) {
        try {
            DB::beginTransaction();

                $jabatan = MasterJabatan::where('uuid',$params)->first();
                $jabatan->nama_struktur = $request->nama_struktur;   
                $jabatan->nama_jabatan = $request->nama_jabatan;   
                $jabatan->jenis_jabatan = $request->jenis_jabatan;   
                $jabatan->pagu_tpp = intval(str_replace(['Rp ', '.'], '', $request->pagu_tpp));   
                $jabatan->id_satuan_kerja = $request->id_satuan_kerja;    
                $jabatan->level_jabatan = $request->level_jabatan;   
                $jabatan->kelas_jabatan = $request->kelas_jabatan;    
                $jabatan->id_kelompok_jabatan = $request->id_kelompok_jabatan;   
                $jabatan->save(); 

            DB::commit();

        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($jabatan, 'Master Jabatan Updated success');
    }

    public function show($params){
       $data = array();
        try {
            $data = DB::table('tb_master_jabatan')->where('uuid',$params)->first();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Master Jabatan Show success'); 
    }

    public function showByid($params){
       $data = array();
        try {
            $data = DB::table('tb_master_jabatan')->select('id_satuan_kerja','kelas_jabatan as kelas','level_jabatan as level')->where('id',$params)->first();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Master Jabatan Show success'); 
    }


    public function delete(Request $request, $params){
        $data = array();
        try {
            DB::table('tb_master_jabatan')->where('uuid',$params)->delete();
           
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Master Jabatan Delete success');
    }

    public function cetak_jabatans(){
        $result = array();
        $idSatuanKerja = request('satuan_kerja');
        return $this->dataJabatan($idSatuanKerja);
    }

    public function getHierarchicalStructure($idSatuanKerja, $parentId = null)
    {

        ini_set('max_execution_time', 820);
        set_time_limit(820);
        
        $result = array();
        $jabatan = array();

        $query = DB::table('tb_master_jabatan')
            ->select('tb_master_jabatan.id','tb_master_jabatan.nama_struktur','tb_master_jabatan.nama_jabatan','tb_master_jabatan.jenis_jabatan','tb_master_jabatan.kelas_jabatan','tb_master_jabatan.level_jabatan','tb_kelompok_jabatan.kelompok','tb_lokasi.nama_lokasi','tb_pegawai.nama as nama_pegawai','tb_pegawai.nip','tb_jabatan.id as id_jabatan','tb_master_jabatan.pagu_tpp','lokasi_apel.nama_lokasi as lokasi_apel')
            ->leftJoin('tb_kelompok_jabatan','tb_master_jabatan.id_kelompok_jabatan','=','tb_kelompok_jabatan.id')
            ->leftJoin('tb_jabatan','tb_jabatan.id_master_jabatan','=','tb_master_jabatan.id')
            ->leftJoin('tb_pegawai','tb_jabatan.id_pegawai','=','tb_pegawai.id')
            ->leftJoin('tb_lokasi','tb_jabatan.id_lokasi_kerja','=','tb_lokasi.id') 
            ->leftJoin('tb_lokasi as lokasi_apel','tb_jabatan.id_lokasi_apel','=','lokasi_apel.id')
            ->where('tb_jabatan.id_parent', $parentId)
            ->orderBy('tb_master_jabatan.kelas_jabatan', 'desc');
            
            if ($idSatuanKerja > 0) {
                $query->where('tb_jabatan.id_unit_kerja', $idSatuanKerja);
            }

           $jabatan = $query->get();

        //    dd($jabatan);


        foreach ($jabatan as $item) {
            // Rekursi untuk mendapatkan bawahan dari setiap jabatan

            $item->bawahan = $this->getHierarchicalStructure($idSatuanKerja, $item->id_jabatan);

            $result[] = $item;
        }
        return $result;
    }

    // public function moveJabatanPuncakToFront($structure, $jabatanPuncakId)
    // {
    //     foreach ($structure as $key => $item) {
    //         if ($item->id == $jabatanPuncakId) {
    //             // Hapus jabatan puncak dari posisi saat ini
    //             unset($structure[$key]);
    //             // Tempatkan jabatan puncak di awal array
    //             array_unshift($structure, $item);
    //             break;
    //         }

    //         // Rekursif panggil fungsi untuk bawahan
    //         if (isset($item->bawahan) && !empty($item->bawahan)) {
    //             $structure[$key]->bawahan = $this->moveJabatanPuncakToFront($item->bawahan, $jabatanPuncakId);
    //         }
    //     }

    //     return $structure;
    // }

    public function cetak_jabatan()
    {

        ini_set('max_execution_time', 820);
        set_time_limit(820);

        $idSatuanKerja = intval(request('satuan_kerja'));
        $data = array();
        $jabatanPuncak = array();

        if ($idSatuanKerja > 0) {

            $unit_kerja = DB::table('tb_unit_kerja')->select('nama_unit_kerja')->where('id',$idSatuanKerja)->first();



            if (strpos($unit_kerja->nama_unit_kerja,'PUSKESMAS') === false) {
                $jabatanPuncak = DB::table('tb_master_jabatan')
                ->select('tb_master_jabatan.id','tb_master_jabatan.nama_struktur','tb_master_jabatan.nama_jabatan','tb_master_jabatan.jenis_jabatan','tb_master_jabatan.kelas_jabatan','tb_master_jabatan.level_jabatan','tb_kelompok_jabatan.kelompok','tb_lokasi.nama_lokasi','tb_pegawai.nama as nama_pegawai','tb_pegawai.nip','tb_jabatan.id_parent','tb_jabatan.id as id_jabatan','tb_master_jabatan.pagu_tpp','lokasi_apel.nama_lokasi as lokasi_apel')
                ->leftJoin('tb_kelompok_jabatan','tb_master_jabatan.id_kelompok_jabatan','=','tb_kelompok_jabatan.id')
                ->leftJoin('tb_jabatan','tb_jabatan.id_master_jabatan','=','tb_master_jabatan.id')
                ->leftJoin('tb_pegawai','tb_jabatan.id_pegawai','=','tb_pegawai.id')
                ->leftJoin('tb_lokasi','tb_jabatan.id_lokasi_kerja','=','tb_lokasi.id')
                ->leftJoin('tb_lokasi as lokasi_apel','tb_jabatan.id_lokasi_apel','=','lokasi_apel.id')
                ->orderBy('kelas_jabatan', 'desc')
                ->where('tb_jabatan.id_unit_kerja', $idSatuanKerja)
                ->first();

                // return $jabatanPuncak;
                
                $jabatanpuncak_tmt = DB::table('tb_master_jabatan')
                    ->select('tb_master_jabatan.id','tb_master_jabatan.nama_struktur','tb_master_jabatan.nama_jabatan','tb_master_jabatan.jenis_jabatan','tb_master_jabatan.kelas_jabatan','tb_master_jabatan.level_jabatan','tb_kelompok_jabatan.kelompok','tb_lokasi.nama_lokasi','tb_pegawai.nama as nama_pegawai','tb_pegawai.nip','tb_jabatan.id_parent','tb_jabatan.id_unit_kerja','tb_jabatan.id_parent','tb_jabatan.id as id_jabatan','tb_master_jabatan.pagu_tpp','lokasi_apel.nama_lokasi as lokasi_apel')
                    ->leftJoin('tb_kelompok_jabatan','tb_master_jabatan.id_kelompok_jabatan','=','tb_kelompok_jabatan.id')
                    ->leftJoin('tb_jabatan','tb_jabatan.id_master_jabatan','=','tb_master_jabatan.id')
                    ->leftJoin('tb_pegawai','tb_jabatan.id_pegawai','=','tb_pegawai.id')
                    ->leftJoin('tb_lokasi','tb_jabatan.id_lokasi_kerja','=','tb_lokasi.id')
                    ->leftJoin('tb_lokasi as lokasi_apel','tb_jabatan.id_lokasi_apel','=','lokasi_apel.id')
                    ->orderBy('kelas_jabatan', 'desc')
                    ->where('tb_jabatan.id_unit_kerja', $idSatuanKerja)
                    ->where('tb_jabatan.id_parent',$jabatanPuncak->id_parent)
                    ->get();    
            
                if (count($jabatanpuncak_tmt) > 0) {
                    foreach ($jabatanpuncak_tmt as $key => $value) {
                        $result = $this->getHierarchicalStructure($value->id_unit_kerja, $value->id_jabatan);
                        $value->bawahan = $result;
                    }
                }

                $result = $this->getHierarchicalStructure($idSatuanKerja, $jabatanPuncak->id_jabatan);

                $jabatanPuncak->bawahan = $result;

                $data = $jabatanpuncak_tmt;
            }

            if (strpos($unit_kerja->nama_unit_kerja,'PUSKESMAS') === 0) {
                $jabatanPuncak = DB::table('tb_master_jabatan')
                ->select('tb_master_jabatan.id','tb_master_jabatan.nama_struktur','tb_master_jabatan.nama_jabatan','tb_master_jabatan.jenis_jabatan','tb_master_jabatan.kelas_jabatan','tb_master_jabatan.level_jabatan','tb_kelompok_jabatan.kelompok','tb_lokasi.nama_lokasi','tb_pegawai.nama as nama_pegawai','tb_pegawai.nip','tb_jabatan.id_parent','tb_jabatan.id as id_jabatan','tb_jabatan.id_unit_kerja','tb_master_jabatan.pagu_tpp','lokasi_apel.nama_lokasi as lokasi_apel')
                ->leftJoin('tb_kelompok_jabatan','tb_master_jabatan.id_kelompok_jabatan','=','tb_kelompok_jabatan.id')
                ->leftJoin('tb_jabatan','tb_jabatan.id_master_jabatan','=','tb_master_jabatan.id')
                ->leftJoin('tb_pegawai','tb_jabatan.id_pegawai','=','tb_pegawai.id')
                ->leftJoin('tb_lokasi','tb_jabatan.id_lokasi_kerja','=','tb_lokasi.id')
                ->leftJoin('tb_lokasi as lokasi_apel','tb_jabatan.id_lokasi_apel','=','lokasi_apel.id')
                ->orderBy('kelas_jabatan', 'desc')
                ->where('tb_jabatan.id_unit_kerja', $idSatuanKerja)
                ->get();

                if (count($jabatanPuncak) > 0) {
                    foreach ($jabatanPuncak as $key => $value) {
                        $result = $this->getHierarchicalStructure($value->id_unit_kerja, $value->id_jabatan);
                        $value->bawahan = $result;
                    }
                }

                $data = $jabatanPuncak;
            }

            
        }

        $pdf = PDF::loadView('admin_kabupaten.master_jabatan.cetak', ['result' => $data]);
        $pdf->setPaper('a2', 'landscape');

        return $pdf->stream('example.pdf');
    }

    public function export_jabatan($data){
        $spreadsheet = new Spreadsheet();

        $spreadsheet->getProperties()->setCreator('BKPSDM ENREKANG')
            ->setLastModifiedBy('BKPSDM ENREKANG')
            ->setTitle('Laporan Struktur Jabatan')
            ->setSubject('Laporan Struktur Jabatan')
            ->setDescription('Laporan Struktur Jabatan')
            ->setKeywords('pdf php')
            ->setCategory('Laporan Struktur Jabatan');
        $sheet = $spreadsheet->getActiveSheet();

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

        $sheet->setCellValue('A1', 'STRUKTUR JABATAN')->mergeCells('A1:I1');

        // $sheet->getStyle('A6:G6')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('E1F5FE');

        $sheet->setCellValue('A4', 'NO');
        $sheet->setCellValue('B4', 'STRUKTUR')->getColumnDimension('B')->setWidth(50);
        $sheet->setCellValue('C4', 'NAMA JABATAN')->getColumnDimension('C')->setWidth(50);
        $sheet->setCellValue('D4', 'JABATAN')->getColumnDimension('D')->setWidth(30);
        $sheet->setCellValue('E4', 'KELAS JABATAN')->getColumnDimension('E')->setWidth(10);
        $sheet->setCellValue('F4', 'PAGU TPP')->getColumnDimension('F')->setWidth(20);
        $sheet->setCellValue('G4', 'KELOMPOK JABATAN')->getColumnDimension('G')->setWidth(30);
        $sheet->setCellValue('I4', 'LOKASI ABSEN')->getColumnDimension('I')->setWidth(30);

        $cell = 5;
        $no = 1;
        foreach ($data as $key => $value) {
            $sheet->setCellValue('A' . $cell, $no++);
            $sheet->setCellValue('B' . $cell, $value->nama_struktur);
            $sheet->setCellValue('C' . $cell, $value->nama_jabatan);
            $sheet->setCellValue('D' . $cell, $value->jenis_jabatan);
            $sheet->setCellValue('E' . $cell, $value->kelas_jabatan);
            $sheet->setCellValue('F' . $cell, $value->pagu_tpp);
            $sheet->setCellValue('G' . $cell, $value->kelompok);
            $sheet->setCellValue('H' . $cell, $value->nama_unit_kerja);
            $sheet->setCellValue('I' . $cell, $value->nama_lokasi);


            $cell++;
            if (count($value->bawahan) > 0) {
                foreach ($value->bawahan as $k => $v) {
                    $sheet->setCellValue('A' . $cell, $no++);
                    $sheet->setCellValue('B' . $cell, '    '.$v->nama_struktur);
                    $sheet->setCellValue('C' . $cell, $v->nama_jabatan);
                    $sheet->setCellValue('D' . $cell, $v->jenis_jabatan);
                    $sheet->setCellValue('E' . $cell, $v->kelas_jabatan);
                    $sheet->setCellValue('F' . $cell, $v->pagu_tpp);
                    $sheet->setCellValue('G' . $cell, $v->kelompok);
                    $sheet->setCellValue('H' . $cell, $v->nama_unit_kerja);
                    $sheet->setCellValue('I' . $cell, $v->nama_lokasi);
                    $cell++;
                }
                
            }   
        }

          $border_row = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '0000000'],
                ],
            ],
        ];
        $sheet->getStyle('A4:I'.$cell)->applyFromArray($border_row);

        $sheet->getStyle('A:I')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A:I')->getAlignment()->setVertical('center');
        $sheet->getStyle('B4:I' . $cell)->getAlignment()->setHorizontal('rigth');
        $sheet->getStyle('C4:I' . $cell)->getAlignment()->setHorizontal('rigth');
        //$sheet->getStyle('A3:G')->getAlignment()->setHorizontal('rigth');
        

        $cell++;

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

}
