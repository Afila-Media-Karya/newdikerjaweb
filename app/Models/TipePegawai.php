<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Uuid as Generator;

class TipePegawai extends Model
{
    use HasFactory;
    protected $table = 'tb_tipe_pegawai';
    protected $fillable = ['id', 'uuid', 'kode', 'nama', 'keterangan', 'is_active', 'user_insert', 'user_update'];

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
     * Ambil semua tipe pegawai aktif untuk dropdown.
     */
    public static function getOptions()
    {
        return self::where('is_active', true)
            ->orderBy('nama')
            ->get(['kode', 'nama']);
    }
}
