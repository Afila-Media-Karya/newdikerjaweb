<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PotonganKehadiran;

class PotonganKehadiranSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // Keterlambatan Masuk Kerja (KMK)
            ['jenis' => 'kmk', 'label' => 'Terlambat 1-30 menit', 'menit_awal' => 1, 'menit_akhir' => 30, 'persentase' => 0.50],
            ['jenis' => 'kmk', 'label' => 'Terlambat 31-60 menit', 'menit_awal' => 31, 'menit_akhir' => 60, 'persentase' => 1.00],
            ['jenis' => 'kmk', 'label' => 'Terlambat 61-90 menit', 'menit_awal' => 61, 'menit_akhir' => 90, 'persentase' => 1.25],
            ['jenis' => 'kmk', 'label' => 'Terlambat > 90 menit', 'menit_awal' => 91, 'menit_akhir' => 9999, 'persentase' => 1.50],

            // Cepat Pulang Kerja (CPK)
            ['jenis' => 'cpk', 'label' => 'Pulang cepat 1-30 menit', 'menit_awal' => 1, 'menit_akhir' => 30, 'persentase' => 0.50],
            ['jenis' => 'cpk', 'label' => 'Pulang cepat 31-60 menit', 'menit_awal' => 31, 'menit_akhir' => 60, 'persentase' => 1.00],
            ['jenis' => 'cpk', 'label' => 'Pulang cepat 61-90 menit', 'menit_awal' => 61, 'menit_akhir' => 90, 'persentase' => 1.25],
            ['jenis' => 'cpk', 'label' => 'Pulang cepat > 90 menit', 'menit_awal' => 91, 'menit_akhir' => 9999, 'persentase' => 1.50],

            // Tanpa Keterangan (Alfa)
            ['jenis' => 'alfa', 'label' => 'Tidak hadir tanpa keterangan', 'menit_awal' => null, 'menit_akhir' => null, 'persentase' => 3.00],

            // Apel
            ['jenis' => 'apel', 'label' => 'Tidak apel', 'menit_awal' => null, 'menit_akhir' => null, 'persentase' => 2.00],
            ['jenis' => 'apel_senin', 'label' => 'Tidak apel hari Senin', 'menit_awal' => null, 'menit_akhir' => null, 'persentase' => 0.25],
        ];

        foreach ($data as $item) {
            PotonganKehadiran::create($item);
        }
    }
}
