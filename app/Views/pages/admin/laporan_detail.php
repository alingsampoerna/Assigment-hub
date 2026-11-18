<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php
$kodeLaporan = sprintf('CC-%04d', (int) $report['id']);
$actionUrl   = site_url('admin/laporan/' . (int) $report['id'] . '/status');

$formError = session('form_error');           // proses | tolak | selesai
$errors    = session('errors') ?? [];
$old       = session('old') ?? [];
$success   = session()->getFlashdata('success');
$flashErr  = session()->getFlashdata('error');

$errProses  = $formError === 'proses'  ? $errors : [];
$errTolak   = $formError === 'tolak'   ? $errors : [];
$errSelesai = $formError === 'selesai' ? $errors : [];
?>

<div class="cc-page">
    <div class="container-fluid px-3 px-lg-4">

        <a class="cc-back" href="<?= site_url('admin/laporan') ?>">
            <i class="bi bi-arrow-left" aria-hidden="true"></i> Kembali ke Semua Laporan
        </a>

        <div class="cc-page__header">
            <div>
                <p class="cc-caption mb-1">Nomor laporan <?= esc($kodeLaporan) ?></p>
                <h1><?= esc($report['judul']) ?></h1>
            </div>
            <div><?= cc_status_badge($report['status']) ?></div>
        </div>

        <?= $success ? cc_alert('success', $success) : '' ?>
        <?= $flashErr ? cc_alert('error', $flashErr) : '' ?>
        <?php if ($alasanDitolak): ?>
            <?= cc_alert('error', $alasanDitolak, 'Laporan ditolak') ?>
        <?php endif; ?>

        <div class="row g-4">

            <!-- Informasi laporan -->
            <div class="col-xl-7">
                <section class="cc-card" aria-labelledby="judul-isi">
                    <h2 id="judul-isi" class="h3 mb-4">Informasi Laporan</h2>

                    <dl class="cc-dl">
                        <dt>Pelapor</dt>
                        <dd><?= esc($report['pelapor']) ?> <span class="text-muted-cc">(NIM <?= esc($report['nim']) ?>)</span></dd>

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

            <div class="col-xl-5">

                <!-- Panel tindakan admin -->
                <section class="cc-card mb-4" aria-labelledby="judul-tindakan">
                    <h2 id="judul-tindakan" class="h3 mb-3">Tindakan Admin</h2>

                    <?php if ($report['status'] === 'menunggu'): ?>

                        <?= cc_error_summary($errProses) ?>

                        <form action="<?= $actionUrl ?>" method="post" novalidate>
                            <?= csrf_field() ?>
                            <input type="hidden" name="aksi" value="proses">

                            <?= cc_select([
                                'id'          => 'petugas_id',
                                'name'        => 'petugas_id',
                                'label'       => 'Tugaskan petugas',
                                'options'     => $opsiPetugas,
                                'selected'    => $old['petugas_id'] ?? '',
                                'error'       => $errProses['petugas_id'] ?? null,
                                'placeholder' => 'Pilih petugas',
                                'hint'        => 'Laporan akan berstatus Diproses setelah petugas ditugaskan.',
                            ]) ?>

                            <?= cc_input([
                                'id'       => 'catatan_proses',
                                'name'     => 'catatan_proses',
                                'label'    => 'Catatan (opsional)',
                                'value'    => $old['catatan_proses'] ?? '',
                                'error'    => $errProses['catatan_proses'] ?? null,
                                'required' => false,
                            ]) ?>

                            <div class="cc-action-buttons">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-gear-fill" aria-hidden="true"></i> Proses Laporan
                                </button>
                                <button type="button" class="btn btn-outline-danger"
                                        data-bs-toggle="modal" data-bs-target="#modal-tolak">
                                    <i class="bi bi-x-circle" aria-hidden="true"></i> Tolak Laporan
                                </button>
                            </div>
                        </form>

                    <?php elseif ($report['status'] === 'diproses'): ?>

                        <p class="mb-3">
                            Petugas:
                            <strong><?= $report['petugas'] ? esc($report['petugas']) : '-' ?></strong>
                        </p>

                        <?= cc_error_summary($errSelesai) ?>

                        <form action="<?= $actionUrl ?>" method="post" novalidate>
                            <?= csrf_field() ?>
                            <input type="hidden" name="aksi" value="selesai">

                            <?= cc_textarea([
                                'id'    => 'catatan_selesai',
                                'name'  => 'catatan_selesai',
                                'label' => 'Catatan penyelesaian',
                                'value' => $old['catatan_selesai'] ?? '',
                                'error' => $errSelesai['catatan_selesai'] ?? null,
                                'hint'  => 'Jelaskan apa yang sudah diperbaiki. Catatan ini tampil di riwayat.',
                                'rows'  => 3,
                            ]) ?>

                            <div class="cc-action-buttons">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle-fill" aria-hidden="true"></i> Tandai Selesai
                                </button>
                            </div>
                        </form>

                    <?php else: ?>

                        <p class="cc-final-note">
                            <i class="bi bi-lock" aria-hidden="true"></i>
                            <span>
                                Laporan ini berstatus <?= cc_status_badge($report['status']) ?>
                                dan sudah final, sehingga statusnya tidak dapat diubah lagi.
                            </span>
                        </p>

                    <?php endif; ?>
                </section>

                <!-- Riwayat status -->
                <section class="cc-card" aria-labelledby="judul-riwayat">
                    <h2 id="judul-riwayat" class="h3 mb-4">Riwayat Status</h2>
                    <?= $this->setVar('riwayat', $riwayat)
                             ->setVar('status', $report['status'])
                             ->include('partials/status_timeline') ?>
                </section>
            </div>

        </div>
    </div>
</div>

<?php if ($report['status'] === 'menunggu'): ?>
    <!-- Modal: tolak laporan -->
    <div class="modal fade" id="modal-tolak" tabindex="-1"
         aria-labelledby="modal-tolak-judul" aria-hidden="true"
         <?= $errTolak !== [] ? 'data-auto-open="true"' : '' ?>>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="<?= $actionUrl ?>" method="post" novalidate>
                    <?= csrf_field() ?>
                    <input type="hidden" name="aksi" value="tolak">

                    <div class="modal-header">
                        <h2 class="modal-title h5" id="modal-tolak-judul">Tolak laporan</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>

                    <div class="modal-body">
                        <?= cc_error_summary($errTolak) ?>

                        <p>Anda akan menolak laporan <strong><?= esc($report['judul']) ?></strong>.
                            Setelah ditolak, status tidak dapat diubah lagi.</p>

                        <?= cc_textarea([
                            'id'    => 'alasan',
                            'name'  => 'alasan',
                            'label' => 'Alasan penolakan',
                            'value' => $old['alasan'] ?? '',
                            'error' => $errTolak['alasan'] ?? null,
                            'hint'  => 'Alasan ini akan terlihat oleh mahasiswa pelapor.',
                            'rows'  => 4,
                        ]) ?>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Tolak Laporan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/admin-laporan.js') ?>"></script>
<?= $this->endSection() ?>