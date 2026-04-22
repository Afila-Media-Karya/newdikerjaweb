<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KehadiranExportJob extends Model
{
    use HasFactory;

    protected $table = 'tb_export_kehadiran_jobs';

    protected $fillable = [
        'user_id',
        'status',
        'type',
        'payload',
        'estimated_workload',
        'result_path',
        'error_message',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];
}
