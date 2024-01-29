<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Uuid as Generator;
class PegawaiMasuk extends Model
{
    use HasFactory;

    protected $table = 'tb_pegawai_masuk';
    protected $fillable = ['id','uuid','id_pegawai','asal_daerah','id_jabatan_masuk','tmt','tahun'];

    protected static function boot(){
        parent::boot();
        static::creating(function ($model) {
            try {
                $model->uuid = Generator::uuid4()->toString();
                $model->tahun = date('Y');
            } catch (UnsatisfiedDependencyException $e) {
                abort(500, $e->getMessage());
            }
        });
    }
}
