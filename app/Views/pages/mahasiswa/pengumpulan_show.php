<?= $this->extend('layouts/student') ?>
<?= $this->section('content') ?>

<div class="ah-page">
    <div class="container">

        <div class="mb-4">
            <a class="ah-back" href="<?= site_url('mahasiswa/tugas/' . (int) $pengumpulan['tugas_id']) ?>">
                <i class="bi bi-arrow-left" aria-hidden="true"></i> Kembali ke Tugas
            </a>
        </div>

        <?php
        $flashSuccess = session()->getFlashdata('success');
        $flashError   = session()->getFlashdata('error');
        $errors       = session()->getFlashdata('errors') ?? [];
        ?>
        <?= $flashSuccess ? cc_alert('success', $flashSuccess) : '' ?>
        <?= $flashError   ? cc_alert('error',   $flashError)   : '' ?>
        <?= cc_error_summary($errors) ?>

        <div class="row g-4">

            <!-- Detail pengumpulan -->
            <div class="col-lg-7">
                <div class="ah-admin-panel">
                    <h1 class="h4 mb-1"><?= esc($pengumpulan['judul_tugas']) ?></h1>
                    <p class="ah-caption mb-4"><?= esc($pengumpulan['kode_mata_kuliah'] . ' — ' . $pengumpulan['nama_mata_kuliah']) ?></p>

                    <?php
                    $badgeClass = $pengumpulan['status'] === 'terlambat' ? 'ah-badge--overdue' : 'ah-badge--submitted';
                    $badgeLabel = $pengumpulan['status'] === 'terlambat' ? 'Terlambat' : 'Tepat Waktu';
                    ?>

                    <dl class="ah-dl">
                        <dt>Status Pengumpulan</dt>
                        <dd><span class="ah-badge <?= $badgeClass ?>"><?= $badgeLabel ?></span></dd>

                        <dt>Tanggal Dikumpulkan</dt>
                        <dd><?= cc_datetime_id($pengumpulan['tanggal_pengumpulan']) ?></dd>

                        <dt>Deadline Tugas</dt>
                        <dd><?= cc_datetime_id($pengumpulan['deadline']) ?></dd>

                        <?php if (! empty($pengumpulan['keterangan'])): ?>
                            <dt>Keterangan</dt>
                            <dd><?= esc($pengumpulan['keterangan']) ?></dd>
                        <?php endif; ?>

                        <dt>File</dt>
                        <dd>
                            <a class="btn btn-sm btn-outline-primary"
                               href="<?= site_url('mahasiswa/pengumpulan/' . (int) $pengumpulan['id'] . '/berkas') ?>">
                                <i class="bi bi-download" aria-hidden="true"></i>
                                <?= esc($pengumpulan['nama_file_asli'] ?? 'Unduh Berkas') ?>
                            </a>
                        </dd>
                    </dl>

                    <div class="d-flex gap-2 mt-3 pt-3" style="border-top:1px solid var(--ah-border);">
                        <?php if (count($riwayat) > 0): ?>
                            <a class="btn btn-outline-secondary btn-sm"
                               href="<?= site_url('mahasiswa/pengumpulan/' . (int) $pengumpulan['id'] . '/riwayat') ?>">
                                <i class="bi bi-clock-history" aria-hidden="true"></i> Riwayat
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Penilaian -->
            <div class="col-lg-5">
                <div class="ah-admin-panel">
                    <h2 class="h5 mb-3">Penilaian</h2>

                    <?php if ($pengumpulan['nilai'] !== null): ?>
                        <div class="text-center py-3">
                            <div class="display-4 fw-bold" style="color:var(--ah-primary);">
                                <?= (int) $pengumpulan['nilai'] ?>
                            </div>
                            <p class="ah-caption mb-3">dari 100</p>
                        </div>

                        <?php if (! empty($pengumpulan['komentar'])): ?>
                            <div class="p-3 mb-3" style="background:var(--ah-surface-2);border-radius:var(--ah-radius-md);border:1px solid var(--ah-border);">
                                <p class="fw-semibold mb-1 small">Komentar:</p>
                                <p class="mb-0"><?= esc($pengumpulan['komentar']) ?></p>
                            </div>
                        <?php endif; ?>

                        <p class="ah-caption mb-0">
                            Dinilai oleh <?= esc($pengumpulan['nama_penilai'] ?? 'Admin') ?>
                            pada <?= cc_datetime_id($pengumpulan['tanggal_penilaian']) ?>.
                        </p>
                    <?php else: ?>
                        <div class="ah-empty py-4">
                            <i class="bi bi-hourglass-split ah-empty__icon" aria-hidden="true"></i>
                            <p class="text-muted-ah mt-2 mb-0">Belum dinilai.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<?= $this->endSection() ?>
