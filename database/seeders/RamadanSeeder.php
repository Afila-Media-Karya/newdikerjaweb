<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ramadan;

class RamadanSeeder extends Seeder
{
    public function run(): void
    {
        Ramadan::create([
            'tahun' => 2025,
            'tanggal_mulai' => '2025-03-01',
            'tanggal_selesai' => '2025-03-31',
            'keterangan' => 'Ramadan 1446 H',
        ]);
    }
}
