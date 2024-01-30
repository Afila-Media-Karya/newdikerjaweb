<?php

namespace App\Http\Controllers\pegawai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Requests\pegawaiRequest;
use App\Http\Requests\pegawaiUpdateRequest;
use App\Models\Pegawai;
use App\Models\User;
use DB;
use App\Traits\General;
use Hash;
use Auth;

class listPegawaiController extends BaseController
{
    use General;

    public function breadcumb()
    {
        return [
            [
                'label' => 'Pegawai',
                'url' => '#'
            ],
            [
                'label' => 'Daftar Pegawai',
                'url' => '#'
            ],
        ];
    }

    public function datatable()
    {
        $satuan_kerja = request('satuan_kerja');
        $jenis_kelamin = request('jenis_kelamin');
        $agama = request('agama');
        $pendidikan = request('pendidikan');
        $golongan = request('golongan');
        $jenis_jabatan = request('jenis_jabatan');
        $status_kepegawaian = request('status_kepegawaian');
        $tipe_pegawai = request('tipe_pegawai');
        $eselon = request('eselon');
        $unit_kerja = request('unit_kerja');
        $role = hasRole();

        $data = array();
        $query = DB::table('tb_pegawai')
            ->LeftJoin('tb_satuan_kerja', 'tb_pegawai.id_satuan_kerja', 'tb_satuan_kerja.id')
            ->LeftJoin('tb_jabatan', 'tb_jabatan.id_pegawai', 'tb_pegawai.id')
            ->LeftJoin('tb_master_jabatan', 'tb_jabatan.id_master_jabatan', 'tb_master_jabatan.id')
            ->select('tb_pegawai.id', 'tb_pegawai.uuid', 'tb_pegawai.nip', 'tb_pegawai.nama', 'tb_pegawai.status', 'tb_master_jabatan.nama_jabatan', 'level_jabatan', 'tb_satuan_kerja.kode_satuan_kerja', 'tb_satuan_kerja.kode_satuan_kerja','tb_jabatan.status as status_jabatan')
            ->orderBy('tb_satuan_kerja.kode_satuan_kerja', 'DESC')
            ->orderBy('tb_master_jabatan.kelas_jabatan', 'ASC')
            ->orderBy('tb_jabatan.id', 'ASC');

        if ($role['guard'] == 'administrator') {
            if (!is_null($satuan_kerja) && $satuan_kerja !== 'semua') {
                $query->where('tb_pegawai.id_satuan_kerja', $satuan_kerja);
            }

            if (!is_null($unit_kerja) && $unit_kerja !== 'all') {
                $query->where('tb_jabatan.id_unit_kerja', $unit_kerja);
            }
        }    

        if (!is_null($jenis_kelamin) && $jenis_kelamin !== 'semua') {
            $query->where('tb_pegawai.jenis_kelamin', $jenis_kelamin);
        }

        if (!is_null($agama) && $agama !== 'semua') {
            $query->where('tb_pegawai.agama', $agama);
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
            $get_satuan_kerja = $this->infoSatuanKerja(Auth::user()->id_pegawai);
            $query->where('tb_pegawai.id_satuan_kerja', $get_satuan_kerja->id_satuan_kerja);
            $query->where('tb_jabatan.id_unit_kerja', $get_satuan_kerja->id_unit_kerja);
        }

        // if ($role['guard'] == 'web' && $role['role'] == '3') {
        //     $get_satuan_kerja = $this->infoSatuanKerja(Auth::user()->id_pegawai);
        //     $query->where('tb_jabatan.id_unit_kerja', $get_satuan_kerja->id_unit_kerja);
        // }

        $data = $query->get();
        return $this->sendResponse($data, 'Data Pegawai Fetched Success');
    }

    public function index()
    {
        $module = $this->breadcumb();
        $golongan = $this->option_golongan();
        $pendidikan = $this->option_pendidikan();
        $satuan_kerja = $this->option_satuan_kerja();
        $agama = $this->option_agama();
        $jenis_jabatan = $this->option_jenis_jabatan_all();
        $eselon = $this->option_eselon();
        $satuan_kerja_user = '';

        if (hasRole()['guard'] == 'web') {
            hasRole()['role'] == '1' ? $satuan_kerja_user = $this->infoSatuanKerja(hasRole()['id_pegawai'])->id_satuan_kerja : $satuan_kerja_user = $this->infoSatuanKerja(hasRole()['id_pegawai'])->id_unit_kerja;
        }

        return view('pegawai.listpegawai', compact('module', 'golongan', 'pendidikan', 'satuan_kerja', 'satuan_kerja_user', 'agama', 'jenis_jabatan', 'eselon'));
    }

