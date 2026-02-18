<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\TipePegawai;
use DB;

class TipePegawaiController extends BaseController
{
    public function breadcumb()
    {
        return [['label' => 'Tipe Pegawai', 'url' => '#']];
    }

    public function datatable()
    {
        $data = DB::table('tb_tipe_pegawai')->select('id', 'uuid', 'kode', 'nama', 'keterangan', 'is_active')->orderBy('nama')->get();
        return $this->sendResponse($data, 'Tipe Pegawai Fetched Success');
    }

    public function index()
    {
        $module = $this->breadcumb();
        return view('admin_kabupaten.tipe_pegawai.index', compact('module'));
    }

    public function store(Request $request)
    {
        $request->validate(['kode' => 'required|string|unique:tb_tipe_pegawai,kode', 'nama' => 'required|string']);
        try {
            $data = new TipePegawai();
            $data->kode = $request->kode;
            $data->nama = $request->nama;
            $data->keterangan = $request->keterangan;
            $data->save();
        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Tipe Pegawai Added success');
    }

    public function update(Request $request, $params)
    {
        $request->validate(['kode' => 'required|string', 'nama' => 'required|string']);
        try {
            $data = TipePegawai::where('uuid', $params)->first();
            $data->kode = $request->kode;
            $data->nama = $request->nama;
            $data->keterangan = $request->keterangan;
            $data->save();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Tipe Pegawai Update success');
    }

    public function show($params)
    {
        try {
            $data = TipePegawai::where('uuid', $params)->first();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Tipe Pegawai Fetched success');
    }

    public function delete(Request $request, $params)
    {
        try {
            $data = DB::table('tb_tipe_pegawai')->where('uuid', $params)->delete();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Tipe Pegawai Delete success');
    }

    /**
     * Options untuk dropdown (digunakan oleh halaman lain).
     */
    public function option()
    {
        $data = TipePegawai::getOptions();
        return $this->sendResponse($data, 'Tipe Pegawai Options success');
    }
}
