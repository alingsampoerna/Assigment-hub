<?= $this->extend('layouts/dosen') ?>
<?= $this->section('content') ?>

<div class="ah-page">
    <div class="container-fluid px-3 px-lg-4">

        <div class="ah-page__header">
            <div>
                <h1>Notifikasi</h1>
                <p class="text-muted-ah mb-0">
                    <?= (int) $belumBaca ?> belum dibaca dari <?= (int) $total ?> notifikasi.
                </p>
            </div>
            <?php if ($belumBaca > 0): ?>
                <form action="<?= site_url('dosen/notifikasi/baca-semua') ?>" method="post">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-check2-all" aria-hidden="true"></i> Tandai Semua Dibaca
                    </button>
                </form>
            <?php endif; ?>
        </div>

        <?php $flash = session()->getFlashdata('success'); ?>
        <?= $flash ? cc_alert('success', $flash) : '' ?>

        <div class="ah-panel">
            <?php if ($items === []): ?>
                <div class="ah-empty">
                    <i class="bi bi-bell ah-empty__icon" aria-hidden="true"></i>
                    <h3 class="mt-3">Belum ada notifikasi</h3>
                    <p class="text-muted-ah mb-0">Notifikasi pengumpulan dari mahasiswa akan muncul di sini.</p>
                </div>
            <?php else: ?>
                <ul class="list-unstyled mb-0">
                    <?php foreach ($items as $n): ?>
                        <li class="p-3 d-flex justify-content-between align-items-start gap-3
                                   <?= $n['status_baca'] === 'belum' ? 'ah-notif--unread' : '' ?>"
                            style="border-bottom:1px solid var(--ah-border);">
                            <div class="flex-grow-1">
                                <p class="fw-semibold mb-1 <?= $n['status_baca'] === 'belum' ? '' : 'text-muted-ah' ?>">
                                    <?= esc($n['judul']) ?>
                                </p>
                                <p class="mb-1"><?= esc($n['pesan']) ?></p>
                                <p class="ah-caption mb-0"><?= cc_datetime_id($n['created_at']) ?></p>
                                <?php if (! empty($n['link'])): ?>
                                    <a href="<?= esc($n['link']) ?>" class="btn btn-sm btn-outline-primary mt-2">
                                        Lihat Pengumpulan
                                    </a>
                                <?php endif; ?>
                            </div>
                            <?php if ($n['status_baca'] === 'belum'): ?>
                                <form action="<?= site_url('dosen/notifikasi/baca/' . (int) $n['id']) ?>"
                                      method="post" class="flex-shrink-0">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-secondary"
                                            title="Tandai sudah dibaca">
                                        <i class="bi bi-check2" aria-hidden="true"></i>
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="ah-badge ah-badge--active flex-shrink-0">Dibaca</span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <?php if ($totalPages > 1): ?>
                    <div class="p-3">
                        <?= $this->setVar('baseUrl', site_url('dosen/notifikasi'))
                                 ->include('partials/pagination') ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

    </div>
</div>

<?= $this->endSection() ?>
