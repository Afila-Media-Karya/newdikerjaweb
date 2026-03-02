<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tb_jam_kerja', function (Blueprint $table) {
            $table->time('batas_awal_masuk')->nullable()->after('jam_keluar');   // earliest clock-in (e.g. 06:30)
            $table->time('batas_akhir_masuk')->nullable()->after('batas_awal_masuk');  // latest clock-in (e.g. 11:30)
            $table->time('batas_awal_pulang')->nullable()->after('batas_akhir_masuk'); // earliest clock-out (e.g. 14:00)
            $table->time('batas_akhir_pulang')->nullable()->after('batas_awal_pulang'); // latest clock-out (e.g. 22:00)
        });
    }

    public function down(): void
    {
        Schema::table('tb_jam_kerja', function (Blueprint $table) {
            $table->dropColumn(['batas_awal_masuk', 'batas_akhir_masuk', 'batas_awal_pulang', 'batas_akhir_pulang']);
        });
    }
};
