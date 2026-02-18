<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JamApel;
use Illuminate\Support\Facades\DB;

class JamApelSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // Apel Pagi - Pegawai Administratif
            [
                'tipe_pegawai' => 'pegawai_administratif',
                'jenis' => 'reguler',
                'shift' => null,
                'batas_awal' => '06:15:00',
                'batas_akhir' => '07:30:00',
            ],
            // Apel Pagi - Tenaga Pendidik
            [
                'tipe_pegawai' => 'tenaga_pendidik',
                'jenis' => 'reguler',
                'shift' => null,
                'batas_awal' => '06:15:00',
                'batas_akhir' => '07:30:00',
            ],
            // Apel Pagi - Tenaga Pendidik Non Guru
            [
                'tipe_pegawai' => 'tenaga_pendidik_non_guru',
                'jenis' => 'reguler',
                'shift' => null,
                'batas_awal' => '06:15:00',
                'batas_akhir' => '07:30:00',
            ],
            // Apel Pagi - Tenaga Kesehatan (shift pagi)
            [
                'tipe_pegawai' => 'tenaga_kesehatan',
                'jenis' => 'reguler',
                'shift' => 'pagi',
                'batas_awal' => '06:00:00',
                'batas_akhir' => '07:30:00',
            ],
            // Apel Pagi - Tenaga Kesehatan Non Shift
            [
                'tipe_pegawai' => 'tenaga_kesehatan_non_shift',
                'jenis' => 'reguler',
                'shift' => null,
                'batas_awal' => '06:15:00',
                'batas_akhir' => '07:30:00',
            ],

            // Apel Hari Besar - all tipe pegawai
            [
                'tipe_pegawai' => 'pegawai_administratif',
                'jenis' => 'hari_besar',
                'shift' => null,
                'batas_awal' => '06:15:00',
                'batas_akhir' => '07:30:00',
            ],
            [
                'tipe_pegawai' => 'tenaga_pendidik',
                'jenis' => 'hari_besar',
                'shift' => null,
                'batas_awal' => '06:15:00',
                'batas_akhir' => '07:30:00',
            ],
            [
                'tipe_pegawai' => 'tenaga_pendidik_non_guru',
                'jenis' => 'hari_besar',
                'shift' => null,
                'batas_awal' => '06:15:00',
                'batas_akhir' => '07:30:00',
            ],
            [
                'tipe_pegawai' => 'tenaga_kesehatan_non_shift',
                'jenis' => 'hari_besar',
                'shift' => null,
                'batas_awal' => '06:15:00',
                'batas_akhir' => '07:30:00',
            ],
        ];

        foreach ($data as $item) {
            JamApel::create($item);
        }
    }
}
