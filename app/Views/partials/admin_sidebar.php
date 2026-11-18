<?php
$activeNav = $activeNav ?? '';

$menu = [
    'utama' => [
        'label' => 'Utama',
        'items' => [
            'dashboard' => ['Dashboard', 'admin/dashboard', 'bi-speedometer2'],
        ],
    ],
    'akademik' => [
        'label' => 'Akademik',
        'items' => [
            'mata-kuliah' => ['Mata Kuliah',  'admin/mata-kuliah', 'bi-book'],
            'kelas'       => ['Kelas',        'admin/kelas',       'bi-people'],
            'mahasiswa'   => ['Mahasiswa',    'admin/mahasiswa',   'bi-person-badge'],
            'dosen'       => ['Dosen',        'admin/dosen',       'bi-person-workspace'],
        ],
    ],
    'tugas' => [
        'label' => 'Tugas',
        'items' => [
            'tugas'        => ['Kelola Tugas',    'admin/tugas',               'bi-journal-text'],
            'pengumpulan'  => ['Pengumpulan',      'admin/pengumpulan',         'bi-cloud-upload'],
        ],
    ],
    'lainnya' => [
        'label' => 'Lainnya',
        'items' => [
            'notifikasi'  => ['Notifikasi',   'admin/notifikasi',  'bi-bell'],
            'pengaturan'  => ['Pengaturan',   'admin/pengaturan',  'bi-gear'],
        ],
    ],
];
?>
<div class="ah-sidebar">
    <div class="ah-sidebar__inner">
        <div class="offcanvas-lg offcanvas-start ah-sidebar__panel" tabindex="-1"
             id="sidebar-admin" aria-labelledby="sidebar-admin-judul">

            <div class="offcanvas-header" style="background:var(--ah-primary);border-bottom:1px solid rgba(255,255,255,0.12);">
                <h2 class="offcanvas-title visually-hidden" id="sidebar-admin-judul">Menu navigasi admin</h2>
                <button type="button" class="btn-close btn-close-white ms-auto"
                        data-bs-dismiss="offcanvas" data-bs-target="#sidebar-admin"
                        aria-label="Tutup menu"></button>
            </div>

            <div class="offcanvas-body p-0" style="background:var(--ah-primary);">
                <div class="ah-sidebar__content">

                    <!-- Brand -->
                    <a class="ah-sidebar__brand" href="<?= site_url('admin/dashboard') ?>">
                        <span class="ah-brand-icon">
                            <i class="bi bi-journal-bookmark-fill" aria-hidden="true"></i>
                        </span>
                        <span><?= esc(cc_app_name()) ?></span>
                    </a>

                    <!-- Menu -->
                    <nav aria-label="Navigasi admin">
                        <?php foreach ($menu as $grup): ?>
                            <p class="ah-sidebar__section-label"><?= esc($grup['label']) ?></p>
                            <ul class="ah-sidebar__list mb-3">
                                <?php foreach ($grup['items'] as $key => [$label, $path, $icon]): ?>
                                    <li>
                                        <a class="ah-sidebar__link" href="<?= site_url($path) ?>"
                                           <?= $activeNav === $key ? 'aria-current="page"' : '' ?>>
                                            <i class="bi <?= esc($icon, 'attr') ?>" aria-hidden="true"></i>
                                            <span><?= esc($label) ?></span>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endforeach; ?>
                    </nav>

                    <!-- Footer sidebar -->
                    <div class="ah-sidebar__footer">
                        <p class="ah-sidebar__label">
                            <i class="bi bi-person-circle" aria-hidden="true"></i>
                            <?= esc((string) session()->get('nama')) ?>
                        </p>
                        <form action="<?= site_url('logout') ?>" method="post">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-outline-primary w-100">
                                <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
                                Keluar
                            </button>
                        </form>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
