<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PegawaiOtomatis;
use App\Models\Absen;

class PresensiPulangPegawai extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'insert:presensi_pulang_pegawai';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $data = PegawaiOtomatis::all();

        foreach ($data as $key => $value) {
            $absensi = Absen::where('id_pegawai',$value->id_pegawai)->where('tanggal_absen',date('Y-m-d'))->first();
            $absensi->id_pegawai = $value->id_pegawai;
            $absensi->waktu_keluar = date('H:i:s', strtotime('16:00:00') + rand(0, 1800));
            $absensi->save();
        }

        $this->info('Mahasiswa data updated successfully!');
    }
}
