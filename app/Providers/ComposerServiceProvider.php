<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use DB;
use Auth;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {

        View::composer('layouts.header', function ($view) {
            $jabatan = array();

            if (hasRole()['guard'] == 'web') {
                $jabatan = DB::table('tb_pegawai')->join('tb_jabatan','tb_jabatan.id_pegawai','tb_pegawai.id')->join('tb_master_jabatan','tb_jabatan.id_master_jabatan','tb_master_jabatan.id')->select(
                    'tb_pegawai.id','tb_master_jabatan.nama_jabatan',
                    'tb_jabatan.status',
                    DB::raw('
                        CASE 
                            WHEN tb_jabatan.status = "definitif" THEN tb_master_jabatan.nama_jabatan
                            ELSE CONCAT(UPPER(tb_jabatan.status), " ", tb_master_jabatan.nama_jabatan)
                        END as text
                    ')
                    )->where('tb_pegawai.id',Auth::user()->id_pegawai)->get();
            }
            $view->with('jabatan',$jabatan);
            
        });
    }
}
