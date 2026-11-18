<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php
$errors = session('errors') ?? [];
$old    = session('old') ?? [];
$tambah = $mode === 'tambah';

$action    = $tambah ? site_url('admin/kategori') : site_url('admin/kategori/' . (int) $item['id']);
$nilaiNama = $old['nama'] ?? ($item['nama'] ?? '');
?>

<div class="cc-page">
    <div class="container-fluid px-3 px-lg-4">

        <a class="cc-back" href="<?= site_url('admin/kategori') ?>">
            <i class="bi bi-arrow-left" aria-hidden="true"></i> Kembali ke Kelola Kategori
        </a>

        <div class="cc-page__header">
            <h1><?= $tambah ? 'Tambah Kategori' : 'Ubah Kategori' ?></h1>
        </div>

        <div class="cc-form-card">
            <?= cc_error_summary($errors) ?>

            <form action="<?= $action ?>" method="post" novalidate>
                <?= csrf_field() ?>

                <p class="cc-caption">Kolom bertanda <span aria-hidden="true">*</span><span class="visually-hidden">bintang</span> wajib diisi.</p>

                <?= cc_input([
                    'id'    => 'nama',
                    'name'  => 'nama',
                    'label' => 'Nama kategori',
                    'value' => $nilaiNama,
                    'error' => $errors['nama'] ?? null,
                    'hint'  => 'Contoh: Fasilitas Kelas, Listrik, Internet.',
                ]) ?>

                <div class="cc-form-actions">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-lg" aria-hidden="true"></i> Simpan
                    </button>
                    <a class="btn btn-outline-primary btn-lg" href="<?= site_url('admin/kategori') ?>">Batal</a>
                </div>
            </form>
        </div>

    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/admin-crud.js') ?>"></script>
<?= $this->endSection() ?>