<?php

namespace App\Models\profil;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Uuid as Generator;

class CatatanHukumanDinas extends Model
{
    use HasFactory;
    protected $table = 'tb_profil_catatan_hukuman_dinas';
    protected $fillable = ['id','uuid','id_pegawai','kategori_hukuman','nama_hukuman','nama_sk','tanggal_sk','tanggal_mulai','tanggal_selesai','keterangan_pelanggaran','surat_keputusan'];
    
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
