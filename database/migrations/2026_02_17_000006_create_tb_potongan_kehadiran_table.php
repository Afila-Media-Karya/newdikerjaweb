<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tb_potongan_kehadiran', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('jenis'); // kmk (keterlambatan masuk kerja), cpk (cepat pulang kerja), alfa, apel
            $table->string('label'); // e.g. 'Terlambat 1-30 menit'
            $table->integer('menit_awal')->nullable();
            $table->integer('menit_akhir')->nullable();
            $table->decimal('persentase', 5, 2); // e.g. 0.50, 1.00, 1.25
            $table->string('keterangan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('user_insert')->nullable();
            $table->string('user_update')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_potongan_kehadiran');
    }
};
