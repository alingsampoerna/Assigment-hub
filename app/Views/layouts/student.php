<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc(cc_page_title($title ?? '')) ?></title>
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

    <?= $this->include('partials/navbar_student') ?>

    <main id="konten-utama" tabindex="-1">
        <?= $this->renderSection('content') ?>
    </main>

    <?= $this->include('partials/footer_app') ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
