<?= $this->extend('layouts/student') ?>
<?= $this->section('content') ?>

<div class="ah-page">
    <div class="container">

        <div class="mb-4">
            <a class="ah-back" href="<?= site_url('mahasiswa/tugas') ?>">
                <i class="bi bi-arrow-left" aria-hidden="true"></i> Kembali ke Daftar Tugas
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

        <?php
        $now      = new DateTime();
        $deadline = new DateTime($tugas['deadline']);
        $lewat    = $now > $deadline;
        $selisih  = (int) $now->diff($deadline)->format('%r%a');
        ?>

        <div class="row g-4">

            <!-- Detail tugas -->
            <div class="col-lg-7">
                <div class="ah-admin-panel">
                    <p class="ah-caption mb-1"><?= esc($tugas['kode_mata_kuliah'] . ' — ' . $tugas['nama_mata_kuliah']) ?></p>
                    <h1 class="h3 mb-3"><?= esc($tugas['judul']) ?></h1>

                    <?php if (! empty($tugas['deskripsi'])): ?>
                        <div class="mb-4 p-3" style="background:var(--ah-surface-2);border-radius:var(--ah-radius-md);border:1px solid var(--ah-border);">
                            <p class="fw-semibold mb-2">Instruksi</p>
                            <p class="mb-0" style="white-space:pre-line;"><?= esc($tugas['deskripsi']) ?></p>
                        </div>
                    <?php endif; ?>

                    <dl class="ah-dl">
                        <dt>Tanggal Mulai</dt>
                        <dd><?= cc_datetime_id($tugas['tanggal_mulai']) ?></dd>

                        <dt>Deadline</dt>
                        <dd>
                            <span class="ah-deadline ah-deadline--<?= $lewat ? 'passed' : ($selisih <= 1 ? 'urgent' : ($selisih <= 3 ? 'near' : 'ok')) ?>">
                                <i class="bi bi-clock" aria-hidden="true"></i>
                                <?= cc_datetime_id($tugas['deadline']) ?>
                                <?php if ($lewat): ?>
                                    <span class="ms-1">(Sudah lewat)</span>
                                <?php elseif ($selisih === 0): ?>
                                    <span class="ms-1">(Hari ini)</span>
                                <?php else: ?>
                                    <span class="ms-1">(<?= $selisih ?> hari lagi)</span>
                                <?php endif; ?>
                            </span>
                        </dd>

                        <dt>Batas Ukuran File</dt>
                        <dd><?= round($tugas['batas_ukuran_file'] / 1024, 1) ?> MB</dd>

                        <dt>Revisi</dt>
                        <dd><?= $tugas['izin_revisi'] ? 'Diizinkan sebelum deadline' : 'Tidak diizinkan' ?></dd>

                        <?php if (! empty($tugas['file_lampiran'])): ?>
                            <dt>Lampiran</dt>
                            <dd>
                                <a class="btn btn-sm btn-outline-primary"
                                   href="<?= site_url('mahasiswa/tugas/' . (int) $tugas['id'] . '/lampiran') ?>">
                                    <i class="bi bi-download" aria-hidden="true"></i>
                                    <?= esc($tugas['nama_lampiran_asli'] ?? 'Unduh Lampiran') ?>
                                </a>
                            </dd>
                        <?php endif; ?>
                    </dl>
                </div>
            </div>

            <!-- Panel pengumpulan -->
            <div class="col-lg-5">
                <div class="ah-admin-panel">

                    <?php if ($pengumpulan !== null): ?>
                        <!-- Sudah mengumpulkan -->
                        <h2 class="h5 mb-3">Status Pengumpulan</h2>

                        <?php
                        $badgeClass = $pengumpulan['status'] === 'terlambat' ? 'ah-badge--overdue' : 'ah-badge--submitted';
                        $badgeLabel = $pengumpulan['status'] === 'terlambat' ? 'Terlambat' : 'Tepat Waktu';
                        ?>
                        <p>
                            <span class="ah-badge <?= $badgeClass ?> fs-6"><?= $badgeLabel ?></span>
                        </p>
                        <p class="ah-caption mb-3">
                            Dikumpulkan: <?= cc_datetime_id($pengumpulan['tanggal_pengumpulan']) ?>
                        </p>

                        <div class="d-flex flex-wrap gap-2">
                            <a class="btn btn-outline-primary"
                               href="<?= site_url('mahasiswa/pengumpulan/' . (int) $pengumpulan['id']) ?>">
                                <i class="bi bi-eye" aria-hidden="true"></i> Lihat Detail
                            </a>

                            <?php if ($tugas['izin_revisi'] && ! $lewat): ?>
                                <button type="button" class="btn btn-outline-warning"
                                        data-bs-toggle="collapse" data-bs-target="#form-revisi">
                                    <i class="bi bi-pencil" aria-hidden="true"></i> Revisi
                                </button>
                            <?php endif; ?>
                        </div>

                        <!-- Form revisi (tersembunyi) -->
                        <?php if ($tugas['izin_revisi'] && ! $lewat): ?>
                            <div class="collapse mt-4" id="form-revisi">
                                <form action="<?= site_url('mahasiswa/pengumpulan/' . (int) $pengumpulan['id'] . '/revisi') ?>"
                                      method="post" enctype="multipart/form-data">
                                    <?= csrf_field() ?>
                                    <div class="mb-3">
                                        <label for="file_revisi" class="form-label fw-semibold">
                                            File Pengganti <span class="text-danger">*</span>
                                        </label>
                                        <input type="file" id="file_revisi" name="file_tugas"
                                               class="form-control" required
                                               accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip">
                                        <div class="form-text">Format: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, ZIP. Maks <?= round($tugas['batas_ukuran_file'] / 1024, 1) ?> MB.</div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="ket_revisi" class="form-label fw-semibold">Keterangan</label>
                                        <textarea id="ket_revisi" name="keterangan" class="form-control"
                                                  rows="2" maxlength="300" placeholder="Opsional…"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="bi bi-upload" aria-hidden="true"></i> Kirim Revisi
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>

                    <?php elseif (! $lewat): ?>
                        <!-- Belum mengumpulkan, masih bisa -->
                        <h2 class="h5 mb-3">Kumpulkan Tugas</h2>
                        <form action="<?= site_url('mahasiswa/tugas/' . (int) $tugas['id'] . '/kumpul') ?>"
                              method="post" enctype="multipart/form-data">
                            <?= csrf_field() ?>

                            <div class="mb-3">
                                <label for="file_tugas" class="form-label fw-semibold">
                                    File Tugas <span class="text-danger">*</span>
                                </label>
                                <input type="file" id="file_tugas" name="file_tugas"
                                       class="form-control" required
                                       accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip">
                                <div class="form-text">
                                    Format: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, ZIP.<br>
                                    Maks <?= round($tugas['batas_ukuran_file'] / 1024, 1) ?> MB.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="keterangan" class="form-label fw-semibold">Keterangan</label>
                                <textarea id="keterangan" name="keterangan" class="form-control"
                                          rows="3" maxlength="300"
                                          placeholder="Catatan tambahan untuk dosen (opsional)…"></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-upload" aria-hidden="true"></i> Kumpulkan Tugas
                            </button>
                        </form>

                    <?php else: ?>
                        <!-- Deadline sudah lewat, belum mengumpulkan -->
                        <div class="ah-empty py-3">
                            <i class="bi bi-clock-history ah-empty__icon text-danger" aria-hidden="true"></i>
                            <h3 class="h5 mt-3 text-danger">Deadline Sudah Lewat</h3>
                            <p class="text-muted-ah mb-0">
                                Anda tidak mengumpulkan tugas ini sebelum deadline.
                            </p>
                        </div>
                    <?php endif; ?>

                </div>
            </div>

        </div>
    </div>
</div>

<?= $this->endSection() ?>
