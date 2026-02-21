<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\JamKerja;
use App\Models\JamApel;
use App\Models\HariKerja;
use DB;

class JamKerjaController extends BaseController
{
    public function breadcumb()
    {
        return [
            [
                'label' => 'Pengaturan Web Absensi',
                'url' => '#'
            ],
        ];
    }

    /**
     * Display the settings page with all jam kerja using unified dropdown UI.
     */
    public function index()
    {
        $module = $this->breadcumb();

        $namaHari = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];
        $tipeLabels = [
            'pegawai_administratif' => 'Pegawai Administratif',
            'tenaga_pendidik' => 'Tenaga Pendidik',
            'tenaga_pendidik_non_guru' => 'Tenaga Pendidik Non Guru',
            'tenaga_kesehatan' => 'Tenaga Kesehatan',
            'tenaga_kesehatan_non_shift' => 'Tenaga Kesehatan Non Shift',
        ];

        // Get distinct tipe pegawai from jam kerja records
        $tipePegawaiList = JamKerja::getTipePegawaiList();

        // Get hari kerja per tipe pegawai (for dropdown options)
        $hariKerjaPerTipe = [];
        foreach ($tipePegawaiList as $tipe) {
            $hariKerjaPerTipe[$tipe] = HariKerja::getHariKerja($tipe);
        }

        // Non-shift jam kerja: grouped by [tipe_pegawai][hari] => record
        $jamKerjaAll = JamKerja::where('is_active', true)
            ->where('kategori', 'reguler')
            ->whereNull('shift')
            ->orderBy('tipe_pegawai')
            ->orderBy('hari')
            ->get();

        // Shift-based jam kerja (tenaga_kesehatan): grouped by [tipe_pegawai][shift_jumlah][hari]
        $jamKerjaShift = JamKerja::where('is_active', true)
            ->where('kategori', 'reguler')
            ->whereNotNull('shift')
            ->orderBy('tipe_pegawai')
            ->orderBy('shift')
            ->orderBy('jumlah_shift')
            ->orderBy('hari')
            ->get();

        // Determine which tipe_pegawai are shift-based
        $shiftTipeList = $jamKerjaShift->pluck('tipe_pegawai')->unique()->values();
        $nonShiftTipeList = $tipePegawaiList->diff($shiftTipeList)->values();

        // Get apel settings
        $apelSettings = JamApel::getAllSettings();

        return view('admin_kabupaten.jam_kerja.index', compact(
            'module',
            'tipePegawaiList',
            'nonShiftTipeList',
            'shiftTipeList',
            'jamKerjaAll',
            'jamKerjaShift',
            'hariKerjaPerTipe',
            'apelSettings',
            'namaHari',
            'tipeLabels'
        ));
    }

    /**
     * Get all settings data as JSON (for AJAX refresh).
     */
    public function data()
    {
        $settings = JamKerja::where('is_active', true)
            ->orderBy('tipe_pegawai')
            ->orderBy('kategori')
            ->orderBy('shift')
            ->orderBy('hari')
            ->get();

        $apel = JamApel::where('is_active', true)->get();

        return $this->sendResponse([
            'jam_kerja' => $settings,
            'jam_apel' => $apel,
        ], 'Settings Fetched Success');
    }

    /**
     * Bulk save all jam kerja settings from the settings page.
     */
    public function save(Request $request)
    {
        try {
            DB::beginTransaction();

            // Update jam kerja records
            if ($request->has('jam_kerja')) {
                foreach ($request->jam_kerja as $id => $values) {
                    $record = JamKerja::find($id);
                    if ($record) {
                        $record->jam_masuk = $values['jam_masuk'] ?? $record->jam_masuk;
                        $record->jam_keluar = $values['jam_keluar'] ?? $record->jam_keluar;
                        $record->batas_awal_masuk = $values['batas_awal_masuk'] ?? $record->batas_awal_masuk;
                        $record->batas_akhir_masuk = $values['batas_akhir_masuk'] ?? $record->batas_akhir_masuk;
                        $record->batas_awal_pulang = $values['batas_awal_pulang'] ?? $record->batas_awal_pulang;
                        $record->batas_akhir_pulang = $values['batas_akhir_pulang'] ?? $record->batas_akhir_pulang;
                        $record->save();
                    }
                }
            }

            // Update apel records
            if ($request->has('jam_apel')) {
                foreach ($request->jam_apel as $id => $values) {
                    $record = JamApel::find($id);
                    if ($record) {
                        $record->batas_awal = $values['batas_awal'] ?? $record->batas_awal;
                        $record->batas_akhir = $values['batas_akhir'] ?? $record->batas_akhir;
                        $record->save();
                    }
                }
            }

            DB::commit();
            return $this->sendResponse([], 'Pengaturan berhasil disimpan');
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage(), $e->getMessage(), 200);
        }
    }
}
