<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tb_tipe_pegawai', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('kode'); // e.g. 'pegawai_administratif'
            $table->string('nama'); // e.g. 'Pegawai Administratif'
            $table->string('keterangan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('user_insert')->nullable();
            $table->string('user_update')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_tipe_pegawai');
    }
};
