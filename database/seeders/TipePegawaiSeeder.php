<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipePegawai;

class TipePegawaiSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['kode' => 'pegawai_administratif', 'nama' => 'Pegawai Administratif'],
            ['kode' => 'tenaga_pendidik', 'nama' => 'Tenaga Pendidik'],
            ['kode' => 'tenaga_pendidik_non_guru', 'nama' => 'Tenaga Pendidik Non Guru'],
            ['kode' => 'tenaga_kesehatan', 'nama' => 'Tenaga Kesehatan'],
            ['kode' => 'tenaga_kesehatan_non_shift', 'nama' => 'Tenaga Kesehatan Non Shift'],
        ];

        foreach ($data as $item) {
            TipePegawai::create($item);
        }
    }
}
