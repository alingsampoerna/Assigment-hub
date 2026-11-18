<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php
$errors = session('errors') ?? [];
$old    = session('old') ?? [];
$tambah = $mode === 'tambah';

$action = $tambah ? site_url('admin/petugas') : site_url('admin/petugas/' . (int) $item['id']);
?>

<div class="cc-page">
    <div class="container-fluid px-3 px-lg-4">

        <a class="cc-back" href="<?= site_url('admin/petugas') ?>">
            <i class="bi bi-arrow-left" aria-hidden="true"></i> Kembali ke Kelola Petugas
        </a>

        <div class="cc-page__header">
            <h1><?= $tambah ? 'Tambah Petugas' : 'Ubah Petugas' ?></h1>
        </div>

        <div class="cc-form-card">
            <?= cc_error_summary($errors) ?>

            <form action="<?= $action ?>" method="post" novalidate>
                <?= csrf_field() ?>

                <p class="cc-caption">Kolom bertanda <span aria-hidden="true">*</span><span class="visually-hidden">bintang</span> wajib diisi.</p>

                <?= cc_input([
                    'id'           => 'nama',
                    'name'         => 'nama',
                    'label'        => 'Nama petugas',
                    'value'        => $old['nama'] ?? ($item['nama'] ?? ''),
                    'error'        => $errors['nama'] ?? null,
                    'autocomplete' => 'off',
                ]) ?>

                <?= cc_input([
                    'id'           => 'bidang',
                    'name'         => 'bidang',
                    'label'        => 'Bidang',
                    'value'        => $old['bidang'] ?? ($item['bidang'] ?? ''),
                    'error'        => $errors['bidang'] ?? null,
                    'hint'         => 'Contoh: Listrik dan Elektronik, Jaringan dan IT.',
                    'autocomplete' => 'off',
                ]) ?>

                <?= cc_input([
                    'id'           => 'kontak',
                    'name'         => 'kontak',
                    'label'        => 'Nomor kontak',
                    'type'         => 'tel',
                    'value'        => $old['kontak'] ?? ($item['kontak'] ?? ''),
                    'error'        => $errors['kontak'] ?? null,
                    'hint'         => 'Angka, spasi, tanda + atau -, contoh: 0812-3456-7890.',
                    'inputmode'    => 'tel',
                    'autocomplete' => 'off',
                ]) ?>

                <div class="cc-form-actions">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-lg" aria-hidden="true"></i> Simpan
                    </button>
                    <a class="btn btn-outline-primary btn-lg" href="<?= site_url('admin/petugas') ?>">Batal</a>
                </div>
            </form>
        </div>

    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/admin-crud.js') ?>"></script>
<?= $this->endSection() ?>