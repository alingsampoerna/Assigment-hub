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
                <h1>Kelola Kategori</h1>
                <p class="text-muted-cc mb-0">Kategori fasilitas yang dapat dipilih mahasiswa saat membuat laporan.</p>
            </div>
            <a class="btn btn-primary" href="<?= site_url('admin/kategori/baru') ?>">
                <i class="bi bi-plus-circle" aria-hidden="true"></i> Tambah Kategori
            </a>
        </div>

        <?= $success ? cc_alert('success', $success) : '' ?>
        <?= $error ? cc_alert('error', $error) : '' ?>

        <div class="cc-panel">
            <?php if ($items === []): ?>
                <div class="cc-empty">
                    <i class="bi bi-tags cc-empty__icon" aria-hidden="true"></i>
                    <h2 class="h3 mt-3">Belum ada kategori</h2>
                    <p class="text-muted-cc">Tambahkan kategori agar mahasiswa dapat membuat laporan.</p>
                    <a class="btn btn-primary" href="<?= site_url('admin/kategori/baru') ?>">Tambah Kategori</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table cc-table">
                        <caption class="visually-hidden">Daftar kategori fasilitas</caption>
                        <thead>
                            <tr>
                                <th scope="col">Nama kategori</th>
                                <th scope="col" class="text-end">Jumlah laporan</th>
                                <th scope="col"><span class="visually-hidden">Aksi</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <th scope="row" class="fw-semibold"><?= esc($item['nama']) ?></th>
                                    <td class="text-end"><?= (int) $item['jumlah'] ?></td>
                                    <td>
                                        <div class="cc-row-actions">
                                            <a class="btn btn-outline-primary btn-sm"
                                               href="<?= site_url('admin/kategori/' . (int) $item['id'] . '/ubah') ?>">
                                                Ubah<span class="visually-hidden"> kategori <?= esc($item['nama']) ?></span>
                                            </a>

                                            <?php if ($item['jumlah'] === 0): ?>
                                                <button type="button" class="btn btn-outline-danger btn-sm"
                                                        data-bs-toggle="modal" data-bs-target="#modal-hapus"
                                                        data-action="<?= esc(site_url('admin/kategori/' . (int) $item['id'] . '/hapus'), 'attr') ?>"
                                                        data-nama="<?= esc($item['nama'], 'attr') ?>"
                                                        data-jenis="kategori">
                                                    Hapus<span class="visually-hidden"> kategori <?= esc($item['nama']) ?></span>
                                                </button>
                                            <?php else: ?>
                                                <span class="cc-caption">
                                                    Tidak dapat dihapus<span class="visually-hidden">
                                                    karena dipakai <?= (int) $item['jumlah'] ?> laporan</span>
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

        <p class="cc-caption mt-3 mb-0">Kategori yang masih dipakai oleh laporan tidak dapat dihapus.</p>
    </div>
</div>

<?= $this->include('partials/confirm_delete_modal') ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/admin-crud.js') ?>"></script>
<?= $this->endSection() ?>