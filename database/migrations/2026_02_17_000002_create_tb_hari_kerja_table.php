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
        Schema::create('tb_hari_kerja', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('tipe_pegawai');
            $table->tinyInteger('hari'); // 1=Senin .. 7=Minggu
            $table->boolean('is_hari_kerja')->default(true);
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
        Schema::dropIfExists('tb_hari_kerja');
    }
};
