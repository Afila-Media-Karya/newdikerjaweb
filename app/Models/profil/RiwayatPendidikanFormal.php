<?php

namespace App\Models\profil;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Uuid as Generator;
class RiwayatPendidikanFormal extends Model
{
    use HasFactory;
    protected $table = 'tb_profil_riwayat_pendidikan';
    protected $fillable = ['id','uuid','pendidikan','fakultas','nomor_ijazah','tanggal','pimpinan','nama_sekolah','alamat','foto_ijazah'];
    
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
