<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use DB;
use App\Traits\General;
use App\Models\SasaranKinerja;
use Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Carbon\Carbon;

class LaporanKinerjaController extends BaseController
{
    use General;

    public function breadcumb()
    {
        return [
            [
                'label' => 'Laporan',
                'url' => '#'
            ],
            [
                'label' => 'Kinerja',
                'url' => '#'
            ],
        ];
    }

    public function index()
    {
        $module = $this->breadcumb();
        return view('laporan.kinerja.index', compact('module'));
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
            return view('laporan.kinerja.index_opd', compact('module', 'pegawai','satuan_kerja_user','nama_satuan_kerja'));
        } else {
            return view('laporan.kinerja.index_unit', compact('module', 'pegawai'));
        }
    }

    public function index_kabupaten()
    {
        $module = $this->breadcumb();
        $satuan_kerja = $this->option_satuan_kerja();
        return view('laporan.kinerja.index_kabupaten', compact('module', 'satuan_kerja'));
    }

   public function data_kinerja_pegawai($pegawai, $jabatan, $bulan){
    $tahun = session('tahun_penganggaran') ? session('tahun_penganggaran') : date('Y');
        $dataArray = SasaranKinerja::query()
            ->select('id', 'id_satuan_kerja', 'rencana', 'id_jabatan', 'tahun')
            ->with(['aktivitas' => function ($query) use ($bulan, $jabatan,$tahun, $pegawai) {
                $query->select('id_sasaran', 'tanggal', 'aktivitas', 'keterangan', 'volume', 'satuan', 'created_at', DB::raw('SUM(id) as total_id'), DB::raw('SUM(volume) as total_volume'), DB::raw('SUM(waktu) as total_waktu'));
                $query->groupBy('id_sasaran', 'tanggal', 'aktivitas', 'keterangan', 'volume', 'satuan', 'created_at');
                $query->whereMonth('tanggal', $bulan);
                $query->where('tahun',$tahun);
                $query->where('id_pegawai', $pegawai);
                $query->orderBy('tanggal', 'ASC');
            }])
            ->where('tahun', $tahun)
            ->where('id_jabatan', $jabatan->id_jabatan)
            ->orderBy('created_at', 'DESC')
            ->get();


            $aktivitas_sasaran_tanpa_jabatan = DB::table('tb_aktivitas')
            ->select('id_sasaran', 'tanggal', 'aktivitas', 'keterangan', 'volume', 'satuan', 'created_at', DB::raw('SUM(id) as total_id'), DB::raw('SUM(volume) as total_volume'), DB::raw('SUM(waktu) as total_waktu'))
            ->where('id_pegawai', $pegawai)
            ->where('id_sasaran','>', 0)
            ->whereMonth('tanggal', $bulan)
            ->where('tahun', $tahun)
            ->where("validation",1)
            ->groupBy('id_sasaran', 'tanggal', 'aktivitas', 'keterangan', 'volume', 'satuan', 'created_at')
            ->orderBy('tanggal', 'ASC')
            ->get();

            $aktivitas_tambahan = [];

            foreach ($aktivitas_sasaran_tanpa_jabatan as $aktivitas) {
                $id_sasaran = $aktivitas->id_sasaran;
                $sasaranAda = false;
                foreach ($dataArray as $skp) {
                    foreach ($skp->aktivitas as $item) {
                        if ($item->id_sasaran == $id_sasaran) {
                            $sasaranAda = true;
                            break 2;
                        }
                    }
                }
                if (!$sasaranAda) {
                    $aktivitas_tambahan[] = $aktivitas;
                }
            }


        $aktivitas_non_sasaran = DB::table('tb_aktivitas')
            ->select('id_sasaran', 'tanggal', 'aktivitas', 'keterangan', 'volume', 'satuan', 'created_at', DB::raw('SUM(id) as total_id'), DB::raw('SUM(volume) as total_volume'), DB::raw('SUM(waktu) as total_waktu'))
            ->where('id_pegawai', $pegawai)
            ->where('id_sasaran', 0)
            ->whereMonth('tanggal', $bulan)
            ->where('tahun', $tahun)
            ->where("validation",1)
            ->groupBy('id_sasaran', 'tanggal', 'aktivitas', 'keterangan', 'volume', 'satuan', 'created_at')
            ->orderBy('tanggal', 'ASC')
            ->get();

            // dd($aktivitas_non_sasaran);

       
            // Konversi $aktivitas_tambahan menjadi objek Laravel Collection
            $aktivitas_tambahan_collection = collect($aktivitas_tambahan);

            // Gabungkan dua koleksi menggunakan metode merge()
            $aktivitas_group_non_sasaran_jabatan = $aktivitas_non_sasaran->merge($aktivitas_tambahan_collection);


        $skp_tmt = [
            'id_satuan_kerja' => 0,
            'rencana' => '-',
            'id_jabatan' => 0,
            'tahun' => date('Y'),
            'aktivitas' => $aktivitas_group_non_sasaran_jabatan
        ];

        $dataArray[] = $skp_tmt;
        return $dataArray;
   }

    

    public function export_pegawai()
    {
        $type = request('type');
        $pegawai_params = request('pegawai') ? request('pegawai') : Auth::user()->id_pegawai;
        $bulan = request('bulan');
        $role = hasRole();
        $role_check = 0;

        if ($role['guard'] == 'web' && $role['role'] == '2') {
            $role_check = 1; 
        }

        $jabatan_req = request("status");
        $pegawai = $this->findPegawai($pegawai_params, $jabatan_req,$role_check);
        $checkJabatan = $this->checkJabatanDefinitif($pegawai_params, $jabatan_req,$role_check);

        $data = array();

        if ($checkJabatan) {
            $atasan = $this->findAtasan($pegawai_params);
            $data = $this->data_kinerja_pegawai($pegawai_params, $checkJabatan, $bulan);
     
            return $this->export_kinerja_pegawai($data, $type, $pegawai, $atasan, $bulan);

        } else {
            return redirect()->back()->withErrors(['error' => 'Belum bisa membuka laporan, pegawai tersebut belum mempunyai jabatan']);
        }
    }

    public function data_kinerja_pegawai_by_opd($satuan_kerja, $unit_kerja, $bulan)
    {
        $result = array();
        $query = DB::table('tb_pegawai')
            ->select(
                'tb_pegawai.id',
                'tb_pegawai.nama',
                'tb_pegawai.nip',
                'tb_pegawai.golongan',
                'tb_master_jabatan.nama_jabatan',
                'tb_jabatan.target_waktu',
                'tb_master_jabatan.kelas_jabatan',
                'tb_jabatan.status as status_jabatan'
            )
            ->join('tb_jabatan', 'tb_jabatan.id_pegawai', 'tb_pegawai.id')
            ->join('tb_master_jabatan', 'tb_jabatan.id_master_jabatan', '=', 'tb_master_jabatan.id')
            ->join('tb_satuan_kerja', 'tb_pegawai.id_satuan_kerja', '=', 'tb_satuan_kerja.id')
            ->where('tb_pegawai.status', '=', '1')
            ->groupBy('tb_pegawai.id', 'tb_pegawai.nama', 'tb_pegawai.nip', 'tb_pegawai.golongan', 'tb_master_jabatan.nama_jabatan', 'tb_jabatan.target_waktu', 'tb_master_jabatan.kelas_jabatan','tb_jabatan.status')
            ->orderBy('tb_master_jabatan.kelas_jabatan', 'DESC');


        $role = hasRole();

        if ($role['guard'] == 'web') {
            if ($role['role'] == '1') {
                if ($satuan_kerja !== null) {
                    $query->where('tb_pegawai.id_satuan_kerja', $satuan_kerja);
                }
            } else {
                $query->where('tb_jabatan.id_unit_kerja', $unit_kerja);
            }
        }

        if (hasRole()['guard'] == 'administrator') {
            $query->where('tb_jabatan.id_satuan_kerja', $satuan_kerja);
            if ($unit_kerja !== 'all') {
                $query->where('tb_jabatan.id_unit_kerja', $unit_kerja);
            }
        }

        $result = $query->get();

        $result = $result->map(function ($item) use ($bulan) {
            $capaian = DB::table('tb_aktivitas')
                ->selectRaw('COALESCE(SUM(waktu), 0) as capaian_waktu')
                ->where('id_pegawai', $item->id)
                ->whereMonth('tanggal', $bulan)
                ->where('validation',1)
                ->where('tahun',session('tahun_penganggaran'))
                ->limit(1)
                ->first();
            $item->capaian_waktu = intval($capaian->capaian_waktu);
            return $item;
        });

        return $result;
    }

    public function export_opd()
    {
        $satuan_kerja = '';
        $nama_satuan_kerja = '';
        $unit_kerja = '';
        $nama_unit_kerja = '';
        $bulan = request('bulan');
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

        $data = $this->data_kinerja_pegawai_by_opd($satuan_kerja, $unit_kerja, $bulan);
        $type = request('type');
        return $this->export_kinerja_rekapitulasi($data, $type, $nama_satuan_kerja, $nama_unit_kerja, $bulan);
    }

    public function export_kinerja_pegawai($data, $type, $pegawai, $atasan, $bulan)
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator('BKPSDM BULUKUMBA')
            ->setLastModifiedBy('BKPSDM BULUKUMBA')
            ->setTitle('Laporan Pembayaran TPP')
            ->setSubject('Laporan Pembayaran TPP')
            ->setDescription('Laporan Pembayaran TPP')
            ->setKeywords('pdf php')
            ->setCategory('LAPORAN Pembayaran TPP');
        $sheet = $spreadsheet->getActiveSheet();
        // $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

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

        $sheet->setCellValue('A1', 'LAPORAN KINERJA PEGAWAI (AKTIVITAS)')->mergeCells('A1:J1');
        $sheet->setCellValue('A2', strtoupper(konvertBulan($bulan)))->mergeCells('A2:J2');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

        $spreadsheet->getActiveSheet()->getStyle('A1:F4')->getFont()->setBold(true);


        $sheet->setCellValue('A4', 'PEGAWAI YANG DINILAI')->mergeCells('A4:E4');
        $sheet->setCellValue('F4', 'PEJABAT PENILAI')->mergeCells('F4:K4');
        $sheet->getStyle('A4:F4')->getAlignment()->setHorizontal('center');

        $sheet->getStyle('A4:K4')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('BCCBE1');

        $sheet->setCellValue('A5', ' Nama')->mergeCells('A5:C5');
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->setCellValue('D5', ' ' . $pegawai->nama)->mergeCells('D5:E5');
        $sheet->getColumnDimension('D')->setWidth(45);

        $sheet->setCellValue('F5', ' Nama');
        $sheet->getColumnDimension('F')->setWidth(30);
        $sheet->setCellValue('G5', ' ' . ($atasan !== null ? $atasan->nama : '-'))->mergeCells('G5:K5');
        $sheet->getColumnDimension('G')->setWidth(45);

        $sheet->setCellValue('A6', ' NIP')->mergeCells('A6:C6');
        $sheet->setCellValue('D6', " " . $pegawai->nip)->mergeCells('D6:E6');

        $sheet->setCellValue('F6', ' NIP');
        $sheet->setCellValue('G6', " " . ($atasan !== null ? $atasan->nip : '-'))->mergeCells('G6:K6');

        $golongan_pegawai = '';
        $golongan_atasan = '';

        $pegawai->golongan !== null ? $golongan_pegawai = $pegawai->golongan : $golongan_pegawai = '-';
        $atasan && $atasan->golongan !== null ? $golongan_atasan = $atasan->golongan : $golongan_atasan = '-';

        $sheet->setCellValue('A7', ' Pangkat / Gol Ruang')->mergeCells('A7:C7');
        $sheet->setCellValue('D7', ' ' . $golongan_pegawai)->mergeCells('D7:E7');

        $sheet->setCellValue('F7', ' Pangkat / Gol Ruang');
        $sheet->setCellValue('G7', ' ' . $golongan_atasan)->mergeCells('G7:K7');

        $sheet->setCellValue('A8', ' Jabatan')->mergeCells('A8:C8');
        $sheet->setCellValue('D8', ' ' . $pegawai->nama_jabatan)->mergeCells('D8:E8');

        $sheet->setCellValue('F8', ' Jabatan');
        $sheet->setCellValue('G8', ' ' . ($atasan !== null ? $atasan->nama_jabatan : '-'))->mergeCells('G8:K8');

        $sheet->setCellValue('A9', ' Unit kerja')->mergeCells('A9:C9');
        $sheet->setCellValue('D9', ' ' . $pegawai->nama_unit_kerja)->mergeCells('D9:E9');

        $sheet->setCellValue('F9', ' Unit kerja');
        $sheet->setCellValue('G9', ' ' . ($atasan !== null ? $atasan->nama_unit_kerja : '-'))->mergeCells('G9:K9');

        $spreadsheet->getActiveSheet()->getRowDimension('4')->setRowHeight(20);
        $spreadsheet->getActiveSheet()->getRowDimension('5')->setRowHeight(20);
        $spreadsheet->getActiveSheet()->getRowDimension('6')->setRowHeight(20);
        $spreadsheet->getActiveSheet()->getRowDimension('7')->setRowHeight(20);
        $spreadsheet->getActiveSheet()->getRowDimension('8')->setRowHeight(20);
        $spreadsheet->getActiveSheet()->getRowDimension('9')->setRowHeight(20);
        $sheet->getStyle('A4:K9')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A4:K9')->getAlignment()->setVertical('center');
        $sheet->getStyle('A5:K9')->getAlignment()->setHorizontal('rigth');

        $border_header = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '0000000'],
                ],
            ],
        ];

        $sheet->getStyle('A4:K9')->applyFromArray($border_header);
        $sheet->setCellValue('A10', ' ');

        $sheet->setCellValue('A12', 'No');
        $sheet->setCellValue('B12', 'Tanggal');
        $sheet->setCellValue('C12', 'Aktifitas')->mergeCells('C12:E12');
        $sheet->setCellValue('F12', 'Keterangan aktivitas')->mergeCells('F12:G12');
        $sheet->setCellValue('H12', 'Hasil');
        $sheet->setCellValue('I12', 'Satuan');
        $sheet->setCellValue('J12', 'Waktu (menit)');
        $sheet->setCellValue('K12', 'Waktu di muat');
        $sheet->getStyle('I12')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('J12')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('K12')->getAlignment()->setHorizontal('center');


        $spreadsheet->getActiveSheet()->getStyle('A12:K12')->getFont()->setBold(true);
        $sheet->getStyle('A12:K12')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('BCCBE1');


        // $sheet->setCellValue('F12', 'Waktu (Menit)');

        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(20);
        $sheet->getColumnDimension('J')->setWidth(10);

        $cell = 13;

        $capaian_prod_kinerja = 0;

        foreach ($data as $key => $value) {
            if (count($value['aktivitas']) > 0) {
                $spreadsheet->getActiveSheet()->getRowDimension($cell)->setRowHeight(20);
                $sheet->setCellValue('A' . $cell, $key + 1);
                $sheet->getStyle('A' . $cell)->getAlignment()->setHorizontal('center');
                $sheet->setCellValue('B' . $cell,  " " . $value['rencana'])->mergeCells('B' . $cell . ':K' . $cell);
                $sheet->getStyle('A' . $cell . ':K' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('ECF1E0');
                $sheet->setCellValue('G' . $cell,  '')->mergeCells('G' . $cell . ':H' . $cell);
                $sheet->getStyle('A' . $cell . ':K' . $cell)->getAlignment()->setVertical('center');

                $cell++;

                $index1 = $key + 1;
                $index2 = 0;

                foreach ($value['aktivitas'] as $k => $v) {
                    $spreadsheet->getActiveSheet()->getRowDimension($cell)->setRowHeight(20);
                    $selisih = strtotime($v->created_at) - strtotime($v->tanggal);
                    $selisih_hari = $selisih / (60 * 60 * 24);

                    $index2 = $k + 1;
                    $capaian_prod_kinerja += $v->total_waktu;
                    $sheet->setCellValue('A' . $cell, '');
                    $sheet->setCellValue('B' . $cell, " " . Carbon::createFromFormat('Y-m-d', $v->tanggal)->format('d/m/y'));
                    $sheet->getStyle('B' . $cell)->getAlignment()->setHorizontal('center');
                    $sheet->setCellValue('C' . $cell,  " " . $v->aktivitas)->mergeCells('C' . $cell . ':E' . $cell);
                    $sheet->setCellValue('F' . $cell,  " " . $v->keterangan)->mergeCells('F' . $cell . ':G' . $cell);
                    $sheet->setCellValue('H' . $cell, $v->volume);
                    $sheet->setCellValue('I' . $cell, $v->satuan);
                    $sheet->getStyle('H' . $cell .':I'.$cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $sheet->setCellValue('J' . $cell, $v->total_waktu);
                    $sheet->setCellValue('K' . $cell, date("d/m/y", strtotime($v->created_at)));
                    if ($selisih_hari >= 6) {
                        $sheet->getStyle('K' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('e83343');
                    }
                    $sheet->getStyle('A' . $cell . ':K' . $cell)->getAlignment()->setVertical('center');
                    $sheet->getStyle('J' . $cell)->getAlignment()->setHorizontal('center');
                    $cell++;
                }
            }
        }


        $target_produktivitas_kerja = 0;
        $nilai_produktivitas_kerja = 0;

        if ($pegawai->target_waktu !== null) {
            $target_produktivitas_kerja = $pegawai->target_waktu;
        }

        if ($capaian_prod_kinerja > 0 || $target_produktivitas_kerja > 0) {
            $nilai_produktivitas_kerja = $target_produktivitas_kerja ? ($capaian_prod_kinerja / $target_produktivitas_kerja) * 100 : 0;
        }

        if ($nilai_produktivitas_kerja > 100) {
            $nilai_produktivitas_kerja = 100;
        }



        for ($i = 0; $i < 3; $i++) {
            if ($i == 0) {
                $sheet->setCellValue('B' . $cell, ' Capaian Produktivitas Kerja (Menit)')->mergeCells('B' . $cell . ':I' . $cell);
                $sheet->setCellValue('K' . $cell, $capaian_prod_kinerja);
                $sheet->getStyle('K' . $cell)->getAlignment()->setHorizontal('center');
                $sheet->getStyle('A' . $cell . ':K' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('ECF1E0');
            } elseif ($i == 1) {
                $sheet->setCellValue('B' . $cell, ' Target Produktivitas Kerja (Menit)')->mergeCells('B' . $cell . ':I' . $cell);
                $sheet->setCellValue('K' . $cell, $target_produktivitas_kerja);
                $sheet->getStyle('K' . $cell)->getAlignment()->setHorizontal('center');
                $sheet->getStyle('A' . $cell . ':K' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('ECF1E0');
            } else {
                $sheet->setCellValue('B' . $cell, ' Nilai Produktifitas Kerja (%)')->mergeCells('B' . $cell . ':I' . $cell);
                $sheet->setCellValue('K' . $cell, round($nilai_produktivitas_kerja, 2));
                $sheet->getStyle('K' . $cell)->getAlignment()->setHorizontal('center');
                $sheet->getStyle('A' . $cell . ':K' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('BCCBE1');
            }
            $spreadsheet->getActiveSheet()->getRowDimension($cell)->setRowHeight(20);
            $sheet->getStyle('A' . $cell . ':K' . $cell)->getAlignment()->setVertical('center');
            $spreadsheet->getActiveSheet()->getStyle('A' . $cell . ':K' . $cell)->getFont()->setBold(true);
            $cell++;
        }

        $tahun_n = session('tahun_penganggaran') ? session('tahun_penganggaran') : date('Y');

        $tgl_cetak = date("t", strtotime((int)$tahun_n)) . ' ' . strftime('%B %Y', mktime(0, 0, 0, date("n") + 1, 0, (int)$tahun_n));


        $border_row = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '0000000'],
                ],
            ],
        ];

        $sheet->getStyle('A12:K' . $cell)->applyFromArray($border_row);
        
        $cell++;

        $sheet->setCellValue('H' . ++$cell, 'BULUKUMBA, ' . $tgl_cetak)->mergeCells('H' . $cell . ':J' . $cell);
        $sheet->getStyle('H' . $cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('H' . ++$cell, 'Pejabat Penilai Kinerja')->mergeCells('H' . $cell . ':J' . $cell);
        $sheet->getStyle('H' . $cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('B' .$cell, 'Pegawai dinilai')->mergeCells('B' . $cell . ':D' . $cell);
        $cell_pegawai = $cell + 1;
        $cell = $cell + 3;
        // $sheet->setCellValue('B' . ++$cell_pegawai, $pegawai->nama)->mergeCells('B' . $cell_pegawai . ':D' . $cell_pegawai);

        $sheet->setCellValue('H' . ++$cell, $atasan ?  $atasan->nama : '')->mergeCells('H' . $cell . ':J' . $cell);
        $sheet->getStyle('H' . $cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('H' . ++$cell, $atasan ? $atasan->nip : '')->mergeCells('H' . $cell . ':J' . $cell);
        $sheet->getStyle('H' . $cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('B' . $cell - 1, $pegawai->nama)->mergeCells('B' . $cell - 1 . ':D' . $cell - 1);
        $sheet->setCellValue('B' . $cell, $pegawai->nip)->mergeCells('B' . $cell . ':D' . $cell);
        $sheet->getStyle('B' . $cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

        

        
        $sheet->getStyle('A12:K12')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A12:K12')->getAlignment()->setVertical('center');
        // $sheet->getStyle('B6:C' . $cell)->getAlignment()->setHorizontal('rigth');

        

        if ($type == 'excel') {

            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

            $bulan_tmt = strtoupper(konvertBulan($bulan));
            $filename = "LAPORAN KINERJA {$pegawai->nama} BULAN {$bulan_tmt}.xlsx";
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

    public function export_kinerja_rekapitulasi($data, $type, $nama_satuan_kerja, $nama_unit_kerja, $bulan)
    {
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

        // $perangka_daerah = '';
        // if ($satuan_kerja == $nama_unit_kerja || $nama_unit_kerja == 'Semua') {
        //     $perangka_daerah = $satuan_kerja;
        // } else {
        //     $perangka_daerah = $satuan_kerja . ' - ' . $nama_unit_kerja;
        // }

        $sheet->setCellValue('A1', 'REKAPITULASI CAPAIAN PRODUKTIFITAS KERJA (AKTIVITAS)')->mergeCells('A1:H1');
        $sheet->setCellValue('A2',strtoupper($nama_satuan_kerja))->mergeCells('A2:H2');
        $row_bulan = 3;
        if ($nama_unit_kerja !== 'Semua' && $nama_satuan_kerja !== $nama_unit_kerja) {
            $sheet->setCellValue('A3', strtoupper($nama_unit_kerja))->mergeCells('A3:H3');
            $row_bulan = 4;
        }
        $sheet->setCellValue('A' . $row_bulan, 'BULAN ' . strtoupper(konvertBulan($bulan)))->mergeCells('A' . $row_bulan . ':H' . $row_bulan);

        $sheet->getStyle('A6:H6')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('E1F5FE');

        $sheet->setCellValue('A6', 'No');
        $sheet->setCellValue('B6', 'Nama / NIP')->getColumnDimension('B')->setWidth(40);
        $sheet->setCellValue('C6', 'Pangkat Golongan')->getColumnDimension('C')->setWidth(30);
        $sheet->setCellValue('D6', 'Nama Jabatan')->getColumnDimension('D')->setWidth(40);
        $sheet->setCellValue('E6', 'Target Menit');
        $sheet->setCellValue('F6', 'Capaian Menit');
        $sheet->setCellValue('G6', 'Nilai Kinerja (%) ');
        $sheet->setCellValue('H6', 'Keterangan');

        $cell = 7;
        $no = 1;
        $golongan = '';
        $keterangan = '';
        $target_nilai = 0;
        $pegawai_ttd = array();
        foreach ($data as $key => $value) {
            $value->golongan !== null ? $golongan = $value->golongan : $golongan = '';
            $value->target_waktu !== null ? $target_nilai = $value->target_waktu : $target_nilai = 0;

            if ($value->kelas_jabatan == 1 || $value->kelas_jabatan == 3) {
                $nilai_kinerja = 100;
            } else {
                $target_nilai > 0 ? $nilai_kinerja = ($value->capaian_waktu / $target_nilai) * 100 : $nilai_kinerja = 0;
            }

            if ($nilai_kinerja > 100) {
                $nilai_kinerja = 100;
            }

            $sheet->setCellValue('A' . $cell, $no++);
            $sheet->setCellValue('B' . $cell, $value->nama . PHP_EOL . $value->nip);

            $jabatan_pegawai = $value->nama_jabatan;

            if ($value->status_jabatan !== 'definitif') {
                $jabatan_pegawai = strtoupper($value->status_jabatan).' '.$value->nama_jabatan;
            }

            $sheet->setCellValue('C' . $cell, $golongan);
            $sheet->setCellValue('D' . $cell, $jabatan_pegawai);
            $sheet->setCellValue('E' . $cell, $target_nilai);
            $sheet->setCellValue('F' . $cell, $value->capaian_waktu);
            $sheet->setCellValue('G' . $cell, round($nilai_kinerja, 2));


            if ($nilai_kinerja < 50) {
                $keterangan = 'TMS';
                $sheet->getStyle('H' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('F44336');
            } else {
                $keterangan = 'MS';
                $sheet->getStyle('H' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('00E676');   
            }

            $sheet->setCellValue('H' . $cell, $keterangan);
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
        $sheet->getStyle('A6:H' . $cell)->applyFromArray($border_row);

        $sheet->getStyle('E7:E' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('FFECB3');
        $sheet->getStyle('F7:F' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('FFF9D4');
        $sheet->getStyle('G7:G' . $cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('00E676');


        $sheet->getStyle('A:H')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A:H')->getAlignment()->setVertical('center');
        $sheet->getStyle('B7:B' . $cell)->getAlignment()->setHorizontal('rigth');
        $sheet->getStyle('C7:D' . $cell)->getAlignment()->setHorizontal('rigth');
        //$sheet->getStyle('A3:H')->getAlignment()->setHorizontal('rigth');


        $cell++;

        if (count($pegawai_ttd) > 0) {
            $sheet->setCellValue('E' . ++$cell, 'Kabupaten BULUKUMBA ' . date('d/m/Y'))->mergeCells('E' . $cell . ':H' . $cell);
            $sheet->getStyle('E' . $cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
            $sheet->setCellValue('E' . ++$cell, $pegawai_ttd['nama_jabatan'])->mergeCells('E' . $cell . ':H' . $cell);
            $sheet->getStyle('E' . $cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
            $cell = $cell + 3;
            $sheet->setCellValue('E' . ++$cell, $pegawai_ttd['nama'])->mergeCells('E' . $cell . ':H' . $cell);
            $sheet->getStyle('E' . $cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
            $sheet->setCellValue('E' . ++$cell, 'Pangkat/Golongan : ' . $pegawai_ttd['golongan'])->mergeCells('E' . $cell . ':H' . $cell);
            $sheet->getStyle('E' . $cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
            $sheet->setCellValue('E' . ++$cell, 'NIP : ' . $pegawai_ttd['nip'])->mergeCells('E' . $cell . ':H' . $cell);
            $sheet->getStyle('E' . $cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        }

        if ($type == 'excel') {
            // Untuk download 
            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $satuan_kerja_tmt = strtoupper($nama_satuan_kerja);
            $bulan_tmt = strtoupper(konvertBulan($bulan));
            $filename = "LAPORAN ABSEN {$satuan_kerja_tmt} BULAN {$bulan_tmt}.xlsx";
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
