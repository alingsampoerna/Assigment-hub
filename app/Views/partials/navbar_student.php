<?php
$activeNav = $activeNav ?? '';
$items = [
    'dashboard'  => ['Dashboard',   site_url('mahasiswa/dashboard'),  'bi-speedometer2'],
    'kelas'      => ['Kelas Saya',  site_url('mahasiswa/kelas'),      'bi-people'],
    'tugas'      => ['Tugas',       site_url('mahasiswa/tugas'),      'bi-journal-text'],
    'notifikasi' => ['Notifikasi',  site_url('mahasiswa/notifikasi'), 'bi-bell'],
    'profil'     => ['Profil',      site_url('mahasiswa/profil'),     'bi-person-circle'],
];
?>
<header>
    <nav class="navbar navbar-expand-lg ah-student-nav" aria-label="Navigasi utama mahasiswa">
        <div class="container">

            <!-- Brand -->
            <a class="navbar-brand" href="<?= site_url('mahasiswa/dashboard') ?>">
                <span class="ah-brand-icon me-2">
                    <i class="bi bi-journal-bookmark-fill" aria-hidden="true"></i>
                </span>
                <?= esc(cc_app_name()) ?>
            </a>

            <button class="navbar-toggler" type="button"
                    data-bs-toggle="collapse" data-bs-target="#menu-mahasiswa"
                    aria-controls="menu-mahasiswa" aria-expanded="false"
                    aria-label="Buka atau tutup menu navigasi">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="menu-mahasiswa">
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
