<?php
// Halaman ini tidak boleh bergantung pada database; hanya membaca session.
$dashboard = function_exists('cc_dashboard_url') ? cc_dashboard_url() : null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Halaman tidak ditemukan - Assignment Hub</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/assignmenthub.css') ?>" rel="stylesheet">
</head>
<body>
    <main class="cc-error" id="konten-utama">
        <div class="cc-error__card">
            <p class="cc-error__code" aria-hidden="true">404</p>
            <h1 class="mt-3">Halaman tidak ditemukan</h1>
            <p class="text-muted-cc">
                Alamat yang Anda buka tidak tersedia atau sudah dipindahkan.
                Periksa kembali alamatnya, atau kembali ke halaman utama.
            </p>
            <div class="d-flex flex-wrap justify-content-center gap-2 mt-4">
                <a class="btn btn-primary" href="<?= site_url('/') ?>">Kembali ke Beranda</a>
                <?php if ($dashboard !== null): ?>
                    <a class="btn btn-outline-primary" href="<?= $dashboard ?>">Ke Dashboard</a>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>