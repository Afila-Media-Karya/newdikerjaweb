<?php

namespace App\Models\profil;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Uuid as Generator;

class RiwayatOrangTua extends Model
{
    use HasFactory;
    protected $table = 'tb_profil_riwayat_orang_tua';
    protected $fillable = ['id','uuid','id_pegawai','nama_orang_tua','jk','tempat_lahir','tanggal_lahir','pendidikan','pekerjaan','keterangan','foto_buku_nikah'];
    
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
