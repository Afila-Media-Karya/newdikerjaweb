<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tb_jam_apel', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('tipe_pegawai');         // which tipe_pegawai this applies to
            $table->string('jenis');                 // reguler, hari_besar
            $table->string('shift')->nullable();     // pagi (for nakes), null for non-shift
            $table->time('batas_awal');              // e.g. 06:15
            $table->time('batas_akhir');             // e.g. 07:30
            $table->boolean('is_active')->default(true);
            $table->string('user_insert')->nullable();
            $table->string('user_update')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_jam_apel');
    }
};
