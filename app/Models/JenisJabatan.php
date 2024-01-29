<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisJabatan extends Model
{
    use HasFactory;
    protected $table = 'tb_jenis_jabatan';
    protected $fillable = ['id','uuid','level','jenis_jabatan','kelas_jabatan'];
}
