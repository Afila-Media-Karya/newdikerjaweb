<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Traits\General;
trait Presensi
{
    use General;
    public function jumlahHariKerja($bulan){
       $tahun = date('Y');
        $tanggalAwal = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $tanggalAkhir = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        if ($tanggalAwal->isCurrentMonth()) {
            $tanggalAkhir = Carbon::now();
        }


        $jumlahHari = 0;

        while ($tanggalAwal <= $tanggalAkhir) {
            // Cek apakah hari merupakan hari Sabtu atau Minggu
            if ($tanggalAwal->isWeekday() && !$tanggalAwal->isSaturday() && !$tanggalAwal->isSunday()) {
                // Cek apakah tanggal merupakan hari libur
                $tanggal = $tanggalAwal->format('Y-m-d');
                $libur = DB::table('tb_libur')
                    ->whereDate('tanggal_mulai', '<=', $tanggal)
                    ->whereDate('tanggal_selesai', '>=', $tanggal)
                    ->exists();

                if (!$libur) {
                    $jumlahHari++;
                }
            }

            $tanggalAwal->addDay();
        }

        return $jumlahHari;
    }

    public function jmlAlfa($bulan){
        $jumlahHariKerja = $this->jumlahHariKerja($bulan);
          $jumlahAlfa = DB::table('tb_absen')
            ->whereMonth('tanggal_absen', $bulan)
            ->count();

            return $jumlahHariKerja - $jumlahAlfa;
    }

    function getDateRange()
    {
        $start_date = '2024-03-10';
        $end_date = '2024-04-09';

        $dates = [];
        for ($date = Carbon::parse($start_date); $date->lte(Carbon::parse($end_date)); $date->addDay()) {
            $dates[] = $date->format('Y-m-d');
        }

        return $dates;
    }

    public function konvertWaktuNakes($params, $waktu, $tanggal,$shift,$waktu_tetap)
    {
        $diff = '';
        $selisih_waktu = '';
        $menit = 0;

        $waktu_absen_datang = '';
        $waktu_absen_pulang = '';

        if ($shift == 'pagi') {
            
            $waktu_absen_datang = '08:00:00';
            $waktu_absen_pulang = '14:00:00';

            $tanggalCarbon = Carbon::createFromFormat('Y-m-d', $tanggal);

            if ($tanggalCarbon->isMonday()) {
                if ($params == 'masuk') {
                    $waktu_absen_datang = $waktu_tetap;
                }
            }
        }elseif ($shift == 'siang') {
            $waktu_absen_datang = '14:00:00';
            $waktu_absen_pulang = '21:00:00';
        }else {
            $waktu_absen_datang = '21:00:00';
            $waktu_absen_pulang = '08:00:00';
        }

        if ($waktu !== null) {
            if ($params == 'masuk') {
                $waktu_tetap_absen = strtotime($waktu_absen_datang);
                $waktu_absen = strtotime($waktu);
                $diff = $waktu_absen - $waktu_tetap_absen;
            } else {
                $waktu_checkout = $waktu_absen_pulang;
                $arr = $this->getDateRange();
                $key = array_search($waktu, $arr);

                // if ($key !== false) {
                //     $waktu_checkout = '15:00:00';
                // }

                $waktu_tetap_absen = strtotime($waktu_checkout);
                $waktu_absen = strtotime($waktu);
                $diff = $waktu_tetap_absen - $waktu_absen;
            }

            if ($diff > 0) {
                $menit = floor($diff / 60);
            } else {
                $diff = 0;
            }
        }else{
             $menit = 90;
        }
        return $menit;
    }

    public function konvertWaktu($params, $waktu, $tanggal,$waktu_default_absen,$tipe_pegawai)
    {
   
        $diff = '';
        $selisih_waktu = '';
        $menit = 0;

        if ($waktu !== null) {
            if ($params == 'masuk') {
                $waktu_tetap_absen = '';
                if (!$this->isRhamadan($tanggal)) {
                    $waktu_tetap_absen = strtotime($waktu_default_absen);
                }else {
                    $waktu_tetap_absen = strtotime('08:00:00');
                }

                $waktu_absen = strtotime($waktu);
                $diff = $waktu_absen - $waktu_tetap_absen;
            } else {
                $waktu_checkout = '';

                if (!$this->isRhamadan($tanggal)) {
                    $waktu_checkout = $waktu_default_absen;
                }else {
                    $waktu_checkout = '15:00:00';
                }

                $arr = $this->getDateRange();
                $key = array_search($waktu, $arr);                

                if ($tipe_pegawai == 'pegawai_administratif') {
                    if (Carbon::parse($tanggal)->dayOfWeek === Carbon::FRIDAY) {
                        $waktu_checkout = '15:30:00';
                    }
                }

                if ($tipe_pegawai == 'tenaga_pendidik') {
                    
                        $waktu_checkout = '14:00:00';

                        if (Carbon::parse($tanggal)->dayOfWeek === Carbon::FRIDAY) {
                            $waktu_checkout = '11:30:00';
                        }

                        if ($this->isRhamadan($tanggal)) {
                            $waktu_checkout = '13:30:00';
                            if (Carbon::parse($tanggal)->dayOfWeek === Carbon::FRIDAY) {
                                $waktu_checkout = '11:00:00';
                            }
                        }

                        
                }

                if ($tipe_pegawai == 'tenaga_pendidik_non_guru') {
                    $waktu_checkout = '16:00:00';
                    if (Carbon::parse($tanggal)->dayOfWeek === Carbon::FRIDAY) {
                        $waktu_checkout = '15:30:00';
                    }

                    if ($this->isRhamadan($tanggal)) {
                            $waktu_checkout = '15:00:00';
                        }
                }

                $waktu_tetap_absen = strtotime($waktu_checkout);
                $waktu_absen = strtotime($waktu);
                $diff = $waktu_tetap_absen - $waktu_absen;
            }

            if ($diff > 0) {
                $menit = floor($diff / 60);
            } else {
                $diff = 0;
            }
        }else{
             $menit = 90;
        }

        

        return $menit;
    }
}




