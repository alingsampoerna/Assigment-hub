<header>
    <nav class="navbar navbar-expand-lg cc-navbar" aria-label="Navigasi utama">
        <div class="container">

            <!-- Brand -->
            <a class="navbar-brand" href="<?= base_url('/') ?>">
                <span class="ah-brand-icon me-2">
                    <i class="bi bi-journal-bookmark-fill" aria-hidden="true"></i>
                </span>
                <?= esc(cc_app_name()) ?>
            </a>

            <button class="navbar-toggler" type="button"
                    data-bs-toggle="collapse" data-bs-target="#menu-utama"
                    aria-controls="menu-utama" aria-expanded="false"
                    aria-label="Buka atau tutup menu navigasi">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="menu-utama">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">

                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('/') ?>"
                           <?= ($activeNav ?? '') === 'beranda' ? 'aria-current="page"' : '' ?>>
                            Beranda
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('/#fitur') ?>">Fitur</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('/#cara-kerja') ?>">Cara Kerja</a>
                    </li>

                    <?php $dashboardUrl = cc_dashboard_url(); ?>

                    <?php if ($dashboardUrl !== null): ?>
                        <li class="nav-item ms-lg-2 mt-2 mt-lg-0 d-grid d-lg-block">
                            <a class="btn btn-primary" href="<?= $dashboardUrl ?>">
                                <i class="bi bi-speedometer2" aria-hidden="true"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item ms-lg-1 mt-2 mt-lg-0">
                            <form action="<?= site_url('logout') ?>" method="post" class="d-grid d-lg-block">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-outline-primary">
                                    Keluar
                                </button>
                            </form>
                        </li>
                    <?php else: ?>
                        <li class="nav-item ms-lg-2 mt-2 mt-lg-0 d-grid d-lg-block">
                            <a class="btn btn-outline-primary" href="<?= site_url('login') ?>"
                               <?= ($activeNav ?? '') === 'login' ? 'aria-current="page"' : '' ?>>
                                Masuk
                            </a>
                        </li>
                        <li class="nav-item ms-lg-1 mt-2 mt-lg-0 d-grid d-lg-block">
                            <a class="btn btn-primary" href="<?= site_url('register') ?>"
                               <?= ($activeNav ?? '') === 'register' ? 'aria-current="page"' : '' ?>>
                                Daftar
                            </a>
                        </li>
                    <?php endif; ?>

                </ul>
            </div>
        </div>
    </nav>
</header>
