<?php

namespace App\Jobs;

use App\Http\Controllers\LaporanKehadiranController;
use App\Models\KehadiranExportJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessKehadiranExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public function __construct(public int $exportJobId)
    {
    }

    public function handle(): void
    {
        $job = KehadiranExportJob::find($this->exportJobId);
        if (!$job || $job->status === 'done') {
            return;
        }

        $job->update([
            'status' => 'processing',
            'started_at' => now(),
            'error_message' => null,
        ]);

        try {
            $payload = $job->payload ?? [];
            $type = $job->type === 'excel' ? 'excel' : 'pdf';
            $extension = $type === 'excel' ? 'xlsx' : 'pdf';

            $relativePath = 'exports/kehadiran/laporan-kehadiran-' . $job->id . '-' . now()->format('YmdHis') . '.' . $extension;
            $absolutePath = storage_path('app/' . $relativePath);

            $directory = dirname($absolutePath);
            if (!is_dir($directory)) {
                mkdir($directory, 0775, true);
            }

            /** @var LaporanKehadiranController $controller */
            $controller = app(LaporanKehadiranController::class);
            $controller->runExportOpdBulanWithContext($payload, $absolutePath);

            $job->update([
                'status' => 'done',
                'result_path' => $relativePath,
                'finished_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('ProcessKehadiranExportJob failed', [
                'job_id' => $this->exportJobId,
                'message' => $e->getMessage(),
            ]);

            $job->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'finished_at' => now(),
            ]);

            throw $e;
        }
    }
}
