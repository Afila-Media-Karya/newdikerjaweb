<?php

use App\Models\Pegawai;

function hasRole(){
    if (Auth::guard('administrator')->check()) {
        return [
            'guard' => 'administrator',
            'role' => Auth::guard('administrator')->user()->role,
            'id' => Auth::guard('administrator')->user()->id
        ];
    } else {
        return [
            'guard' => 'web',
            'role' => Auth::user()->role,
            'id' => Auth::user()->id,
            'id_pegawai' => Auth::user()->id_pegawai,
        ];
    }
}

function konvertBulan($bulan){
    $array = [
        date('Y'),
        'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    ];

    return $array[$bulan];
}

function ManageFileDatatable($path){
    $res = true;
    if ($path[0] == 'sasaran-kinerja' || $path[0] == 'realisasi') {
        $res = false;
    }

    if (isset($path[1]) && isset($path[2])) {
        if ($path[0] == 'review' && $path[1] == 'sasaran-kinerja' && $path[2] == 'review') {
            $res = false;
         }    
    }

    
    return $res;
}