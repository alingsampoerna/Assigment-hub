<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<?php
$errors = session('errors') ?? [];
$old    = session('old') ?? [];
$isEdit = $isEdit ?? false;
$item   = $item ?? null;
?>

<div class="ah-page">
    <div class="container-fluid px-3 px-lg-4">

        <div class="mb-4">
            <a class="ah-back" href="<?= site_url('admin/mata-kuliah') ?>">
                <i class="bi bi-arrow-left" aria-hidden="true"></i> Kembali ke Mata Kuliah
            </a>
            <h1><?= $isEdit ? 'Edit Mata Kuliah' : 'Tambah Mata Kuliah' ?></h1>
        </div>

        <?= cc_error_summary($errors) ?>

        <div class="ah-form-card">
            <form action="<?= site_url($isEdit ? 'admin/mata-kuliah/' . (int) $item['id'] : 'admin/mata-kuliah') ?>"
                  method="post" novalidate>
                <?= csrf_field() ?>

                <p class="ah-caption mb-4">
                    Kolom bertanda <span aria-hidden="true">*</span><span class="visually-hidden">bintang</span> wajib diisi.
                </p>

                <?= cc_input([
                    'id'          => 'kode_mata_kuliah',
                    'name'        => 'kode_mata_kuliah',
                    'label'       => 'Kode Mata Kuliah',
                    'value'       => $old['kode_mata_kuliah'] ?? ($item['kode_mata_kuliah'] ?? ''),
                    'error'       => $errors['kode_mata_kuliah'] ?? null,
                    'hint'        => 'Contoh: IF201, TK305. Akan diubah otomatis ke huruf kapital.',
                    'placeholder' => 'Contoh: IF201',
                    'required'    => true,
                ]) ?>

                <?= cc_input([
                    'id'          => 'nama_mata_kuliah',
                    'name'        => 'nama_mata_kuliah',
                    'label'       => 'Nama Mata Kuliah',
                    'value'       => $old['nama_mata_kuliah'] ?? ($item['nama_mata_kuliah'] ?? ''),
                    'error'       => $errors['nama_mata_kuliah'] ?? null,
                    'placeholder' => 'Contoh: Pemrograman Web',
                    'required'    => true,
                ]) ?>

                <!-- Deskripsi -->
                <?= cc_textarea([
                    'id'       => 'deskripsi',
                    'name'     => 'deskripsi',
                    'label'    => 'Deskripsi',
                    'value'    => $old['deskripsi'] ?? ($item['deskripsi'] ?? ''),
                    'error'    => $errors['deskripsi'] ?? null,
                    'hint'     => 'Opsional. Deskripsi singkat tentang mata kuliah ini.',
                    'rows'     => 4,
                    'required' => false,
                    'max'      => 500,
                ]) ?>

                <!-- Status -->
                <?= cc_select([
                    'id'       => 'status',
                    'name'     => 'status',
                    'label'    => 'Status',
                    'options'  => ['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif'],
                    'selected' => $old['status'] ?? ($item['status'] ?? 'aktif'),
                    'error'    => $errors['status'] ?? null,
                    'required' => true,
                ]) ?>

                <div class="ah-form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-<?= $isEdit ? 'check-circle' : 'plus-circle' ?>" aria-hidden="true"></i>
                        <?= $isEdit ? 'Simpan Perubahan' : 'Tambah Mata Kuliah' ?>
                    </button>
                    <a class="btn btn-outline-secondary" href="<?= site_url('admin/mata-kuliah') ?>">Batal</a>
                </div>
            </form>
        </div>

    </div>
</div>

<?= $this->endSection() ?>
