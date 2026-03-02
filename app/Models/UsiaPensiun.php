<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Uuid as Generator;

class UsiaPensiun extends Model
{
    use HasFactory;
    protected $table = 'tb_usia_pensiun';
    protected $fillable = ['id', 'uuid', 'tipe_pegawai', 'usia_pensiun', 'keterangan', 'user_insert', 'user_update'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            try {
                $model->uuid = Generator::uuid4()->toString();
            } catch (UnsatisfiedDependencyException $e) {
                abort(500, $e->getMessage());
            }
        });
    }

    /**
     * Ambil usia pensiun untuk tipe pegawai tertentu.
     */
    public static function getUsiaPensiun($tipe_pegawai)
    {
        $record = self::where('tipe_pegawai', $tipe_pegawai)->first();
        return $record ? $record->usia_pensiun : 58; // default 58 tahun
    }
}
