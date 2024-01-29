<?php

namespace App\Models\profil;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Uuid as Generator;

class RiwayatPenghargaan extends Model
{
    use HasFactory;
    protected $table = 'tb_profil_riwayat_penghargaan';
    protected $fillable = ['id','uuid','nama_penghargaan','id_pegawai','nomor_surat_keputusan','tanggal','pejabat_pendantanganan','nama_instansi','lokasi','sertifikat'];
    
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
