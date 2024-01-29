<?php

namespace App\Models\profil;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Uuid as Generator;

class RiwayatPendidikanNonFormal extends Model
{
    use HasFactory;
    protected $table = 'tb_profil_riwayat_pendidikan_non_formal';
    protected $fillable = ['id','uuid','nama_kursus','id_pegawai','tanggal_mulai','tanggal_selesai','nomor','tanggal','nama_pejabat','penyelenggara','nama_tempat','foto_ijazah'];
    
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
