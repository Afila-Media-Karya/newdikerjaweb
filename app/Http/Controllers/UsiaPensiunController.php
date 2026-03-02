<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\UsiaPensiun;
use DB;

class UsiaPensiunController extends BaseController
{
    public function breadcumb()
    {
        return [['label' => 'Usia Pensiun', 'url' => '#']];
    }

    public function datatable()
    {
        $data = DB::table('tb_usia_pensiun')->select('id', 'uuid', 'tipe_pegawai', 'usia_pensiun', 'keterangan')
            ->orderBy('tipe_pegawai')->get()
            ->map(function ($item) {
                $item->tipe_label = str_replace('_', ' ', ucwords($item->tipe_pegawai, '_'));
                return $item;
            });
        return $this->sendResponse($data, 'Usia Pensiun Fetched Success');
    }

    public function index()
    {
        $module = $this->breadcumb();
        return view('admin_kabupaten.usia_pensiun.index', compact('module'));
    }

    public function store(Request $request)
    {
        $request->validate(['tipe_pegawai' => 'required|string', 'usia_pensiun' => 'required|integer|min:1']);
        try {
            $data = new UsiaPensiun();
            $data->tipe_pegawai = $request->tipe_pegawai;
            $data->usia_pensiun = $request->usia_pensiun;
            $data->keterangan = $request->keterangan;
            $data->save();
        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Usia Pensiun Added success');
    }

    public function update(Request $request, $params)
    {
        $request->validate(['tipe_pegawai' => 'required|string', 'usia_pensiun' => 'required|integer|min:1']);
        try {
            $data = UsiaPensiun::where('uuid', $params)->first();
            $data->tipe_pegawai = $request->tipe_pegawai;
            $data->usia_pensiun = $request->usia_pensiun;
            $data->keterangan = $request->keterangan;
            $data->save();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Usia Pensiun Update success');
    }

    public function show($params)
    {
        try {
            $data = UsiaPensiun::where('uuid', $params)->first();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Usia Pensiun Fetched success');
    }

    public function delete(Request $request, $params)
    {
        try {
            $data = DB::table('tb_usia_pensiun')->where('uuid', $params)->delete();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Usia Pensiun Delete success');
    }
}
