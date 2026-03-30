<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Traits\General;
use App\Models\JamKerja;
trait Presensi
{
    use General;
    public function jumlahHariKerja($bulan)
    {
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

    public function jmlAlfa($bulan)
    {
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

    public function konvertWaktuNakes($params, $waktu, $tanggal, $shift, $waktu_tetap, $jumlah_shift, $tipe_pegawai)
    {
        $diff = '';
        $selisih_waktu = '';
        $menit = 0;

        $waktu_absen_datang = '';
        $waktu_absen_pulang = '';

        // Ambil hari (1=Senin .. 7=Minggu)
        $dayOfWeek = date('N', strtotime($tanggal));

        // Cek kategori (reguler/ramadan)
        $kategori = $this->isRhamadan($tanggal) ? 'ramadan' : 'reguler';

        // Query jam kerja dari database
        $jamKerja = JamKerja::getJamKerja($tipe_pegawai, $dayOfWeek, $shift, $jumlah_shift, $kategori);

        // Jangan fallback ke reguler untuk tanggal ramadan
        // Biarkan hardcoded logic di bawah yang menangani jam kerja ramadan

        if ($jamKerja) {
            $waktu_absen_datang = $jamKerja->jam_masuk;
            $waktu_absen_pulang = $jamKerja->jam_keluar;

            // Khusus tenaga kesehatan shift pagi hari Senin: gunakan waktu_tetap dari unit_kerja
            $tanggalCarbon = Carbon::createFromFormat('Y-m-d', $tanggal);
            if ($tipe_pegawai == 'tenaga_kesehatan' && $shift == 'pagi' && $tanggalCarbon->isMonday()) {
                if ($params == 'masuk' && $jumlah_shift == 3) {
                    $waktu_absen_datang = $waktu_tetap;
                }
            }
        } else {
            // Fallback ke logika lama jika data belum ada di database
            if ($tipe_pegawai == 'tenaga_kesehatan') {
                if ($shift == 'pagi') {
                    $waktu_absen_datang = $jumlah_shift == 3 ? '08:00:00' : '07:30:00';
                    $waktu_absen_pulang = $jumlah_shift == 3 ? '14:00:00' : '17:00:00';
                } elseif ($shift == 'siang') {
                    $waktu_absen_datang = '14:00:00';
                    $waktu_absen_pulang = '21:00:00';
                } else {
                    $waktu_absen_datang = $jumlah_shift == 3 ? '21:00:00' : '17:00:00';
                    $waktu_absen_pulang = $jumlah_shift == 3 ? '08:00:00' : '07:30:00';
                }
            }
            if ($tipe_pegawai == 'tenaga_kesehatan_non_shift') {
                if ($dayOfWeek == 1 || $dayOfWeek == 5) {
                    $waktu_absen_datang = '07:00:00';
                } else if ($dayOfWeek == 2 || $dayOfWeek == 3 || $dayOfWeek == 4 || $dayOfWeek == 6) {
                    $waktu_absen_datang = '07:30:00';
                }
                $waktu_absen_pulang = '14:00:00';
                if ($dayOfWeek == 5) {
                    $waktu_absen_pulang = '11:30:00';
                }
            }
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

                $waktu_tetap_absen = strtotime($waktu_checkout);
                $waktu_absen = strtotime($waktu);
                $diff = $waktu_tetap_absen - $waktu_absen;
            }

            if ($diff > 0) {
                $menit = floor($diff / 60);
            } else {
                $diff = 0;
            }
        } else {
            $menit = 90;
        }
        return $menit;
    }

    public function konvertWaktu($params, $waktu, $tanggal, $waktu_default_absen, $tipe_pegawai)
    {

        $diff = '';
        $selisih_waktu = '';
        $menit = 0;

        // Ambil hari (1=Senin .. 7=Minggu)
        $dayOfWeek = date('N', strtotime($tanggal));

        // Cek kategori (reguler/ramadan)
        $kategori = $this->isRhamadan($tanggal) ? 'ramadan' : 'reguler';

        // Query jam kerja dari database
        $jamKerja = JamKerja::getJamKerja($tipe_pegawai, $dayOfWeek, null, null, $kategori);

        // Jangan fallback ke reguler untuk tanggal ramadan
        // Biarkan hardcoded logic di bawah yang menangani jam kerja ramadan

        if ($waktu !== null) {
            if ($params == 'masuk') {
                $waktu_tetap_absen = '';

                if ($jamKerja) {
                    $waktu_tetap_absen = strtotime($jamKerja->jam_masuk);
                } else {
                    // Fallback ke logika lama
                    if (!$this->isRhamadan($tanggal)) {
                        $waktu_tetap_absen = strtotime($waktu_default_absen);
                    } else {
                        $waktu_tetap_absen = strtotime('08:00:00');
                    }

                    if (($tipe_pegawai == 'tenaga_pendidik' || $tipe_pegawai == 'tenaga_pendidik_non_guru')) {
                        if ($dayOfWeek == 1 || $dayOfWeek == 5) {
                            $waktu_tetap_absen = strtotime('07:00');
                        } else if ($dayOfWeek == 2 || $dayOfWeek == 3 || $dayOfWeek == 4 || $dayOfWeek == 6) {
                            $waktu_tetap_absen = strtotime('07:30');
                        }
                    }
                }

                $waktu_absen = strtotime($waktu);
                $diff = $waktu_absen - $waktu_tetap_absen;
            } else {
                $waktu_checkout = '';

                if ($jamKerja) {
                    $waktu_checkout = $jamKerja->jam_keluar;
                } else {
                    // Fallback ke logika lama
                    if (!$this->isRhamadan($tanggal)) {
                        $waktu_checkout = $waktu_default_absen;
                    } else {
                        $waktu_checkout = '15:00:00';
                    }

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
                }

                $arr = $this->getDateRange();
                $key = array_search($waktu, $arr);

                $waktu_tetap_absen = strtotime($waktu_checkout);
                $waktu_absen = strtotime($waktu);
                $diff = $waktu_tetap_absen - $waktu_absen;
            }

            if ($diff > 0) {
                $menit = floor($diff / 60);
            } else {
                $diff = 0;
            }
        } else {
            $menit = 90;
        }

        return $menit;
    }
}
