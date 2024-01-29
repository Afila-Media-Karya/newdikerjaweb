<?php

namespace App\Models\profil;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Uuid as Generator;

class DiklatTeknis extends Model
{
    use HasFactory;
    protected $table = 'tb_profil_riwayat_diklat_teknis';
    protected $fillable = ['id','uuid','id_pegawai','nama_diklat_teknis','tanggal_mulai','tanggal_selesai','jumlah_jam','nomor_sttb','tanggal','pejabat_pendantanganan','nama_instansi','lokasi','sertifikat'];
    
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
