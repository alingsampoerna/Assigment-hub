<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="ah-page">
    <div class="container-fluid px-3 px-lg-4">

        <!-- Header -->
        <div class="ah-page__header">
            <div>
                <h1>Kelas</h1>
                <p class="text-muted-ah mb-0">Kelola kelas perkuliahan dan anggotanya.</p>
            </div>
            <a class="btn btn-primary" href="<?= site_url('admin/kelas/baru') ?>">
                <i class="bi bi-plus-circle" aria-hidden="true"></i> Tambah Kelas
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
        <form method="get" action="<?= site_url('admin/kelas') ?>" class="ah-filter mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-sm-6 col-lg-4">
                    <label for="q" class="form-label fw-semibold">Cari</label>
                    <input type="search" id="q" name="q" class="form-control"
                           placeholder="Nama kelas atau mata kuliah…"
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
                <div class="col-sm-4 col-lg-2">
                    <label for="status" class="form-label fw-semibold">Status</label>
                    <select id="status" name="status" class="form-select">
                        <option value="">Semua</option>
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
                        <a href="<?= site_url('admin/kelas') ?>" class="btn btn-outline-secondary w-100">Reset</a>
                    </div>
                <?php endif; ?>
            </div>
        </form>

        <p class="ah-caption mb-2">
            Menampilkan <strong><?= count($items) ?></strong> dari <strong><?= (int) $total ?></strong> kelas.
        </p>

        <!-- Tabel -->
        <div class="ah-panel">
            <?php if ($items === []): ?>
                <div class="ah-empty">
                    <i class="bi bi-people ah-empty__icon" aria-hidden="true"></i>
                    <h3 class="mt-3">Belum ada kelas</h3>
                    <p class="text-muted-ah">Tambahkan kelas untuk mulai mendaftarkan mahasiswa.</p>
                    <a class="btn btn-primary" href="<?= site_url('admin/kelas/baru') ?>">Tambah Kelas</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table ah-table">
                        <caption class="visually-hidden">Daftar kelas</caption>
                        <thead>
                            <tr>
                                <th scope="col">Mata Kuliah</th>
                                <th scope="col">Kelas</th>
                                <th scope="col">Tahun / Semester</th>
                                <th scope="col">Status</th>
                                <th scope="col" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $kelas): ?>
                                <tr>
                                    <td>
                                        <p class="ah-table__title mb-0"><?= esc($kelas['nama_mata_kuliah']) ?></p>
                                        <p class="ah-table__sub"><?= esc($kelas['kode_mata_kuliah']) ?></p>
                                    </td>
                                    <td class="fw-semibold"><?= esc($kelas['nama_kelas']) ?></td>
                                    <td>
                                        <?= esc($kelas['tahun_akademik']) ?>
                                        <span class="ah-badge ah-badge--<?= $kelas['semester'] === 'ganjil' ? 'pending' : 'submitted' ?> ms-1">
                                            <?= ucfirst($kelas['semester']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($kelas['status'] === 'aktif'): ?>
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
                                            <a class="btn btn-sm btn-outline-secondary"
                                               href="<?= site_url('admin/kelas/' . (int) $kelas['id'] . '/anggota') ?>">
                                                <i class="bi bi-people" aria-hidden="true"></i> Anggota
                                            </a>
                                            <a class="btn btn-sm btn-outline-primary"
                                               href="<?= site_url('admin/kelas/' . (int) $kelas['id'] . '/ubah') ?>">
                                                <i class="bi bi-pencil" aria-hidden="true"></i> Edit
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                    data-bs-toggle="modal" data-bs-target="#modal-hapus"
                                                    data-hapus-url="<?= site_url('admin/kelas/' . (int) $kelas['id'] . '/hapus') ?>"
                                                    data-hapus-nama="<?= esc($kelas['nama_kelas'] . ' (' . $kelas['nama_mata_kuliah'] . ')', 'attr') ?>"
                                                    data-hapus-jenis-val="kelas">
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
                        <?= $this->setVar('baseUrl', site_url('admin/kelas'))
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
