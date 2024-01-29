<?php

namespace App\Models\profil;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Uuid as Generator;

class RiwayatKeahlian extends Model
{
    use HasFactory;
    protected $table = 'tb_profil_keahlian';
    protected $fillable = ['id','uuid','id_pegawai','jenis_riwayat_tambahan','nama_keahlian','level_keahlian','tanggal','pelatihan','predikat','sertifikat'];
    
    protected static function boot(){
        parent::boot();
        static::creating(function ($model) {
            try {
                $model->uuid = Generator::uuid4()->toString();
            } catch (UnsatisfiedDependencyException $e) {
                abort(500, $e->getMessage());
            }
        });
    }
    
}
