<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Requests\HariKerjaRequest;
use App\Models\HariKerja;
use DB;

class HariKerjaController extends BaseController
{
    public function breadcumb()
    {
        return [
            [
                'label' => 'Hari Kerja',
                'url' => '#'
            ],
        ];
    }

    public function datatable()
    {
        $data = DB::table('tb_hari_kerja')
            ->select('id', 'uuid', 'tipe_pegawai', 'hari', 'is_hari_kerja')
            ->orderBy('tipe_pegawai')
            ->orderBy('hari')
            ->get()
            ->map(function ($item) {
                $namaHari = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];
                $item->nama_hari = $namaHari[$item->hari] ?? '-';
                $item->tipe_label = str_replace('_', ' ', ucwords($item->tipe_pegawai, '_'));
                $item->status_label = $item->is_hari_kerja ? 'Hari Kerja' : 'Libur';
                return $item;
            });
        return $this->sendResponse($data, 'Hari Kerja Fetched Success');
    }

    public function index()
    {
        $module = $this->breadcumb();
        return view('admin_kabupaten.hari_kerja.index', compact('module'));
    }

    public function store(HariKerjaRequest $request)
    {
        $data = array();
        try {
            $data = new HariKerja();
            $data->tipe_pegawai = $request->tipe_pegawai;
            $data->hari = $request->hari;
            $data->is_hari_kerja = $request->is_hari_kerja;
            $data->save();
        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Hari Kerja Added success');
    }

    public function update(HariKerjaRequest $request, $params)
    {
        $data = array();
        try {
            $data = HariKerja::where('uuid', $params)->first();
            $data->tipe_pegawai = $request->tipe_pegawai;
            $data->hari = $request->hari;
            $data->is_hari_kerja = $request->is_hari_kerja;
            $data->save();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Hari Kerja Update success');
    }

    public function show($params)
    {
        $data = array();
        try {
            $data = HariKerja::where('uuid', $params)->first();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Hari Kerja Fetched success');
    }

    public function delete(Request $request, $params)
    {
        $data = array();
        try {
            $data = DB::table('tb_hari_kerja')->where('uuid', $params)->delete();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Hari Kerja Delete success');
    }
}