    public function detail($params)
    {
        $module =  [
            [
                'label' => 'Pegawai',
                'url' => '#'
            ],
            [
                'label' => 'Daftar Pegawai',
                'url' => '#'
            ],
            [
                'label' => 'Detail Pegawai',
                'url' => '#'
            ],
        ];

        $data = DB::table('tb_pegawai')->where('tb_pegawai.uuid', $params)->first();
        return view('pegawai.detail_pegawai', compact('data', 'module'));
    }

    public function store(pegawaiRequest $request)
    {
        $data = array();
        try {
            DB::beginTransaction();
            $data = new Pegawai();
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
            // $data->eselon = $request->eselon;
            $data->tmt_golongan = $request->tmt_golongan;
            $data->tmt_jabatan = $request->tmt_jabatan;
            $data->pendidikan = $request->pendidikan;
            $data->pendidikan_lulus = $request->pendidikan_lulus;
            $data->pendidikan_struktural = $request->pendidikan_struktural;
            $data->pendidikan_struktural_lulus = $request->pendidikan_struktural_lulus;
            $data->status_kepegawaian = $request->status_kepegawaian;
            $data->tipe_pegawai = $request->tipe_pegawai;
            $data->status = '1';
            $data->status_rekam = 0;
            $data->user_insert = hasRole()['id'];
            $data->save();

            $user = new User();
            $user->id_pegawai = $data->id;
            $user->username = $data->nip;
            $user->password = Hash::make('DIKERJAmaspul');
            $user->role = '2';
            $user->status = 1;
            $user->save();

            DB::commit();
        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Pegawai Added success');
    }

    public function update(pegawaiUpdateRequest $request, $params)
    {
        $data = array();
        try {
            DB::beginTransaction();

            $data = Pegawai::where('uuid', $params)->first();
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
            // $data->eselon = $request->eselon;
            $data->pendidikan_lulus = $request->pendidikan_lulus;
            $data->pendidikan_struktural = $request->pendidikan_struktural;
            $data->pendidikan_struktural_lulus = $request->pendidikan_struktural_lulus;
            $data->status_kepegawaian = $request->status_kepegawaian;
            $data->tipe_pegawai = $request->tipe_pegawai;
            $data->user_update = hasRole()['id'];
            $data->save();

            // $user =  User::where('id_pegawai', $data->id)->first();
            // // dd($user);
            // $user->id_pegawai = $data->id;
            // $user->username = $data->nip;
            // $user->password = Hash::make($data->nip);
            // $user->role = '2';
            // $user->status = 1;
            // $user->save();

            DB::commit();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Pegawai Update success');
    }

    public function show($params)
    {
        $data = array();
        try {
            $data = Pegawai::where('uuid', $params)->first();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Pegawai Update success');
    }

    public function option()
    {

        $data = array();
        try {
            $satuan_kerja = request('satuan_kerja');
            $unit_kerja = request("unit_kerja");
            $kelas_jabatan = intval(request("kelas_jabatan"));
           
            $data = $this->option_pegawaiBy_satuan_kerja($satuan_kerja, $kelas_jabatan);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Pegawai Option success');
    }

    public function option_by_unitkerja()
    {
        $data = array();
        try {
            $satuan_kerja = request('satuan_kerja');
            $unit_kerja = request("unit_kerja");
            $data = $this->option_pegawaiBy_unit_kerja($satuan_kerja, $unit_kerja);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Pegawai Option success');
    }

    public function reset_wajah(Request $request)
    {
        $data = array();
        try {
            $data = Pegawai::where('uuid', $request->uuid)->first();
            $data->face_character = null;
            $data->status_rekam = 0;
            $data->status_verifikasi = 0;
            $data->save();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Pegawai Update success');
    }

    public function delete(Request $request, $params)
    {
        $data = array();
        try {
            $check_jabatan = $this->checkJabatan($params);

            if (!isset($check_jabatan)) {
                $data =  DB::table('tb_pegawai')->where('uuid', $params)->delete();
            } else {
                return $this->sendError('Pegawai dengan nip ' . $check_jabatan->nip . ' sedang mengisi jabatan ' . $check_jabatan->nama_jabatan, 'Pegawai Tidak dapat di hapus!', 422);
            }
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Pegawai Delete success');
    }
}
