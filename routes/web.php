<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::group(['namespace' => 'App\Http\Controllers'], function () {
    Route::get('/', 'DashboardController@index')->name('app.index');
    Route::get('set-tahun-penganggaran', 'DashboardController@setTahunAnggaran')->name('set-tahun-penganggaran');

    Route::get('/tambah-user/{params}', 'DashboardController@setTahunAnggaran');

    Route::prefix('set-laporan')->group(function () {
        Route::get('/laporan-kehadiran-pegawai', 'LaporanKehadiranController@export_pegawai_bulan')->name('setlaporan.laporan.kehadiran.export');
        Route::get('/laporan-kinerja-pegawai', 'LaporanKinerjaController@export_pegawai')->name('setlaporan.laporan.kinerja.export');
        Route::get('/laporan-tpp-pegawai', 'LaporanTppController@export_pegawai')->name('setlaporan.laporan.tpp.export');
    });

    Route::get('/profil/get-image-profil', 'ProfilController@getImageProfil')->name('pegawai.profil.image');
    Route::get('/get-file-pegawai', 'ProfilController@file_dokumen_pribadi')->name('get.file');

    Route::prefix('login')->middleware(['authcheck'])->group(function () {
        Route::get('/', 'AuthController@view_login')->name('login');
        Route::post('/', 'AuthController@login')->name('login.post');
    });

    Route::get('/change-session-jabatan/{params}', 'DashboardController@change_session')->name('change_session');

    Route::prefix('login')->middleware(['authcheck'])->group(function () {
        Route::get('/', 'AuthController@view_login')->name('login');
        Route::post('/', 'AuthController@login')->name('login.post');
    });

    Route::prefix('get-dashboard')->group(function () {
        Route::get('/persentase', 'DashboardController@data_opd')->name('dashboard.admin.data');
        Route::get('/rank-opd', 'DashboardController@data_per_skpd')->name('dashboard.admin.data_skpd');
        Route::get('/persentase-pegawai', 'DashboardController@pegawai_jumlah')->name('dashboard.admin.pegawai_jumlah');
    });

    Route::middleware(['admin_kabupaten'])->group(function () {
        Route::get('/dashboard-kabupaten', 'DashboardController@admin_kabupaten')->name('dashboard.admin');

        Route::prefix('master-data')->group(function () {
            Route::prefix('agama')->group(function () {
                Route::get('/', 'master_data\agamaController@index')->name('master_data.agama.index');
                Route::get('/datatable', 'master_data\agamaController@datatable')->name('master_data.agama.datatable');
                Route::post('/store', 'master_data\agamaController@store')->name('master_data.agama.store');
                Route::post('/update/{params}', 'master_data\agamaController@update')->name('master_data.agama.update');
                Route::get('/show/{params}', 'master_data\agamaController@show')->name('master_data.agama.show');
                Route::delete('/delete/{params}', 'master_data\agamaController@delete')->name('master_data.agama.delete');
            });
            Route::prefix('eselon')->group(function () {
                Route::get('/', 'master_data\eselonController@index')->name('master_data.eselon.index');
                Route::get('/datatable', 'master_data\eselonController@datatable')->name('master_data.eselon.datatable');
                Route::post('/store', 'master_data\eselonController@store')->name('master_data.eselon.store');
                Route::post('/update/{params}', 'master_data\eselonController@update')->name('master_data.eselon.update');
                Route::get('/show/{params}', 'master_data\eselonController@show')->name('master_data.eselon.show');
                Route::delete('/delete/{params}', 'master_data\eselonController@delete')->name('master_data.eselon.delete');
            });
            Route::prefix('pendidikan')->group(function () {
                Route::get('/', 'master_data\pendidikanController@index')->name('master_data.pendidikan.index');
                Route::get('/datatable', 'master_data\pendidikanController@datatable')->name('master_data.pendidikan.datatable');
                Route::post('/store', 'master_data\pendidikanController@store')->name('master_data.pendidikan.store');
                Route::post('/update/{params}', 'master_data\pendidikanController@update')->name('master_data.pendidikan.update');
                Route::get('/show/{params}', 'master_data\pendidikanController@show')->name('master_data.pendidikan.show');
                Route::delete('/delete/{params}', 'master_data\pendidikanController@delete')->name('master_data.pendidikan.delete');
            });

            Route::prefix('golongan')->group(function () {
                Route::get('/', 'master_data\golonganController@index')->name('master_data.golongan.index');
                Route::get('/datatable', 'master_data\golonganController@datatable')->name('master_data.golongan.datatable');
                Route::post('/store', 'master_data\golonganController@store')->name('master_data.golongan.store');
                Route::post('/update/{params}', 'master_data\golonganController@update')->name('master_data.golongan.update');
                Route::get('/show/{params}', 'master_data\golonganController@show')->name('master_data.golongan.show');
                Route::delete('/delete/{params}', 'master_data\golonganController@delete')->name('master_data.golongan.delete');
            });

            Route::prefix('satuan')->group(function () {
                Route::get('/', 'master_data\satuanController@index')->name('master_data.satuan.index');
                Route::get('/datatable', 'master_data\satuanController@datatable')->name('master_data.satuan.datatable');
                Route::post('/store', 'master_data\satuanController@store')->name('master_data.satuan.store');
                Route::post('/update/{params}', 'master_data\satuanController@update')->name('master_data.satuan.update');
                Route::get('/show/{params}', 'master_data\satuanController@show')->name('master_data.satuan.show');
                Route::delete('/delete/{params}', 'master_data\satuanController@delete')->name('master_data.satuan.delete');
            });
        });

        Route::prefix('pegawai')->group(function () {
            Route::prefix('list-pegawai')->group(function () {
                Route::get('/', 'pegawai\listPegawaiController@index')->name('kabupaten.pegawai.listpegawai.index');
                Route::get('/datatable', 'pegawai\listPegawaiController@datatable')->name('kabupaten.pegawai.listpegawai.datatable');
                Route::post('/store', 'pegawai\listPegawaiController@store')->name('kabupaten.pegawai.listpegawai.store');
                Route::post('/update/{params}', 'pegawai\listPegawaiController@update')->name('kabupaten.pegawai.listpegawai.update');
                Route::get('/show/{params}', 'pegawai\listPegawaiController@show')->name('kabupaten.pegawai.listpegawai.show');
                Route::get('/option', 'pegawai\listPegawaiController@option')->name('kabupaten.pegawai.listpegawai.option');
                Route::get('/option-by-unit-kerja', 'pegawai\listPegawaiController@option_by_unitkerja')->name('kabupaten.pegawai.listpegawai.optionbyunitkerja');
                Route::post('/reset-wajah', 'pegawai\listPegawaiController@reset_wajah')->name('kabupaten.pegawai.listpegawai.reset_wajah');
                Route::delete('/delete/{params}', 'pegawai\listPegawaiController@delete')->name('kabupaten.pegawai.listpegawai.delete');
            });
            Route::prefix('verifikasi')->group(function () {
                Route::get('/', 'pegawai\verifikasiPegawaiController@index')->name('kabupaten.pegawai.verifikasi.index');
                Route::post('/verifikasi', 'pegawai\verifikasiPegawaiController@verifikasi')->name('kabupaten.pegawai.verifikasi.verifikasi');
                Route::get('/datatable', 'pegawai\verifikasiPegawaiController@datatable')->name('kabupaten.pegawai.verifikasi.datatable');
                Route::get('/detail/{params}', 'pegawai\verifikasiPegawaiController@detail')->name('kabupaten.pegawai.verifikasi.detail');
            });
            Route::prefix('pegawai-masuk')->group(function () {
                Route::get('/', 'pegawai\pegawaiMasukController@index')->name('kabupaten.pegawai.pegawaimasuk.index');
                Route::get('/datatable', 'pegawai\pegawaiMasukController@datatable')->name('kabupaten.pegawai.pegawaimasuk.datatable');
                Route::post('/store', 'pegawai\pegawaiMasukController@store')->name('kabupaten.pegawai.pegawaimasuk.store');
                Route::post('/update/{params}', 'pegawai\pegawaiMasukController@update')->name('kabupaten.pegawai.pegawaimasuk.update');
                Route::get('/show/{params}', 'pegawai\pegawaiMasukController@show')->name('kabupaten.pegawai.pegawaimasuk.show');
                Route::get('/detail/{params}', 'pegawai\pegawaiMasukController@detail')->name('kabupaten.pegawai.pegawaimasuk.detail');
                Route::delete('/delete/{params}', 'pegawai\pegawaiMasukController@delete')->name('kabupaten.pegawai.pegawaimasuk.delete');
            });
            Route::prefix('pegawai-keluar')->group(function () {
                Route::get('/', 'pegawai\pegawaiKeluarController@index')->name('kabupaten.pegawai.pegawaikeluar.index');
                Route::get('/datatable', 'pegawai\pegawaiKeluarController@datatable')->name('kabupaten.pegawai.pegawaikeluar.datatable');
                Route::post('/store', 'pegawai\pegawaiKeluarController@store')->name('kabupaten.pegawai.pegawaikeluar.store');
                Route::post('/update/{params}', 'pegawai\pegawaiKeluarController@update')->name('kabupaten.pegawai.pegawaikeluar.update');
                Route::get('/show/{params}', 'pegawai\pegawaiKeluarController@show')->name('kabupaten.pegawai.pegawaikeluar.show');
                Route::get('/detail/{params}', 'pegawai\pegawaiKeluarController@detail')->name('kabupaten.pegawai.pegawaikeluar.detail');
                Route::delete('/delete/{params}', 'pegawai\pegawaiKeluarController@delete')->name('kabupaten.pegawai.pegawaikeluar.delete');
            });
            Route::prefix('pegawai-pensiun')->group(function () {
                Route::get('/', 'pegawai\pegawaiPensiunController@index')->name('kabupaten.pegawai.pegawaipensiun.index');
                Route::get('/datatable', 'pegawai\pegawaiPensiunController@datatable')->name('kabupaten.pegawai.pegawaipensiun.datatable');
                Route::post('/store', 'pegawai\pegawaiPensiunController@store')->name('kabupaten.pegawai.pegawaipensiun.store');
                Route::post('/update/{params}', 'pegawai\pegawaiPensiunController@update')->name('kabupaten.pegawai.pegawaipensiun.update');
                Route::get('/show/{params}', 'pegawai\pegawaiPensiunController@show')->name('kabupaten.pegawai.pegawaipensiun.show');
                Route::get('/detail/{params}', 'pegawai\pegawaiPensiunController@detail')->name('kabupaten.pegawai.pegawaipensiun.detail');
                Route::delete('/delete/{params}', 'pegawai\pegawaiPensiunController@delete')->name('kabupaten.pegawai.pegawaipensiun.delete');
            });

            Route::prefix('pegawai-non-job')->group(function () {
                Route::get('/', 'pegawai\PegawaiNonJobController@index')->name('kabupaten.pegawai.pegawainonjob.index');
                Route::get('/datatable', 'pegawai\PegawaiNonJobController@datatable')->name('kabupaten.pegawai.pegawainonjob.datatable');
            });

            Route::prefix('pegawai-akan-pensiun')->group(function () {
                Route::get('/', 'pegawai\pegawaiPensiunController@index_akan_pensiun')->name('kabupaten.pegawai.pegawaiakanpensiun.index');
                Route::get('/datatable', 'pegawai\pegawaiPensiunController@datatable_akan_pensiun')->name('kabupaten.pegawai.pegawaiakanpensiun.datatable');
                Route::get('/option/{params}', 'pegawai\pegawaiPensiunController@option')->name('kabupaten.pegawai.pegawaiakanpensiun.option');
            });
        });

        Route::prefix('user')->group(function () {
            Route::get('/', 'UserController@index')->name('kabupaten.pegawai.user.index');
            Route::get('/datatable', 'UserController@datatable')->name('kabupaten.pegawai.user.datatable');
            Route::post('/store', 'UserController@store')->name('kabupaten.pegawai.user.store');
            Route::post('/update/{params}', 'UserController@update')->name('kabupaten.pegawai.user.update');
            Route::post('/reset-password', 'UserController@reset')->name('kabupaten.pegawai.user.reset');
            Route::get('/show/{params}', 'UserController@show')->name('kabupaten.pegawai.user.show');
            Route::get('/option/{params}', 'UserController@option')->name('kabupaten.pegawai.user.option');
            Route::delete('/delete/{params}', 'UserController@delete')->name('kabupaten.pegawai.user.delete');
        });
        Route::prefix('admins')->group(function () {
            Route::get('/', 'AdminController@index')->name('kabupaten.admin.index');
            Route::get('/datatable', 'AdminController@datatable')->name('kabupaten.admin.datatable');
            Route::post('/store', 'AdminController@store')->name('kabupaten.admin.store');
            Route::post('/update/{params}', 'AdminController@update')->name('kabupaten.admin.update');
            Route::get('/show/{params}', 'AdminController@show')->name('kabupaten.admin.show');
            Route::get('/option/{params}', 'AdminController@option')->name('kabupaten.admin.option');
            Route::delete('/delete/{params}', 'AdminController@delete')->name('kabupaten.admin.delete');
        });

        Route::prefix('perangkat-daerah')->group(function () {
            Route::prefix('perangkat-daerah')->group(function () {
                Route::get('/', 'perangkat_daerah\PerangkatDaerahController@index')->name('kabupaten.perangkat_daerah.index');
                Route::get('/datatable', 'perangkat_daerah\PerangkatDaerahController@datatable')->name('kabupaten.perangkat_daerah.datatable');
                Route::post('/store', 'perangkat_daerah\PerangkatDaerahController@store')->name('kabupaten.perangkat_daerah.store');
                Route::post('/update/{params}', 'perangkat_daerah\PerangkatDaerahController@update')->name('kabupaten.perangkat_daerah.update');
                Route::get('/show/{params}', 'perangkat_daerah\PerangkatDaerahController@show')->name('kabupaten.perangkat_daerah.show');
                Route::delete('/delete/{params}', 'perangkat_daerah\PerangkatDaerahController@delete')->name('kabupaten.perangkat_daerah.delete');
            });
            Route::prefix('unit-kerja')->group(function () {
                Route::get('/', 'perangkat_daerah\UnitKerjaController@index')->name('kabupaten.unit_kerja.index');
                Route::get('/datatable', 'perangkat_daerah\UnitKerjaController@datatable')->name('kabupaten.unit_kerja.datatable');
                Route::post('/store', 'perangkat_daerah\UnitKerjaController@store')->name('kabupaten.unit_kerja.store');
                Route::post('/update/{params}', 'perangkat_daerah\UnitKerjaController@update')->name('kabupaten.unit_kerja.update');
                Route::get('/show/{params}', 'perangkat_daerah\UnitKerjaController@show')->name('kabupaten.unit_kerja.show');
                Route::delete('/delete/{params}', 'perangkat_daerah\UnitKerjaController@delete')->name('kabupaten.unit_kerja.delete');
                Route::get('/option', 'perangkat_daerah\UnitKerjaController@option')->name('kabupaten.unit_kerja.option');
            });
            Route::prefix('lokasi')->group(function () {
                Route::get('/', 'perangkat_daerah\lokasiController@index')->name('kabupaten.perangkat_daerah.lokasi.index');
                Route::get('/datatable', 'perangkat_daerah\lokasiController@datatable')->name('kabupaten.perangkat_daerah.lokasi.datatable');
                Route::post('/store', 'perangkat_daerah\lokasiController@store')->name('kabupaten.perangkat_daerah.lokasi.store');
                Route::post('/update/{params}', 'perangkat_daerah\lokasiController@update')->name('kabupaten.perangkat_daerah.lokasi.update');
                Route::get('/show/{params}', 'perangkat_daerah\lokasiController@show')->name('kabupaten.perangkat_daerah.lokasi.show');
                Route::delete('/delete/{params}', 'perangkat_daerah\lokasiController@delete')->name('kabupaten.perangkat_daerah.lokasi.delete');
                Route::get('/option-lokasi/{params}', 'perangkat_daerah\lokasiController@optionLokasiSatuanKerja')->name('kabupaten.perangkat_daerah.lokasi.option_lokasi_satuan_kerja');
                Route::get('/option-lokasi-apel/{params}', 'perangkat_daerah\lokasiController@optionLokasiApel')->name('kabupaten.perangkat_daerah.lokasi.option_lokasi_apel');
            });
        });

        Route::prefix('master-jabatan')->group(function () {
            Route::prefix('jenis-jabatan')->group(function () {
                Route::get('/', 'master_jabatan\JenisJabatanController@index')->name('kabupaten.master_jabatan.jenis_jabatan.index');
                Route::get('/datatable', 'master_jabatan\JenisJabatanController@datatable')->name('kabupaten.master_jabatan.jenis_jabatan.datatable');
            });

            Route::prefix('kelompok-aktivitas')->group(function () {
                Route::get('/', 'master_aktivitas\KelompokAktivitasController@index')->name('kabupaten.master_jabatan.kelompok_aktivitas.index');
                Route::get('/datatable', 'master_aktivitas\KelompokAktivitasController@datatable')->name('kabupaten.master_jabatan.kelompok_aktivitas.datatable');
                Route::get('/tambah', 'master_aktivitas\KelompokAktivitasController@create')->name('kabupaten.master_jabatan.kelompok_aktivitas.create');
                Route::get('/edit/{params}', 'master_aktivitas\KelompokAktivitasController@edit')->name('kabupaten.master_jabatan.kelompok_aktivitas.edit');
                Route::post('/store', 'master_aktivitas\KelompokAktivitasController@store')->name('kabupaten.master_jabatan.kelompok_aktivitas.store');
                Route::post('/update/{params}', 'master_aktivitas\KelompokAktivitasController@update')->name('kabupaten.master_jabatan.kelompok_aktivitas.update');
                Route::get('/show/{params}', 'master_aktivitas\KelompokAktivitasController@show')->name('kabupaten.master_jabatan.kelompok_aktivitas.show');
                Route::delete('/delete/{params}', 'master_aktivitas\KelompokAktivitasController@delete')->name('kabupaten.master_jabatan.kelompok_aktivitas.delete');
            });

            Route::prefix('master-jabatan')->group(function () {
                Route::get('/', 'master_jabatan\MasterJabatanController@index')->name('kabupaten.master_jabatan.master_jabatan.index');
                Route::get('/datatable', 'master_jabatan\MasterJabatanController@datatable')->name('kabupaten.master_jabatan.master_jabatan.datatable');
                Route::post('/store', 'master_jabatan\MasterJabatanController@store')->name('kabupaten.master_jabatan.master_jabatan.store');
                Route::post('/update/{params}', 'master_jabatan\MasterJabatanController@update')->name('kabupaten.master_jabatan.master_jabatan.update');
                Route::get('/show/{params}', 'master_jabatan\MasterJabatanController@show')->name('kabupaten.master_jabatan.master_jabatan.show');
                Route::get('/showId/{params}', 'master_jabatan\MasterJabatanController@showByid')->name('kabupaten.master_jabatan.master_jabatan.showByid');
                Route::delete('/delete/{params}', 'master_jabatan\MasterJabatanController@delete')->name('kabupaten.master_jabatan.master_jabatan.delete');
                Route::get('/option', 'master_jabatan\MasterJabatanController@optionJabatan')->name('kabupaten. master_jabatan.master_jabatan.option');
                Route::get('/option-kelompok-jabatan', 'master_jabatan\MasterJabatanController@optionKelompokJabatan')->name('kabupaten.master_jabatan.master_jabatan.option_kelompok_jabatan');
                Route::get('/option-atasan-langsung', 'master_jabatan\MasterJabatanController@optionAtasanLangsung')->name('kabupaten.master_jabatan.master_jabatan.optionAtasanLangsung');
                Route::get('/cetak-jabatan', 'master_jabatan\MasterJabatanController@cetak_jabatan')->name('kabupaten.master_jabatan.master_jabatan.cetak_jabatan');
            });
        });

        // Route::prefix('master-aktivitas')->group(function () {
        //     Route::prefix('master-aktivitas')->group(function () {
        //         Route::get('/', 'master_aktivitas\MasterAktivitasController@index')->name('kabupaten.master_aktivitas.master_aktivitas.index');
        //         Route::get('/datatable', 'master_aktivitas\MasterAktivitasController@datatable')->name('kabupaten.master_aktivitas.master_aktivitas.datatable');
        //         Route::post('/store', 'master_aktivitas\MasterAktivitasController@store')->name('kabupaten.master_aktivitas.master_aktivitas.store');
        //         Route::post('/update/{params}', 'master_aktivitas\MasterAktivitasController@update')->name('kabupaten.master_aktivitas.master_aktivitas.update');
        //         Route::get('/show/{params}', 'master_aktivitas\MasterAktivitasController@show')->name('kabupaten.master_aktivitas.master_aktivitas.show');
        //         Route::delete('/delete/{params}', 'master_aktivitas\MasterAktivitasController@delete')->name('kabupaten.master_aktivitas.master_aktivitas.delete');
        //     }); 
        // }); 

        Route::prefix('hari-libur')->group(function () {
            Route::get('/', 'HariLiburController@index')->name('kabupaten.harilibur.index');
            Route::get('/datatable', 'HariLiburController@datatable')->name('kabupaten.harilibur.datatable');
            Route::post('/store', 'HariLiburController@store')->name('kabupaten.harilibur.store');
            Route::post('/update/{params}', 'HariLiburController@update')->name('kabupaten.harilibur.update');
            Route::get('/show/{params}', 'HariLiburController@show')->name('kabupaten.harilibur.show');
            Route::delete('/delete/{params}', 'HariLiburController@delete')->name('kabupaten.harilibur.delete');
        });

        Route::prefix('laporan')->group(function () {
            Route::prefix('sasaran-kinerja')->group(function () {
                Route::get('/', 'LaporanSasaranKinerjaController@index_kabupaten')->name('kabupaten.laporan.index');
                Route::get('/export-pegawai', 'LaporanSasaranKinerjaController@export_pegawai')->name('kabupaten.laporan.export_pegawai');
                Route::get('/export-opd', 'LaporanSasaranKinerjaController@export_opd')->name('kabupaten.laporan.export_opd');
            });
            Route::prefix('kehadiran')->group(function () {
                Route::get('/', 'LaporanKehadiranController@index_kabupaten')->name('kabupaten.laporan.kehadiran.index');
                Route::get('/export-pegawai', 'LaporanKehadiranController@export_pegawai')->name('kabupaten.laporan.kehadiran.export');
                Route::get('/export-pegawai-bulan', 'LaporanKehadiranController@export_pegawai_bulan')->name('kabupaten.laporan.kehadiran.export2');
                Route::get('/export-opd', 'LaporanKehadiranController@export_opd')->name('kabupaten.laporan.kehadiran_opd.export');
                Route::get('/export-opd-bulan', 'LaporanKehadiranController@export_opd_bulan')->name('kabupaten.laporan.kehadiran_opd_bulan.export');
            });
            Route::prefix('kinerja')->group(function () {
                Route::get('/', 'LaporanKinerjaController@index_kabupaten')->name('kabupaten.laporan.kinerja.index');
                Route::get('/export-pegawai', 'LaporanKinerjaController@export_pegawai')->name('kabupaten.laporan.kinerja.export');
                Route::get('/export-opd', 'LaporanKinerjaController@export_opd')->name('kabupaten.laporan.kinerja_opd.export');
            });
            Route::prefix('tpp')->group(function () {
                Route::get('/', 'LaporanTppController@index_kabupaten')->name('kabupaten.laporan.tpp.index');
                Route::get('/export', 'LaporanTppController@export')->name('kabupaten.laporan.tpp.export');
            });
            Route::prefix('profil')->group(function () {
                Route::get('/', 'ProfilController@index_laporan')->name('kabupaten.laporan.profil.index');
                Route::get('/cetak-laporan-profil-pegawai', 'ProfilController@laporan_pegawai')->name('kabupaten.laporan.profil.export');
            });

            Route::prefix('pegawai')->group(function () {
                Route::get('/', 'LaporanListPegawaiController@export')->name('kabupaten.laporan.pegawai.export');
            });
        });

        Route::get('/profil-kepala-daerah', 'ProfilKepalaDaerahController@index')->name('kabupaten.profil_kepala_daerah');


        Route::prefix('pengumuman')->group(function () {
            Route::get('/', 'PengumumanController@index')->name('kabupaten.pengumuman.index');
            Route::get('/datatable', 'PengumumanController@datatable')->name('kabupaten.pengumuman.datatable');
            Route::post('/store', 'PengumumanController@store')->name('kabupaten.pengumuman.store');
            Route::post('/update/{params}', 'PengumumanController@update')->name('kabupaten.pengumuman.update');
            Route::get('/show/{params}', 'PengumumanController@show')->name('kabupaten.pengumuman.show');
            Route::delete('/delete/{params}', 'PengumumanController@delete')->name('kabupaten.pengumuman.delete');
        });

        Route::prefix('kehadiran')->group(function () {
            Route::get('/', 'KehadiranController@index')->name('kabupaten.kehadiran.index');
            Route::get('/datatable', 'KehadiranController@datatable')->name('kabupaten.kehadiran.datatable');
            Route::post('/store', 'KehadiranController@store')->name('kabupaten.kehadiran.store');
            Route::post('/validation', 'KehadiranController@validation')->name('kabupaten.kehadiran.validation');
            Route::post('/update/{params}', 'KehadiranController@update')->name('kabupaten.kehadiran.update');
            Route::get('/show/{params}', 'KehadiranController@show')->name('kabupaten.kehadiran.show');
            Route::delete('/delete/{params}', 'KehadiranController@delete')->name('kabupaten.kehadiran.delete');
        });


        Route::prefix('jabatan')->group(function () {
            Route::prefix('list-jabatan')->group(function () {
                Route::get('/', 'jabatan\ListJabatanControlller@index')->name('kabupaten.Jabatan.Jabatan.index');
                Route::get('/datatable', 'jabatan\ListJabatanControlller@datatable')->name('kabupaten.Jabatan.Jabatan.datatable');
                Route::get('/cetak', 'jabatan\ListJabatanControlller@cetak')->name('kabupaten.Jabatan.Jabatan.cetak');
                Route::get('/cetak-kelas-jabatan', 'jabatan\ListJabatanControlller@cetakKelasJabatan')->name('kabupaten.Jabatan.Jabatan.cetakKelasJabatan');
                Route::post('/store', 'jabatan\ListJabatanControlller@store')->name('kabupaten.Jabatan.Jabatan.store');
                Route::post('/update/{params}', 'jabatan\ListJabatanControlller@update')->name('kabupaten.Jabatan.Jabatan.update');
                Route::get('/show/{params}', 'jabatan\ListJabatanControlller@show')->name('kabupaten.Jabatan.Jabatan.show');
                Route::get('/detail/{params}', 'jabatan\ListJabatanControlller@detail')->name('kabupaten.Jabatan.Jabatan.detail');
                Route::delete('/delete/{params}', 'jabatan\ListJabatanControlller@delete')->name('kabupaten.Jabatan.Jabatan.delete');
            });
            Route::prefix('jabatan-kosong')->group(function () {
                Route::get('/', 'jabatan\JabatanKosongControlller@index')->name('kabupaten.Jabatan.jabatan_kosong.index');
                Route::get('/datatable', 'jabatan\JabatanKosongControlller@datatable')->name('kabupaten.Jabatan.jabatan_kosong.datatable');
                Route::get('/cetak', 'jabatan\JabatanKosongControlller@cetak')->name('kabupaten.Jabatan.jabatan_kosong.cetak');
                Route::post('/store', 'jabatan\JabatanKosongControlller@store')->name('kabupaten.Jabatan.jabatan_kosong.store');
                Route::post('/update/{params}', 'jabatan\JabatanKosongControlller@update')->name('kabupaten.Jabatan.jabatan_kosong.update');
                Route::get('/show/{params}', 'jabatan\JabatanKosongControlller@show')->name('kabupaten.Jabatan.jabatan_kosong.show');
                Route::delete('/delete/{params}', 'jabatan\JabatanKosongControlller@delete')->name('kabupaten.Jabatan.jabatan_kosong.delete');
                Route::get('/option/{params}', 'jabatan\JabatanKosongControlller@option')->name('kabupaten.Jabatan.jabatan_kosong.option');
            });
            Route::prefix('jabatan-plt')->group(function () {
                Route::get('/', 'jabatan\JabatanPltController@index')->name('kabupaten.Jabatan.jabatan_plt.index');
                Route::get('/datatable', 'jabatan\JabatanPltController@datatable')->name('kabupaten.Jabatan.jabatan_plt.datatable');
                Route::get('/cetak', 'jabatan\JabatanPltController@cetak')->name('kabupaten.Jabatan.jabatan_plt.cetak');
                Route::post('/store', 'jabatan\JabatanPltController@store')->name('kabupaten.Jabatan.jabatan_plt.store');
                Route::post('/update/{params}', 'jabatan\JabatanPltController@update')->name('kabupaten.Jabatan.jabatan_plt.update');
                Route::get('/show/{params}', 'jabatan\JabatanPltController@show')->name('kabupaten.Jabatan.jabatan_plt.show');
                Route::get('/detail/{params}', 'jabatan\JabatanPltController@detail')->name('kabupaten.Jabatan.jabatan_plt.detail');
                Route::delete('/delete/{params}', 'jabatan\JabatanPltController@delete')->name('kabupaten.Jabatan.jabatan_plt.delete');
            });
            Route::prefix('mutasi')->group(function () {
                Route::get('/', 'jabatan\MutasiController@index')->name('kabupaten.Jabatan.mutasi.index');
                Route::get('/datatable', 'jabatan\MutasiController@datatable')->name('kabupaten.Jabatan.mutasi.datatable');
                Route::post('/store', 'jabatan\MutasiController@store')->name('kabupaten.Jabatan.mutasi.store');
                Route::post('/update/{params}', 'jabatan\MutasiController@update')->name('kabupaten.Jabatan.mutasi.update');
                Route::get('/show/{params}', 'jabatan\MutasiController@show')->name('kabupaten.Jabatan.mutasi.show');
                Route::get('/detail/{params}', 'jabatan\MutasiController@detail')->name('kabupaten.Jabatan.mutasi.detail');
                Route::delete('/delete/{params}', 'jabatan\MutasiController@delete')->name('kabupaten.Jabatan.mutasi.delete');
            });
        });

        Route::prefix('layanan')->group(function () {
            Route::prefix('master-layanan')->group(function () {
                Route::get('/', 'layanan\MasterLayananController@index')->name('kabupaten.layanan.masterlayanan.index');
                Route::get('/datatable', 'layanan\MasterLayananController@datatable')->name('kabupaten.layanan.masterlayanan.datatable');
                Route::post('/store', 'layanan\MasterLayananController@store')->name('kabupaten.layanan.masterlayanan.store');
                Route::post('/update/{params}', 'layanan\MasterLayananController@update')->name('kabupaten.layanan.masterlayanan.update');
                Route::get('/show/{params}', 'layanan\MasterLayananController@show')->name('kabupaten.layanan.masterlayanan.show');
                Route::delete('/delete/{params}', 'layanan\MasterLayananController@delete')->name('kabupaten.layanan.masterlayanan.delete');
            });
            Route::prefix('layanan-cuti')->group(function () {
                Route::get('/', 'layanan\LayananCutiController@index')->name('kabupaten.layanan.layanancuti.index');
                Route::get('/datatable', 'layanan\LayananCutiController@datatable')->name('kabupaten.layanan.layanancuti.datatable');
                Route::post('/update/{params}', 'layanan\LayananCutiController@update')->name('kabupaten.layanan.layanancuti.update');
                Route::get('/show/{params}', 'layanan\LayananCutiController@show')->name('kabupaten.layanan.layanancuti.show');
                Route::get('/detail/{params}', 'layanan\LayananCutiController@detail')->name('kabupaten.layanan.layanancuti.detail');
                Route::delete('/delete/{params}', 'layanan\LayananCutiController@delete')->name('kabupaten.layanan.layanancuti.delete');
            });

            Route::prefix('layanan-general')->group(function () {
                Route::get('/', 'layanan\LayananGeneralController@index')->name('kabupaten.layanan.layanangeneral.index');
                Route::get('/datatable', 'layanan\LayananGeneralController@datatable')->name('kabupaten.layanan.layanangeneral.datatable');
                Route::post('/store', 'layanan\LayananGeneralController@store')->name('kabupaten.layanan.layanangeneral.store');
                Route::post('/update/{params}', 'layanan\LayananGeneralController@update')->name('kabupaten.layanan.layanangeneral.update');
                Route::get('/show/{params}', 'layanan\LayananGeneralController@show')->name('kabupaten.layanan.layanangeneral.show');
                Route::delete('/delete/{params}', 'layanan\LayananGeneralController@delete')->name('kabupaten.layanan.layanangeneral.delete');
            });
        });
    });

    Route::middleware(['admin_keuangan'])->group(function () {
        Route::get('/dashboard-keuangan', 'DashboardController@keuangan')->name('dashboard.keuangan');
    });


    Route::middleware(['users'])->group(function () {

        Route::prefix('dashboard-pegawai')->group(function () {
            Route::get('/', 'DashboardController@pegawai')->name('dashboard.pegawai');
        });


        Route::prefix('dashboard-pegawai-data')->group(function () {
            Route::get('/', 'DashboardController@data_pegawai')->name('dashboard.pegawai.data');
        });

        Route::prefix('sasaran-kinerja')->group(function () {
            Route::get('/', 'SasaranKinerjaController@index')->name('pegawai.skp.index');
            Route::get('/datatable', 'SasaranKinerjaController@datatable')->name('pegawai.skp.datatable');
            Route::post('/store', 'SasaranKinerjaController@store')->name('pegawai.skp.store');
            Route::post('/update/{params}', 'SasaranKinerjaController@update')->name('pegawai.skp.update');
            Route::get('/show/{params}', 'SasaranKinerjaController@show')->name('pegawai.skp.show');
            Route::delete('/delete/{params}', 'SasaranKinerjaController@delete')->name('pegawai.skp.delete');
        });

        Route::prefix('aktivitas')->group(function () {
            Route::get('/', 'AktivitasController@index')->name('pegawai.aktivitas.index');
            Route::get('/getAktivitasForCalender', 'AktivitasController@getAktivitasForCalender')->name('pegawai.aktivitas.getAktivitasForCalender');
            Route::get('/getAktivitasForCalenderbyPegawai', 'AktivitasController@getAktivitasForCalenderbyPegawai')->name('pegawai.aktivitas.getAktivitasForCalenderbyPegawai');
            Route::post('/store', 'AktivitasController@store')->name('pegawai.aktivitas.store');
            Route::post('/update/{params}', 'AktivitasController@update')->name('pegawai.aktivitas.update');
            Route::get('/show/{params}', 'AktivitasController@show')->name('pegawai.aktivitas.show');
            Route::delete('/delete/{params}', 'AktivitasController@delete')->name('pegawai.aktivitas.delete');
        });

        Route::prefix('review')->group(function () {
            Route::prefix('sasaran-kinerja')->group(function () {
                Route::get('/', 'review\SasaranKinerjaReviewController@index')->name('pegawai.review.sasaran_kinerja.index');
                Route::get('/datatable', 'review\SasaranKinerjaReviewController@datatable')->name('pegawai.review.sasaran_kinerja.datatable');
                Route::get('/review', 'review\SasaranKinerjaReviewController@review')->name('pegawai.review.sasaran_kinerja.review');
                Route::get('/data-review-skp', 'review\SasaranKinerjaReviewController@data_review_skp')->name('pegawai.review.sasaran_kinerja.data_review_skp');
                Route::post('/post-review-skp', 'review\SasaranKinerjaReviewController@postReviewSkp')->name('pegawai.review.sasaran_kinerja.postReviewSkp');
            });
            Route::prefix('aktivitas')->group(function () {
                Route::get('/', 'review\AktivitasReviewController@index')->name('pegawai.review.aktivitas.index');
                Route::get('/datatable', 'review\AktivitasReviewController@datatable')->name('pegawai.review.aktivitas.datatable');
                Route::get('/review', 'review\AktivitasReviewController@review')->name('pegawai.review.aktivitas.review');
                Route::get('/data-review-aktivitas', 'review\AktivitasReviewController@data_review_aktivitas')->name('pegawai.review.aktivitas.data_review_aktivitas');
                Route::post('/post-review-aktivitas', 'review\AktivitasReviewController@postReviewAktivitas')->name('pegawai.review.aktivitas.postReviewAktivitas');
            });
            Route::prefix('realisasi-skp')->group(function () {
                Route::get('/', 'review\RealisasiReviewController@index')->name('pegawai.review.realisasi.index');
                Route::get('/datatable', 'review\RealisasiReviewController@datatable')->name('pegawai.review.realisasi.datatable');
                Route::get('/review', 'review\RealisasiReviewController@review')->name('pegawai.review.realisasi.review');
                Route::post('/post-review-skp', 'review\RealisasiReviewController@postReviewSkp')->name('pegawai.review.realisasi.postReviewSkp');
            });
        });

        Route::prefix('realisasi')->group(function () {
            Route::get('/', 'RealisasiSkpController@index')->name('pegawai.realisasi.index');
            Route::get('/datatable', 'RealisasiSkpController@datatable')->name('pegawai.realisasi.datatable');
            Route::post('/realisasi-skp', 'RealisasiSkpController@update')->name('pegawai.realisasi.realisasi_skp');
            Route::get('/show/{params}', 'RealisasiSkpController@show')->name('pegawai.realisasi.show');
        });

        Route::prefix('akun')->group(function () {
            Route::get('/', 'AkunController@index')->name('pegawai.akun.index');
            Route::post('/change-password', 'AkunController@changePassword')->name('pegawai.akun.changepassword');
        });

        Route::prefix('layanan-pegawai')->group(function () {
            Route::prefix('layanan-cuti')->group(function () {
                Route::get('/', 'CutiPegawaiController@index')->name('pegawai.layanan_cuti_pegawai.index');
                Route::get('/datatable', 'CutiPegawaiController@datatable')->name('pegawai.layanan_cuti_pegawai.datatable');
                Route::post('/store', 'CutiPegawaiController@store')->name('pegawai.layanan_cuti_pegawai.store');
                Route::post('/update/{params}', 'CutiPegawaiController@update')->name('pegawai.layanan_cuti_pegawai.update');
                Route::get('/detail/{params}', 'CutiPegawaiController@detail')->name('pegawai.layanan_cuti_pegawai.detail');
                Route::get('/show/{params}', 'CutiPegawaiController@show')->name('pegawai.layanan_cuti_pegawai.show');
                Route::delete('/delete/{params}', 'CutiPegawaiController@delete')->name('pegawai.layanan_cuti_pegawai.delete');
            });
        });

        Route::prefix('profil')->group(function () {
            Route::get('/', 'ProfilController@index')->name('pegawai.profil.index');

            Route::get('/cetak-laporan-profil-pegawai', 'ProfilController@laporan_pegawai')->name('pegawai.profil.cetak');
            Route::prefix('data-pribadi')->group(function () {
                Route::post('/update/{params}', 'ProfilController@updateDataPribadi')->name('kabupaten.profil.data_pribadi.update');
            });
            Route::prefix('riwayat-pendidikan-formal')->group(function () {
                Route::get('/datatable', 'ProfilController@datatable_pendidikan_formal')->name('kabupaten.profil.riwayat_pendidikan_formal.datatable');
                Route::post('/store', 'ProfilController@store_pendidikan_formal')->name('kabupaten.profil.riwayat_pendidikan_formal.store');
                Route::post('/update/{params}', 'ProfilController@update_pendidikan_formal')->name('kabupaten.profil.riwayat_pendidikan_formal.update');
                Route::get('/show/{params}', 'ProfilController@show_pendidikan_formal')->name('kabupaten.profil.riwayat_pendidikan_formal.show');
                Route::delete('/delete/{params}', 'ProfilController@delete_pendidikan_formal')->name('kabupaten.profil.riwayat_pendidikan_formal.delete');
            });
            Route::prefix('riwayat-pendidikan-non-formal')->group(function () {
                Route::get('/datatable', 'ProfilController@datatable_pendidikan_non_formal')->name('kabupaten.profil.riwayat_pendidikan_non_formal.datatable');
                Route::post('/store', 'ProfilController@store_pendidikan_non_formal')->name('kabupaten.profil.riwayat_pendidikan_non_formal.store');
                Route::post('/update/{params}', 'ProfilController@update_pendidikan_non_formal')->name('kabupaten.profil.riwayat_pendidikan_non_formal.update');
                Route::get('/show/{params}', 'ProfilController@show_pendidikan_non_formal')->name('kabupaten.profil.riwayat_pendidikan_non_formal.show');
                Route::delete('/delete/{params}', 'ProfilController@delete_pendidikan_non_formal')->name('kabupaten.profil.riwayat_pendidikan_non_formal.delete');
            });
            Route::prefix('riwayat-kepangkatan')->group(function () {
                Route::get('/datatable', 'ProfilController@datatable_riwayat_kepangkatan')->name('kabupaten.profil.riwayat_kepangkatan.datatable');
                Route::post('/store', 'ProfilController@store_riwayat_kepangkatan')->name('kabupaten.profil.riwayat_kepangkatan.store');
                Route::post('/update/{params}', 'ProfilController@update_riwayat_kepangkatan')->name('kabupaten.profil.riwayat_kepangkatan.update');
                Route::get('/show/{params}', 'ProfilController@show_riwayat_kepangkatan')->name('kabupaten.profil.riwayat_kepangkatan.show');
                Route::delete('/delete/{params}', 'ProfilController@delete_riwayat_kepangkatan')->name('kabupaten.profil.riwayat_kepangkatan.delete');
            });
            Route::prefix('riwayat-jabatan')->group(function () {
                Route::post('/store', 'ProfilController@store_riwayat_jabatan')->name('kabupaten.profil.riwayat_jabatan.store');
                Route::post('/update/{params}', 'ProfilController@update_riwayat_jabatan')->name('kabupaten.profil.riwayat_jabatan.update');
                Route::get('/show/{params}', 'ProfilController@show_riwayat_jabatan')->name('kabupaten.profil.riwayat_jabatan.show');
                Route::delete('/delete/{params}', 'ProfilController@delete_riwayat_jabatan')->name('kabupaten.profil.riwayat_jabatan.delete');
            });
            Route::prefix('catatan-hukuman-dinas')->group(function () {
                Route::post('/store', 'ProfilController@store_catatan_hukuman_dinas')->name('kabupaten.profil.catatan_hukuman_dinas.store');
                Route::post('/update/{params}', 'ProfilController@update_catatan_hukuman_dinas')->name('kabupaten.profil.catatan_hukuman_dinas.update');
                Route::get('/show/{params}', 'ProfilController@show_catatan_hukuman_dinas')->name('kabupaten.profil.catatan_hukuman_dinas.show');
                Route::delete('/delete/{params}', 'ProfilController@delete_catatan_hukuman_dinas')->name('kabupaten.profil.catatan_hukuman_dinas.delete');
            });
            Route::prefix('diklat-struktural')->group(function () {
                Route::post('/store', 'ProfilController@store_diklat_struktural')->name('kabupaten.profil.diklat_struktural.store');
                Route::post('/update/{params}', 'ProfilController@update_diklat_struktural')->name('kabupaten.profil.diklat_struktural.update');
                Route::get('/show/{params}', 'ProfilController@show_diklat_struktural')->name('kabupaten.profil.diklat_struktural.show');
                Route::delete('/delete/{params}', 'ProfilController@delete_diklat_struktural')->name('kabupaten.profil.diklat_struktural.delete');
            });
            Route::prefix('diklat-fungsional')->group(function () {
                Route::post('/store', 'ProfilController@store_diklat_fungsional')->name('kabupaten.profil.diklat_fungsional.store');
                Route::post('/update/{params}', 'ProfilController@update_diklat_fungsional')->name('kabupaten.profil.diklat_fungsional.update');
                Route::get('/show/{params}', 'ProfilController@show_diklat_fungsional')->name('kabupaten.profil.diklat_fungsional.show');
                Route::delete('/delete/{params}', 'ProfilController@delete_diklat_fungsional')->name('kabupaten.profil.diklat_fungsional.delete');
            });
            Route::prefix('diklat-teknis')->group(function () {
                Route::post('/store', 'ProfilController@store_diklat_teknis')->name('kabupaten.profil.diklat_teknis.store');
                Route::post('/update/{params}', 'ProfilController@update_diklat_teknis')->name('kabupaten.profil.diklat_teknis.update');
                Route::get('/show/{params}', 'ProfilController@show_diklat_teknis')->name('kabupaten.profil.diklat_teknis.show');
                Route::delete('/delete/{params}', 'ProfilController@delete_diklat_teknis')->name('kabupaten.profil.diklat_teknis.delete');
            });
            Route::prefix('riwayat-penghargaan')->group(function () {
                Route::post('/store', 'ProfilController@store_riwayat_penghargaan')->name('kabupaten.profil.riwayat_penghargaan.store');
                Route::post('/update/{params}', 'ProfilController@update_riwayat_penghargaan')->name('kabupaten.profil.riwayat_penghargaan.update');
                Route::get('/show/{params}', 'ProfilController@show_riwayat_penghargaan')->name('kabupaten.profil.riwayat_penghargaan.show');
                Route::delete('/delete/{params}', 'ProfilController@delete_riwayat_penghargaan')->name('kabupaten.profil.riwayat_penghargaan.delete');
            });
            Route::prefix('riwayat-istri')->group(function () {
                Route::post('/store', 'ProfilController@store_riwayat_istri')->name('kabupaten.profil.riwayat_istri.store');
                Route::post('/update/{params}', 'ProfilController@update_riwayat_istri')->name('kabupaten.profil.riwayat_istri.update');
                Route::get('/show/{params}', 'ProfilController@show_riwayat_istri')->name('kabupaten.profil.riwayat_istri.show');
                Route::delete('/delete/{params}', 'ProfilController@delete_riwayat_istri')->name('kabupaten.profil.riwayat_istri.delete');
            });
            Route::prefix('riwayat-anak')->group(function () {
                Route::post('/store', 'ProfilController@store_riwayat_anak')->name('kabupaten.profil.riwayat_anak.store');
                Route::post('/update/{params}', 'ProfilController@update_riwayat_anak')->name('kabupaten.profil.riwayat_anak.update');
                Route::get('/show/{params}', 'ProfilController@show_riwayat_anak')->name('kabupaten.profil.riwayat_anak.show');
                Route::delete('/delete/{params}', 'ProfilController@delete_riwayat_anak')->name('kabupaten.profil.riwayat_anak.delete');
            });
            Route::prefix('riwayat-orang-tua')->group(function () {
                Route::post('/store', 'ProfilController@store_riwayat_orang_tua')->name('kabupaten.profil.riwayat_orang_tua.store');
                Route::post('/update/{params}', 'ProfilController@update_riwayat_orang_tua')->name('kabupaten.profil.riwayat_orang_tua.update');
                Route::get('/show/{params}', 'ProfilController@show_riwayat_orang_tua')->name('kabupaten.profil.riwayat_orang_tua.show');
                Route::delete('/delete/{params}', 'ProfilController@delete_riwayat_orang_tua')->name('kabupaten.profil.riwayat_orang_tua.delete');
            });
            Route::prefix('riwayat-saudara')->group(function () {
                Route::post('/store', 'ProfilController@store_riwayat_saudara')->name('kabupaten.profil.riwayat_saudara.store');
                Route::post('/update/{params}', 'ProfilController@update_riwayat_saudara')->name('kabupaten.profil.riwayat_saudara.update');
                Route::get('/show/{params}', 'ProfilController@show_riwayat_saudara')->name('kabupaten.profil.riwayat_saudara.show');
                Route::delete('/delete/{params}', 'ProfilController@delete_riwayat_saudara')->name('kabupaten.profil.riwayat_saudara.delete');
            });
            Route::prefix('riwayat-tambahan')->group(function () {
                Route::post('/store', 'ProfilController@store_riwayat_tambahan')->name('kabupaten.profil.riwayat_tambahan.store');
                Route::post('/update/{params}', 'ProfilController@update_riwayat_tambahan')->name('kabupaten.profil.riwayat_tambahan.update');
                Route::get('/show/{params}', 'ProfilController@show_riwayat_tambahan')->name('kabupaten.profil.riwayat_tambahan.show');
                Route::delete('/delete/{params}', 'ProfilController@delete_riwayat_tambahan')->name('kabupaten.profil.riwayat_tambahan.delete');
            });
            Route::prefix('file-pegawai')->group(function () {
                Route::post('/store', 'ProfilController@store_file_pegawai')->name('kabupaten.profil.file_pegawai.store');
                Route::post('/update/{params}', 'ProfilController@update_file_pegawai')->name('kabupaten.profil.file_pegawai.update');
                Route::get('/show/{params}', 'ProfilController@show_file_pegawai')->name('kabupaten.profil.file_pegawai.show');
                Route::delete('/delete/{params}', 'ProfilController@delete_file_pegawai')->name('kabupaten.profil.file_pegawai.delete');
            });
        });

        Route::prefix('laporan-pegawai')->group(function () {
            Route::prefix('sasaran-kinerja')->group(function () {
                Route::get('/', 'LaporanSasaranKinerjaController@index')->name('pegawai.laporan.index');
                Route::get('/export-pegawai', 'LaporanSasaranKinerjaController@export_pegawai')->name('pegawai.laporan.export');
            });
            Route::prefix('kehadiran')->group(function () {
                Route::get('/', 'LaporanKehadiranController@index')->name('pegawai.laporan.kehadiran.index');
                Route::get('/export-pegawai', 'LaporanKehadiranController@export_pegawai')->name('pegawai.laporan.kehadiran.export');
                Route::get('/export-pegawai-bulan', 'LaporanKehadiranController@export_pegawai_bulan')->name('pegawai.laporan.kehadiran.export2');
            });
            Route::prefix('kinerja')->group(function () {
                Route::get('/', 'LaporanKinerjaController@index')->name('pegawai.laporan.kinerja.index');
                Route::get('/export-pegawai', 'LaporanKinerjaController@export_pegawai')->name('pegawai.laporan.kinerja.export');
            });
            Route::prefix('tpp')->group(function () {
                Route::get('/', 'LaporanTppController@index')->name('pegawai.laporan.tpp.index');
                Route::get('/export-pegawai', 'LaporanTppController@export_pegawai')->name('pegawai.laporan.tpp.export');
            });
        });

        Route::prefix('master-aktivitas-pegawai')->group(function () {
            Route::prefix('master-aktivitas')->group(function () {
                Route::get('/show/{params}', 'master_aktivitas\MasterAktivitasController@show')->name('kabupaten.master_aktivitas_pegawai.master_aktivitas.show');
            });
        });
    });

    Route::middleware(['admin_opd', 'users', 'admin_unit'])->group(function () {
        Route::get('/dashboard-opd', 'DashboardController@admin_opd')->name('dashboard.admin_opd');

        Route::prefix('pegawai-opd')->group(function () {
            Route::prefix('list-pegawai-opd')->group(function () {
                Route::get('/', 'pegawai\listPegawaiController@index')->name('opd.pegawai.listpegawai.index');
                Route::get('/datatable', 'pegawai\listPegawaiController@datatable')->name('opd.pegawai.listpegawai.datatable');
                Route::post('/store', 'pegawai\listPegawaiController@store')->name('opd.pegawai.listpegawai.store');
                Route::get('/detail/{params}', 'pegawai\listPegawaiController@detail')->name('kabupaten.pegawai.listpegawai.detail');
                Route::post('/update/{params}', 'pegawai\listPegawaiController@update')->name('opd.pegawai.listpegawai.update');
                Route::get('/show/{params}', 'pegawai\listPegawaiController@show')->name('opd.pegawai.listpegawai.show');
                Route::delete('/delete/{params}', 'pegawai\listPegawaiController@delete')->name('opd.pegawai.listpegawai.delete');
                Route::get('/option', 'pegawai\listPegawaiController@option')->name('opd.pegawai.listpegawai.option');
                Route::post('/reset-wajah', 'pegawai\listPegawaiController@reset_wajah')->name('opd.pegawai.listpegawai.reset_wajah');
                Route::get('/option-by-unit-kerja', 'pegawai\listPegawaiController@option_by_unitkerja')->name('opd.pegawai.listpegawai.optionbyunitkerja');
            });
            Route::prefix('verifikasi-opd')->group(function () {
                Route::get('/', 'pegawai\verifikasiPegawaiController@index')->name('opd.pegawai.verifikasi.index');
                Route::post('/verifikasi', 'pegawai\verifikasiPegawaiController@verifikasi')->name('opd.pegawai.verifikasi.verifikasi');
                Route::get('/datatable', 'pegawai\verifikasiPegawaiController@datatable')->name('opd.pegawai.verifikasi.datatable');
                Route::get('/detail/{params}', 'pegawai\verifikasiPegawaiController@detail')->name('opd.pegawai.verifikasi.detail');
            });
            Route::prefix('pegawai-pensiun-opd')->group(function () {
                Route::get('/', 'pegawai\pegawaiPensiunController@index')->name('opd.pegawai.pegawaipensiun.index');
                Route::get('/datatable', 'pegawai\pegawaiPensiunController@datatable')->name('opd.pegawai.pegawaipensiun.datatable');
                Route::post('/store', 'pegawai\pegawaiPensiunController@store')->name('opd.pegawai.pegawaipensiun.store');
                Route::post('/update/{params}', 'pegawai\pegawaiPensiunController@update')->name('opd.pegawai.pegawaipensiun.update');
                Route::get('/show/{params}', 'pegawai\pegawaiPensiunController@show')->name('opd.pegawai.pegawaipensiun.show');
                Route::get('/detail/{params}', 'pegawai\pegawaiPensiunController@detail')->name('opd.pegawai.pegawaipensiun.detail');
                Route::delete('/delete/{params}', 'pegawai\pegawaiPensiunController@delete')->name('opd.pegawai.pegawaipensiun.delete');
            }); 

            Route::prefix('pegawai-akan-pensiun')->group(function () {
                Route::get('/', 'pegawai\pegawaiPensiunController@index_akan_pensiun')->name('opd.pegawai.pegawaiakanpensiun.index');
                Route::get('/datatable', 'pegawai\pegawaiPensiunController@datatable_akan_pensiun')->name('opd.pegawai.pegawaiakanpensiun.datatable');
                Route::get('/option/{params}', 'pegawai\pegawaiPensiunController@option')->name('opd.pegawai.pegawaiakanpensiun.option');
            }); 

            Route::prefix('pegawai-non-job')->group(function () {
                Route::get('/', 'pegawai\PegawaiNonJobController@index')->name('opd.pegawai.pegawainonjob.index');
                Route::get('/datatable', 'pegawai\PegawaiNonJobController@datatable')->name('opd.pegawai.pegawainonjob.datatable');
            });
        });

        Route::prefix('aktivitas-opd')->group(function () {
            Route::get('/', 'AktivitasOpdController@index')->name('opd.aktivitas.index');
            Route::get('/datatable', 'AktivitasOpdController@datatable')->name('opd.aktivitas.datatable');
            Route::get('/detail-pegawai', 'AktivitasOpdController@detail')->name('opd.aktivitas.detail');
        });

        Route::prefix('master-jabatan-opd')->group(function () {
            Route::prefix('master-jabatan')->group(function () {
                Route::get('/showId/{params}', 'master_jabatan\MasterJabatanController@showByid')->name('opd.master_jabatan.master_jabatan.showByid');
                Route::get('/option', 'master_jabatan\MasterJabatanController@optionJabatan')->name('opd.master_jabatan.master_jabatan.option');
                Route::get('/option-atasan-langsung', 'master_jabatan\MasterJabatanController@optionAtasanLangsung')->name('opd.master_jabatan.master_jabatan.optionAtasanLangsung');
                Route::get('/cetak-jabatan', 'master_jabatan\MasterJabatanController@cetak_jabatan')->name('opd.master_jabatan.master_jabatan.cetak_jabatan');
            });
        });

        Route::prefix('jabatan-opd')->group(function () {
            Route::prefix('list-jabatan')->group(function () {
                Route::get('/', 'jabatan\ListJabatanControlller@index')->name('kabupaten.Jabatan.jabatanopd.index');
                Route::get('/datatable', 'jabatan\ListJabatanControlller@datatable')->name('kabupaten.Jabatan.jabatanopd.datatable');
                Route::get('/cetak', 'jabatan\ListJabatanControlller@cetak')->name('kabupaten.Jabatan.jabatanopd.cetak');
                Route::post('/store', 'jabatan\ListJabatanControlller@store')->name('kabupaten.Jabatan.jabatanopd.store');
                Route::post('/update/{params}', 'jabatan\ListJabatanControlller@update')->name('kabupaten.Jabatan.jabatanopd.update');
                Route::get('/show/{params}', 'jabatan\ListJabatanControlller@show')->name('kabupaten.Jabatan.jabatanopd.show');
                Route::get('/detail/{params}', 'jabatan\ListJabatanControlller@detail')->name('kabupaten.Jabatan.jabatanopd.detail');
                Route::delete('/delete/{params}', 'jabatan\ListJabatanControlller@delete')->name('kabupaten.Jabatan.jabatanopd.delete');
            });
            Route::prefix('jabatan-kosong')->group(function () {
                Route::get('/', 'jabatan\JabatanKosongControlller@index')->name('opd.Jabatan.jabatan_kosong.index');
                Route::get('/datatable', 'jabatan\JabatanKosongControlller@datatable')->name('opd.Jabatan.jabatan_kosong.datatable');
                Route::get('/cetak', 'jabatan\JabatanKosongControlller@cetak')->name('opd.Jabatan.jabatan_kosong.cetak');
                Route::post('/store', 'jabatan\JabatanKosongControlller@store')->name('opd.Jabatan.jabatan_kosong.store');
                Route::post('/update/{params}', 'jabatan\JabatanKosongControlller@update')->name('opd.Jabatan.jabatan_kosong.update');
                Route::get('/show/{params}', 'jabatan\JabatanKosongControlller@show')->name('opd.Jabatan.jabatan_kosong.show');
                Route::delete('/delete/{params}', 'jabatan\JabatanKosongControlller@delete')->name('opd.Jabatan.jabatan_kosong.delete');
                Route::get('/option/{params}', 'jabatan\JabatanKosongControlller@option')->name('opd.Jabatan.jabatan_kosong.option');
            });
            Route::prefix('jabatan-plt')->group(function () {
                Route::get('/', 'jabatan\JabatanPltController@index')->name('opd.Jabatan.jabatan_plt.index');
                Route::get('/datatable', 'jabatan\JabatanPltController@datatable')->name('opd.Jabatan.jabatan_plt.datatable');
                Route::get('/cetak', 'jabatan\JabatanPltController@cetak')->name('opd.Jabatan.jabatan_plt.cetak');
                Route::post('/store', 'jabatan\JabatanPltController@store')->name('opd.Jabatan.jabatan_plt.store');
                Route::post('/update/{params}', 'jabatan\JabatanPltController@update')->name('opd.Jabatan.jabatan_plt.update');
                Route::get('/show/{params}', 'jabatan\JabatanPltController@show')->name('opd.Jabatan.jabatan_plt.show');
                Route::delete('/delete/{params}', 'jabatan\JabatanPltController@delete')->name('opd.Jabatan.jabatan_plt.delete');
            });
        });

        Route::prefix('kehadiran-opd')->group(function () {
            Route::get('/', 'KehadiranController@index')->name('opd.kehadiran.index');
            Route::get('/datatable', 'KehadiranController@datatable')->name('opd.kehadiran.datatable');
            Route::post('/store', 'KehadiranController@store')->name('opd.kehadiran.store');
            Route::post('/validation', 'KehadiranController@validation')->name('opd.kehadiran.validation');
            Route::post('/update/{params}', 'KehadiranController@update')->name('opd.kehadiran.update');
            Route::get('/show/{params}', 'KehadiranController@show')->name('opd.kehadiran.show');
            Route::delete('/delete/{params}', 'KehadiranController@delete')->name('opd.kehadiran.delete');
        });

        Route::prefix('layanan-opd')->group(function () {
            Route::prefix('layanan-cuti')->group(function () {
                Route::get('/', 'layanan\LayananCutiController@index')->name('opd.layanan.layanancuti.index');
                Route::get('/datatable', 'layanan\LayananCutiController@datatable')->name('opd.layanan.layanancuti.datatable');
                Route::post('/update/{params}', 'layanan\LayananCutiController@update')->name('opd.layanan.layanancuti.update');
                Route::get('/show/{params}', 'layanan\LayananCutiController@show')->name('opd.layanan.layanancuti.show');
                Route::get('/detail/{params}', 'layanan\LayananCutiController@detail')->name('opd.layanan.layanancuti.detail');
                Route::delete('/delete/{params}', 'layanan\LayananCutiController@delete')->name('opd.layanan.layanancuti.delete');
            });
        });

        Route::prefix('akun-opd')->group(function () {
            Route::get('/', 'AkunController@index')->name('opd.akun.index');
            Route::post('/change-password', 'AkunController@changePassword')->name('opd.akun.changepassword');
        });

        Route::prefix('laporan-opd')->group(function () {
            Route::prefix('sasaran-kinerja')->group(function () {
                Route::get('/', 'LaporanSasaranKinerjaController@index_opd')->name('opd.laporan.index');
                Route::get('/export-pegawai', 'LaporanSasaranKinerjaController@export_pegawai')->name('pegawai.laporan.export_pegawai');
                Route::get('/export-opd', 'LaporanSasaranKinerjaController@export_opd')->name('pegawai.laporan.export_opd');
            });
            Route::prefix('kehadiran')->group(function () {
                Route::get('/', 'LaporanKehadiranController@index_opd')->name('opd.laporan.kehadiran.index');
                Route::get('/export-pegawai', 'LaporanKehadiranController@export_pegawai')->name('opd.laporan.kehadiran.export');
                Route::get('/export-opd', 'LaporanKehadiranController@export_opd')->name('opd.laporan.kehadiran_opd.export');
                Route::get('/export-pegawai-bulan', 'LaporanKehadiranController@export_pegawai_bulan')->name('opd.laporan.kehadiran.export2');
                Route::get('/export-opd-bulan', 'LaporanKehadiranController@export_opd_bulan')->name('opd.laporan.kehadiran_opd_bulan.export');

            });
            Route::prefix('kinerja')->group(function () {
                Route::get('/', 'LaporanKinerjaController@index_opd')->name('opd.laporan.kinerja.index');
                Route::get('/export-pegawai', 'LaporanKinerjaController@export_pegawai')->name('opd.laporan.kinerja.export');
                Route::get('/export-opd', 'LaporanKinerjaController@export_opd')->name('opd.laporan.kinerja_opd.export');
            });
            Route::prefix('tpp')->group(function () {
                Route::get('/', 'LaporanTppController@index_opd')->name('opd.laporan.tpp.index');
                Route::get('/export', 'LaporanTppController@export')->name('opd.laporan.tpp.export');
                Route::get('/export-pegawai', 'LaporanTppController@export_pegawai')->name('setlaporan.laporan_opd.tpp.export');
            });
            Route::prefix('profil')->group(function () {
                Route::get('/', 'ProfilController@index_laporan')->name('opd.laporan.profil.index');
                //    Route::get('/export', 'LaporanTppController@export')->name('kabupaten.laporan.tpp.export');
            });

            Route::prefix('pegawai')->group(function () {
                Route::get('/', 'LaporanListPegawaiController@export')->name('opd.laporan.pegawai.export');
            });
        }); 

        Route::prefix('perangkat-daerah-opd')->group(function () {
            Route::prefix('perangkat-daerah')->group(function () {
                Route::get('/', 'perangkat_daerah\PerangkatDaerahController@index')->name('opd.perangkat_daerah.index');
                Route::get('/datatable', 'perangkat_daerah\PerangkatDaerahController@datatable')->name('opd.perangkat_daerah.datatable');
                Route::post('/store', 'perangkat_daerah\PerangkatDaerahController@store')->name('opd.perangkat_daerah.store');
                Route::post('/update/{params}', 'perangkat_daerah\PerangkatDaerahController@update')->name('opd.perangkat_daerah.update');
                Route::get('/show/{params}', 'perangkat_daerah\PerangkatDaerahController@show')->name('opd.perangkat_daerah.show');
                Route::delete('/delete/{params}', 'perangkat_daerah\PerangkatDaerahController@delete')->name('opd.perangkat_daerah.delete');
            }); 
            Route::prefix('unit-kerja')->group(function () {
                Route::get('/', 'perangkat_daerah\UnitKerjaController@index')->name('opd.unit_kerja.index');
                Route::get('/datatable', 'perangkat_daerah\UnitKerjaController@datatable')->name('opd.unit_kerja.datatable');
                Route::post('/store', 'perangkat_daerah\UnitKerjaController@store')->name('opd.unit_kerja.store');
                Route::post('/update/{params}', 'perangkat_daerah\UnitKerjaController@update')->name('opd.unit_kerja.update');
                Route::get('/show/{params}', 'perangkat_daerah\UnitKerjaController@show')->name('opd.unit_kerja.show');
                Route::delete('/delete/{params}', 'perangkat_daerah\UnitKerjaController@delete')->name('opd.unit_kerja.delete');
                Route::get('/option', 'perangkat_daerah\UnitKerjaController@option')->name('opd.unit_kerja.option');
            }); 
            Route::prefix('lokasi')->group(function () {
                Route::get('/', 'perangkat_daerah\lokasiController@index')->name('opd.perangkat_daerah.lokasi.index');
                Route::get('/datatable', 'perangkat_daerah\lokasiController@datatable')->name('opd.perangkat_daerah.lokasi.datatable');
                Route::post('/store', 'perangkat_daerah\lokasiController@store')->name('opd.perangkat_daerah.lokasi.store');
                Route::post('/update/{params}', 'perangkat_daerah\lokasiController@update')->name('opd.perangkat_daerah.lokasi.update');
                Route::get('/show/{params}', 'perangkat_daerah\lokasiController@show')->name('opd.perangkat_daerah.lokasi.show');
                Route::delete('/delete/{params}', 'perangkat_daerah\lokasiController@delete')->name('opd.perangkat_daerah.lokasi.delete');
                Route::get('/option-lokasi/{params}', 'perangkat_daerah\lokasiController@optionLokasiSatuanKerja')->name('opd.perangkat_daerah.lokasi.option_lokasi_satuan_kerja');
                Route::get('/option-lokasi-apel/{params}', 'perangkat_daerah\lokasiController@optionLokasiApel')->name('opd.perangkat_daerah.lokasi.option_lokasi_apel');
            }); 
        }); 
    });         

    Route::post('/logout', 'AuthController@logout')->name('logout');
});
