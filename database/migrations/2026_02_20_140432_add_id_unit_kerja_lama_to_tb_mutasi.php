<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tb_mutasi', function (Blueprint $table) {
            $table->unsignedBigInteger('id_unit_kerja_lama')
                  ->nullable()
                  ->after('id_unit_kerja'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_mutasi', function (Blueprint $table) {
            $table->dropColumn('id_unit_kerja_lama');
        });
    }
};
