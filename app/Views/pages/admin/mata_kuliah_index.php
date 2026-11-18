<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="ah-page">
    <div class="container-fluid px-3 px-lg-4">

        <!-- Header -->
        <div class="ah-page__header">
            <div>
                <h1>Mata Kuliah</h1>
                <p class="text-muted-ah mb-0">Kelola daftar mata kuliah yang tersedia.</p>
            </div>
            <a class="btn btn-primary" href="<?= site_url('admin/mata-kuliah/baru') ?>">
                <i class="bi bi-plus-circle" aria-hidden="true"></i> Tambah Mata Kuliah
            </a>
        </div>

        <!-- Flash -->
        <?php
        $flashSuccess = session()->getFlashdata('success');
        $flashError   = session()->getFlashdata('error');
        ?>
        <?= $flashSuccess ? cc_alert('success', $flashSuccess) : '' ?>
        <?= $flashError   ? cc_alert('error',   $flashError)   : '' ?>

        <!-- Filter -->
        <form method="get" action="<?= site_url('admin/mata-kuliah') ?>" class="ah-filter mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-sm-6 col-lg-5">
                    <label for="q" class="form-label fw-semibold">Cari</label>
                    <input type="search" id="q" name="q" class="form-control"
                           placeholder="Kode atau nama mata kuliah…"
                           value="<?= esc($filter['q']) ?>">
                </div>
                <div class="col-sm-4 col-lg-3">
                    <label for="status" class="form-label fw-semibold">Status</label>
                    <select id="status" name="status" class="form-select">
                        <option value="">Semua status</option>
                        <option value="aktif"    <?= $filter['status'] === 'aktif'    ? 'selected' : '' ?>>Aktif</option>
                        <option value="nonaktif" <?= $filter['status'] === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                    </select>
                </div>
                <div class="col-sm-auto">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search" aria-hidden="true"></i> Cari
                    </button>
                </div>
                <?php if ($query !== []): ?>
                    <div class="col-sm-auto">
                        <a href="<?= site_url('admin/mata-kuliah') ?>" class="btn btn-outline-secondary w-100">Reset</a>
                    </div>
                <?php endif; ?>
            </div>
        </form>

        <!-- Info hasil -->
        <p class="ah-caption mb-2">
            Menampilkan <strong><?= count($items) ?></strong> dari <strong><?= (int) $total ?></strong> mata kuliah.
        </p>

        <!-- Tabel -->
        <div class="ah-panel">
            <?php if ($items === []): ?>
                <div class="ah-empty">
                    <i class="bi bi-book ah-empty__icon" aria-hidden="true"></i>
                    <h3 class="mt-3">Belum ada mata kuliah</h3>
                    <p class="text-muted-ah">Tambahkan mata kuliah untuk mulai membuat kelas dan tugas.</p>
                    <a class="btn btn-primary" href="<?= site_url('admin/mata-kuliah/baru') ?>">
                        Tambah Mata Kuliah
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table ah-table">
                        <caption class="visually-hidden">Daftar mata kuliah</caption>
                        <thead>
                            <tr>
                                <th scope="col">Kode</th>
                                <th scope="col">Nama Mata Kuliah</th>
                                <th scope="col">Status</th>
                                <th scope="col" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $mk): ?>
                                <tr>
                                    <td>
                                        <span class="fw-bold font-monospace text-muted-ah">
                                            <?= esc($mk['kode_mata_kuliah']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <p class="ah-table__title mb-0"><?= esc($mk['nama_mata_kuliah']) ?></p>
                                        <?php if (! empty($mk['deskripsi'])): ?>
                                            <p class="ah-table__sub"><?= esc(mb_strimwidth($mk['deskripsi'], 0, 60, '…')) ?></p>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($mk['status'] === 'aktif'): ?>
                                            <span class="ah-badge ah-badge--active">
                                                <i class="bi bi-check-circle-fill" aria-hidden="true"></i> Aktif
                                            </span>
                                        <?php else: ?>
                                            <span class="ah-badge ah-badge--closed">
                                                <i class="bi bi-dash-circle" aria-hidden="true"></i> Nonaktif
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="ah-row-actions">
                                            <a class="btn btn-sm btn-outline-primary"
                                               href="<?= site_url('admin/mata-kuliah/' . (int) $mk['id'] . '/ubah') ?>">
                                                <i class="bi bi-pencil" aria-hidden="true"></i> Edit
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                    data-bs-toggle="modal" data-bs-target="#modal-hapus"
                                                    data-hapus-url="<?= site_url('admin/mata-kuliah/' . (int) $mk['id'] . '/hapus') ?>"
                                                    data-hapus-nama="<?= esc($mk['nama_mata_kuliah'], 'attr') ?>"
                                                    data-hapus-jenis-val="mata kuliah">
                                                <i class="bi bi-trash" aria-hidden="true"></i> Hapus
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
                        <?= $this->setVar('baseUrl', site_url('admin/mata-kuliah'))
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
// Hubungkan modal hapus dengan tombol yang diklik
document.getElementById('modal-hapus').addEventListener('show.bs.modal', function (e) {
    var btn  = e.relatedTarget;
    var form = this.querySelector('form');
    form.action = btn.dataset.hapusUrl;
    this.querySelector('[data-hapus-nama]').textContent  = btn.dataset.hapusNama;
    this.querySelector('[data-hapus-jenis]').textContent = btn.dataset.hapusJenisVal || 'data';
});
</script>
<?= $this->endSection() ?>
