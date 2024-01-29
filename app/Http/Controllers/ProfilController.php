<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use App\Traits\General;
use App\Models\Pegawai;
use App\Models\profil\RiwayatPendidikanFormal;
use App\Models\profil\RiwayatPendidikanNonFormal;
use App\Models\profil\RiwayatKepangkatan;
use App\Models\profil\RiwayatJabatan;
use App\Models\profil\CatatanHukumanDinas;
use App\Models\profil\DiklatStruktural;
use App\Models\profil\DiklatFungsional;
use App\Models\profil\DiklatTeknis;
use App\Models\profil\RiwayatPenghargaan;
use App\Models\profil\RiwayatIstri;
use App\Models\profil\RiwayatAnak;
use App\Models\profil\RiwayatOrangTua;
use App\Models\profil\RiwayatSaudara;
use App\Models\profil\RiwayatKeahlian;
use App\Models\profil\RiwayatBahasa;
use App\Models\profil\FilePegawai;

use App\Http\Requests\pegawaiUpdateRequest;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Http\Response;

class ProfilController extends BaseController
{
    use General;

    public function breadcumb(){
        return [
            [
                'label' => 'Profil',
                'url' => '#'
            ],
        ];
    }

    public function getImageProfil(){
        $pegawai =  Pegawai::where('id',Auth::user()->id_pegawai)->first();
        $url = Storage::disk('sftp')->get('/'.$pegawai->foto);
        $response = new Response($url, 200, [
            'Content-Type' => 'image/png',
        ]);
        ob_end_clean();
        return $response;
    }

    public function breadcumb_laporan(){
        return [
            [
                'label' => 'Laporan',
                'url' => '#'
            ],
            [
                'label' => 'Pegawai',
                'url' => '#'
            ],
        ];
    }

    public function index(){
        $module = $this->breadcumb();
        $golongan = $this->option_golongan();
        $pendidikan = $this->option_pendidikan();
        $satuan_kerja = $this->option_satuan_kerja();
        $agama = $this->option_agama();
        $satuan_kerja_user = $this->infoSatuanKerja(hasRole()['id_pegawai']);
        $data = DB::table('tb_pegawai')->where('id',Auth::user()->id_pegawai)->first();
        $riwayat_pendidikan_formal = RiwayatPendidikanFormal::where('id_pegawai',Auth::user()->id_pegawai)->get();
        $riwayat_pendidikan_non_formal = RiwayatPendidikanNonFormal::where('id_pegawai',Auth::user()->id_pegawai)->get();
        $riwayat_kepangkatan =  RiwayatKepangkatan::where('id_pegawai',Auth::user()->id_pegawai)->get();
        $riwayat_jabatan =  RiwayatJabatan::where('id_pegawai',Auth::user()->id_pegawai)->get();
        $catatan_hukuman_dinas =  CatatanHukumanDinas::where('id_pegawai',Auth::user()->id_pegawai)->get();
        $diklat_struktral = DiklatStruktural::where('id_pegawai',Auth::user()->id_pegawai)->get();
        $diklat_fungsional = DiklatFungsional::where('id_pegawai',Auth::user()->id_pegawai)->get();
        $diklat_teknis = DiklatTeknis::where('id_pegawai',Auth::user()->id_pegawai)->get();
        $riwayat_penghargaan = RiwayatPenghargaan::where('id_pegawai',Auth::user()->id_pegawai)->get();
        $riwayat_istri = RiwayatIstri::where('id_pegawai',Auth::user()->id_pegawai)->get();
        $riwayat_anak = RiwayatAnak::where('id_pegawai',Auth::user()->id_pegawai)->get();
        $riwayat_orang_tua = RiwayatOrangTua::where('id_pegawai',Auth::user()->id_pegawai)->get();
        $riwayat_saudara = RiwayatSaudara::where('id_pegawai',Auth::user()->id_pegawai)->get();
        $riwayat_keahlian = RiwayatKeahlian::where('id_pegawai',Auth::user()->id_pegawai)->get();
        $riwayat_bahasa = RiwayatBahasa::where('id_pegawai',Auth::user()->id_pegawai)->get();
        $file_pegawai = FilePegawai::where('id_pegawai',Auth::user()->id_pegawai)->get();
        $show_collapse = request('collapse');

        return view('pegawai.profil.index',compact('module','data','satuan_kerja_user','golongan','pendidikan','satuan_kerja','agama','riwayat_pendidikan_formal','riwayat_pendidikan_non_formal','riwayat_kepangkatan','riwayat_jabatan','catatan_hukuman_dinas','diklat_struktral','diklat_fungsional','diklat_teknis','riwayat_penghargaan','riwayat_istri','riwayat_anak','riwayat_orang_tua','riwayat_saudara','riwayat_keahlian','riwayat_bahasa','file_pegawai','show_collapse'));
    }

    public function index_laporan(){
        $role = hasRole();
        $module = $this->breadcumb_laporan();
        if ($role['guard'] == 'administrator') {
            $satuan_kerja = $this->option_satuan_kerja();
            return view('laporan.pegawai.index',compact('module','satuan_kerja'));
        }else{
            $satuan_kerja = $this->infoSatuanKerja(Auth::user()->id_pegawai);
            // $pegawai = DB::table('tb_pegawai')->select('id','nama as text')
            // ->where('id_satuan_kerja',$satuan_kerja->id_satuan_kerja)
            // ->where('status','1')->get();
        
             $pegawai = array();
        $role = hasRole();
        $query = DB::table('tb_pegawai')
        ->select('tb_pegawai.id','tb_pegawai.nama as text')
        ->leftJoin('tb_jabatan','tb_jabatan.id_pegawai','=','tb_pegawai.id')
        ->where('tb_pegawai.status','1');

        if ($role['role'] == '1') {
            $query->where('tb_pegawai.id_satuan_kerja',$satuan_kerja->id_satuan_kerja);
        }else {
            $query->where('tb_jabatan.id_unit_kerja',$satuan_kerja->id_unit_kerja);
        }
        
        $pegawai = $query->get();

        return view('laporan.pegawai.index_opd',compact('module','pegawai'));
        }
    }

    public function getImageProfilBase64($pegawai){
        $pegawai =  Pegawai::where('id',$pegawai)->first();
        $url = Storage::disk('sftp')->get('/'.$pegawai->foto);
        $base64Image = 'data:image/png;base64,' . base64_encode($url);

        return $base64Image;
    }

    public function laporan_pegawai(){
        $pegawai = request('pegawai') ? request('pegawai') : Auth::user()->id_pegawai;
        $data = [
            'data_pribadi' => DB::table('tb_pegawai')->where('id',$pegawai)->first(),
            'riwayat_pendidikan_formal' => RiwayatPendidikanFormal::where('id_pegawai',$pegawai)->get(),
            'riwayat_pendidikan_non_formal' => RiwayatPendidikanNonFormal::where('id_pegawai',$pegawai)->get(),
            'riwayat_kepangkatan' =>  RiwayatKepangkatan::where('id_pegawai',$pegawai)->get(),
            'riwayat_jabatan' =>  RiwayatJabatan::where('id_pegawai',$pegawai)->get(),
            'catatan_hukuman_dinas' =>  CatatanHukumanDinas::where('id_pegawai',$pegawai)->get(),
            'diklat_struktral' => DiklatStruktural::where('id_pegawai',$pegawai)->get(),
            'diklat_fungsional' => DiklatFungsional::where('id_pegawai',$pegawai)->get(),
            'diklat_teknis' => DiklatTeknis::where('id_pegawai',$pegawai)->get(),
            'riwayat_penghargaan' => RiwayatPenghargaan::where('id_pegawai',$pegawai)->get(),
            'riwayat_istri' => RiwayatIstri::where('id_pegawai',$pegawai)->get(),
            'riwayat_anak' => RiwayatAnak::where('id_pegawai',$pegawai)->get(),
            'riwayat_orang_tua' => RiwayatOrangTua::where('id_pegawai',$pegawai)->get(),
            'riwayat_saudara' => RiwayatSaudara::where('id_pegawai',$pegawai)->get(),
            'riwayat_keahlian' => RiwayatKeahlian::where('id_pegawai',$pegawai)->get(),
            'riwayat_bahasa' => RiwayatBahasa::where('id_pegawai',$pegawai)->get(),
            'file_pegawai' => FilePegawai::where('id_pegawai',$pegawai)->get(),
            'foto_profil' => $this->getImageProfilBase64($pegawai)
        ]; // Isi data yang ingin Anda tampilkan dalam tampilan Blade

        // return $data;

        $pdf = PDF::loadView('pegawai.profil.cetak_profil', $data);

        return $pdf->stream('example.pdf');

    }

