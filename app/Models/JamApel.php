<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Uuid as Generator;

class JamApel extends Model
{
    use HasFactory;
    protected $table = 'tb_jam_apel';
    protected $fillable = [
        'id',
        'uuid',
        'tipe_pegawai',
        'jenis',
        'shift',
        'batas_awal',
        'batas_akhir',
        'is_active',
        'hari',
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
     * Get apel configuration for a given tipe_pegawai and jenis.
     */
    public static function getJamApel($tipe_pegawai, $jenis = 'reguler', $shift = null)
    {
        $query = self::where('tipe_pegawai', $tipe_pegawai)
            ->where('jenis', $jenis)
            ->where('is_active', true);

        if ($shift !== null) {
            $query->where('shift', $shift);
        } else {
            $query->whereNull('shift');
        }

        return $query->first();
    }

    /**
     * Get all apel settings grouped for display.
     */
    public static function getAllSettings()
    {
        return self::where('is_active', true)
            ->orderBy('tipe_pegawai')
            ->orderBy('jenis')
            ->get();
    }
}
