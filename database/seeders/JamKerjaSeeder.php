<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JamKerja;
use Illuminate\Support\Facades\DB;

class JamKerjaSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        DB::table('tb_jam_kerja')->truncate();

        $data = [];

        // =============================================
        // PEGAWAI ADMINISTRATIF - REGULER (Sen-Jum)
        // =============================================
        // Senin - Kamis: masuk 07:30, pulang 16:00
        for ($hari = 1; $hari <= 4; $hari++) {
            $data[] = [
                'tipe_pegawai' => 'pegawai_administratif',
                'kategori' => 'reguler',
                'shift' => null,
                'jumlah_shift' => null,
                'hari' => $hari,
                'jam_masuk' => '07:30:00',
                'jam_keluar' => '16:00:00',
                'batas_awal_masuk' => '06:30:00',
                'batas_akhir_masuk' => '11:30:00',
                'batas_awal_pulang' => '14:00:00',
                'batas_akhir_pulang' => '22:00:00',
            ];
        }
        // Jumat: masuk 07:30, pulang 15:30
        $data[] = [
            'tipe_pegawai' => 'pegawai_administratif',
            'kategori' => 'reguler',
            'shift' => null,
            'jumlah_shift' => null,
            'hari' => 5,
            'jam_masuk' => '07:30:00',
            'jam_keluar' => '15:30:00',
            'batas_awal_masuk' => '06:30:00',
            'batas_akhir_masuk' => '11:30:00',
            'batas_awal_pulang' => '14:00:00',
            'batas_akhir_pulang' => '22:00:00',
        ];

        // =============================================
        // PEGAWAI ADMINISTRATIF - RAMADAN (Sen-Jum)
        // =============================================
        for ($hari = 1; $hari <= 5; $hari++) {
            $data[] = [
                'tipe_pegawai' => 'pegawai_administratif',
                'kategori' => 'ramadan',
                'shift' => null,
                'jumlah_shift' => null,
                'hari' => $hari,
                'jam_masuk' => '08:00:00',
                'jam_keluar' => '15:00:00',
                'batas_awal_masuk' => '06:30:00',
                'batas_akhir_masuk' => '11:30:00',
                'batas_awal_pulang' => '13:00:00',
                'batas_akhir_pulang' => '22:00:00',
            ];
        }

        // =============================================
        // TENAGA PENDIDIK - REGULER (Sen-Sab)
        // =============================================
        // Senin & Jumat: masuk 07:00
        foreach ([1, 5] as $hari) {
            $data[] = [
                'tipe_pegawai' => 'tenaga_pendidik',
                'kategori' => 'reguler',
                'shift' => null,
                'jumlah_shift' => null,
                'hari' => $hari,
                'jam_masuk' => '07:00:00',
                'jam_keluar' => $hari == 5 ? '11:30:00' : '14:00:00',
                'batas_awal_masuk' => '06:30:00',
                'batas_akhir_masuk' => '11:30:00',
                'batas_awal_pulang' => '14:00:00',
                'batas_akhir_pulang' => '22:00:00',
            ];
        }
        // Selasa, Rabu, Kamis, Sabtu: masuk 07:30
        foreach ([2, 3, 4, 6] as $hari) {
            $data[] = [
                'tipe_pegawai' => 'tenaga_pendidik',
                'kategori' => 'reguler',
                'shift' => null,
                'jumlah_shift' => null,
                'hari' => $hari,
                'jam_masuk' => '07:30:00',
                'jam_keluar' => '14:00:00',
                'batas_awal_masuk' => '06:30:00',
                'batas_akhir_masuk' => '11:30:00',
                'batas_awal_pulang' => '14:00:00',
                'batas_akhir_pulang' => '22:00:00',
            ];
        }

        // =============================================
        // TENAGA PENDIDIK - RAMADAN (Sen-Sab)
        // =============================================
        foreach ([1, 5] as $hari) {
            $data[] = [
                'tipe_pegawai' => 'tenaga_pendidik',
                'kategori' => 'ramadan',
                'shift' => null,
                'jumlah_shift' => null,
                'hari' => $hari,
                'jam_masuk' => '07:00:00',
                'jam_keluar' => $hari == 5 ? '11:00:00' : '13:30:00',
                'batas_awal_masuk' => '06:30:00',
                'batas_akhir_masuk' => '11:30:00',
                'batas_awal_pulang' => '13:00:00',
                'batas_akhir_pulang' => '22:00:00',
            ];
        }
        foreach ([2, 3, 4, 6] as $hari) {
            $data[] = [
                'tipe_pegawai' => 'tenaga_pendidik',
                'kategori' => 'ramadan',
                'shift' => null,
                'jumlah_shift' => null,
                'hari' => $hari,
                'jam_masuk' => '07:30:00',
                'jam_keluar' => '13:30:00',
                'batas_awal_masuk' => '06:30:00',
                'batas_akhir_masuk' => '11:30:00',
                'batas_awal_pulang' => '13:00:00',
                'batas_akhir_pulang' => '22:00:00',
            ];
        }

        // =============================================
        // TENAGA PENDIDIK NON GURU - REGULER (Sen-Sab)
        // =============================================
        foreach ([1, 5] as $hari) {
            $data[] = [
                'tipe_pegawai' => 'tenaga_pendidik_non_guru',
                'kategori' => 'reguler',
                'shift' => null,
                'jumlah_shift' => null,
                'hari' => $hari,
                'jam_masuk' => '07:00:00',
                'jam_keluar' => $hari == 5 ? '11:30:00' : '14:00:00',
                'batas_awal_masuk' => '06:30:00',
                'batas_akhir_masuk' => '11:30:00',
                'batas_awal_pulang' => '14:00:00',
                'batas_akhir_pulang' => '22:00:00',
            ];
        }
        foreach ([2, 3, 4, 6] as $hari) {
            $data[] = [
                'tipe_pegawai' => 'tenaga_pendidik_non_guru',
                'kategori' => 'reguler',
                'shift' => null,
                'jumlah_shift' => null,
                'hari' => $hari,
                'jam_masuk' => '07:30:00',
                'jam_keluar' => '14:00:00',
                'batas_awal_masuk' => '06:30:00',
                'batas_akhir_masuk' => '11:30:00',
                'batas_awal_pulang' => '14:00:00',
                'batas_akhir_pulang' => '22:00:00',
            ];
        }

        // =============================================
        // TENAGA PENDIDIK NON GURU - RAMADAN (Sen-Sab)
        // =============================================
        foreach ([1, 5] as $hari) {
            $data[] = [
                'tipe_pegawai' => 'tenaga_pendidik_non_guru',
                'kategori' => 'ramadan',
                'shift' => null,
                'jumlah_shift' => null,
                'hari' => $hari,
                'jam_masuk' => '07:00:00',
                'jam_keluar' => $hari == 5 ? '11:00:00' : '13:30:00',
                'batas_awal_masuk' => '06:30:00',
                'batas_akhir_masuk' => '11:30:00',
                'batas_awal_pulang' => '13:00:00',
                'batas_akhir_pulang' => '22:00:00',
            ];
        }
        foreach ([2, 3, 4, 6] as $hari) {
            $data[] = [
                'tipe_pegawai' => 'tenaga_pendidik_non_guru',
                'kategori' => 'ramadan',
                'shift' => null,
                'jumlah_shift' => null,
                'hari' => $hari,
                'jam_masuk' => '07:30:00',
                'jam_keluar' => '13:30:00',
                'batas_awal_masuk' => '06:30:00',
                'batas_akhir_masuk' => '11:30:00',
                'batas_awal_pulang' => '13:00:00',
                'batas_akhir_pulang' => '22:00:00',
            ];
        }

        // =============================================
        // TENAGA KESEHATAN - 3 SHIFT (Setiap hari)
        // =============================================
        for ($hari = 1; $hari <= 7; $hari++) {
            // Shift Pagi
            $data[] = [
                'tipe_pegawai' => 'tenaga_kesehatan',
                'kategori' => 'reguler',
                'shift' => 'pagi',
                'jumlah_shift' => 3,
                'hari' => $hari,
                'jam_masuk' => '08:00:00',
                'jam_keluar' => '14:00:00',
                'batas_awal_masuk' => '07:30:00',
                'batas_akhir_masuk' => '10:29:00',
                'batas_awal_pulang' => '12:00:00',
                'batas_akhir_pulang' => '17:44:00',
            ];
            // Shift Siang
            $data[] = [
                'tipe_pegawai' => 'tenaga_kesehatan',
                'kategori' => 'reguler',
                'shift' => 'siang',
                'jumlah_shift' => 3,
                'hari' => $hari,
                'jam_masuk' => '14:00:00',
                'jam_keluar' => '21:00:00',
                'batas_awal_masuk' => '13:00:00',
                'batas_akhir_masuk' => '17:44:00',
                'batas_awal_pulang' => '18:00:00',
                'batas_akhir_pulang' => '23:59:00',
            ];
            // Shift Malam
            $data[] = [
                'tipe_pegawai' => 'tenaga_kesehatan',
                'kategori' => 'reguler',
                'shift' => 'malam',
                'jumlah_shift' => 3,
                'hari' => $hari,
                'jam_masuk' => '21:00:00',
                'jam_keluar' => '08:00:00',
                'batas_awal_masuk' => '21:00:00',
                'batas_akhir_masuk' => '23:59:00',
                'batas_awal_pulang' => '05:30:00',
                'batas_akhir_pulang' => '10:29:00',
            ];
        }

        // =============================================
        // TENAGA KESEHATAN - 2 SHIFT (Setiap hari)
        // =============================================
        for ($hari = 1; $hari <= 7; $hari++) {
            // Shift Pagi
            $data[] = [
                'tipe_pegawai' => 'tenaga_kesehatan',
                'kategori' => 'reguler',
                'shift' => 'pagi',
                'jumlah_shift' => 2,
                'hari' => $hari,
                'jam_masuk' => '07:30:00',
                'jam_keluar' => '17:00:00',
                'batas_awal_masuk' => '07:00:00',
                'batas_akhir_masuk' => '10:29:00',
                'batas_awal_pulang' => '14:00:00',
                'batas_akhir_pulang' => '22:00:00',
            ];
            // Shift Malam
            $data[] = [
                'tipe_pegawai' => 'tenaga_kesehatan',
                'kategori' => 'reguler',
                'shift' => 'malam',
                'jumlah_shift' => 2,
                'hari' => $hari,
                'jam_masuk' => '17:00:00',
                'jam_keluar' => '07:30:00',
                'batas_awal_masuk' => '17:00:00',
                'batas_akhir_masuk' => '23:59:00',
                'batas_awal_pulang' => '05:00:00',
                'batas_akhir_pulang' => '10:29:00',
            ];
        }

        // =============================================
        // TENAGA KESEHATAN NON SHIFT - REGULER (Sen-Sab)
        // =============================================
        foreach ([1, 5] as $hari) {
            $data[] = [
                'tipe_pegawai' => 'tenaga_kesehatan_non_shift',
                'kategori' => 'reguler',
                'shift' => null,
                'jumlah_shift' => null,
                'hari' => $hari,
                'jam_masuk' => '07:00:00',
                'jam_keluar' => $hari == 5 ? '11:30:00' : '14:00:00',
                'batas_awal_masuk' => '06:30:00',
                'batas_akhir_masuk' => '11:30:00',
                'batas_awal_pulang' => '14:00:00',
                'batas_akhir_pulang' => '22:00:00',
            ];
        }
        foreach ([2, 3, 4, 6] as $hari) {
            $data[] = [
                'tipe_pegawai' => 'tenaga_kesehatan_non_shift',
                'kategori' => 'reguler',
                'shift' => null,
                'jumlah_shift' => null,
                'hari' => $hari,
                'jam_masuk' => '07:30:00',
                'jam_keluar' => '14:00:00',
                'batas_awal_masuk' => '06:30:00',
                'batas_akhir_masuk' => '11:30:00',
                'batas_awal_pulang' => '14:00:00',
                'batas_akhir_pulang' => '22:00:00',
            ];
        }

        foreach ($data as $item) {
            JamKerja::create($item);
        }
    }
}
