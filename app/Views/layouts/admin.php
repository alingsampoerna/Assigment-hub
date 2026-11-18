<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc(cc_page_title($title ?? 'Admin')) ?></title>
    <!-- Font Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 dan Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Design System Assignment Hub -->
    <link href="<?= base_url('assets/css/assignmenthub.css') ?>" rel="stylesheet">
</head>
<body>
    <a class="skip-link" href="#konten-utama">Lewati ke konten utama</a>

    <div class="ah-admin-shell">

        <?= $this->include('partials/admin_sidebar') ?>

        <div class="ah-admin-main">
            <!-- Top bar: hanya tampil di layar kecil -->
            <header class="ah-topbar d-lg-none">
                <button class="btn btn-outline-primary" type="button"
                        data-bs-toggle="offcanvas" data-bs-target="#sidebar-admin"
                        aria-controls="sidebar-admin" aria-label="Buka menu navigasi admin">
                    <i class="bi bi-list fs-4" aria-hidden="true"></i>
                </button>
                <span class="fw-bold" style="color:var(--ah-primary);">
                    <i class="bi bi-journal-bookmark-fill" aria-hidden="true"></i>
                    <?= esc(cc_app_name()) ?> Admin
                </span>
            </header>

            <main id="konten-utama" tabindex="-1">
                <?= $this->renderSection('content') ?>
            </main>

            <?= $this->include('partials/footer_app') ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
