<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Uuid as Generator;
use Auth;

class Aktivitas extends Model
{
    use HasFactory;

    protected $table = 'tb_aktivitas';
    protected $fillable = ['id','uuid','aktivitas','keterangan','volume','satuan','waktu','tanggal','validation','id_sasaran','id_pegawai'];

    public function skp() {
        return $this->belongsTo('App\Models\skp', 'id_sasaran','id');
    }

    protected static function boot(){
        parent::boot();
        static::creating(function ($model) {
            try {
                $model->uuid = Generator::uuid4()->toString();
                $model->tahun = date('Y');
                $model->user_insert = Auth::user()->id;
                $model->user_update = Auth::user()->id;
            } catch (UnsatisfiedDependencyException $e) {
                abort(500, $e->getMessage());
            }
        });
    }
}
