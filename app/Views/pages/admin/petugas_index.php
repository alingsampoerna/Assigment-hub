<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php
$success = session()->getFlashdata('success');
$error   = session()->getFlashdata('error');
?>

<div class="cc-page">
    <div class="container-fluid px-3 px-lg-4">

        <div class="cc-page__header">
            <div>
                <h1>Kelola Petugas</h1>
                <p class="text-muted-cc mb-0">Petugas yang dapat ditugaskan untuk menangani laporan.</p>
            </div>
            <a class="btn btn-primary" href="<?= site_url('admin/petugas/baru') ?>">
                <i class="bi bi-plus-circle" aria-hidden="true"></i> Tambah Petugas
            </a>
        </div>

        <?= $success ? cc_alert('success', $success) : '' ?>
        <?= $error ? cc_alert('error', $error) : '' ?>

        <div class="cc-panel">
            <?php if ($items === []): ?>
                <div class="cc-empty">
                    <i class="bi bi-person-gear cc-empty__icon" aria-hidden="true"></i>
                    <h2 class="h3 mt-3">Belum ada petugas</h2>
                    <p class="text-muted-cc">Tambahkan petugas agar laporan dapat ditugaskan.</p>
                    <a class="btn btn-primary" href="<?= site_url('admin/petugas/baru') ?>">Tambah Petugas</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table cc-table">
                        <caption class="visually-hidden">Daftar petugas</caption>
                        <thead>
                            <tr>
                                <th scope="col">Nama</th>
                                <th scope="col">Bidang</th>
                                <th scope="col">Kontak</th>
                                <th scope="col" class="text-end">Laporan aktif</th>
                                <th scope="col" class="text-end">Total ditangani</th>
                                <th scope="col"><span class="visually-hidden">Aksi</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <th scope="row" class="fw-semibold"><?= esc($item['nama']) ?></th>
                                    <td><?= esc($item['bidang']) ?></td>
                                    <td><?= esc($item['kontak']) ?></td>
                                    <td class="text-end"><?= (int) $item['aktif'] ?></td>
                                    <td class="text-end"><?= (int) $item['total'] ?></td>
                                    <td>
                                        <div class="cc-row-actions">
                                            <a class="btn btn-outline-primary btn-sm"
                                               href="<?= site_url('admin/petugas/' . (int) $item['id'] . '/ubah') ?>">
                                                Ubah<span class="visually-hidden"> petugas <?= esc($item['nama']) ?></span>
                                            </a>

                                            <?php if ($item['total'] === 0): ?>
                                                <button type="button" class="btn btn-outline-danger btn-sm"
                                                        data-bs-toggle="modal" data-bs-target="#modal-hapus"
                                                        data-action="<?= esc(site_url('admin/petugas/' . (int) $item['id'] . '/hapus'), 'attr') ?>"
                                                        data-nama="<?= esc($item['nama'], 'attr') ?>"
                                                        data-jenis="petugas">
                                                    Hapus<span class="visually-hidden"> petugas <?= esc($item['nama']) ?></span>
                                                </button>
                                            <?php else: ?>
                                                <span class="cc-caption">
                                                    Tidak dapat dihapus<span class="visually-hidden">
                                                    karena pernah ditugaskan pada <?= (int) $item['total'] ?> laporan</span>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <p class="cc-caption mt-3 mb-0">
            Laporan aktif adalah laporan berstatus Diproses. Petugas yang pernah ditugaskan tidak dapat dihapus agar riwayat laporan tetap utuh.
        </p>
    </div>
</div>

<?= $this->include('partials/confirm_delete_modal') ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/admin-crud.js') ?>"></script>
<?= $this->endSection() ?>