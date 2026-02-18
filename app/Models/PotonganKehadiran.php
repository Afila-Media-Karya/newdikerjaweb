<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Uuid as Generator;

class PotonganKehadiran extends Model
{
    use HasFactory;
    protected $table = 'tb_potongan_kehadiran';
    protected $fillable = [
        'id',
        'uuid',
        'jenis',
        'label',
        'menit_awal',
        'menit_akhir',
        'persentase',
        'keterangan',
        'is_active',
        'user_insert',
        'user_update'
    ];

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
     * Ambil persentase potongan berdasarkan jenis dan jumlah menit.
     */
    public static function getPersentase($jenis, $menit)
    {
        return self::where('jenis', $jenis)
            ->where('is_active', true)
            ->where('menit_awal', '<=', $menit)
            ->where('menit_akhir', '>=', $menit)
            ->value('persentase');
    }

    /**
     * Ambil semua aturan potongan per jenis.
     */
    public static function getByJenis($jenis)
    {
        return self::where('jenis', $jenis)
            ->where('is_active', true)
            ->orderBy('menit_awal')
            ->get();
    }
}
