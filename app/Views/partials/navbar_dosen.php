<?php
$activeNav = $activeNav ?? '';
$items = [
    'dashboard'  => ['Dashboard',    site_url('dosen/dashboard'),  'bi-speedometer2'],
    'mata-kuliah'=> ['Mata Kuliah',  site_url('dosen/mata-kuliah'), 'bi-book'],
    'kelas'      => ['Kelas',        site_url('dosen/kelas'),      'bi-people'],
    'tugas'      => ['Tugas',        site_url('dosen/tugas'),      'bi-journal-text'],
    'notifikasi' => ['Notifikasi',   site_url('dosen/notifikasi'), 'bi-bell'],
    'profil'     => ['Profil',       site_url('dosen/profil'),     'bi-person-circle'],
];
?>
<header>
    <nav class="navbar navbar-expand-lg ah-student-nav" aria-label="Navigasi utama dosen">
        <div class="container">

            <a class="navbar-brand" href="<?= site_url('dosen/dashboard') ?>">
                <span class="ah-brand-icon me-2">
                    <i class="bi bi-journal-bookmark-fill" aria-hidden="true"></i>
                </span>
                <?= esc(cc_app_name()) ?>
                <span class="ms-2 badge" style="background:var(--ah-warning);color:#fff;font-size:0.65rem;">Dosen</span>
            </a>

            <button class="navbar-toggler" type="button"
                    data-bs-toggle="collapse" data-bs-target="#menu-dosen"
                    aria-controls="menu-dosen" aria-expanded="false"
                    aria-label="Buka atau tutup menu navigasi">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="menu-dosen">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                    <?php foreach ($items as $key => [$label, $url, $icon]): ?>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center gap-1" href="<?= $url ?>"
                               <?= $activeNav === $key ? 'aria-current="page"' : '' ?>>
                                <i class="bi <?= esc($icon, 'attr') ?> d-lg-none" aria-hidden="true"></i>
                                <?= esc($label) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>

                    <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                        <form action="<?= site_url('logout') ?>" method="post" class="d-grid d-lg-block">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
                                Keluar
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>
