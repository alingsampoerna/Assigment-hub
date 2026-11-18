<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="ah-page">
    <div class="container-fluid px-3 px-lg-4">

        <div class="ah-page__header">
            <div>
                <h1>Kelola Dosen</h1>
                <p class="text-muted-ah mb-0">Buat dan kelola akun dosen.</p>
            </div>
            <a class="btn btn-primary" href="<?= site_url('admin/dosen/baru') ?>">
                <i class="bi bi-plus-circle" aria-hidden="true"></i> Tambah Dosen
            </a>
        </div>

        <?php
        $flashSuccess = session()->getFlashdata('success');
        $flashError   = session()->getFlashdata('error');
        ?>
        <?= $flashSuccess ? cc_alert('success', $flashSuccess) : '' ?>
        <?= $flashError   ? cc_alert('error',   $flashError)   : '' ?>

        <!-- Filter -->
        <form method="get" action="<?= site_url('admin/dosen') ?>" class="ah-filter mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-sm-6 col-lg-5">
                    <label for="q" class="form-label fw-semibold">Cari</label>
                    <input type="search" id="q" name="q" class="form-control"
                           placeholder="Nama atau email…"
                           value="<?= esc($filter['q']) ?>">
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
                        <a href="<?= site_url('admin/dosen') ?>" class="btn btn-outline-secondary w-100">Reset</a>
                    </div>
                <?php endif; ?>
            </div>
        </form>

        <p class="ah-caption mb-2">
            Menampilkan <strong><?= count($items) ?></strong> dari <strong><?= (int) $total ?></strong> dosen.
        </p>

        <div class="ah-panel">
            <?php if ($items === []): ?>
                <div class="ah-empty">
                    <i class="bi bi-person-workspace ah-empty__icon" aria-hidden="true"></i>
                    <h3 class="mt-3">Belum ada dosen terdaftar</h3>
                    <p class="text-muted-ah">Tambahkan akun dosen agar mereka dapat membuat tugas.</p>
                    <a class="btn btn-primary" href="<?= site_url('admin/dosen/baru') ?>">Tambah Dosen</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table ah-table">
                        <caption class="visually-hidden">Daftar dosen</caption>
                        <thead>
                            <tr>
                                <th scope="col">Dosen</th>
                                <th scope="col">Status</th>
                                <th scope="col">Terdaftar</th>
                                <th scope="col" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $d): ?>
                                <tr>
                                    <td>
                                        <p class="ah-table__title mb-0"><?= esc($d['nama']) ?></p>
                                        <p class="ah-table__sub"><?= esc($d['email']) ?></p>
                                    </td>
                                    <td>
                                        <?php if ($d['status'] === 'aktif'): ?>
                                            <span class="ah-badge ah-badge--active">Aktif</span>
                                        <?php else: ?>
                                            <span class="ah-badge ah-badge--closed">Nonaktif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="ah-caption"><?= cc_date_id($d['created_at'] ?? '') ?></td>
                                    <td>
                                        <div class="ah-row-actions">
                                            <a class="btn btn-sm btn-outline-primary"
                                               href="<?= site_url('admin/dosen/' . (int) $d['id']) ?>">
                                                <i class="bi bi-eye" aria-hidden="true"></i> Detail
                                            </a>
                                            <form action="<?= site_url('admin/dosen/' . (int) $d['id'] . '/status') ?>"
                                                  method="post">
                                                <?= csrf_field() ?>
                                                <button type="submit"
                                                        class="btn btn-sm <?= $d['status'] === 'aktif' ? 'btn-outline-danger' : 'btn-outline-success' ?>">
                                                    <?= $d['status'] === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' ?>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($totalPages > 1): ?>
                    <div class="p-3">
                        <?= $this->setVar('baseUrl', site_url('admin/dosen'))
                                 ->include('partials/pagination') ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

    </div>
</div>

<?= $this->endSection() ?>
