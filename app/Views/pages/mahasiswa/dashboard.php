<?= $this->extend('layouts/student') ?>
<?= $this->section('content') ?>

<div class="ah-page">
    <div class="container">

        <div class="ah-page__header">
            <div>
                <h1>Halo, <?= esc($namaDepan) ?>!</h1>
                <p class="text-muted-ah mb-0">Pantau tugas dan pengumpulan Anda di sini.</p>
            </div>
            <a class="btn btn-primary" href="<?= site_url('mahasiswa/tugas') ?>">
                <i class="bi bi-journal-text" aria-hidden="true"></i> Lihat Tugas
            </a>
        </div>

        <?php $flashSuccess = session()->getFlashdata('success'); ?>
        <?= $flashSuccess ? cc_alert('success', $flashSuccess) : '' ?>
        <?php $flashError = session()->getFlashdata('error'); ?>
        <?= $flashError ? cc_alert('error', $flashError) : '' ?>

        <?php if ($tugasBelumKumpul > 0): ?>
            <div class="cc-alert cc-alert--warning" role="status">
                <i class="bi bi-exclamation-circle-fill cc-alert__icon" aria-hidden="true"></i>
                <div>
                    <p><?= (int) $tugasBelumKumpul ?> tugas belum dikumpulkan.
                        <a href="<?= site_url('mahasiswa/tugas?status=belum') ?>">Lihat sekarang</a>
                    </p>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($belumBaca > 0): ?>
            <div class="cc-alert cc-alert--info" role="status">
                <i class="bi bi-bell-fill cc-alert__icon" aria-hidden="true"></i>
                <div>
                    <p><?= (int) $belumBaca ?> notifikasi belum dibaca.
                        <a href="<?= site_url('mahasiswa/notifikasi') ?>">Lihat notifikasi</a>
                    </p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Statistik -->
        <section aria-labelledby="judul-statistik" class="mb-5">
            <h2 id="judul-statistik" class="h4 mb-3">Ringkasan Pengumpulan</h2>
            <div class="row g-3">
                <?php
                $cards = [
                    ['label' => 'Total Dikumpulkan', 'value' => $stats['total'],       'icon' => 'bi-cloud-upload',         'mod' => 'total'],
                    ['label' => 'Tepat Waktu',        'value' => $stats['tepat_waktu'], 'icon' => 'bi-check-circle-fill',    'mod' => 'done'],
                    ['label' => 'Terlambat',          'value' => $stats['terlambat'],   'icon' => 'bi-clock-history',        'mod' => 'processing'],
                    ['label' => 'Sudah Dinilai',      'value' => $stats['dinilai'],     'icon' => 'bi-award-fill',           'mod' => 'waiting'],
                ];
                foreach ($cards as $card):
                ?>
                    <div class="col-6 col-lg-3">
                        <div class="cc-stat cc-stat--<?= esc($card['mod']) ?>">
                            <span class="cc-stat__icon" aria-hidden="true">
                                <i class="bi <?= esc($card['icon']) ?>"></i>
                            </span>
                            <div>
                                <p class="cc-stat__label"><?= esc($card['label']) ?></p>
                                <p class="cc-stat__value"><?= (int) $card['value'] ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Tugas aktif -->
        <section aria-labelledby="judul-tugas">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <h2 id="judul-tugas" class="h4 mb-0">Tugas Mendatang</h2>
                <?php if ($tugasAktif !== []): ?>
                    <a href="<?= site_url('mahasiswa/tugas') ?>">
                        Lihat semua <i class="bi bi-arrow-right" aria-hidden="true"></i>
                    </a>
                <?php endif; ?>
            </div>

            <div class="ah-panel">
                <?php if ($tugasAktif === []): ?>
                    <div class="ah-empty">
                        <i class="bi bi-journal-text ah-empty__icon" aria-hidden="true"></i>
                        <h3 class="mt-3">Tidak ada tugas aktif</h3>
                        <p class="text-muted-ah">Tugas dari kelas yang Anda ikuti akan muncul di sini.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table ah-table">
                            <thead>
                                <tr>
                                    <th>Tugas</th>
                                    <th>Deadline</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tugasAktif as $t):
                                    $lewat   = (bool) $t['sudah_lewat'];
                                    $selisih = (int) round((strtotime($t['deadline']) - time()) / 86400);

                                    if ($t['pengumpulan_id']) {
                                        $badgeClass = $t['status_pengumpulan'] === 'terlambat' ? 'ah-badge--overdue' : 'ah-badge--submitted';
                                        $badgeLabel = $t['status_pengumpulan'] === 'terlambat' ? 'Terlambat' : 'Sudah Dikumpulkan';
                                    } else {
                                        $badgeClass = $lewat ? 'ah-badge--overdue' : 'ah-badge--pending';
                                        $badgeLabel = $lewat ? 'Melewati Deadline' : 'Belum Dikumpulkan';
                                    }
                                ?>
                                    <tr>
                                        <td>
                                            <p class="ah-table__title mb-0">
                                                <a href="<?= site_url('mahasiswa/tugas/' . (int) $t['id']) ?>">
                                                    <?= esc($t['judul']) ?>
                                                </a>
                                            </p>
                                            <p class="ah-table__sub"><?= esc($t['kode_mata_kuliah'] . ' — ' . $t['nama_mata_kuliah']) ?></p>
                                        </td>
                                        <td>
                                            <span class="ah-deadline ah-deadline--<?= $lewat ? 'passed' : ($selisih <= 1 ? 'urgent' : ($selisih <= 3 ? 'near' : 'ok')) ?>">
                                                <i class="bi bi-clock" aria-hidden="true"></i>
                                                <?= cc_datetime_id($t['deadline']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="ah-badge <?= $badgeClass ?>"><?= esc($badgeLabel) ?></span>
                                        </td>
                                        <td>
                                            <a class="btn btn-sm btn-outline-primary"
                                               href="<?= site_url('mahasiswa/tugas/' . (int) $t['id']) ?>">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </section>

    </div>
</div>

<?= $this->endSection() ?>
