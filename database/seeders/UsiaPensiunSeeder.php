<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UsiaPensiun;

class UsiaPensiunSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['tipe_pegawai' => 'pegawai_administratif', 'usia_pensiun' => 58, 'keterangan' => 'PNS Umum'],
            ['tipe_pegawai' => 'tenaga_pendidik', 'usia_pensiun' => 60, 'keterangan' => 'Guru/Dosen'],
            ['tipe_pegawai' => 'tenaga_pendidik_non_guru', 'usia_pensiun' => 58, 'keterangan' => 'Tendik non guru'],
            ['tipe_pegawai' => 'tenaga_kesehatan', 'usia_pensiun' => 58, 'keterangan' => 'Nakes shift'],
            ['tipe_pegawai' => 'tenaga_kesehatan_non_shift', 'usia_pensiun' => 58, 'keterangan' => 'Nakes non shift'],
        ];

        foreach ($data as $item) {
            UsiaPensiun::create($item);
        }
    }
}
