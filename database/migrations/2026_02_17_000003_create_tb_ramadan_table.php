<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tb_ramadan', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->integer('tahun');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->string('keterangan')->nullable();
            $table->string('user_insert')->nullable();
            $table->string('user_update')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_ramadan');
    }
};
