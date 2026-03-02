<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tb_jam_kerja', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('tipe_pegawai');
            $table->string('kategori')->default('reguler'); // reguler, ramadan
            $table->string('shift')->nullable(); // pagi, siang, malam
            $table->integer('jumlah_shift')->nullable(); // 2 atau 3
            $table->tinyInteger('hari'); // 1=Senin .. 7=Minggu
            $table->time('jam_masuk');
            $table->time('jam_keluar');
            $table->boolean('is_active')->default(true);
            $table->string('user_insert')->nullable();
            $table->string('user_update')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_jam_kerja');
    }
};
