<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HariKerja;

class HariKerjaSeeder extends Seeder
{
    public function run(): void
    {
        $data = [];

        // Pegawai Administratif: Sen-Jum (1-5)
        for ($hari = 1; $hari <= 5; $hari++) {
            $data[] = ['tipe_pegawai' => 'pegawai_administratif', 'hari' => $hari, 'is_hari_kerja' => true];
        }

        // Tenaga Pendidik: Sen-Sab (1-6)
        for ($hari = 1; $hari <= 6; $hari++) {
            $data[] = ['tipe_pegawai' => 'tenaga_pendidik', 'hari' => $hari, 'is_hari_kerja' => true];
        }

        // Tenaga Pendidik Non Guru: Sen-Sab (1-6)
        for ($hari = 1; $hari <= 6; $hari++) {
            $data[] = ['tipe_pegawai' => 'tenaga_pendidik_non_guru', 'hari' => $hari, 'is_hari_kerja' => true];
        }

        // Tenaga Kesehatan: Setiap hari (1-7)
        for ($hari = 1; $hari <= 7; $hari++) {
            $data[] = ['tipe_pegawai' => 'tenaga_kesehatan', 'hari' => $hari, 'is_hari_kerja' => true];
        }

        // Tenaga Kesehatan Non Shift: Sen-Sab (1-6)
        for ($hari = 1; $hari <= 6; $hari++) {
            $data[] = ['tipe_pegawai' => 'tenaga_kesehatan_non_shift', 'hari' => $hari, 'is_hari_kerja' => true];
        }

        foreach ($data as $item) {
            HariKerja::create($item);
        }
    }
}
