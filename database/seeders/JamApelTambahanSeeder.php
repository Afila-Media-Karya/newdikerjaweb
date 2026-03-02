<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JamApel;

class JamApelTambahanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Buat data jam apel untuk hari Selasa s/d Sabtu berdasarkan data Senin.
     */
    public function run(): void
    {
        $tipePegawaiData = [
            [
                'tipe_pegawai' => 'pegawai_administratif',
                'batas_awal' => '10:15:00',
                'batas_akhir' => '09:30:00',
            ],
            [
                'tipe_pegawai' => 'tenaga_pendidik',
                'batas_awal' => '08:15:00',
                'batas_akhir' => '09:30:00',
            ],
            [
                'tipe_pegawai' => 'tenaga_kesehatan',
                'batas_awal' => '08:15:00',
                'batas_akhir' => '09:30:00',
            ],
            [
                'tipe_pegawai' => 'tenaga_kesehatan_non_shift',
                'batas_awal' => '08:15:00',
                'batas_akhir' => '09:30:00',
            ],
            [
                'tipe_pegawai' => 'tenaga_pendidik_non_guru',
                'batas_awal' => '08:15:00',
                'batas_akhir' => '09:30:00',
            ],
        ];

        // Hari: 2=Selasa, 3=Rabu, 4=Kamis, 5=Jumat, 6=Sabtu
        $days = [2, 3, 4, 5, 6];

        foreach ($days as $hari) {
            foreach ($tipePegawaiData as $data) {
                // Gunakan updateOrCreate untuk menghindari duplikasi jika dijalankan ulang
                JamApel::updateOrCreate(
                    [
                        'tipe_pegawai' => $data['tipe_pegawai'],
                        'hari' => $hari,
                        'jenis' => 'reguler',
                    ],
                    [
                        'shift' => null,
                        'batas_awal' => $data['batas_awal'],
                        'batas_akhir' => $data['batas_akhir'],
                        'is_active' => 1,
                    ]
                );
            }
        }
    }
}
