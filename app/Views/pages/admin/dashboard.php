<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="ah-page">
    <div class="container-fluid px-3 px-lg-4">

        <div class="ah-page__header">
            <div>
                <h1>Dashboard Admin</h1>
                <p class="text-muted-ah mb-0">Ringkasan aktivitas Assignment Hub.</p>
            </div>
            <a class="btn btn-primary" href="<?= site_url('admin/tugas/baru') ?>">
                <i class="bi bi-plus-circle" aria-hidden="true"></i> Buat Tugas
            </a>
        </div>

        <?php
        $flashSuccess = session()->getFlashdata('success');
        $flashError   = session()->getFlashdata('error');
        ?>
        <?= $flashSuccess ? cc_alert('success', $flashSuccess) : '' ?>
        <?= $flashError   ? cc_alert('error',   $flashError)   : '' ?>

        <?php if ($belumBaca > 0): ?>
            <div class="cc-alert cc-alert--warning" role="status">
                <i class="bi bi-bell-fill cc-alert__icon" aria-hidden="true"></i>
                <div>
                    <p><?= (int) $belumBaca ?> notifikasi belum dibaca.
                        <a href="<?= site_url('admin/notifikasi') ?>">Lihat notifikasi</a>
                    </p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Stat cards -->
        <div class="row g-3 mb-4">
            <?php
            $cards = [
                ['label' => 'Tugas Aktif',     'value' => $totalTugas,     'icon' => 'bi-journal-text',   'color' => '--ah-primary'],
                ['label' => 'Mahasiswa Aktif',  'value' => $totalMahasiswa, 'icon' => 'bi-person-badge',   'color' => '--ah-success'],
                ['label' => 'Kelas Aktif',      'value' => $totalKelas,     'icon' => 'bi-people',         'color' => '--ah-warning'],
                ['label' => 'Mata Kuliah',       'value' => $totalMK,        'icon' => 'bi-book',           'color' => '--ah-info'],
            ];
            foreach ($cards as $c):
            ?>
                <div class="col-6 col-xl-3">
                    <div class="ah-admin-panel h-100 d-flex align-items-center gap-3 p-3">
                        <span class="ah-feature-icon mb-0 flex-shrink-0" style="background:color-mix(in srgb, var(<?= $c['color'] ?>) 15%, transparent);">
                            <i class="bi <?= $c['icon'] ?>" style="color:var(<?= $c['color'] ?>);" aria-hidden="true"></i>
                        </span>
                        <div>
                            <p class="ah-caption mb-0"><?= esc($c['label']) ?></p>
                            <p class="h3 mb-0 fw-bold"><?= (int) $c['value'] ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="row g-4">

            <!-- Tugas aktif dengan deadline terdekat -->
            <div class="col-xl-7">
                <div class="ah-admin-panel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h5 mb-0">Tugas Aktif (Deadline Terdekat)</h2>
                        <a href="<?= site_url('admin/tugas') ?>" class="ah-caption">Lihat semua</a>
                    </div>

                    <?php if ($tugasAktif === []): ?>
                        <div class="ah-empty py-4">
                            <i class="bi bi-journal-text ah-empty__icon" aria-hidden="true"></i>
                            <p class="text-muted-ah mt-2 mb-0">Belum ada tugas aktif.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table ah-table">
                                <thead>
                                    <tr>
                                        <th>Judul Tugas</th>
                                        <th>Mata Kuliah</th>
                                        <th>Deadline</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tugasAktif as $t):
                                        $lewat = strtotime($t['deadline']) < time();
                                        $selisih = (int) round((strtotime($t['deadline']) - time()) / 86400);
                                    ?>
                                        <tr>
                                            <td>
                                                <a href="<?= site_url('admin/tugas/' . (int) $t['id']) ?>">
                                                    <?= esc($t['judul']) ?>
                                                </a>
                                            </td>
                                            <td class="ah-caption"><?= esc($t['nama_mata_kuliah']) ?></td>
                                            <td>
                                                <span class="ah-deadline ah-deadline--<?= $lewat ? 'passed' : ($selisih <= 1 ? 'urgent' : ($selisih <= 3 ? 'near' : 'ok')) ?>">
                                                    <i class="bi bi-clock" aria-hidden="true"></i>
                                                    <?= cc_datetime_id($t['deadline']) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Pengumpulan terbaru -->
            <div class="col-xl-5">
                <div class="ah-admin-panel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h5 mb-0">Pengumpulan Terbaru</h2>
                        <a href="<?= site_url('admin/tugas') ?>" class="ah-caption">Lihat semua</a>
                    </div>

                    <?php if ($pengumpulanTerbaru === []): ?>
                        <div class="ah-empty py-4">
                            <i class="bi bi-cloud-upload ah-empty__icon" aria-hidden="true"></i>
                            <p class="text-muted-ah mt-2 mb-0">Belum ada pengumpulan.</p>
                        </div>
                    <?php else: ?>
                        <ul class="list-unstyled mb-0">
                            <?php foreach ($pengumpulanTerbaru as $p):
                                $badgeClass = $p['status_pengumpulan'] === 'terlambat' ? 'ah-badge--overdue' : 'ah-badge--submitted';
                                $badgeLabel = $p['status_pengumpulan'] === 'terlambat' ? 'Terlambat' : 'Tepat Waktu';
                            ?>
                                <li class="py-2" style="border-bottom:1px solid var(--ah-border);">
                                    <div class="d-flex justify-content-between align-items-start gap-2">
                                        <div>
                                            <p class="fw-semibold mb-0 small">
                                                <a href="<?= site_url('admin/pengumpulan/' . (int) $p['id']) ?>">
                                                    <?= esc($p['nama']) ?>
                                                </a>
                                            </p>
                                            <p class="ah-caption mb-0"><?= esc($p['judul_tugas']) ?></p>
                                        </div>
                                        <span class="ah-badge <?= $badgeClass ?> flex-shrink-0"><?= $badgeLabel ?></span>
                                    </div>
                                    <p class="ah-caption mb-0 mt-1"><?= cc_datetime_id($p['tanggal_pengumpulan']) ?></p>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<?= $this->endSection() ?>
