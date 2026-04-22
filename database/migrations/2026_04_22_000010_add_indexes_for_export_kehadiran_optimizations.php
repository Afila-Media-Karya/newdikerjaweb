<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private function indexExists(string $table, string $index): bool
    {
        if (!Schema::hasTable($table)) {
            return false;
        }

        $rows = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$index]);
        return !empty($rows);
    }

    public function up(): void
    {
        if (Schema::hasTable('tb_absen')) {
            Schema::table('tb_absen', function (Blueprint $table) {
                if (!$this->indexExists('tb_absen', 'idx_tb_absen_pegawai_validation_tanggal')) {
                    $table->index(['id_pegawai', 'validation', 'tanggal_absen'], 'idx_tb_absen_pegawai_validation_tanggal');
                }
                if (!$this->indexExists('tb_absen', 'idx_tb_absen_tanggal_pegawai')) {
                    $table->index(['tanggal_absen', 'id_pegawai'], 'idx_tb_absen_tanggal_pegawai');
                }
            });
        }

        if (Schema::hasTable('tb_libur')) {
            Schema::table('tb_libur', function (Blueprint $table) {
                if (!$this->indexExists('tb_libur', 'idx_tb_libur_tipe_mulai_selesai')) {
                    $table->index(['tipe', 'tanggal_mulai', 'tanggal_selesai'], 'idx_tb_libur_tipe_mulai_selesai');
                }
            });
        }

        if (Schema::hasTable('tb_jam_kerja')) {
            Schema::table('tb_jam_kerja', function (Blueprint $table) {
                if (!$this->indexExists('tb_jam_kerja', 'idx_tb_jam_kerja_lookup')) {
                    $table->index(['tipe_pegawai', 'kategori', 'hari', 'shift', 'jumlah_shift', 'is_active'], 'idx_tb_jam_kerja_lookup');
                }
            });
        }

        if (Schema::hasTable('tb_jam_apel')) {
            Schema::table('tb_jam_apel', function (Blueprint $table) {
                if (!$this->indexExists('tb_jam_apel', 'idx_tb_jam_apel_lookup')) {
                    $table->index(['tipe_pegawai', 'jenis', 'shift', 'is_active'], 'idx_tb_jam_apel_lookup');
                }
            });
        }

        if (Schema::hasTable('tb_mutasi')) {
            Schema::table('tb_mutasi', function (Blueprint $table) {
                if (!$this->indexExists('tb_mutasi', 'idx_tb_mutasi_skpd_tmt_pegawai')) {
                    $table->index(['id_satuan_kerja', 'tmt', 'id_pegawai'], 'idx_tb_mutasi_skpd_tmt_pegawai');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('tb_absen')) {
            Schema::table('tb_absen', function (Blueprint $table) {
                if ($this->indexExists('tb_absen', 'idx_tb_absen_pegawai_validation_tanggal')) {
                    $table->dropIndex('idx_tb_absen_pegawai_validation_tanggal');
                }
                if ($this->indexExists('tb_absen', 'idx_tb_absen_tanggal_pegawai')) {
                    $table->dropIndex('idx_tb_absen_tanggal_pegawai');
                }
            });
        }

        if (Schema::hasTable('tb_libur') && $this->indexExists('tb_libur', 'idx_tb_libur_tipe_mulai_selesai')) {
            Schema::table('tb_libur', function (Blueprint $table) {
                $table->dropIndex('idx_tb_libur_tipe_mulai_selesai');
            });
        }

        if (Schema::hasTable('tb_jam_kerja') && $this->indexExists('tb_jam_kerja', 'idx_tb_jam_kerja_lookup')) {
            Schema::table('tb_jam_kerja', function (Blueprint $table) {
                $table->dropIndex('idx_tb_jam_kerja_lookup');
            });
        }

        if (Schema::hasTable('tb_jam_apel') && $this->indexExists('tb_jam_apel', 'idx_tb_jam_apel_lookup')) {
            Schema::table('tb_jam_apel', function (Blueprint $table) {
                $table->dropIndex('idx_tb_jam_apel_lookup');
            });
        }

        if (Schema::hasTable('tb_mutasi') && $this->indexExists('tb_mutasi', 'idx_tb_mutasi_skpd_tmt_pegawai')) {
            Schema::table('tb_mutasi', function (Blueprint $table) {
                $table->dropIndex('idx_tb_mutasi_skpd_tmt_pegawai');
            });
        }
    }
};