    public function updateDataPribadi(pegawaiUpdateRequest $request, $params){
        $data = array();
        try {
            $data = Pegawai::where('uuid',$params)->first();
            $data->id_satuan_kerja = $request->id_satuan_kerja;
            $data->nip = $request->nip;
            $data->nama = $request->nama;
            $data->tempat_lahir = $request->tempat_lahir;
            $data->tanggal_lahir = $request->tanggal_lahir;
            $data->jenis_kelamin = $request->jenis_kelamin;
            $data->agama = $request->agama;
            $data->status_perkawinan = $request->status_perkawinan;
            $data->tmt_pegawai = $request->tmt_pegawai;
            $data->golongan = $request->golongan;
            $data->tmt_golongan = $request->tmt_golongan;
            $data->tmt_jabatan = $request->tmt_jabatan;
            $data->pendidikan = $request->pendidikan;
            $data->pendidikan_lulus = $request->pendidikan_lulus;
            $data->pendidikan_struktural = $request->pendidikan_struktural;
            $data->pendidikan_struktural_lulus = $request->pendidikan_struktural_lulus;
            $data->status_kepegawaian = $request->status_kepegawaian;
            $data->tipe_pegawai = $request->tipe_pegawai;
            $data->user_update = hasRole()['id'];
            $data->save();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Pegawai Update success');
    }

    // RIWAYAT PENDIDIKAN FORMAL

    public function datatable_pendidikan_format(){
        $data = array();
        $data = RiwayatPendidikanFormal::where('id_pegawai',Auth::user()->id_pegawai)->get();

        return $this->sendResponse($data, 'Riwayat Pendidikan Format get success');
    }

    public function store_pendidikan_formal(Request $request){
        try {
            $customMessages = [
                'pendidikan.required' => 'kolom tingkat pendidikan tidak boleh kosong.',
                'fakultas.required' => 'kolom fakultas tidak boleh kosong.',
                'nomor_ijazah.required' => 'kolom nomor ijazah tidak boleh kosong.',
                'tanggal.required' => 'kolom tanggal tidak boleh kosong.',
                'pimpinan.required' => 'kolom pimpinan tidak boleh kosong.',
                'nama_sekolah.required' => 'kolom nama sekolah/perguruan tinggi tidak boleh kosong.',
                'alamat.required' => 'kolom alamat tidak boleh kosong.',
                'foto_ijazah.required' => 'kolom foto ijazah tidak boleh kosong.',
            ];

            $request->validate([
                'pendidikan' => 'required',
                'fakultas' => 'required',
                'nomor_ijazah' => 'required',
                'tanggal' => 'required',
                'pimpinan' => 'required',
                'nama_sekolah' => 'required',
                'alamat' => 'required',
                'foto_ijazah' => 'required|file|max:500|mimes:pdf',
            ], $customMessages);

            $data = new RiwayatPendidikanFormal();
            $data->pendidikan = $request->pendidikan;
            $data->fakultas = $request->fakultas;
            $data->nomor_ijazah = $request->nomor_ijazah;
            $data->tanggal = $request->tanggal;
            $data->pimpinan = $request->pimpinan;
            $data->nama_sekolah = $request->nama_sekolah;
            $data->alamat = $request->alamat;
            $data->id_pegawai = Auth::user()->id_pegawai;
            if (isset($request->foto_ijazah)) {
                $file_konten = $request->file('foto_ijazah');
                $filePath = Storage::disk('sftp')->put('/sftpasn/profil/ijazah', $file_konten);
                $data->foto_ijazah =  $filePath;
            }
            $data->save();
            
            if ($data) {
                $data->show_collapse = 'kt_accordion_1_body_1';
            }

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Pendidikan Formal Added success');

    }

    public function update_pendidikan_formal(Request $request, $params){
        try {
            $customMessages = [
                'pendidikan.required' => 'kolom tingkat pendidikan tidak boleh kosong.',
                'fakultas.required' => 'kolom fakultas tidak boleh kosong.',
                'nomor_ijazah.required' => 'kolom nomor ijazah tidak boleh kosong.',
                'tanggal.required' => 'kolom tanggal tidak boleh kosong.',
                'pimpinan.required' => 'kolom pimpinan tidak boleh kosong.',
                'nama_sekolah.required' => 'kolom nama sekolah/perguruan tinggi tidak boleh kosong.',
                'alamat.required' => 'kolom alamat tidak boleh kosong.',
            ];

            $request->validate([
                'pendidikan' => 'required',
                'fakultas' => 'required',
                'nomor_ijazah' => 'required',
                'tanggal' => 'required',
                'pimpinan' => 'required',
                'nama_sekolah' => 'required',
                'alamat' => 'required',
                'foto_ijazah' => 'nullable|file|max:500|mimes:pdf',
            ], $customMessages);

            $data = RiwayatPendidikanFormal::where('uuid',$params)->first();
            $data->pendidikan = $request->pendidikan;
            $data->fakultas = $request->fakultas;
            $data->nomor_ijazah = $request->nomor_ijazah;
            $data->tanggal = $request->tanggal;
            $data->pimpinan = $request->pimpinan;
            $data->nama_sekolah = $request->nama_sekolah;
            $data->alamat = $request->alamat;
            $data->id_pegawai = Auth::user()->id_pegawai;
            if (isset($request->foto_ijazah)) {
                $file_konten = $request->file('foto_ijazah');
                $filePath = Storage::disk('sftp')->put('/sftpasn/profil/ijazah', $file_konten);
                $data->foto_ijazah =  $filePath;
            }
            $data->save();

            if ($data) {
                $data->show_collapse = 'kt_accordion_1_body_1';
            }
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Pendidikan Formal Added success');
    }

    public function show_pendidikan_formal($params){
        $data = array();
        try {
            $data = RiwayatPendidikanFormal::where('uuid',$params)->first();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Pendidikan Formal show success'); 
    }

    public function delete_pendidikan_formal(Request $request, $params){
        $data = array();
        try {
            RiwayatPendidikanFormal::where('uuid', $params)->delete();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Pendidikan Formal Delete success');
    }

    // END RIWAYAT PENDIDIKAN FORMAL

    // RIWAYAT PENDIDIKAN NON FORMAL

    public function datatable_pendidikan_non_formal(){
        $data = array();
        $data = RiwayatPendidikanNonFormal::where('id_pegawai',Auth::user()->id_pegawai)->get();

        return $this->sendResponse($data, 'Riwayat Pendidikan non Format get success');
    }

    public function store_pendidikan_non_formal(Request $request){
        try {
            $customMessages = [
                'nama_kursus.required' => 'kolom nama kursus tidak boleh kosong.',
                'tanggal_mulai.required' => 'kolom tanggal mulai tidak boleh kosong.',
                'tanggal_selesai.required' => 'kolom tanggal selesai tidak boleh kosong.',
                'tanggal.required' => 'kolom tanggal tidak boleh kosong.',
                'nomor.required' => 'kolom nomor tidak boleh kosong.',
                'nama_pejabat.required' => 'kolom nama pejabat tidak boleh kosong.',
                'penyelenggara.required' => 'kolom penyelenggara tidak boleh kosong.',
                'nama_tempat.required' => 'kolom nama tempat tidak boleh kosong.',
                
                'foto_ijazah.required' => 'kolom foto ijazah tidak boleh kosong.',
            ];

            $request->validate([
                'nama_kursus' => 'required',
                'tanggal_mulai' => 'required',
                'tanggal_selesai' => 'required',
                'tanggal' => 'required',
                'nomor' => 'required',
                'nama_pejabat' => 'required',
                'penyelenggara' => 'required',
                'nama_tempat' => 'required',
                'foto_ijazah' => 'required|file|max:500|mimes:pdf',
            ], $customMessages);

            $data = new RiwayatPendidikanNonFormal();
            $data->nama_kursus = $request->nama_kursus;
            $data->tanggal_mulai = $request->tanggal_mulai;
            $data->tanggal_selesai = $request->tanggal_selesai;
            $data->nomor = $request->nomor;
            $data->tanggal = $request->tanggal;
            $data->nama_pejabat = $request->nama_pejabat;
            $data->penyelenggara = $request->penyelenggara;
            $data->nama_tempat = $request->nama_tempat;
            $data->id_pegawai = Auth::user()->id_pegawai;
            $data->user_insert = Auth::user()->id;
            $data->user_update = Auth::user()->id;
            if (isset($request->foto_ijazah)) {
                $file_konten = $request->file('foto_ijazah');
                $filePath = Storage::disk('sftp')->put('/sftpasn/profil/ijazah', $file_konten);
                $data->foto_ijazah =  $filePath;
            }
            $data->save();

            if ($data) {
                $data->show_collapse = 'kt_accordion_1_body_2';
            }

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 422);
        }
        return $this->sendResponse($data, 'Pendidikan Non Formal Added success');

    }

    public function update_pendidikan_non_formal(Request $request, $params){
        try {
            $customMessages = [
                'nama_kursus.required' => 'kolom nama kursus tidak boleh kosong.',
                'tanggal_mulai.required' => 'kolom tanggal mulai tidak boleh kosong.',
                'tanggal_selesai.required' => 'kolom tanggal selesai tidak boleh kosong.',
                'tanggal.required' => 'kolom tanggal tidak boleh kosong.',
                'nomor.required' => 'kolom nomor tidak boleh kosong.',
                'nama_pejabat.required' => 'kolom nama pejabat tidak boleh kosong.',
                'penyelenggara.required' => 'kolom penyelenggara tidak boleh kosong.',
                'nama_tempat.required' => 'kolom nama tempat tidak boleh kosong.',
            ];

            $request->validate([
                'nama_kursus' => 'required',
                'tanggal_mulai' => 'required',
                'tanggal_selesai' => 'required',
                'tanggal' => 'required',
                'nomor' => 'required',
                'nama_pejabat' => 'required',
                'penyelenggara' => 'required',
                'nama_tempat' => 'required',
                'foto_ijazah' => 'nullable|file|max:500|mimes:pdf',
            ], $customMessages);

            $data = RiwayatPendidikanNonFormal::where('uuid',$params)->first();
            $data->nama_kursus = $request->nama_kursus;
            $data->tanggal_mulai = $request->tanggal_mulai;
            $data->tanggal_selesai = $request->tanggal_selesai;
            $data->nomor = $request->nomor;
            $data->tanggal = $request->tanggal;
            $data->nama_pejabat = $request->nama_pejabat;
            $data->penyelenggara = $request->penyelenggara;
            $data->nama_tempat = $request->nama_tempat;
            $data->id_pegawai = Auth::user()->id_pegawai;
            $data->user_update = Auth::user()->id;
            if (isset($request->foto_ijazah)) {
                $file_konten = $request->file('foto_ijazah');
                $filePath = Storage::disk('sftp')->put('/sftpasn/profil/ijazah', $file_konten);
                $data->foto_ijazah =  $filePath;
            }
            $data->save();

            if ($data) {
                $data->show_collapse = 'kt_accordion_1_body_2';
            }

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Pendidikan Formal Added success');
    }

    public function show_pendidikan_non_formal($params){
        $data = array();
        try {
            $data = RiwayatPendidikanNonFormal::where('uuid',$params)->first();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Pendidikan Formal show success'); 
    }

    public function delete_pendidikan_non_formal(Request $request, $params){
        $data = array();
        try {
            RiwayatPendidikanNonFormal::where('uuid', $params)->delete();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Pendidikan Formal Delete success');
    }

    // END RIWAYAT PENDIDIDKAN NON FORMAL

    // RIWAYAT KEPANGKATAN

    public function datatable_riwayat_kepangkatan(){
        $data = array();
        $data = RiwayatKepangkatan::where('id_pegawai',Auth::user()->id_pegawai)->get();
        return $this->sendResponse($data, 'Riwayat Pendidikan non Format get success');
    }

    public function store_riwayat_kepangkatan(Request $request){
        try {
            $customMessages = [
                'golongan.required' => 'kolom golongan tidak boleh kosong.',
                'gaji_pokok.required' => 'kolom gaji pokok tidak boleh kosong.',
                'tahun.required' => 'kolom tahun tidak boleh kosong.',
                'tahun.numeric' => 'kolom tahun harus berupa angka.',
                'bulan.required' => 'kolom bulan tidak boleh kosong.',
                'tanggal.required' => 'kolom tanggal tidak boleh kosong.',
                'nomor.required' => 'kolom nomor tidak boleh kosong.',
                'pejabat_pendantanganan.required' => 'kolom pejabat pendantanganan tidak boleh kosong.',
                'tmt.required' => 'kolom tmt tidak boleh kosong.',
                'nama_unit_kerja.required' => 'kolom nama unit kerja tidak boleh kosong.',
                'surat_keputusan.required' => 'kolom surat keputusan tidak boleh kosong.',
            ];

            $request->validate([
                'golongan' => 'required',
                'gaji_pokok' => 'required',
                'tahun' => 'required|numeric',
                'bulan' => 'required',
                'tanggal' => 'required',
                'nomor' => 'required',
                'pejabat_pendantanganan' => 'required',
                'tmt' => 'required',
                'nama_unit_kerja' => 'required',
                'surat_keputusan' => 'required|file|max:500|mimes:pdf',
            ], $customMessages);

            $data = new RiwayatKepangkatan();
            $data->golongan = $request->golongan;
            $data->gaji_pokok = $request->gaji_pokok;
            $data->tahun = $request->tahun;
            $data->bulan = $request->bulan;
            $data->nomor = $request->nomor;
            $data->tanggal = $request->tanggal;
            $data->pejabat_pendantanganan = $request->pejabat_pendantanganan;
            $data->tmt = $request->tmt;
            $data->nama_unit_kerja = $request->nama_unit_kerja;
            $data->id_pegawai = Auth::user()->id_pegawai;
            $data->user_insert = Auth::user()->id;
            $data->user_update = Auth::user()->id;
            if (isset($request->surat_keputusan)) {
                $file_konten = $request->file('surat_keputusan');
                $filePath = Storage::disk('sftp')->put('/sftpasn/profil/surat_keputusan', $file_konten);
                $data->surat_keputusan =  $filePath;
            }
            $data->save();

            if ($data) {
                $data->show_collapse = 'kt_accordion_1_body_3';
            }

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Riwayat Kepangkatan Added success');

    }

    public function update_riwayat_kepangkatan(Request $request, $params){
        try {
            $customMessages = [
                'golongan.required' => 'kolom golongan tidak boleh kosong.',
                'gaji_pokok.required' => 'kolom gaji pokok tidak boleh kosong.',
                'tahun.required' => 'kolom tahun tidak boleh kosong.',
                'bulan.required' => 'kolom bulan tidak boleh kosong.',
                'tanggal.required' => 'kolom tanggal tidak boleh kosong.',
                'nomor.required' => 'kolom nomor tidak boleh kosong.',
                'pejabat_pendantanganan.required' => 'kolom pejabat pendantanganan tidak boleh kosong.',
                'tmt.required' => 'kolom tmt tidak boleh kosong.',
                'nama_unit_kerja.required' => 'kolom nama unit kerja tidak boleh kosong.',
            ];

            $request->validate([
                'golongan' => 'required',
                'gaji_pokok' => 'required',
                'tahun' => 'required',
                'bulan' => 'required',
                'tanggal' => 'required',
                'nomor' => 'required',
                'pejabat_pendantanganan' => 'required',
                'tmt' => 'required',
                'nama_unit_kerja' => 'required',
                'surat_keputusan' => 'nullable|file|max:500|mimes:pdf',
            ], $customMessages);

            $data = RiwayatKepangkatan::where('uuid',$params)->first();
            $data->golongan = $request->golongan;
            $data->gaji_pokok = $request->gaji_pokok;
            $data->tahun = $request->tahun;
            $data->bulan = $request->bulan;
            $data->nomor = $request->nomor;
            $data->tanggal = $request->tanggal;
            $data->pejabat_pendantanganan = $request->pejabat_pendantanganan;
            $data->tmt = $request->tmt;
            $data->nama_unit_kerja = $request->nama_unit_kerja;
            $data->id_pegawai = Auth::user()->id_pegawai;
            $data->user_insert = Auth::user()->id;
            $data->user_update = Auth::user()->id;
            if (isset($request->surat_keputusan)) {
                $file_konten = $request->file('surat_keputusan');
                $filePath = Storage::disk('sftp')->put('/sftpasn/profil/surat_keputusan', $file_konten);
                $data->surat_keputusan =  $filePath;
            }
            $data->save();

            if ($data) {
                $data->show_collapse = 'kt_accordion_1_body_3';
            }

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Pendidikan Formal Added success');
    }

    public function show_riwayat_kepangkatan($params){
        $data = array();
        try {
            $data = RiwayatKepangkatan::where('uuid',$params)->first();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Pendidikan Formal show success'); 
    }

    public function delete_riwayat_kepangkatan(Request $request, $params){
        $data = array();
        try {
            RiwayatKepangkatan::where('uuid', $params)->delete();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Pendidikan Formal Delete success');
    }

    // END RIWAYAT KEPANGKATAN

    // RIWAYAT JABATAN

    public function store_riwayat_jabatan(Request $request){
        try {
            $customMessages = [
                'golongan.required' => 'kolom golongan tidak boleh kosong.',
                'nama_jabatan.required' => 'kolom nama jabatan tidak boleh kosong.',
                'tanggal.required' => 'kolom tanggal tidak boleh kosong.',
                'nomor.required' => 'kolom nomor tidak boleh kosong.',
                'pejabat_pendantanganan.required' => 'kolom pejabat pendantanganan tidak boleh kosong.',
                'tmt.required' => 'kolom tmt tidak boleh kosong.',
                'nama_unit_kerja.required' => 'kolom nama unit kerja tidak boleh kosong.',
                'surat_keputusan.required' => 'kolom surat keputusan tidak boleh kosong.',
            ];

            $request->validate([
                'golongan' => 'required',
                'nama_jabatan' => 'required',
                'tanggal' => 'required',
                'nomor' => 'required',
                'pejabat_pendantanganan' => 'required',
                'tmt' => 'required',
                'nama_unit_kerja' => 'required',
                'surat_keputusan' => 'required|file|max:1000|mimes:pdf',
            ], $customMessages);

            $data = new RiwayatJabatan();
            $data->nama_jabatan = $request->nama_jabatan;
            $data->golongan = $request->golongan;
            $data->nomor = $request->nomor;
            $data->tanggal = $request->tanggal;
            $data->pejabat_pendantanganan = $request->pejabat_pendantanganan;
            $data->tmt = $request->tmt;
            $data->nama_unit_kerja = $request->nama_unit_kerja;
            $data->id_pegawai = Auth::user()->id_pegawai;
            $data->user_insert = Auth::user()->id;
            $data->user_update = Auth::user()->id;
            if (isset($request->surat_keputusan)) {
                $file_konten = $request->file('surat_keputusan');
                $filePath = Storage::disk('sftp')->put('/sftpasn/profil/surat_keputusan', $file_konten);
                $data->surat_keputusan =  $filePath;
            }
            $data->save();

            if ($data) {
                $data->show_collapse = 'kt_accordion_1_body_4';
            }

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Riwayat Kepangkatan Added success');

    }

    public function update_riwayat_jabatan(Request $request, $params){
        try {
            $customMessages = [
                'golongan.required' => 'kolom golongan tidak boleh kosong.',
                'nama_jabatan.required' => 'kolom nama jabatan tidak boleh kosong.',
                'tanggal.required' => 'kolom tanggal tidak boleh kosong.',
                'nomor.required' => 'kolom nomor tidak boleh kosong.',
                'pejabat_pendantanganan.required' => 'kolom pejabat pendantanganan tidak boleh kosong.',
                'tmt.required' => 'kolom tmt tidak boleh kosong.',
                'nama_unit_kerja.required' => 'kolom nama unit kerja tidak boleh kosong.',
            ];

            $request->validate([
                'golongan' => 'required',
                'nama_jabatan' => 'required',
                'tanggal' => 'required',
                'nomor' => 'required',
                'pejabat_pendantanganan' => 'required',
                'tmt' => 'required',
                'nama_unit_kerja' => 'required',
                'surat_keputusan' => 'nullable|file|max:500|mimes:pdf',
            ], $customMessages);

            $data = RiwayatJabatan::where('uuid',$params)->first();
            $data->nama_jabatan = $request->nama_jabatan;
            $data->golongan = $request->golongan;
            $data->nomor = $request->nomor;
            $data->tanggal = $request->tanggal;
            $data->pejabat_pendantanganan = $request->pejabat_pendantanganan;
            $data->tmt = $request->tmt;
            $data->nama_unit_kerja = $request->nama_unit_kerja;
            $data->id_pegawai = Auth::user()->id_pegawai;
            $data->user_insert = Auth::user()->id;
            $data->user_update = Auth::user()->id;
            if (isset($request->surat_keputusan)) {
                $file_konten = $request->file('surat_keputusan');
                $filePath = Storage::disk('sftp')->put('/sftpasn/profil/surat_keputusan', $file_konten);
                $data->surat_keputusan =  $filePath;
            }
            $data->save();

            if ($data) {
                $data->show_collapse = 'kt_accordion_1_body_4';
            }

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Pendidikan Formal Added success');
    }

    public function show_riwayat_jabatan($params){
        $data = array();
        try {
            $data = RiwayatJabatan::where('uuid',$params)->first();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Pendidikan Formal show success'); 
    }

    public function delete_riwayat_jabatan(Request $request, $params){
        $data = array();
        try {
            RiwayatJabatan::where('uuid', $params)->delete();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Pendidikan Formal Delete success');
    }
    // END RIWAYAT JABATAN

    // CATATAN HUKUMAN DINAS
    public function store_catatan_hukuman_dinas(Request $request){
        try {
            $customMessages = [
                'kategori_hukuman.required' => 'kolom kategori hukuman tidak boleh kosong.',
                'nama_hukuman.required' => 'kolom nama hukuman tidak boleh kosong.',
                'nama_sk.required' => 'kolom nama sk tidak boleh kosong.',
                'tanggal_sk.required' => 'kolom tanggal sk tidak boleh kosong.',
                'tanggal_mulai.required' => 'kolom tanggal mulai tidak boleh kosong.',
                'tanggal_selesai.required' => 'kolom tanggal selesai tidak boleh kosong.',
                'keterangan_pelanggaran.required' => 'kolom keterangan pelanggaran tidak boleh kosong.',
                'surat_keputusan.required' => 'kolom surat keputusan tidak boleh kosong.',
            ];

            $request->validate([
                'kategori_hukuman' => 'required',
                'nama_hukuman' => 'required',
                'nama_sk' => 'required',
                'tanggal_sk' => 'required',
                'tanggal_mulai' => 'required',
                'tanggal_selesai' => 'required',
                'keterangan_pelanggaran' => 'required',
                'surat_keputusan' => 'required|file|max:500|mimes:pdf',
            ], $customMessages);

            $data = new CatatanHukumanDinas();
            $data->kategori_hukuman = $request->kategori_hukuman;
            $data->nama_hukuman = $request->nama_hukuman;
            $data->nama_sk = $request->nama_sk;
            $data->tanggal_sk = $request->tanggal_sk;
            $data->tanggal_mulai = $request->tanggal_mulai;
            $data->tanggal_selesai = $request->tanggal_selesai;
            $data->keterangan_pelanggaran = $request->keterangan_pelanggaran;
            $data->id_pegawai = Auth::user()->id_pegawai;
            $data->user_insert = Auth::user()->id;
            $data->user_update = Auth::user()->id;
            if (isset($request->surat_keputusan)) {
                $file_konten = $request->file('surat_keputusan');
                $filePath = Storage::disk('sftp')->put('/sftpasn/profil/surat_keputusan', $file_konten);
                $data->surat_keputusan =  $filePath;
            }
            $data->save();

            if ($data) {
                $data->show_collapse = 'kt_accordion_1_body_5';
            }

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Catatan Hukuman Dinas Added success');

    }

    public function update_catatan_hukuman_dinas(Request $request, $params){
        try {
            $customMessages = [
                'kategori_hukuman.required' => 'kolom kategori hukuman tidak boleh kosong.',
                'nama_hukuman.required' => 'kolom nama hukuman tidak boleh kosong.',
                'nama_sk.required' => 'kolom nama sk tidak boleh kosong.',
                'tanggal_sk.required' => 'kolom tanggal sk tidak boleh kosong.',
                'tanggal_mulai.required' => 'kolom tanggal mulai tidak boleh kosong.',
                'tanggal_selesai.required' => 'kolom tanggal selesai tidak boleh kosong.',
                'keterangan_pelanggaran.required' => 'kolom keterangan pelanggaran tidak boleh kosong.',
            ];

            $request->validate([
                'kategori_hukuman' => 'required',
                'nama_hukuman' => 'required',
                'nama_sk' => 'required',
                'tanggal_sk' => 'required',
                'tanggal_mulai' => 'required',
                'tanggal_selesai' => 'required',
                'keterangan_pelanggaran' => 'required',
                'surat_keputusan' => 'nullable|file|max:500|mimes:pdf',
            ], $customMessages);

            $data = CatatanHukumanDinas::where('uuid',$params)->first();
            $data->kategori_hukuman = $request->kategori_hukuman;
            $data->nama_hukuman = $request->nama_hukuman;
            $data->nama_sk = $request->nama_sk;
            $data->tanggal_sk = $request->tanggal_sk;
            $data->tanggal_mulai = $request->tanggal_mulai;
            $data->tanggal_selesai = $request->tanggal_selesai;
            $data->keterangan_pelanggaran = $request->keterangan_pelanggaran;
            $data->id_pegawai = Auth::user()->id_pegawai;
            $data->user_insert = Auth::user()->id;
            $data->user_update = Auth::user()->id;
            if (isset($request->surat_keputusan)) {
                $file_konten = $request->file('surat_keputusan');
                $filePath = Storage::disk('sftp')->put('/sftpasn/profil/surat_keputusan', $file_konten);
                $data->surat_keputusan =  $filePath;
            }
            $data->save();

            if ($data) {
                $data->show_collapse = 'kt_accordion_1_body_5';
            }

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Catatan Hukuman Dinas Added success');
    }

    public function show_catatan_hukuman_dinas($params){
        $data = array();
        try {
            $data = CatatanHukumanDinas::where('uuid',$params)->first();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Catatan Hukuman Dinas show success'); 
    }

    public function delete_catatan_hukuman_dinas(Request $request, $params){
        $data = array();
        try {
            CatatanHukumanDinas::where('uuid', $params)->delete();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Catatan Hukuman Dinas Delete success');
    }
    // END HUKUMAN DINAS

    // DIKLAT STRUKTURAL
    public function store_diklat_struktural(Request $request){
        try {
            $customMessages = [
                'kategori_diklat_struktural.required' => 'kolom kategori diklat struktural tidak boleh kosong.',
                'nama_diklat_struktural.required' => 'kolom nama diklat struktural tidak boleh kosong.',
                'tanggal_mulai.required' => 'kolom tanggal mulai tidak boleh kosong.',
                'tanggal_selesai.required' => 'kolom tanggal selesai tidak boleh kosong.',
                'jumlah_jam.required' => 'kolom jumlah jam tidak boleh kosong.',
                'nomor_sttb' => 'kolom nomor sttb tidak boleh kosong.',
                'tanggal' => 'kolom tanggal tidak boleh kosong.',
                'pejabat_pendantanganan' => 'kolom pejabat pendantanganan tidak boleh kosong.',
                'nama_instansi' => 'kolom nama instansi tidak boleh kosong.',
                'sertifikat.required' => 'kolom sertifikat tidak boleh kosong.',
                'lokasi' => 'kolom lokasi tidak boleh kosong.',
            ];

            $request->validate([
                'kategori_diklat_struktural' => 'required',
                'nama_diklat_struktural' => 'required',
                'tanggal_mulai' => 'required',
                'tanggal_selesai' => 'required',
                'jumlah_jam' => 'required',
                'nomor_sttb' => 'required',
                'tanggal' => 'required',
                'pejabat_pendantanganan' => 'required',
                'nama_instansi' => 'required',
                'lokasi' => 'required',
                'sertifikat' => 'required|file|max:500|mimes:pdf',
            ], $customMessages);

            $data = new DiklatStruktural();
            $data->kategori_diklat_struktural = $request->kategori_diklat_struktural;
            $data->nama_diklat_struktural = $request->nama_diklat_struktural;
            $data->tanggal_mulai = $request->tanggal_mulai;
            $data->tanggal_selesai = $request->tanggal_selesai;
            $data->jumlah_jam = $request->jumlah_jam;
            $data->nomor_sttb = $request->nomor_sttb;
            $data->tanggal = $request->tanggal;
            $data->pejabat_pendantanganan = $request->pejabat_pendantanganan;
            $data->nama_instansi = $request->nama_instansi;
            $data->lokasi = $request->lokasi;
            $data->id_pegawai = Auth::user()->id_pegawai;
            $data->user_insert = Auth::user()->id;
            $data->user_update = Auth::user()->id;
            if (isset($request->sertifikat)) {
                $file_konten = $request->file('sertifikat');
                $filePath = Storage::disk('sftp')->put('/sftpasn/profil/sertifikat', $file_konten);
                $data->sertifikat =  $filePath;
            }
            $data->save();

            if ($data) {
                $data->show_collapse = 'kt_accordion_1_body_6';
            }

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Catatan Hukuman Dinas Added success');

    }

    public function update_diklat_struktural(Request $request, $params){
        try {
            $customMessages = [
                'kategori_diklat_struktural.required' => 'kolom kategori diklat struktural tidak boleh kosong.',
                'nama_diklat_struktural.required' => 'kolom nama diklat struktural tidak boleh kosong.',
                'tanggal_mulai.required' => 'kolom tanggal mulai tidak boleh kosong.',
                'tanggal_selesai.required' => 'kolom tanggal selesai tidak boleh kosong.',
                'jumlah_jam.required' => 'kolom jumlah jam tidak boleh kosong.',
                'nomor_sttb' => 'kolom nomor sttb tidak boleh kosong.',
                'tanggal' => 'kolom tanggal tidak boleh kosong.',
                'pejabat_pendantanganan' => 'kolom pejabat pendantanganan tidak boleh kosong.',
                'nama_instansi' => 'kolom nama instansi tidak boleh kosong.',
                'lokasi' => 'kolom lokasi tidak boleh kosong.',
            ];

            $request->validate([
                'kategori_diklat_struktural' => 'required',
                'nama_diklat_struktural' => 'required',
                'tanggal_mulai' => 'required',
                'tanggal_selesai' => 'required',
                'jumlah_jam' => 'required',
                'nomor_sttb' => 'required',
                'tanggal' => 'required',
                'pejabat_pendantanganan' => 'required',
                'nama_instansi' => 'required',
                'lokasi' => 'required',
                'sertifikat' => 'nullable|file|max:500|mimes:pdf',
            ], $customMessages);

            $data = DiklatStruktural::where('uuid',$params)->first();
            $data->kategori_diklat_struktural = $request->kategori_diklat_struktural;
            $data->nama_diklat_struktural = $request->nama_diklat_struktural;
            $data->tanggal_mulai = $request->tanggal_mulai;
            $data->tanggal_selesai = $request->tanggal_selesai;
            $data->jumlah_jam = $request->jumlah_jam;
            $data->nomor_sttb = $request->nomor_sttb;
            $data->tanggal = $request->tanggal;
            $data->pejabat_pendantanganan = $request->pejabat_pendantanganan;
            $data->nama_instansi = $request->nama_instansi;
            $data->lokasi = $request->lokasi;
            $data->id_pegawai = Auth::user()->id_pegawai;
            $data->user_insert = Auth::user()->id;
            $data->user_update = Auth::user()->id;
            if (isset($request->sertifikat)) {
                $file_konten = $request->file('sertifikat');
                $filePath = Storage::disk('sftp')->put('/sftpasn/profil/sertifikat', $file_konten);
                $data->sertifikat =  $filePath;
            }
            $data->save();

            if ($data) {
                $data->show_collapse = 'kt_accordion_1_body_6';
            }

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Catatan Hukuman Dinas Added success');
    }

    public function show_diklat_struktural($params){
        $data = array();
        try {
            $data = DiklatStruktural::where('uuid',$params)->first();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Catatan Hukuman Dinas show success'); 
    }

    public function delete_diklat_struktural(Request $request, $params){
        $data = array();
        try {
            DiklatStruktural::where('uuid', $params)->delete();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Catatan Hukuman Dinas Delete success');
    }
    // END DIKLAT STRUKTURAL

    // DIKLAT FUNGSIONAL
    public function store_diklat_fungsional(Request $request){
        try {
            $customMessages = [
                'nama_diklat_fungsional.required' => 'kolom nama diklat fungsional tidak boleh kosong.',
                'tanggal_mulai.required' => 'kolom tanggal mulai tidak boleh kosong.',
                'tanggal_selesai.required' => 'kolom tanggal selesai tidak boleh kosong.',
                'jumlah_jam.required' => 'kolom jumlah jam tidak boleh kosong.',
                'nomor_sttb' => 'kolom nomor sttb tidak boleh kosong.',
                'tanggal' => 'kolom tanggal tidak boleh kosong.',
                'pejabat_pendantanganan' => 'kolom pejabat pendantanganan tidak boleh kosong.',
                'nama_instansi' => 'kolom nama instansi tidak boleh kosong.',
                'sertifikat.required' => 'kolom sertifikat tidak boleh kosong.',
                'lokasi' => 'kolom lokasi tidak boleh kosong.',
            ];

            $request->validate([
                'nama_diklat_fungsional' => 'required',
                'tanggal_mulai' => 'required',
                'tanggal_selesai' => 'required',
                'jumlah_jam' => 'required',
                'nomor_sttb' => 'required',
                'tanggal' => 'required',
                'pejabat_pendantanganan' => 'required',
                'nama_instansi' => 'required',
                'lokasi' => 'required',
                'sertifikat' => 'required|file|max:500|mimes:pdf',
            ], $customMessages);

            $data = new DiklatFungsional();
            $data->nama_diklat_fungsional = $request->nama_diklat_fungsional;
            $data->tanggal_mulai = $request->tanggal_mulai;
            $data->tanggal_selesai = $request->tanggal_selesai;
            $data->jumlah_jam = $request->jumlah_jam;
            $data->nomor_sttb = $request->nomor_sttb;
            $data->tanggal = $request->tanggal;
            $data->pejabat_pendantanganan = $request->pejabat_pendantanganan;
            $data->nama_instansi = $request->nama_instansi;
            $data->lokasi = $request->lokasi;
            $data->id_pegawai = Auth::user()->id_pegawai;
            $data->user_insert = Auth::user()->id;
            $data->user_update = Auth::user()->id;
            if (isset($request->sertifikat)) {
                $file_konten = $request->file('sertifikat');
                $filePath = Storage::disk('sftp')->put('/sftpasn/profil/sertifikat', $file_konten);
                $data->sertifikat =  $filePath;
            }
            $data->save();

            if ($data) {
                $data->show_collapse = 'kt_accordion_1_body_7';
            }

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Diklat Fungsional Added success');

    }

    public function update_diklat_fungsional(Request $request, $params){
        try {
            $customMessages = [
                'nama_diklat_fungsional.required' => 'kolom nama diklat fungsional tidak boleh kosong.',
                'tanggal_mulai.required' => 'kolom tanggal mulai tidak boleh kosong.',
                'tanggal_selesai.required' => 'kolom tanggal selesai tidak boleh kosong.',
                'jumlah_jam.required' => 'kolom jumlah jam tidak boleh kosong.',
                'nomor_sttb' => 'kolom nomor sttb tidak boleh kosong.',
                'tanggal' => 'kolom tanggal tidak boleh kosong.',
                'pejabat_pendantanganan' => 'kolom pejabat pendantanganan tidak boleh kosong.',
                'nama_instansi' => 'kolom nama instansi tidak boleh kosong.',
                'lokasi' => 'kolom lokasi tidak boleh kosong.',
            ];

            $request->validate([
                'nama_diklat_fungsional' => 'required',
                'tanggal_mulai' => 'required',
                'tanggal_selesai' => 'required',
                'jumlah_jam' => 'required',
                'nomor_sttb' => 'required',
                'tanggal' => 'required',
                'pejabat_pendantanganan' => 'required',
                'nama_instansi' => 'required',
                'lokasi' => 'required',
                'sertifikat' => 'nullable|file|max:500|mimes:pdf',
            ], $customMessages);

            $data = DiklatFungsional::where('uuid',$params)->first();
            $data->nama_diklat_fungsional = $request->nama_diklat_fungsional;
            $data->tanggal_mulai = $request->tanggal_mulai;
            $data->tanggal_selesai = $request->tanggal_selesai;
            $data->jumlah_jam = $request->jumlah_jam;
            $data->nomor_sttb = $request->nomor_sttb;
            $data->tanggal = $request->tanggal;
            $data->pejabat_pendantanganan = $request->pejabat_pendantanganan;
            $data->nama_instansi = $request->nama_instansi;
            $data->lokasi = $request->lokasi;
            $data->id_pegawai = Auth::user()->id_pegawai;
            $data->user_insert = Auth::user()->id;
            $data->user_update = Auth::user()->id;
            if (isset($request->sertifikat)) {
                $file_konten = $request->file('sertifikat');
                $filePath = Storage::disk('sftp')->put('/sftpasn/profil/sertifikat', $file_konten);
                $data->sertifikat =  $filePath;
            }
            $data->save();

            if ($data) {
                $data->show_collapse = 'kt_accordion_1_body_7';
            }

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Diklat Fungsional Added success');
    }

    public function show_diklat_fungsional($params){
        $data = array();
        try {
            $data = DiklatFungsional::where('uuid',$params)->first();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Diklat Fungsional show success'); 
    }

    public function delete_diklat_fungsional(Request $request, $params){
        $data = array();
        try {
            DiklatFungsional::where('uuid', $params)->delete();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Diklat Fungsional Delete success');
    }
    // END DIKLAT FUNGSIONA

    // DIKLAT TEKNIS
    public function store_diklat_teknis(Request $request){
        try {
            $customMessages = [
                'nama_diklat_teknis.required' => 'kolom nama diklat teknis tidak boleh kosong.',
                'tanggal_mulai.required' => 'kolom tanggal mulai tidak boleh kosong.',
                'tanggal_selesai.required' => 'kolom tanggal selesai tidak boleh kosong.',
                'jumlah_jam.required' => 'kolom jumlah jam tidak boleh kosong.',
                'nomor_sttb' => 'kolom nomor sttb tidak boleh kosong.',
                'tanggal' => 'kolom tanggal tidak boleh kosong.',
                'pejabat_pendantanganan' => 'kolom pejabat pendantanganan tidak boleh kosong.',
                'nama_instansi' => 'kolom nama instansi tidak boleh kosong.',
                'sertifikat.required' => 'kolom sertifikat tidak boleh kosong.',
                'lokasi' => 'kolom lokasi tidak boleh kosong.',
            ];

            $request->validate([
                'nama_diklat_teknis' => 'required',
                'tanggal_mulai' => 'required',
                'tanggal_selesai' => 'required',
                'jumlah_jam' => 'required',
                'nomor_sttb' => 'required',
                'tanggal' => 'required',
                'pejabat_pendantanganan' => 'required',
                'nama_instansi' => 'required',
                'lokasi' => 'required',
                'sertifikat' => 'required|file|max:500|mimes:pdf',
            ], $customMessages);

            $data = new DiklatTeknis();
            $data->nama_diklat_teknis = $request->nama_diklat_teknis;
            $data->tanggal_mulai = $request->tanggal_mulai;
            $data->tanggal_selesai = $request->tanggal_selesai;
            $data->jumlah_jam = $request->jumlah_jam;
            $data->nomor_sttb = $request->nomor_sttb;
            $data->tanggal = $request->tanggal;
            $data->pejabat_pendantanganan = $request->pejabat_pendantanganan;
            $data->nama_instansi = $request->nama_instansi;
            $data->lokasi = $request->lokasi;
            $data->id_pegawai = Auth::user()->id_pegawai;
            $data->user_insert = Auth::user()->id;
            $data->user_update = Auth::user()->id;
            if (isset($request->sertifikat)) {
                $file_konten = $request->file('sertifikat');
                $filePath = Storage::disk('sftp')->put('/sftpasn/profil/sertifikat', $file_konten);
                $data->sertifikat =  $filePath;
            }
            $data->save();

            if ($data) {
                $data->show_collapse = 'kt_accordion_1_body_8';
            }

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Diklat Fungsional Added success');

    }

    public function update_diklat_teknis(Request $request, $params){
        try {
            $customMessages = [
                'nama_diklat_teknis.required' => 'kolom nama diklat teknis tidak boleh kosong.',
                'tanggal_mulai.required' => 'kolom tanggal mulai tidak boleh kosong.',
                'tanggal_selesai.required' => 'kolom tanggal selesai tidak boleh kosong.',
                'jumlah_jam.required' => 'kolom jumlah jam tidak boleh kosong.',
                'nomor_sttb' => 'kolom nomor sttb tidak boleh kosong.',
                'tanggal' => 'kolom tanggal tidak boleh kosong.',
                'pejabat_pendantanganan' => 'kolom pejabat pendantanganan tidak boleh kosong.',
                'nama_instansi' => 'kolom nama instansi tidak boleh kosong.',
                'lokasi' => 'kolom lokasi tidak boleh kosong.',
            ];

            $request->validate([
                'nama_diklat_teknis' => 'required',
                'tanggal_mulai' => 'required',
                'tanggal_selesai' => 'required',
                'jumlah_jam' => 'required',
                'nomor_sttb' => 'required',
                'tanggal' => 'required',
                'pejabat_pendantanganan' => 'required',
                'nama_instansi' => 'required',
                'lokasi' => 'required',
                'sertifikat' => 'nullable|file|max:500|mimes:pdf',
            ], $customMessages);

            $data = DiklatTeknis::where('uuid',$params)->first();
            $data->nama_diklat_teknis = $request->nama_diklat_teknis;
            $data->tanggal_mulai = $request->tanggal_mulai;
            $data->tanggal_selesai = $request->tanggal_selesai;
            $data->jumlah_jam = $request->jumlah_jam;
            $data->nomor_sttb = $request->nomor_sttb;
            $data->tanggal = $request->tanggal;
            $data->pejabat_pendantanganan = $request->pejabat_pendantanganan;
            $data->nama_instansi = $request->nama_instansi;
            $data->lokasi = $request->lokasi;
            $data->id_pegawai = Auth::user()->id_pegawai;
            $data->user_insert = Auth::user()->id;
            $data->user_update = Auth::user()->id;
            if (isset($request->sertifikat)) {
                $file_konten = $request->file('sertifikat');
                $filePath = Storage::disk('sftp')->put('/sftpasn/profil/sertifikat', $file_konten);
                $data->sertifikat =  $filePath;
            }
            $data->save();

            if ($data) {
                $data->show_collapse = 'kt_accordion_1_body_8';
            }

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Diklat Fungsional Added success');
    }

    public function show_diklat_teknis($params){
        $data = array();
        try {
            $data = DiklatTeknis::where('uuid',$params)->first();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Diklat Fungsional show success'); 
    }

    public function delete_diklat_teknis(Request $request, $params){
        $data = array();
        try {
            DiklatTeknis::where('uuid', $params)->delete();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Diklat Fungsional Delete success');
    }

    // END DIKLAT TEKNIS

    // RIWAYAT PENGHARGAAN
    public function store_riwayat_penghargaan(Request $request){
        try {
            $customMessages = [
                'nama_penghargaan.required' => 'kolom nama penghargaan tidak boleh kosong.',
                'nomor_surat_keputusan.required' => 'kolom nomor surat keputusan tidak boleh kosong.',
                'tanggal.required' => 'kolom tanggal tidak boleh kosong.',
                'pejabat_pendantanganan.required' => 'kolom pejabat pendantanganan tidak boleh kosong.',
                'nama_instansi' => 'kolom nama instansi tidak boleh kosong.',
                'lokasi' => 'kolom lokasi tidak boleh kosong.',
                'nama_instansi' => 'kolom nama instansi tidak boleh kosong.',
                'sertifikat.required' => 'kolom sertifikat tidak boleh kosong.',
                'lokasi' => 'kolom lokasi tidak boleh kosong.',
            ];

            $request->validate([
                'nama_penghargaan' => 'required',
                'nomor_surat_keputusan' => 'required',
                'tanggal' => 'required',
                'pejabat_pendantanganan' => 'required',
                'nama_instansi' => 'required',
                'lokasi' => 'required',
                'nama_instansi' => 'required',
                'lokasi' => 'required',
                'sertifikat' => 'required|file|max:500|mimes:pdf',
            ], $customMessages);

            $data = new RiwayatPenghargaan();
            $data->nama_penghargaan = $request->nama_penghargaan;
            $data->nomor_surat_keputusan = $request->nomor_surat_keputusan;
            $data->tanggal = $request->tanggal;
            $data->pejabat_pendantanganan = $request->pejabat_pendantanganan;
            $data->nama_instansi = $request->nama_instansi;
            $data->lokasi = $request->lokasi;
            $data->nama_instansi = $request->nama_instansi;
            $data->lokasi = $request->lokasi;
            $data->id_pegawai = Auth::user()->id_pegawai;
            $data->user_insert = Auth::user()->id;
            $data->user_update = Auth::user()->id;
            if (isset($request->sertifikat)) {
                $file_konten = $request->file('sertifikat');
                $filePath = Storage::disk('sftp')->put('/sftpasn/profil/sertifikat', $file_konten);
                $data->sertifikat =  $filePath;
            }
            $data->save();

            if ($data) {
                $data->show_collapse = 'kt_accordion_1_body_9';
            }

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Riwayat Penghargaan Added success');

    }

    public function update_riwayat_penghargaan(Request $request, $params){
        try {
            
            $customMessages = [
                'nama_penghargaan.required' => 'kolom nama penghargaan tidak boleh kosong.',
                'nomor_surat_keputusan.required' => 'kolom nomor surat keputusan tidak boleh kosong.',
                'tanggal.required' => 'kolom tanggal tidak boleh kosong.',
                'pejabat_pendantanganan.required' => 'kolom pejabat pendantanganan tidak boleh kosong.',
                'nama_instansi' => 'kolom nama instansi tidak boleh kosong.',
                'lokasi' => 'kolom lokasi tidak boleh kosong.',
                'nama_instansi' => 'kolom nama instansi tidak boleh kosong.',
                'lokasi' => 'kolom lokasi tidak boleh kosong.',
            ];

            $request->validate([
                'nama_penghargaan' => 'required',
                'nomor_surat_keputusan' => 'required',
                'tanggal' => 'required',
                'pejabat_pendantanganan' => 'required',
                'nama_instansi' => 'required',
                'lokasi' => 'required',
                'nama_instansi' => 'required',
                'lokasi' => 'required',
                'sertifikat' => 'nullable|file|max:500|mimes:pdf',
            ], $customMessages);

            $data = RiwayatPenghargaan::where('uuid',$params)->first();
             $data->nama_penghargaan = $request->nama_penghargaan;
            $data->nomor_surat_keputusan = $request->nomor_surat_keputusan;
            $data->tanggal = $request->tanggal;
            $data->pejabat_pendantanganan = $request->pejabat_pendantanganan;
            $data->nama_instansi = $request->nama_instansi;
            $data->lokasi = $request->lokasi;
            $data->nama_instansi = $request->nama_instansi;
            $data->lokasi = $request->lokasi;
            $data->id_pegawai = Auth::user()->id_pegawai;
            $data->user_insert = Auth::user()->id;
            $data->user_update = Auth::user()->id;
            if (isset($request->sertifikat)) {
                $file_konten = $request->file('sertifikat');
                $filePath = Storage::disk('sftp')->put('/sftpasn/profil/sertifikat', $file_konten);
                $data->sertifikat =  $filePath;
            }
            $data->save();

            if ($data) {
                $data->show_collapse = 'kt_accordion_1_body_9';
            }

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Riwayat Penghargaan Added success');
    }

    public function show_riwayat_penghargaan($params){
        $data = array();
        try {
            $data = RiwayatPenghargaan::where('uuid',$params)->first();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Riwayat Penghargaan show success'); 
    }

    public function delete_riwayat_penghargaan(Request $request, $params){
        $data = array();
        try {
            RiwayatPenghargaan::where('uuid', $params)->delete();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Riwayat Penghargaan Delete success');
    }

    // END RIWAYAT PENGHARGAAN

    // RIWAYAT ISTRI
    public function store_riwayat_istri(Request $request){
        try {
            $customMessages = [
                'nama_istri.required' => 'kolom nama istri tidak boleh kosong.',
                'tempat_lahir.required' => 'kolom tempat lahir tidak boleh kosong.',
                'tanggal_lahir.required' => 'kolom tanggal lahir tidak boleh kosong.',
                'status_perkawinan.required' => 'kolom status perkawinan tidak boleh kosong.',
                'memperoleh_tunjangan' => 'kolom memperoleh tunjangan tidak boleh kosong.',
                'pendidikan' => 'kolom pendidikan tidak boleh kosong.',
                'pekerjaan' => 'kolom pekerjaan tidak boleh kosong.',
                'keterangan' => 'kolom keterangan tidak boleh kosong.',
                'foto_buku_nikah.required' => 'kolom foto buku nikah tidak boleh kosong.',
            ];

            $request->validate([
                'nama_istri' => 'required',
                'tempat_lahir' => 'required',
                'tanggal_lahir' => 'required',
                'status_perkawinan' => 'required',
                'memperoleh_tunjangan' => 'required',
                'pendidikan' => 'required',
                'pekerjaan' => 'required',
                'keterangan' => 'required',
                'foto_buku_nikah' => 'required|file|max:500|mimes:pdf',
            ], $customMessages);

            $data = new RiwayatIstri();
            $data->nama_istri = $request->nama_istri;
            $data->tempat_lahir = $request->tempat_lahir;
            $data->tanggal_lahir = $request->tanggal_lahir;
            $data->status_perkawinan = $request->status_perkawinan;
            $data->memperoleh_tunjangan = $request->memperoleh_tunjangan;
            $data->pendidikan = $request->pendidikan;
            $data->pekerjaan = $request->pekerjaan;
            $data->keterangan = $request->keterangan;
            $data->id_pegawai = Auth::user()->id_pegawai;
            $data->user_insert = Auth::user()->id;
            $data->user_update = Auth::user()->id;
            if (isset($request->foto_buku_nikah)) {
                $file_konten = $request->file('foto_buku_nikah');
                $filePath = Storage::disk('sftp')->put('/sftpasn/profil/foto_buku_nikah', $file_konten);
                $data->foto_buku_nikah =  $filePath;
            }
            $data->save();

            if ($data) {
                $data->show_collapse = 'kt_accordion_1_body_10';
            }

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Riwayat Penghargaan Added success');

    }

    public function update_riwayat_istri(Request $request, $params){
        try {
            // dd($request->all());
            $customMessages = [
                'nama_istri.required' => 'kolom nama istri tidak boleh kosong.',
                'tempat_lahir.required' => 'kolom tempat lahir tidak boleh kosong.',
                'tanggal_lahir.required' => 'kolom tanggal lahir tidak boleh kosong.',
                'status_perkawinan.required' => 'kolom status perkawinan tidak boleh kosong.',
                'memperoleh_tunjangan' => 'kolom memperoleh tunjangan tidak boleh kosong.',
                'pendidikan' => 'kolom pendidikan tidak boleh kosong.',
                'pekerjaan' => 'kolom pekerjaan tidak boleh kosong.',
                'keterangan' => 'kolom keterangan tidak boleh kosong.',
                'foto_buku_nikah.required' => 'kolom foto buku nikah tidak boleh kosong.',
            ];

            $request->validate([
                'nama_istri' => 'required',
                'tempat_lahir' => 'required',
                'tanggal_lahir' => 'required',
                'status_perkawinan' => 'required',
                'memperoleh_tunjangan' => 'required',
                'pendidikan' => 'required',
                'pekerjaan' => 'required',
                'keterangan' => 'required',
                'foto_buku_nikah' => 'nullable|file|max:500|mimes:pdf',
            ], $customMessages);

            $data = RiwayatIstri::where('uuid',$params)->first();
            $data->nama_istri = $request->nama_istri;
            $data->tempat_lahir = $request->tempat_lahir;
            $data->tanggal_lahir = $request->tanggal_lahir;
            $data->status_perkawinan = $request->status_perkawinan;
            $data->memperoleh_tunjangan = $request->memperoleh_tunjangan;
            $data->pendidikan = $request->pendidikan;
            $data->pekerjaan = $request->pekerjaan;
            $data->keterangan = $request->keterangan;
            $data->id_pegawai = Auth::user()->id_pegawai;
            $data->user_insert = Auth::user()->id;
            $data->user_update = Auth::user()->id;
            if (isset($request->foto_buku_nikah)) {
                $file_konten = $request->file('foto_buku_nikah');
                $filePath = Storage::disk('sftp')->put('/sftpasn/profil/foto_buku_nikah', $file_konten);
                $data->foto_buku_nikah =  $filePath;
            }
            $data->save();

            if ($data) {
                $data->show_collapse = 'kt_accordion_1_body_10';
            }

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Riwayat Penghargaan Added success');
    }

    public function show_riwayat_istri($params){
        $data = array();
        try {
            $data = RiwayatIstri::where('uuid',$params)->first();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Riwayat Penghargaan show success'); 
    }

    public function delete_riwayat_istri(Request $request, $params){
        $data = array();
        try {
            RiwayatIstri::where('uuid', $params)->delete();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Riwayat Penghargaan Delete success');
    }

    // END RIWAYAT ISTRI

    // RIWAYAT ANAK
    public function store_riwayat_anak(Request $request){
        try {
            $customMessages = [
                'nama_anak.required' => 'kolom nama anak tidak boleh kosong.',
                'jk.required' => 'kolom jenis kelamin tidak boleh kosong.',
                'tempat_lahir.required' => 'kolom tempat lahir tidak boleh kosong.',
                'tanggal_lahir.required' => 'kolom tanggal lahir tidak boleh kosong.',
                'status_perkawinan.required' => 'kolom status perkawinan tidak boleh kosong.',
                'memperoleh_tunjangan' => 'kolom memperoleh tunjangan tidak boleh kosong.',
                'pendidikan' => 'kolom pendidikan tidak boleh kosong.',
                'pekerjaan' => 'kolom pekerjaan tidak boleh kosong.',
                'keterangan' => 'kolom keterangan tidak boleh kosong.',
                'foto_kartu_keluarga.required' => 'kolom foto buku nikah tidak boleh kosong.',
            ];

            $request->validate([
                'nama_anak' => 'required',
                'jk' => 'required',
                'tempat_lahir' => 'required',
                'tanggal_lahir' => 'required',
                'status_perkawinan' => 'required',
                'memperoleh_tunjangan' => 'required',
                'pendidikan' => 'required',
                'pekerjaan' => 'required',
                'keterangan' => 'required',
                'foto_kartu_keluarga' => 'required|file|max:500|mimes:pdf',
            ], $customMessages);

            $data = new RiwayatAnak();
            $data->nama_anak = $request->nama_anak;
            $data->jk = $request->jk;
            $data->tempat_lahir = $request->tempat_lahir;
            $data->tanggal_lahir = $request->tanggal_lahir;
            $data->status_perkawinan = $request->status_perkawinan;
            $data->memperoleh_tunjangan = $request->memperoleh_tunjangan;
            $data->pendidikan = $request->pendidikan;
            $data->pekerjaan = $request->pekerjaan;
            $data->keterangan = $request->keterangan;
            $data->id_pegawai = Auth::user()->id_pegawai;
            $data->user_insert = Auth::user()->id;
            $data->user_update = Auth::user()->id;
            if (isset($request->foto_kartu_keluarga)) {
                $file_konten = $request->file('foto_kartu_keluarga');
                $filePath = Storage::disk('sftp')->put('/sftpasn/profil/foto_kartu_keluarga', $file_konten);
                $data->foto_kartu_keluarga =  $filePath;
            }
            $data->save();

            if ($data) {
                $data->show_collapse = 'kt_accordion_1_body_11';
            }

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Riwayat Istri Added success');

    }

    public function update_riwayat_anak(Request $request, $params){
        try {
           
            $customMessages = [
                'nama_anak.required' => 'kolom nama anak tidak boleh kosong.',
                'jk.required' => 'kolom jenis kelamin tidak boleh kosong.',
                'tempat_lahir.required' => 'kolom tempat lahir tidak boleh kosong.',
                'tanggal_lahir.required' => 'kolom tanggal lahir tidak boleh kosong.',
                'status_perkawinan.required' => 'kolom status perkawinan tidak boleh kosong.',
                'memperoleh_tunjangan' => 'kolom memperoleh tunjangan tidak boleh kosong.',
                'pendidikan' => 'kolom pendidikan tidak boleh kosong.',
                'pekerjaan' => 'kolom pekerjaan tidak boleh kosong.',
                'keterangan' => 'kolom keterangan tidak boleh kosong.',
                'foto_buku_nikah.required' => 'kolom foto buku nikah tidak boleh kosong.',
            ];

            $request->validate([
                'nama_anak' => 'required',
                'jk' => 'required',
                'tempat_lahir' => 'required',
                'tanggal_lahir' => 'required',
                'status_perkawinan' => 'required',
                'memperoleh_tunjangan' => 'required',
                'pendidikan' => 'required',
                'pekerjaan' => 'required',
                'keterangan' => 'required',
                'foto_kartu_keluarga' => 'nullable|file|max:500|mimes:pdf',
            ], $customMessages);

            $data = RiwayatAnak::where('uuid',$params)->first();
            $data->nama_anak = $request->nama_anak;
            $data->jk = $request->jk;
            $data->tempat_lahir = $request->tempat_lahir;
            $data->tanggal_lahir = $request->tanggal_lahir;
            $data->status_perkawinan = $request->status_perkawinan;
            $data->memperoleh_tunjangan = $request->memperoleh_tunjangan;
            $data->pendidikan = $request->pendidikan;
            $data->pekerjaan = $request->pekerjaan;
            $data->keterangan = $request->keterangan;
            $data->id_pegawai = Auth::user()->id_pegawai;
            $data->user_insert = Auth::user()->id;
            $data->user_update = Auth::user()->id;
            if (isset($request->foto_kartu_keluarga)) {
                $file_konten = $request->file('foto_kartu_keluarga');
                $filePath = Storage::disk('sftp')->put('/sftpasn/profil/foto_kartu_keluarga', $file_konten);
                $data->foto_kartu_keluarga =  $filePath;
            }
            $data->save();

            if ($data) {
                $data->show_collapse = 'kt_accordion_1_body_11';
            }

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Riwayat Istri Added success');
    }

    public function show_riwayat_anak($params){
        $data = array();
        try {
            $data = RiwayatAnak::where('uuid',$params)->first();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Riwayat Istri show success'); 
    }

    public function delete_riwayat_anak(Request $request, $params){
        $data = array();
        try {
            RiwayatAnak::where('uuid', $params)->delete();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Riwayat Istri Delete success');
    }
    // END RIWAYAT ANAK

    // RIWAYAT ORANG TUA
    public function store_riwayat_orang_tua(Request $request){
        try {
            $customMessages = [
                'nama_orang_tua.required' => 'kolom nama orang tua tidak boleh kosong.',
                'jk.required' => 'kolom jenis kelamin tidak boleh kosong.',
                'tempat_lahir.required' => 'kolom tempat lahir tidak boleh kosong.',
                'tanggal_lahir.required' => 'kolom tanggal lahir tidak boleh kosong.',
                'pendidikan' => 'kolom pendidikan tidak boleh kosong.',
                'pekerjaan' => 'kolom pekerjaan tidak boleh kosong.',
                'keterangan' => 'kolom keterangan tidak boleh kosong.',
                'foto_kartu_keluarga.required' => 'kolom foto buku nikah tidak boleh kosong.',
            ];

            $request->validate([
                'nama_orang_tua' => 'required',
                'jk' => 'required',
                'tempat_lahir' => 'required',
                'tanggal_lahir' => 'required',
                'pendidikan' => 'required',
                'pekerjaan' => 'required',
                'keterangan' => 'required',
                'foto_kartu_keluarga' => 'required|file|max:500|mimes:pdf',
            ], $customMessages);

            $data = new RiwayatOrangTua();
            $data->nama_orang_tua = $request->nama_orang_tua;
            $data->jk = $request->jk;
            $data->tempat_lahir = $request->tempat_lahir;
            $data->tanggal_lahir = $request->tanggal_lahir;
            $data->pendidikan = $request->pendidikan;
            $data->pekerjaan = $request->pekerjaan;
            $data->keterangan = $request->keterangan;
            $data->id_pegawai = Auth::user()->id_pegawai;
            $data->user_insert = Auth::user()->id;
            $data->user_update = Auth::user()->id;
            if (isset($request->foto_kartu_keluarga)) {
                $file_konten = $request->file('foto_kartu_keluarga');
                $filePath = Storage::disk('sftp')->put('/sftpasn/profil/foto_kartu_keluarga', $file_konten);
                $data->foto_kartu_keluarga =  $filePath;
            }
            $data->save();

            if ($data) {
                $data->show_collapse = 'kt_accordion_1_body_12';
            }

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Riwayat Orang Tua Added success');

    }

    public function update_riwayat_orang_tua(Request $request, $params){
        try {
           $customMessages = [
                'nama_orang_tua.required' => 'kolom nama orang tua tidak boleh kosong.',
                'jk.required' => 'kolom jenis kelamin tidak boleh kosong.',
                'tempat_lahir.required' => 'kolom tempat lahir tidak boleh kosong.',
                'tanggal_lahir.required' => 'kolom tanggal lahir tidak boleh kosong.',
                'pendidikan' => 'kolom pendidikan tidak boleh kosong.',
                'pekerjaan' => 'kolom pekerjaan tidak boleh kosong.',
                'keterangan' => 'kolom keterangan tidak boleh kosong.',
                'foto_kartu_keluarga.required' => 'kolom foto buku nikah tidak boleh kosong.',
            ];

            $request->validate([
                'nama_orang_tua' => 'required',
                'jk' => 'required',
                'tempat_lahir' => 'required',
                'tanggal_lahir' => 'required',
                'pendidikan' => 'required',
                'pekerjaan' => 'required',
                'keterangan' => 'required',
                'foto_kartu_keluarga' => 'nullable|file|max:500|mimes:pdf',
            ], $customMessages);

            $data = RiwayatOrangTua::where('uuid',$params)->first();
            $data->nama_orang_tua = $request->nama_orang_tua;
            $data->jk = $request->jk;
            $data->tempat_lahir = $request->tempat_lahir;
            $data->tanggal_lahir = $request->tanggal_lahir;
            $data->pendidikan = $request->pendidikan;
            $data->pekerjaan = $request->pekerjaan;
            $data->keterangan = $request->keterangan;
            $data->id_pegawai = Auth::user()->id_pegawai;
            $data->user_insert = Auth::user()->id;
            $data->user_update = Auth::user()->id;
            if (isset($request->foto_kartu_keluarga)) {
                $file_konten = $request->file('foto_kartu_keluarga');
                $filePath = Storage::disk('sftp')->put('/sftpasn/profil/foto_kartu_keluarga', $file_konten);
                $data->foto_kartu_keluarga =  $filePath;
            }
            $data->save();

            if ($data) {
                $data->show_collapse = 'kt_accordion_1_body_12';
            }

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Riwayat Istri Added success');
    }

    public function show_riwayat_orang_tua($params){
        $data = array();
        try {
            $data = RiwayatOrangTua::where('uuid',$params)->first();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Riwayat Istri show success'); 
    }

    public function delete_riwayat_orang_tua(Request $request, $params){
        $data = array();
        try {
            RiwayatOrangTua::where('uuid', $params)->delete();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Riwayat Istri Delete success');
    }
    // END RIWAYAT ORANG TUA

    // RIWAYAT ORANG TUA
    public function store_riwayat_saudara(Request $request){
        try {
            $customMessages = [
                'nama_saudara.required' => 'kolom nama saudara tidak boleh kosong.',
                'jk.required' => 'kolom jenis kelamin tidak boleh kosong.',
                'tempat_lahir.required' => 'kolom tempat lahir tidak boleh kosong.',
                'tanggal_lahir.required' => 'kolom tanggal lahir tidak boleh kosong.',
                'pendidikan' => 'kolom pendidikan tidak boleh kosong.',
                'pekerjaan' => 'kolom pekerjaan tidak boleh kosong.',
                'keterangan' => 'kolom keterangan tidak boleh kosong.',
                'foto_kartu_keluarga.required' => 'kolom foto buku nikah tidak boleh kosong.',
            ];

            $request->validate([
                'nama_saudara' => 'required',
                'jk' => 'required',
                'tempat_lahir' => 'required',
                'tanggal_lahir' => 'required',
                'pendidikan' => 'required',
                'pekerjaan' => 'required',
                'keterangan' => 'required',
                'foto_kartu_keluarga' => 'required|file|max:500|mimes:pdf',
            ], $customMessages);

            $data = new RiwayatSaudara();
            $data->nama_saudara = $request->nama_saudara;
            $data->jk = $request->jk;
            $data->tempat_lahir = $request->tempat_lahir;
            $data->tanggal_lahir = $request->tanggal_lahir;
            $data->pendidikan = $request->pendidikan;
            $data->pekerjaan = $request->pekerjaan;
            $data->keterangan = $request->keterangan;
            $data->id_pegawai = Auth::user()->id_pegawai;
            $data->user_insert = Auth::user()->id;
            $data->user_update = Auth::user()->id;
            if (isset($request->foto_kartu_keluarga)) {
                $file_konten = $request->file('foto_kartu_keluarga');
                $filePath = Storage::disk('sftp')->put('/sftpasn/profil/foto_kartu_keluarga', $file_konten);
                $data->foto_kartu_keluarga =  $filePath;
            }
            $data->save();

            if ($data) {
                $data->show_collapse = 'kt_accordion_1_body_13';
            }
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Riwayat Saudara Added success');

    }

    public function update_riwayat_saudara(Request $request, $params){
        try {
           $customMessages = [
                'nama_saudara.required' => 'kolom nama saudara tidak boleh kosong.',
                'jk.required' => 'kolom jenis kelamin tidak boleh kosong.',
                'tempat_lahir.required' => 'kolom tempat lahir tidak boleh kosong.',
                'tanggal_lahir.required' => 'kolom tanggal lahir tidak boleh kosong.',
                'pendidikan' => 'kolom pendidikan tidak boleh kosong.',
                'pekerjaan' => 'kolom pekerjaan tidak boleh kosong.',
                'keterangan' => 'kolom keterangan tidak boleh kosong.',
                'foto_kartu_keluarga.required' => 'kolom foto buku nikah tidak boleh kosong.',
            ];

            $request->validate([
                'nama_saudara' => 'required',
                'jk' => 'required',
                'tempat_lahir' => 'required',
                'tanggal_lahir' => 'required',
                'pendidikan' => 'required',
                'pekerjaan' => 'required',
                'keterangan' => 'required',
                'foto_kartu_keluarga' => 'nullable|file|max:500|mimes:pdf',
            ], $customMessages);

            $data = RiwayatSaudara::where('uuid',$params)->first();
            $data->nama_saudara = $request->nama_saudara;
            $data->jk = $request->jk;
            $data->tempat_lahir = $request->tempat_lahir;
            $data->tanggal_lahir = $request->tanggal_lahir;
            $data->pendidikan = $request->pendidikan;
            $data->pekerjaan = $request->pekerjaan;
            $data->keterangan = $request->keterangan;
            $data->id_pegawai = Auth::user()->id_pegawai;
            $data->user_insert = Auth::user()->id;
            $data->user_update = Auth::user()->id;
            if (isset($request->foto_kartu_keluarga)) {
                $file_konten = $request->file('foto_kartu_keluarga');
                $filePath = Storage::disk('sftp')->put('/sftpasn/profil/foto_kartu_keluarga', $file_konten);
                $data->foto_kartu_keluarga =  $filePath;
            }
            $data->save();

            if ($data) {
                $data->show_collapse = 'kt_accordion_1_body_13';
            }

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Riwayat Saudara Added success');
    }

    public function show_riwayat_saudara($params){
        $data = array();
        try {
            $data = RiwayatSaudara::where('uuid',$params)->first();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Riwayat Saudara show success'); 
    }

    public function delete_riwayat_saudara(Request $request, $params){
        $data = array();
        try {
            RiwayatSaudara::where('uuid', $params)->delete();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Riwayat Saudara Delete success');
    }
    // END RIWAYAT ORANG TUA

    // RIWAYAT ORANG TUA
    public function store_riwayat_tambahan(Request $request){
        try {
            if ($request->jenis_riwayat_tambahan === 'Kemampuan Bahasa') {

                $customMessages = [
                    'jenis_riwayat_tambahan.required' => 'kolom jenis riwayat tambahan tidak boleh kosong.',
                    'level_keahlian_membaca.required' => 'kolom level_keahlian_membaca tidak boleh kosong.',
                    'level_keahlian_mendengarkan.required' => 'kolom level keahlian mendengarkan tidak boleh kosong.',
                    'level_keahlian_menulis.required' => 'kolom level keahlian menulis tidak boleh kosong.',
                    'level_keahlian_berbicara' => 'kolom level_keahlian berbicara tidak boleh kosong.',
                    'tanggal' => 'kolom tanggal tidak boleh kosong.',
                    'pelatihan' => 'kolom pelatihan tidak boleh kosong.',
                    'predikat' => 'kolom predikat tidak boleh kosong.',
                    'sertifikat.required' => 'kolom sertifikat tidak boleh kosong.',
                ];

                $request->validate([
                    'jenis_riwayat_tambahan' => 'required',
                    'level_keahlian_membaca' => 'required',
                    'level_keahlian_mendengarkan' => 'required',
                    'level_keahlian_menulis' => 'required',
                    'level_keahlian_berbicara' => 'required',
                    'tanggal' => 'required',
                    'pelatihan' => 'required',
                    'predikat' => 'required',
                    'sertifikat' => 'required|file|max:500|mimes:pdf',
                ], $customMessages);
            }else{

                $customMessages = [
                    'jenis_riwayat_tambahan.required' => 'kolom jenis riwayat tambahan tidak boleh kosong.',
                    'nama_keahlian' => 'kolom nama keahlian tidak boleh kosong.',
                    'level_keahlian' => 'kolom level keahlian tidak boleh kosong.',
                    'tanggal' => 'kolom tanggal tidak boleh kosong.',
                    'pelatihan' => 'kolom pelatihan tidak boleh kosong.',
                    'predikat' => 'kolom predikat tidak boleh kosong.',
                    'sertifikat.required' => 'kolom sertifikat tidak boleh kosong.',
                ];

                $request->validate([
                    'jenis_riwayat_tambahan' => 'required',
                    'nama_keahlian' => 'required',
                    'level_keahlian' => 'required',
                    'tanggal' => 'required',
                    'pelatihan' => 'required',
                    'tanggal' => 'required',
                    'predikat' => 'required',
                    'sertifikat' => 'required|file|max:500|mimes:pdf',
                ], $customMessages);
            }

            $data = array();

           if ($request->jenis_riwayat_tambahan === 'Kemampuan Bahasa') {
                $data = new RiwayatBahasa();
                $data->jenis_riwayat_tambahan = $request->jenis_riwayat_tambahan;
                $data->nama_bahasa = $request->nama_bahasa;
                $data->level_keahlian_membaca = $request->level_keahlian_membaca;
                $data->level_keahlian_mendengarkan = $request->level_keahlian_mendengarkan;
                $data->level_keahlian_menulis = $request->level_keahlian_menulis;
                $data->level_keahlian_berbicara = $request->level_keahlian_berbicara;
                $data->tanggal = $request->tanggal;
                $data->pelatihan = $request->pelatihan;
                $data->predikat = $request->predikat;
                $data->id_pegawai = Auth::user()->id_pegawai;
                $data->user_insert = Auth::user()->id;
                $data->user_update = Auth::user()->id;
                if (isset($request->sertifikat)) {
                    $file_konten = $request->file('sertifikat');
                    $filePath = Storage::disk('sftp')->put('/sftpasn/profil/sertifikat', $file_konten);
                    $data->sertifikat =  $filePath;
                }
                $data->save();
           }else{
                $data = new RiwayatKeahlian();
                $data->jenis_riwayat_tambahan = $request->jenis_riwayat_tambahan;
                $data->nama_keahlian = $request->nama_keahlian;
                $data->level_keahlian = $request->level_keahlian;
                $data->tanggal = $request->tanggal;
                $data->pelatihan = $request->pelatihan;
                $data->predikat = $request->predikat;
                $data->id_pegawai = Auth::user()->id_pegawai;
                $data->user_insert = Auth::user()->id;
                $data->user_update = Auth::user()->id;
                if (isset($request->sertifikat)) {
                    $file_konten = $request->file('sertifikat');
                    $filePath = Storage::disk('sftp')->put('/sftpasn/profil/sertifikat', $file_konten);
                    $data->sertifikat =  $filePath;
                }
                $data->save();
           } 

           if ($data) {
                $data->show_collapse = 'kt_accordion_1_body_14';
            }
  
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Riwayat Tambahan Added success');

    }

    public function update_riwayat_tambahan(Request $request, $params){
        try {
            if ($request->jenis_riwayat_tambahan === 'Kemampuan Bahasa') {

                $customMessages = [
                    'jenis_riwayat_tambahan.required' => 'kolom jenis riwayat tambahan tidak boleh kosong.',
                    'level_keahlian_membaca.required' => 'kolom level_keahlian_membaca tidak boleh kosong.',
                    'level_keahlian_mendengarkan.required' => 'kolom level keahlian mendengarkan tidak boleh kosong.',
                    'level_keahlian_menulis.required' => 'kolom level keahlian menulis tidak boleh kosong.',
                    'level_keahlian_berbicara' => 'kolom level_keahlian berbicara tidak boleh kosong.',
                    'tanggal' => 'kolom tanggal tidak boleh kosong.',
                    'pelatihan' => 'kolom pelatihan tidak boleh kosong.',
                    'predikat' => 'kolom predikat tidak boleh kosong.',
                    'sertifikat.required' => 'kolom sertifikat tidak boleh kosong.',
                ];

                $request->validate([
                    'jenis_riwayat_tambahan' => 'required',
                    'level_keahlian_membaca' => 'required',
                    'level_keahlian_mendengarkan' => 'required',
                    'level_keahlian_menulis' => 'required',
                    'level_keahlian_berbicara' => 'required',
                    'tanggal' => 'required',
                    'pelatihan' => 'required',
                    'predikat' => 'required',
                    'sertifikat' => 'nullable|file|max:500|mimes:pdf',
                ], $customMessages);
            }else{

                $customMessages = [
                    'jenis_riwayat_tambahan.required' => 'kolom jenis riwayat tambahan tidak boleh kosong.',
                    'nama_keahlian' => 'kolom nama keahlian tidak boleh kosong.',
                    'level_keahlian' => 'kolom level keahlian tidak boleh kosong.',
                    'tanggal' => 'kolom tanggal tidak boleh kosong.',
                    'pelatihan' => 'kolom pelatihan tidak boleh kosong.',
                    'predikat' => 'kolom predikat tidak boleh kosong.',
                    'sertifikat.required' => 'kolom sertifikat tidak boleh kosong.',
                ];

                $request->validate([
                    'jenis_riwayat_tambahan' => 'required',
                    'nama_keahlian' => 'required',
                    'level_keahlian' => 'required',
                    'tanggal' => 'required',
                    'pelatihan' => 'required',
                    'tanggal' => 'required',
                    'predikat' => 'required',
                    'sertifikat' => 'nullable|file|max:500|mimes:pdf',
                ], $customMessages);
            }

            $data = array();

           if ($request->jenis_riwayat_tambahan === 'Kemampuan Bahasa') {
                $data = RiwayatBahasa::where('uuid',$params)->first();
                $data->jenis_riwayat_tambahan = $request->jenis_riwayat_tambahan;
                $data->nama_bahasa = $request->nama_bahasa;
                $data->level_keahlian_membaca = $request->level_keahlian_membaca;
                $data->level_keahlian_mendengarkan = $request->level_keahlian_mendengarkan;
                $data->level_keahlian_menulis = $request->level_keahlian_menulis;
                $data->level_keahlian_berbicara = $request->level_keahlian_berbicara;
                $data->tanggal = $request->tanggal;
                $data->pelatihan = $request->pelatihan;
                $data->predikat = $request->predikat;
                $data->id_pegawai = Auth::user()->id_pegawai;
                $data->user_insert = Auth::user()->id;
                $data->user_update = Auth::user()->id;
                if (isset($request->sertifikat)) {
                    $file_konten = $request->file('sertifikat');
                    $filePath = Storage::disk('sftp')->put('/sftpasn/profil/sertifikat', $file_konten);
                    $data->sertifikat =  $filePath;
                }
                $data->save();
           }else{
                $data = RiwayatKeahlian::where('uuid',$params)->first();
                $data->jenis_riwayat_tambahan = $request->jenis_riwayat_tambahan;
                $data->nama_keahlian = $request->nama_keahlian;
                $data->level_keahlian = $request->level_keahlian;
                $data->tanggal = $request->tanggal;
                $data->pelatihan = $request->pelatihan;
                $data->predikat = $request->predikat;
                $data->id_pegawai = Auth::user()->id_pegawai;
                $data->user_insert = Auth::user()->id;
                $data->user_update = Auth::user()->id;
                if (isset($request->sertifikat)) {
                    $file_konten = $request->file('sertifikat');
                    $filePath = Storage::disk('sftp')->put('/sftpasn/profil/sertifikat', $file_konten);
                    $data->sertifikat =  $filePath;
                }
                $data->save();
           } 

           if ($data) {
                $data->show_collapse = 'kt_accordion_1_body_14';
            }
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Riwayat Tambahan Added success');
    }

    public function show_riwayat_tambahan($params){
        $data = array();
        try {
            $jenis = request('jenis');
            if ($jenis === 'keahlian') {
               $data = RiwayatKeahlian::where('uuid',$params)->first();
            }else {
                $data = RiwayatBahasa::where('uuid',$params)->first();
            }
            
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Riwayat Tambahan show success'); 
    }

    public function delete_riwayat_tambahan(Request $request, $params){
        $data = array();
        try {
            $jenis = request('jenis');
            if ($jenis == 'keahlian') {
                RiwayatKeahlian::where('uuid', $params)->delete();
            }else{
                RiwayatBahasa::where('uuid', $params)->delete();
            }
            
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Riwayat Saudara Delete success');
    }
    // END RIWAYAT TAMBAHAN

    // RIWAYAT ORANG TUA
    public function store_file_pegawai(Request $request){
        try {
            $customMessages = [
                'nama_file.required' => 'kolom nama file tidak boleh kosong.',
                'keterangan.required' => 'kolom keterangan tidak boleh kosong.',
                'file.required' => 'kolom file tidak boleh kosong.',
            ];

            $request->validate([
                'nama_file' => 'required',
                'keterangan' => 'required',
                'file' => 'required|file|max:500|mimes:pdf',
            ], $customMessages);

            $data = new FilePegawai();
            $data->nama_file = $request->nama_file;
            $data->keterangan = $request->keterangan;
            $data->id_pegawai = Auth::user()->id_pegawai;
            $data->user_insert = Auth::user()->id;
            $data->user_update = Auth::user()->id;
            if (isset($request->file)) {
                $file_konten = $request->file('file');
                $filePath = Storage::disk('sftp')->put('/sftpasn/profil/file_pegawai', $file_konten);
                $data->file =  $filePath;
            }
            $data->save();

            if ($data) {
                $data->show_collapse = 'kt_accordion_1_body_15';
            }
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'File Pegawai Added success');

    }

    public function update_file_pegawai(Request $request, $params){
        try {
           $customMessages = [
                'nama_file.required' => 'kolom nama file tidak boleh kosong.',
                'keterangan.required' => 'kolom keterangan tidak boleh kosong.',
                'file.required' => 'kolom file tidak boleh kosong.',
            ];

            $request->validate([
                'nama_file' => 'required',
                'keterangan' => 'required',
                'file' => 'nullable|file|max:500|mimes:pdf',
            ], $customMessages);

            $data = FilePegawai::where('uuid',$params)->first();
            $data->nama_file = $request->nama_file;
            $data->keterangan = $request->keterangan;
            $data->id_pegawai = Auth::user()->id_pegawai;
            $data->user_insert = Auth::user()->id;
            $data->user_update = Auth::user()->id;
            if (isset($request->file)) {
                $file_konten = $request->file('file');
                $filePath = Storage::disk('sftp')->put('/sftpasn/profil/file_pegawai', $file_konten);
                $data->file =  $filePath;
            }
            $data->save();

            if ($data) {
                $data->show_collapse = 'kt_accordion_1_body_15';
            }
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'File Pegawai Added success');
    }

    public function show_file_pegawai($params){
        $data = array();
        try {
            $data = FilePegawai::where('uuid',$params)->first();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'File Pegawai show success'); 
    }

    public function delete_file_pegawai(Request $request, $params){
        $data = array();
        try {
            FilePegawai::where('uuid', $params)->delete();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'File Pegawai Delete success');
    }

    public function file_dokumen_pribadi(){
        $path = request('path');
        
        $url = Storage::disk('sftp')->get('/'.$path);

        $extension = pathinfo($path, PATHINFO_EXTENSION);

        if ($extension === 'pdf') {
            $contentType = 'application/pdf';
        } elseif (in_array($extension, ['png', 'jpg', 'jpeg', 'gif'])) {
            $contentType = 'image/' . $extension;
            if ($extension == 'jpg') {
                $contentType = 'image/jpeg';
            }
            
        } else {
            $contentType = 'application/octet-stream'; // Default content type for other file types
        }
        
        $response = new Response($url, 200, [
            'Content-Type' => $contentType,
        ]);
        ob_end_clean();
        return $response;
    }
    // END RIWAYAT ORANG TUA
}
