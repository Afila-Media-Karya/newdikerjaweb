<?php

namespace App\Models\profil;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Uuid as Generator;

class RiwayatBahasa extends Model
{
    use HasFactory;
    protected $table = 'tb_profil_bahasa';
    protected $fillable = ['id','uuid','id_pegawai','jenis_riwayat_tambahan','nama_bahasa','level_keahlian_membaca','level_keahlian_mendengarkan','level_keahlian_menulis','level_keahlian_berbicara','tanggal','pelatihan','predikat','sertifikat'];
    
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
