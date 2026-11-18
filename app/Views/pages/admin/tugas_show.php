<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="ah-page">
    <div class="container-fluid px-3 px-lg-4">

        <div class="mb-4">
            <a class="ah-back" href="<?= site_url('admin/tugas') ?>">
                <i class="bi bi-arrow-left" aria-hidden="true"></i> Kembali ke Tugas
            </a>
        </div>

        <?php
        $flashSuccess = session()->getFlashdata('success');
        $flashError   = session()->getFlashdata('error');
        ?>
        <?= $flashSuccess ? cc_alert('success', $flashSuccess) : '' ?>
        <?= $flashError   ? cc_alert('error',   $flashError)   : '' ?>

        <?php
        $now      = new DateTime();
        $deadline = new DateTime($tugas['deadline']);
        $lewat    = $now > $deadline;
        $selisih  = (int) $now->diff($deadline)->format('%r%a');

        $badgeMap = [
            'draft'   => ['ah-badge--draft',   'bi-pencil-square', 'Draft'],
            'aktif'   => ['ah-badge--active',  'bi-check-circle-fill', 'Aktif'],
            'ditutup' => ['ah-badge--closed',  'bi-lock-fill', 'Ditutup'],
        ];
        [$badgeClass, $badgeIcon, $badgeLabel] = $badgeMap[$tugas['status']] ?? ['ah-badge--draft', 'bi-question', $tugas['status']];
        ?>

        <div class="row g-4">

            <!-- Detail -->
            <div class="col-lg-8">
                <div class="ah-admin-panel">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                        <div>
                            <p class="ah-caption mb-1"><?= esc($tugas['kode_mata_kuliah'] . ' — ' . $tugas['nama_mata_kuliah']) ?></p>
                            <h1 class="h3 mb-0"><?= esc($tugas['judul']) ?></h1>
                        </div>
                        <span class="ah-badge <?= $badgeClass ?> fs-6">
                            <i class="bi <?= $badgeIcon ?>"></i> <?= $badgeLabel ?>
                        </span>
                    </div>

                    <?php if (! empty($tugas['deskripsi'])): ?>
                        <div class="mb-4 p-3" style="background:var(--ah-surface-2);border-radius:var(--ah-radius-md);border:1px solid var(--ah-border);">
                            <p class="fw-semibold mb-2">Instruksi / Deskripsi</p>
                            <p class="mb-0" style="white-space:pre-line;"><?= esc($tugas['deskripsi']) ?></p>
                        </div>
                    <?php endif; ?>

                    <!-- Info detail -->
                    <dl class="ah-dl">
                        <dt>Pembuat</dt>
                        <dd><?= esc($tugas['nama_pembuat']) ?></dd>

                        <dt>Tanggal Mulai</dt>
                        <dd><?= cc_datetime_id($tugas['tanggal_mulai']) ?></dd>

                        <dt>Deadline</dt>
                        <dd>
                            <span class="ah-deadline ah-deadline--<?= $lewat ? 'passed' : ($selisih <= 1 ? 'urgent' : ($selisih <= 3 ? 'near' : 'ok')) ?>">
                                <i class="bi bi-clock<?= $lewat ? '' : '-fill' ?>"></i>
                                <?= cc_datetime_id($tugas['deadline']) ?>
                                <?php if ($lewat): ?>
                                    <span class="ms-1">(Sudah lewat)</span>
                                <?php elseif ($selisih === 0): ?>
                                    <span class="ms-1">(Hari ini)</span>
                                <?php elseif ($selisih === 1): ?>
                                    <span class="ms-1">(Besok)</span>
                                <?php else: ?>
                                    <span class="ms-1">(<?= $selisih ?> hari lagi)</span>
                                <?php endif; ?>
                            </span>
                        </dd>

                        <dt>Batas File</dt>
                        <dd><?= round($tugas['batas_ukuran_file'] / 1024, 1) ?> MB</dd>

                        <dt>Izin Revisi</dt>
                        <dd><?= $tugas['izin_revisi'] ? 'Ya — mahasiswa boleh revisi sebelum deadline' : 'Tidak' ?></dd>

                        <?php if (! empty($tugas['file_lampiran'])): ?>
                            <dt>Lampiran</dt>
                            <dd>
                                <a class="btn btn-sm btn-outline-primary"
                                   href="<?= site_url('admin/tugas/' . (int) $tugas['id'] . '/lampiran') ?>">
                                    <i class="bi bi-download" aria-hidden="true"></i>
                                    <?= esc($tugas['nama_lampiran_asli'] ?? 'Unduh Lampiran') ?>
                                </a>
                            </dd>
                        <?php endif; ?>
                    </dl>

                    <!-- Aksi -->
                    <div class="d-flex flex-wrap gap-2 pt-3" style="border-top:1px solid var(--ah-border);">
                        <a class="btn btn-primary"
                           href="<?= site_url('admin/tugas/' . (int) $tugas['id'] . '/pengumpulan') ?>">
                            <i class="bi bi-cloud-upload" aria-hidden="true"></i>
                            Lihat Pengumpulan
                        </a>
                        <a class="btn btn-outline-primary"
                           href="<?= site_url('admin/tugas/' . (int) $tugas['id'] . '/ubah') ?>">
                            <i class="bi bi-pencil" aria-hidden="true"></i> Edit Tugas
                        </a>
                        <button type="button" class="btn btn-outline-danger"
                                data-bs-toggle="modal" data-bs-target="#modal-hapus"
                                data-hapus-url="<?= site_url('admin/tugas/' . (int) $tugas['id'] . '/hapus') ?>"
                                data-hapus-nama="<?= esc($tugas['judul'], 'attr') ?>"
                                data-hapus-jenis-val="tugas">
                            <i class="bi bi-trash" aria-hidden="true"></i> Hapus
                        </button>
                    </div>
                </div>
            </div>

            <!-- Kelas Penerima -->
            <div class="col-lg-4">
                <div class="ah-admin-panel">
                    <h2 class="h5 mb-3">
                        Kelas Penerima
                        <span class="ah-badge ah-badge--submitted ms-2"><?= count($kelasList) ?></span>
                    </h2>

                    <?php if ($kelasList === []): ?>
                        <p class="text-muted-ah ah-caption text-center py-3 mb-0">Belum ada kelas yang ditentukan.</p>
                    <?php else: ?>
                        <ul class="list-unstyled mb-0">
                            <?php foreach ($kelasList as $k): ?>
                                <li class="py-2" style="border-bottom:1px solid var(--ah-border);">
                                    <p class="fw-semibold mb-0">
                                        <?= esc($k['kode_mata_kuliah']) ?> — Kelas <?= esc($k['nama_kelas']) ?>
                                    </p>
                                    <p class="ah-caption mb-0">
                                        <?= esc($k['tahun_akademik']) ?> / <?= ucfirst($k['semester']) ?>
                                    </p>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<?= $this->include('partials/confirm_delete_modal') ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.getElementById('modal-hapus').addEventListener('show.bs.modal', function (e) {
    var btn  = e.relatedTarget;
    var form = this.querySelector('form');
    form.action = btn.dataset.hapusUrl;
    this.querySelector('[data-hapus-nama]').textContent  = btn.dataset.hapusNama;
    this.querySelector('[data-hapus-jenis]').textContent = btn.dataset.hapusJenisVal || 'data';
});
</script>
<?= $this->endSection() ?>
