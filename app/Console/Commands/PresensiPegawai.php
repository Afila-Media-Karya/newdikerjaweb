<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PegawaiOtomatis;
use App\Models\Absen;

class PresensiPegawai extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'insert:presensi_pegawai';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insert presensi';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $data = PegawaiOtomatis::all();

        foreach ($data as $key => $value) {
            $absensi = new Absen();
            $absensi->id_pegawai = $value->id_pegawai;
            $absensi->tanggal_absen = date('Y-m-d');
            $absensi->waktu_masuk = date('H:i:s', strtotime('07:30:00') + rand(0, 1800));
            $absensi->status = 'hadir';
            $absensi->validation = 1;
            $absensi->user_type = 0;
            $absensi->tahun = date('Y');
            $absensi->user_insert = $value->id_pegawai;
            $absensi->user_update = $value->id_pegawai;
            $absensi->save();
        }

        $this->info('Mahasiswa data inserted successfully!');
    }
}
