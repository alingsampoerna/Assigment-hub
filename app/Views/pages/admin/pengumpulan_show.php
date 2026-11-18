<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="ah-page">
    <div class="container-fluid px-3 px-lg-4">

        <div class="mb-4">
            <a class="ah-back" href="<?= site_url('admin/tugas/' . (int) $pengumpulan['tugas_id'] . '/pengumpulan') ?>">
                <i class="bi bi-arrow-left" aria-hidden="true"></i> Kembali ke Daftar Pengumpulan
            </a>
        </div>

        <?php
        $flashSuccess = session()->getFlashdata('success');
        $flashError   = session()->getFlashdata('error');
        $errors       = session()->getFlashdata('errors') ?? [];
        $old          = session()->getFlashdata('old') ?? [];
        ?>
        <?= $flashSuccess ? cc_alert('success', $flashSuccess) : '' ?>
        <?= $flashError   ? cc_alert('error',   $flashError)   : '' ?>
        <?= cc_error_summary($errors) ?>

        <div class="row g-4">

            <!-- Detail pengumpulan -->
            <div class="col-lg-7">
                <div class="ah-admin-panel">
                    <h1 class="h4 mb-1"><?= esc($pengumpulan['judul_tugas']) ?></h1>
                    <p class="ah-caption mb-4">
                        <?= esc($pengumpulan['kode_mata_kuliah'] . ' — ' . $pengumpulan['nama_mata_kuliah']) ?>
                    </p>

                    <?php
                    $badgeClass = $pengumpulan['status'] === 'terlambat' ? 'ah-badge--overdue' : 'ah-badge--submitted';
                    $badgeLabel = $pengumpulan['status'] === 'terlambat' ? 'Terlambat' : 'Tepat Waktu';
                    ?>

                    <dl class="ah-dl">
                        <dt>Mahasiswa</dt>
                        <dd>
                            <?= esc($pengumpulan['nama']) ?>
                            <span class="ah-caption ms-2 font-monospace"><?= esc($pengumpulan['nim']) ?></span>
                        </dd>

                        <dt>Tanggal Dikumpulkan</dt>
                        <dd><?= cc_datetime_id($pengumpulan['tanggal_pengumpulan']) ?></dd>

                        <dt>Deadline Tugas</dt>
                        <dd><?= cc_datetime_id($pengumpulan['deadline']) ?></dd>

                        <dt>Status</dt>
                        <dd><span class="ah-badge <?= $badgeClass ?>"><?= $badgeLabel ?></span></dd>

                        <?php if (! empty($pengumpulan['keterangan'])): ?>
                            <dt>Keterangan Mahasiswa</dt>
                            <dd><?= esc($pengumpulan['keterangan']) ?></dd>
                        <?php endif; ?>

                        <dt>Berkas</dt>
                        <dd>
                            <a class="btn btn-outline-primary btn-sm"
                               href="<?= site_url('admin/pengumpulan/' . (int) $pengumpulan['id'] . '/berkas') ?>">
                                <i class="bi bi-download" aria-hidden="true"></i>
                                <?= esc($pengumpulan['nama_file_asli'] ?? 'Unduh Berkas') ?>
                            </a>
                        </dd>
                    </dl>
                </div>
            </div>

            <!-- Form penilaian -->
            <div class="col-lg-5">
                <div class="ah-admin-panel">
                    <h2 class="h5 mb-3">
                        <?= $pengumpulan['penilaian_id'] ? 'Perbarui Penilaian' : 'Berikan Penilaian' ?>
                    </h2>

                    <form action="<?= site_url('admin/pengumpulan/' . (int) $pengumpulan['id'] . '/nilai') ?>"
                          method="post">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="nilai" class="form-label fw-semibold">
                                Nilai (0–100) <span class="text-danger">*</span>
                            </label>
                            <input type="number" id="nilai" name="nilai"
                                   class="form-control <?= isset($errors['nilai']) ? 'is-invalid' : '' ?>"
                                   min="0" max="100" required
                                   value="<?= esc($old['nilai'] ?? $pengumpulan['nilai'] ?? '') ?>">
                            <?php if (isset($errors['nilai'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['nilai']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="komentar" class="form-label fw-semibold">Komentar / Feedback</label>
                            <textarea id="komentar" name="komentar"
                                      class="form-control <?= isset($errors['komentar']) ? 'is-invalid' : '' ?>"
                                      rows="4" maxlength="1000"
                                      placeholder="Berikan feedback kepada mahasiswa…"><?= esc($old['komentar'] ?? $pengumpulan['komentar'] ?? '') ?></textarea>
                            <?php if (isset($errors['komentar'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['komentar']) ?></div>
                            <?php endif; ?>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-award" aria-hidden="true"></i>
                            <?= $pengumpulan['penilaian_id'] ? 'Perbarui Nilai' : 'Simpan Nilai' ?>
                        </button>
                    </form>

                    <?php if ($pengumpulan['penilaian_id']): ?>
                        <div class="mt-3 pt-3" style="border-top:1px solid var(--ah-border);">
                            <p class="ah-caption mb-0">
                                Dinilai oleh <?= esc($pengumpulan['nama_penilai'] ?? 'Admin') ?>
                                pada <?= cc_datetime_id($pengumpulan['tanggal_penilaian']) ?>.
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<?= $this->endSection() ?>
