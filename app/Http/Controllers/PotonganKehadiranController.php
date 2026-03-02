<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\PotonganKehadiran;
use DB;

class PotonganKehadiranController extends BaseController
{
    public function breadcumb()
    {
        return [['label' => 'Potongan Kehadiran', 'url' => '#']];
    }

    public function datatable()
    {
        $data = DB::table('tb_potongan_kehadiran')
            ->select('id', 'uuid', 'jenis', 'label', 'menit_awal', 'menit_akhir', 'persentase', 'keterangan', 'is_active')
            ->orderBy('jenis')
            ->orderBy('menit_awal')
            ->get()
            ->map(function ($item) {
                $jenisLabels = ['kmk' => 'Keterlambatan Masuk', 'cpk' => 'Cepat Pulang', 'alfa' => 'Tanpa Keterangan', 'apel' => 'Apel', 'apel_senin' => 'Apel Senin'];
                $item->jenis_label = $jenisLabels[$item->jenis] ?? $item->jenis;
                $item->rentang = $item->menit_awal !== null ? $item->menit_awal . ' - ' . $item->menit_akhir . ' menit' : '-';
                return $item;
            });
        return $this->sendResponse($data, 'Potongan Kehadiran Fetched Success');
    }

    public function index()
    {
        $module = $this->breadcumb();
        return view('admin_kabupaten.potongan_kehadiran.index', compact('module'));
    }

    public function store(Request $request)
    {
        $request->validate(['jenis' => 'required|string', 'label' => 'required|string', 'persentase' => 'required|numeric|min:0']);
        try {
            $data = new PotonganKehadiran();
            $data->jenis = $request->jenis;
            $data->label = $request->label;
            $data->menit_awal = $request->menit_awal;
            $data->menit_akhir = $request->menit_akhir;
            $data->persentase = $request->persentase;
            $data->keterangan = $request->keterangan;
            $data->save();
        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Potongan Kehadiran Added success');
    }

    public function update(Request $request, $params)
    {
        $request->validate(['jenis' => 'required|string', 'label' => 'required|string', 'persentase' => 'required|numeric|min:0']);
        try {
            $data = PotonganKehadiran::where('uuid', $params)->first();
            $data->jenis = $request->jenis;
            $data->label = $request->label;
            $data->menit_awal = $request->menit_awal;
            $data->menit_akhir = $request->menit_akhir;
            $data->persentase = $request->persentase;
            $data->keterangan = $request->keterangan;
            $data->save();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Potongan Kehadiran Update success');
    }

    public function show($params)
    {
        try {
            $data = PotonganKehadiran::where('uuid', $params)->first();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Potongan Kehadiran Fetched success');
    }

    public function delete(Request $request, $params)
    {
        try {
            $data = DB::table('tb_potongan_kehadiran')->where('uuid', $params)->delete();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
        return $this->sendResponse($data, 'Potongan Kehadiran Delete success');
    }
}
