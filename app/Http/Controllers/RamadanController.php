<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\Ramadan;
use DB;

class RamadanController extends BaseController
{
    public function breadcumb()
    {
        return [['label' => 'Tanggal Ramadan', 'url' => '#']];
    }

    public function datatable()
    {
        $data = DB::table('tb_ramadan')->select('id', 'uuid', 'tahun', 'tanggal_mulai', 'tanggal_selesai', 'keterangan')->orderBy('tahun', 'desc')->get();
        return $this->sendResponse($data, 'Ramadan Fetched Success');
    }

    public function index()
    {
        $module = $this->breadcumb();
        return view('admin_kabupaten.ramadan.index', compact('module'));
    }

    public function store(Request $request)
    {
        $request->validate(['tahun' => 'required|integer', 'tanggal_mulai' => 'required|date', 'tanggal_selesai' => 'required|date']);
        try {
            $data = new Ramadan();
            $data->tahun = $request->tahun;
            $data->tanggal_mulai = $request->tanggal_mulai;
            $data->tanggal_selesai = $request->tanggal_selesai;
            $data->keterangan = $request->keterangan;
            $data->save();
        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Ramadan Added success');
    }

    public function update(Request $request, $params)
    {
        $request->validate(['tahun' => 'required|integer', 'tanggal_mulai' => 'required|date', 'tanggal_selesai' => 'required|date']);
        try {
            $data = Ramadan::where('uuid', $params)->first();
            $data->tahun = $request->tahun;
            $data->tanggal_mulai = $request->tanggal_mulai;
            $data->tanggal_selesai = $request->tanggal_selesai;
            $data->keterangan = $request->keterangan;
            $data->save();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Ramadan Update success');
    }

    public function show($params)
    {
        try {
            $data = Ramadan::where('uuid', $params)->first();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Ramadan Fetched success');
    }

    public function delete(Request $request, $params)
    {
        try {
            $data = DB::table('tb_ramadan')->where('uuid', $params)->delete();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Ramadan Delete success');
    }
}
