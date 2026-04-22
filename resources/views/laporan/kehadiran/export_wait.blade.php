<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menyiapkan Export Kehadiran</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f9fc;
            margin: 0;
            color: #1f2937;
        }
        .container {
            max-width: 720px;
            margin: 48px auto;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 24px;
        }
        h1 {
            margin: 0 0 12px;
            font-size: 22px;
        }
        p {
            margin: 8px 0;
            line-height: 1.5;
        }
        .meta {
            font-size: 13px;
            color: #6b7280;
        }
        .status {
            margin-top: 16px;
            padding: 10px 12px;
            background: #f3f4f6;
            border-radius: 8px;
            font-size: 14px;
        }
        .actions {
            margin-top: 16px;
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-block;
            padding: 10px 14px;
            border-radius: 8px;
            text-decoration: none;
            border: 1px solid #d1d5db;
            color: #111827;
            background: #fff;
            font-size: 14px;
        }
        .btn-primary {
            background: #0ea5e9;
            color: #fff;
            border-color: #0ea5e9;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Export Sedang Diproses</h1>
    <p>Mohon tunggu, sistem sedang menyiapkan file laporan PDF Anda.</p>
    <p class="meta">Job ID: <strong>{{ $jobId }}</strong> | Estimasi workload: <strong>{{ $estimatedWorkload }}</strong></p>

    <div class="status" id="statusBox">Status: menunggu antrean...</div>

    <div class="actions">
        <a class="btn" href="{{ $statusUrl }}" target="_blank" rel="noopener">Cek Status (JSON)</a>
        <a class="btn" href="{{ $downloadUrl }}" target="_blank" rel="noopener">Coba Download Manual</a>
        <a class="btn btn-primary" href="javascript:window.close()">Tutup Halaman</a>
    </div>
</div>

<script>
    const statusUrl = @json($statusUrl);
    const downloadUrl = @json($downloadUrl);
    const statusBox = document.getElementById('statusBox');
    let hasTriggeredDownload = false;

    async function pollStatus() {
        try {
            const response = await fetch(statusUrl, { credentials: 'same-origin' });
            const data = await response.json();

            if (!data.success) {
                statusBox.textContent = 'Status: gagal membaca status job.';
                return;
            }

            statusBox.textContent = 'Status: ' + data.status;

            if (data.status === 'done' && !hasTriggeredDownload) {
                hasTriggeredDownload = true;
                statusBox.textContent = 'Status: selesai. Mengunduh file...';
                window.location.href = downloadUrl;
                return;
            }

            if (data.status === 'failed') {
                statusBox.textContent = 'Status: gagal. ' + (data.error_message || 'Silakan coba lagi.');
                return;
            }

            setTimeout(pollStatus, 2000);
        } catch (e) {
            statusBox.textContent = 'Status: koneksi terputus, mencoba lagi...';
            setTimeout(pollStatus, 3000);
        }
    }

    setTimeout(pollStatus, 500);
</script>
</body>
</html>
