<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tb_usia_pensiun', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('tipe_pegawai');
            $table->integer('usia_pensiun');
            $table->string('keterangan')->nullable();
            $table->string('user_insert')->nullable();
            $table->string('user_update')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_usia_pensiun');
    }
};
