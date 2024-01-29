<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Uuid as Generator;
use Str;

class MasterJabatan extends Model
{
    use HasFactory;
    protected $table = 'tb_master_jabatan';
    protected $fillable = ['id','uuid','nama_struktur','nama_jabatan','jenis_jabatan','pagu_tpp','id_satuan_kerja','id_kelompok_jabatan','id_parent','id_lokasi_kerja','id_lokasi_apel'];

    protected static function boot(){
        parent::boot();
        static::creating(function ($model) {
            try {
                $model->uuid = Generator::uuid4()->toString();
                $model->kode_jabatan  = Str::random(4);
            } catch (UnsatisfiedDependencyException $e) {
                abort(500, $e->getMessage());
            }
        });
    }
}
