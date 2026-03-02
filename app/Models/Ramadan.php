<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Uuid as Generator;

class Ramadan extends Model
{
    use HasFactory;
    protected $table = 'tb_ramadan';
    protected $fillable = ['id', 'uuid', 'tahun', 'tanggal_mulai', 'tanggal_selesai', 'keterangan', 'user_insert', 'user_update'];

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
     * Cek apakah tanggal tertentu ada dalam periode Ramadan.
     */
    public static function isRamadan($tanggal)
    {
        return self::whereDate('tanggal_mulai', '<=', $tanggal)
            ->whereDate('tanggal_selesai', '>=', $tanggal)
            ->exists();
    }
}
