<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="ah-page">
    <div class="container-fluid px-3 px-lg-4">

        <div class="ah-page__header">
            <div>
                <h1>Kelola Tugas</h1>
                <p class="text-muted-ah mb-0">Buat, edit, dan pantau pengumpulan tugas mahasiswa.</p>
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

        <!-- Filter -->
        <form method="get" action="<?= site_url('admin/tugas') ?>" class="ah-filter mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-sm-6 col-lg-4">
                    <label for="q" class="form-label fw-semibold">Cari</label>
                    <input type="search" id="q" name="q" class="form-control"
                           placeholder="Judul tugas atau mata kuliah…"
                           value="<?= esc($filter['q']) ?>">
                </div>
                <div class="col-sm-5 col-lg-3">
                    <label for="mk" class="form-label fw-semibold">Mata Kuliah</label>
                    <select id="mk" name="mk" class="form-select">
                        <option value="">Semua</option>
                        <?php foreach ($mataKuliah as $mk): ?>
                            <option value="<?= (int) $mk['id'] ?>"
                                    <?= $filter['mk'] == $mk['id'] ? 'selected' : '' ?>>
                                <?= esc($mk['kode_mata_kuliah'] . ' — ' . $mk['nama_mata_kuliah']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-sm-3 col-lg-2">
                    <label for="status" class="form-label fw-semibold">Status</label>
                    <select id="status" name="status" class="form-select">
                        <option value="">Semua</option>
                        <option value="draft"   <?= $filter['status'] === 'draft'   ? 'selected' : '' ?>>Draft</option>
                        <option value="aktif"   <?= $filter['status'] === 'aktif'   ? 'selected' : '' ?>>Aktif</option>
                        <option value="ditutup" <?= $filter['status'] === 'ditutup' ? 'selected' : '' ?>>Ditutup</option>
                    </select>
                </div>
                <div class="col-sm-auto">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search" aria-hidden="true"></i> Cari
                    </button>
                </div>
                <?php if ($query !== []): ?>
                    <div class="col-sm-auto">
                        <a href="<?= site_url('admin/tugas') ?>" class="btn btn-outline-secondary">Reset</a>
                    </div>
                <?php endif; ?>
            </div>
        </form>

        <p class="ah-caption mb-2">
            Menampilkan <strong><?= count($items) ?></strong> dari <strong><?= (int) $total ?></strong> tugas.
        </p>

        <div class="ah-panel">
            <?php if ($items === []): ?>
                <div class="ah-empty">
                    <i class="bi bi-journal-text ah-empty__icon" aria-hidden="true"></i>
                    <h3 class="mt-3">Belum ada tugas</h3>
                    <p class="text-muted-ah">Buat tugas baru dan tentukan kelas penerimannya.</p>
                    <a class="btn btn-primary" href="<?= site_url('admin/tugas/baru') ?>">Buat Tugas</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table ah-table">
                        <caption class="visually-hidden">Daftar tugas</caption>
                        <thead>
                            <tr>
                                <th scope="col">Judul Tugas</th>
                                <th scope="col">Mata Kuliah</th>
                                <th scope="col">Deadline</th>
                                <th scope="col">Status</th>
                                <th scope="col" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $t):
                                $now       = new DateTime();
                                $deadline  = new DateTime($t['deadline']);
                                $lewat     = $now > $deadline;
                                $selisihHari = (int) $now->diff($deadline)->format('%r%a');
                            ?>
                                <tr>
                                    <td>
                                        <p class="ah-table__title mb-0">
                                            <a href="<?= site_url('admin/tugas/' . (int) $t['id']) ?>">
                                                <?= esc($t['judul']) ?>
                                            </a>
                                        </p>
                                        <?php if (! empty($t['file_lampiran'])): ?>
                                            <p class="ah-table__sub">
                                                <i class="bi bi-paperclip" aria-hidden="true"></i>
                                                Ada lampiran
                                            </p>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <p class="ah-table__title mb-0"><?= esc($t['nama_mata_kuliah']) ?></p>
                                        <p class="ah-table__sub"><?= esc($t['kode_mata_kuliah']) ?></p>
                                    </td>
                                    <td>
                                        <span class="ah-deadline ah-deadline--<?= $lewat ? 'passed' : ($selisihHari <= 1 ? 'urgent' : ($selisihHari <= 3 ? 'near' : 'ok')) ?>">
                                            <i class="bi bi-clock<?= $lewat ? '' : '-fill' ?>" aria-hidden="true"></i>
                                            <?= cc_datetime_id($t['deadline']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        $badgeMap = [
                                            'draft'   => ['ah-badge--draft',   'bi-pencil-square', 'Draft'],
                                            'aktif'   => ['ah-badge--active',  'bi-check-circle-fill', 'Aktif'],
                                            'ditutup' => ['ah-badge--closed',  'bi-lock-fill', 'Ditutup'],
                                        ];
                                        [$badgeClass, $badgeIcon, $badgeLabel] = $badgeMap[$t['status']] ?? ['ah-badge--draft', 'bi-question', $t['status']];
                                        ?>
                                        <span class="ah-badge <?= $badgeClass ?>">
                                            <i class="bi <?= $badgeIcon ?>" aria-hidden="true"></i>
                                            <?= esc($badgeLabel) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="ah-row-actions">
                                            <a class="btn btn-sm btn-outline-secondary"
                                               href="<?= site_url('admin/tugas/' . (int) $t['id'] . '/pengumpulan') ?>">
                                                <i class="bi bi-cloud-upload" aria-hidden="true"></i>
                                            </a>
                                            <a class="btn btn-sm btn-outline-primary"
                                               href="<?= site_url('admin/tugas/' . (int) $t['id'] . '/ubah') ?>">
                                                <i class="bi bi-pencil" aria-hidden="true"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                    data-bs-toggle="modal" data-bs-target="#modal-hapus"
                                                    data-hapus-url="<?= site_url('admin/tugas/' . (int) $t['id'] . '/hapus') ?>"
                                                    data-hapus-nama="<?= esc($t['judul'], 'attr') ?>"
                                                    data-hapus-jenis-val="tugas">
                                                <i class="bi bi-trash" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($totalPages > 1): ?>
                    <div class="p-3">
                        <?= $this->setVar('baseUrl', site_url('admin/tugas'))
                                 ->include('partials/pagination') ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
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
