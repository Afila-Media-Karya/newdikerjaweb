<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Uuid as Generator;
use Auth;

class AspekSkp extends Model
{
    use HasFactory;
    protected $table = 'tb_aspek_skp';
    protected $fillable = ['id','uuid','iki','aspek_skp','target','realisasi','satuan','tahun','id_skp'];

     public function SasaranKinerja(){
        return $this->belongsTo(SasaranKinerja::class,'id_skp','id');
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
