<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Uuid as Generator;

class JamKerja extends Model
{
    use HasFactory;
    protected $table = 'tb_jam_kerja';
    protected $fillable = [
        'id',
        'uuid',
        'tipe_pegawai',
        'kategori',
        'shift',
        'jumlah_shift',
        'hari',
        'jam_masuk',
        'jam_keluar',
        'batas_awal_masuk',
        'batas_akhir_masuk',
        'batas_awal_pulang',
        'batas_akhir_pulang',
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
     * Get jam kerja for a given tipe_pegawai, day number, and optional shift/category.
     */
    public static function getJamKerja($tipe_pegawai, $hari, $shift = null, $jumlah_shift = null, $kategori = 'reguler')
    {
        $query = self::where('tipe_pegawai', $tipe_pegawai)
            ->where('hari', $hari)
            ->where('kategori', $kategori)
            ->where('is_active', true);

        if ($shift !== null) {
            $query->where('shift', $shift);
        } else {
            $query->whereNull('shift');
        }

        if ($jumlah_shift !== null) {
            $query->where('jumlah_shift', $jumlah_shift);
        } else {
            $query->whereNull('jumlah_shift');
        }

        return $query->first();
    }

    /**
     * Get all jam kerja for a given tipe_pegawai, grouped for settings page display.
     */
    public static function getSettingsForTipe($tipe_pegawai, $kategori = 'reguler')
    {
        return self::where('tipe_pegawai', $tipe_pegawai)
            ->where('kategori', $kategori)
            ->where('is_active', true)
            ->orderBy('shift')
            ->orderBy('jumlah_shift')
            ->orderBy('hari')
            ->get();
    }

    /**
     * Get all distinct tipe_pegawai from jam kerja.
     */
    public static function getTipePegawaiList()
    {
        return self::where('is_active', true)
            ->select('tipe_pegawai')
            ->distinct()
            ->pluck('tipe_pegawai');
    }
}
