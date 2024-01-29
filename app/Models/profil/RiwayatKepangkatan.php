<?php

namespace App\Models\profil;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Uuid as Generator;

class RiwayatKepangkatan extends Model
{
    use HasFactory;
    protected $table = 'tb_profil_riwayat_kepangkatan';
    protected $fillable = ['id','uuid','id_pegawai','gaji_pokok','golongan','tahun','bulan','nomor','tanggal','pejabat_pendantanganan','tmt','nama_unit_kerja','surat_keputusan'];
    
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
