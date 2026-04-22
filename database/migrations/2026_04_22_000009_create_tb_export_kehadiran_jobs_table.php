<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tb_export_kehadiran_jobs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('status')->default('queued'); // queued|processing|done|failed
            $table->string('type')->default('pdf'); // pdf|excel
            $table->json('payload');
            $table->unsignedInteger('estimated_workload')->default(0);
            $table->string('result_path')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status'], 'idx_export_kehadiran_user_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_export_kehadiran_jobs');
    }
};
