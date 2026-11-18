<?= $this->extend('layouts/student') ?>

<?= $this->section('content') ?>
<?php
$kodeLaporan = sprintf('CC-%04d', (int) $report['id']);

// Tahap berikutnya yang belum terjadi (hanya untuk status yang belum final)
$langkahBerikut = [
    'menunggu' => 'Menunggu ditinjau oleh admin',
    'diproses' => 'Menunggu penyelesaian oleh petugas',
];
$jumlahRiwayat = count($riwayat);
?>

<div class="cc-page">
    <div class="container">

        <a class="cc-back" href="<?= site_url('mahasiswa/laporan') ?>">
            <i class="bi bi-arrow-left" aria-hidden="true"></i> Kembali ke Laporan Saya
        </a>

        <div class="cc-page__header">
            <div>
                <p class="cc-caption mb-1">Nomor laporan <?= esc($kodeLaporan) ?></p>
                <h1><?= esc($report['judul']) ?></h1>
            </div>
            <div><?= cc_status_badge($report['status']) ?></div>
        </div>

        <?php if ($alasanDitolak): ?>
            <?= cc_alert('error', $alasanDitolak, 'Laporan ditolak') ?>
        <?php endif; ?>

        <div class="row g-4">

            <!-- Isi laporan -->
            <div class="col-lg-7">
                <section class="cc-card" aria-labelledby="judul-isi">
                    <h2 id="judul-isi" class="h3 mb-4">Informasi Laporan</h2>

                    <dl class="cc-dl">
                        <dt>Kategori</dt>
                        <dd><?= esc($report['kategori']) ?></dd>

                        <dt>Lokasi</dt>
                        <dd><?= esc($report['lokasi']) ?></dd>

                        <dt>Dibuat pada</dt>
                        <dd>
                            <time datetime="<?= esc(date('c', strtotime($report['created_at'])), 'attr') ?>">
                                <?= esc(cc_datetime_id($report['created_at'])) ?>
                            </time>
                        </dd>

                        <dt>Petugas</dt>
                        <dd><?= $report['petugas'] ? esc($report['petugas']) : '<span class="text-muted-cc">Belum ditugaskan</span>' ?></dd>
                    </dl>

                    <h3 class="h5">Deskripsi kerusakan</h3>
                    <p><?= nl2br(esc($report['deskripsi'])) ?></p>

                    <h3 class="h5 mt-4">Foto kerusakan</h3>
                    <?php if ($fotoUrl): ?>
                        <div class="cc-detail-photo">
                            <img src="<?= esc($fotoUrl, 'attr') ?>" alt="<?= esc($fotoAlt, 'attr') ?>" loading="lazy">
                        </div>
                        <p class="mt-2 mb-0">
                            <a href="<?= esc($fotoUrl, 'attr') ?>" target="_blank" rel="noopener">
                                Buka foto ukuran penuh<span class="visually-hidden"> (di tab baru)</span>
                            </a>
                        </p>
                    <?php else: ?>
                        <p class="text-muted-cc mb-0">Tidak ada foto yang dilampirkan.</p>
                    <?php endif; ?>
                </section>
            </div>

            <!-- Riwayat status -->
            <div class="col-lg-5">
                <section class="cc-card" aria-labelledby="judul-riwayat">
                    <h2 id="judul-riwayat" class="h3 mb-4">Riwayat Status</h2>

                    <?php if ($riwayat === []): ?>
                        <p class="text-muted-cc mb-0">Belum ada riwayat.</p>
                    <?php else: ?>
                        <ol class="cc-timeline">
                            <?php foreach ($riwayat as $i => $item): ?>
                                <?php $terbaru = ($i === $jumlahRiwayat - 1); ?>
                                <li class="cc-timeline__item"<?= $terbaru ? ' aria-current="step"' : '' ?>>
                                    <span class="cc-timeline__dot" aria-hidden="true"></span>
                                    <?= cc_status_badge($item['status']) ?>
                                    <?php if ($terbaru): ?>
                                        <span class="cc-timeline__current">Status saat ini</span>
                                    <?php endif; ?>
                                    <p class="cc-timeline__time">
                                        <time datetime="<?= esc(date('c', strtotime($item['waktu'])), 'attr') ?>">
                                            <?= esc(cc_datetime_id($item['waktu'])) ?>
                                        </time>
                                    </p>
                                    <?php if (! empty($item['catatan'])): ?>
                                        <p class="cc-timeline__note"><?= esc($item['catatan']) ?></p>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>

                            <?php if (isset($langkahBerikut[$report['status']])): ?>
                                <li class="cc-timeline__item is-pending">
                                    <span class="cc-timeline__dot" aria-hidden="true"></span>
                                    <p class="cc-timeline__pending">
                                        Tahap berikutnya: <?= esc($langkahBerikut[$report['status']]) ?>
                                    </p>
                                </li>
                            <?php endif; ?>
                        </ol>
                    <?php endif; ?>
                </section>
            </div>

        </div>
    </div>
</div>
<?= $this->endSection() ?>