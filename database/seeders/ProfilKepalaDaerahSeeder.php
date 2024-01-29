<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid as Generator;
use DB;

class ProfilKepalaDaerahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tb_profil_daerah')->insert([
            'uuid' => Generator::uuid4()->toString(),
            'nama_daerah' => 'ENREKANG',
            'pimpinan_daerah' => 'Andi Muchtar Ali Yusuf',
            'email' => 'bupati@gmail.com',
            'no_telp' => '081234234234',
            'alamat' => 'Rumah Jabatan Bupati ENREKANG',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
