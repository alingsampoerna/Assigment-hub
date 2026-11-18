<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="ah-page">
    <div class="container-fluid px-3 px-lg-4">

        <div class="ah-page__header">
            <div>
                <h1>Mahasiswa</h1>
                <p class="text-muted-ah mb-0">Kelola akun mahasiswa yang terdaftar.</p>
            </div>
        </div>

        <?php
        $flashSuccess = session()->getFlashdata('success');
        $flashError   = session()->getFlashdata('error');
        ?>
        <?= $flashSuccess ? cc_alert('success', $flashSuccess) : '' ?>
        <?= $flashError   ? cc_alert('error',   $flashError)   : '' ?>

        <!-- Filter -->
        <form method="get" action="<?= site_url('admin/mahasiswa') ?>" class="ah-filter mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-sm-6 col-lg-5">
                    <label for="q" class="form-label fw-semibold">Cari</label>
                    <input type="search" id="q" name="q" class="form-control"
                           placeholder="Nama, NIM, atau email…"
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
                        <a href="<?= site_url('admin/mahasiswa') ?>" class="btn btn-outline-secondary w-100">Reset</a>
                    </div>
                <?php endif; ?>
            </div>
        </form>

        <p class="ah-caption mb-2">
            Menampilkan <strong><?= count($items) ?></strong> dari <strong><?= (int) $total ?></strong> mahasiswa.
        </p>

        <div class="ah-panel">
            <?php if ($items === []): ?>
                <div class="ah-empty">
                    <i class="bi bi-person-badge ah-empty__icon" aria-hidden="true"></i>
                    <h3 class="mt-3">Belum ada mahasiswa terdaftar</h3>
                    <p class="text-muted-ah mb-0">Mahasiswa dapat mendaftar sendiri melalui halaman registrasi.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table ah-table">
                        <caption class="visually-hidden">Daftar mahasiswa</caption>
                        <thead>
                            <tr>
                                <th scope="col">Mahasiswa</th>
                                <th scope="col">NIM</th>
                                <th scope="col">Status</th>
                                <th scope="col">Terdaftar</th>
                                <th scope="col" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $mhs): ?>
                                <tr>
                                    <td>
                                        <p class="ah-table__title mb-0"><?= esc($mhs['nama']) ?></p>
                                        <p class="ah-table__sub"><?= esc($mhs['email']) ?></p>
                                    </td>
                                    <td class="font-monospace"><?= esc($mhs['nim']) ?></td>
                                    <td>
                                        <?php if ($mhs['status'] === 'aktif'): ?>
                                            <span class="ah-badge ah-badge--active">Aktif</span>
                                        <?php else: ?>
                                            <span class="ah-badge ah-badge--closed">Nonaktif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="ah-caption"><?= cc_date_id($mhs['created_at'] ?? '') ?></td>
                                    <td>
                                        <div class="ah-row-actions">
                                            <a class="btn btn-sm btn-outline-primary"
                                               href="<?= site_url('admin/mahasiswa/' . (int) $mhs['id']) ?>">
                                                <i class="bi bi-eye" aria-hidden="true"></i> Detail
                                            </a>
                                            <form action="<?= site_url('admin/mahasiswa/' . (int) $mhs['id'] . '/status') ?>"
                                                  method="post">
                                                <?= csrf_field() ?>
                                                <button type="submit"
                                                        class="btn btn-sm <?= $mhs['status'] === 'aktif' ? 'btn-outline-danger' : 'btn-outline-success' ?>">
                                                    <?= $mhs['status'] === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' ?>
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
                        <?= $this->setVar('baseUrl', site_url('admin/mahasiswa'))
                                 ->include('partials/pagination') ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

    </div>
</div>

<?= $this->endSection() ?>
