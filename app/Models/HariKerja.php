<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Uuid as Generator;

class HariKerja extends Model
{
    use HasFactory;
    protected $table = 'tb_hari_kerja';
    protected $fillable = ['id', 'uuid', 'tipe_pegawai', 'hari', 'is_hari_kerja', 'user_insert', 'user_update'];

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
     * Cek apakah hari tertentu adalah hari kerja untuk tipe pegawai tertentu.
     */
    public static function isHariKerja($tipe_pegawai, $hari)
    {
        $record = self::where('tipe_pegawai', $tipe_pegawai)
            ->where('hari', $hari)
            ->where('is_hari_kerja', true)
            ->first();

        return $record !== null;
    }

    /**
     * Ambil semua hari kerja untuk tipe pegawai tertentu.
     */
    public static function getHariKerja($tipe_pegawai)
    {
        return self::where('tipe_pegawai', $tipe_pegawai)
            ->where('is_hari_kerja', true)
            ->pluck('hari')
            ->toArray();
    }
}
