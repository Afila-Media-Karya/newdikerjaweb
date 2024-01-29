<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Uuid as Generator;

class PegawaiPensiun extends Model
{
    use HasFactory;

    protected $table = 'tb_pegawai_pensiun';
    protected $fillable = ['id','uuid','id_pegawai','id_jabatan_terakhir','tmt','status','tahun'];

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
