<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Uuid as Generator;
use Str;

class Pegawai extends Model
{
    use HasFactory;

    protected $table = 'tb_pegawai';
    protected $fillable = ['id','uuid','nip','nama','tempat_lahir','tanggal_lahir','jenis_kelamin','golongan','agama','status_perkawinan','pendidikan','tmt_jabatan','tmt_golongan','tmt_pegawai','tahun','pendidikan_lulus','pendidikan_struktural','pendidikan_struktural_lulus','id_satuan_kerja','status_verifikasi'];

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
