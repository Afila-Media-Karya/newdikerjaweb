<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Uuid as Generator;

class LayananCuti extends Model
{
    use HasFactory;
    protected $table = 'tb_layanan_cuti';
    protected $fillable = ['id','uuid','jenis_layanan','alasan','tanggal_mulai','tanggal_akhir','alamat','dokumen','keterangan','id_pegawai','status'];

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
