<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_riwayat_aktivitas', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->unsignedBigInteger('id_pegawai');
            $table->unsignedBigInteger('id_sasaran');
            $table->text('aktivitas');
            $table->text('keterangan');
            $table->integer('volume');
            $table->string('satuan');
            $table->integer('waktu');
            $table->date('tanggal');
            $table->tinyInteger('validation');
            $table->string('tahun');
            $table->unsignedBigInteger('user_action');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_riwayat_aktivitas');
    }
};
