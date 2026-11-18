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
            <a class="ah-back" href="<?= site_url('admin/kelas') ?>">
                <i class="bi bi-arrow-left" aria-hidden="true"></i> Kembali ke Kelas
            </a>
            <h1><?= $isEdit ? 'Edit Kelas' : 'Tambah Kelas' ?></h1>
        </div>

        <?= cc_error_summary($errors) ?>

        <div class="ah-form-card">
            <form action="<?= site_url($isEdit ? 'admin/kelas/' . (int) $item['id'] : 'admin/kelas') ?>"
                  method="post" novalidate>
                <?= csrf_field() ?>

                <p class="ah-caption mb-4">Kolom bertanda <span aria-hidden="true">*</span><span class="visually-hidden">bintang</span> wajib diisi.</p>

                <!-- Mata Kuliah -->
                <?= cc_select([
                    'id'          => 'mata_kuliah_id',
                    'name'        => 'mata_kuliah_id',
                    'label'       => 'Mata Kuliah',
                    'options'     => array_column($mataKuliah, 'nama_mata_kuliah', 'id'),
                    'selected'    => $old['mata_kuliah_id'] ?? ($item['mata_kuliah_id'] ?? ''),
                    'error'       => $errors['mata_kuliah_id'] ?? null,
                    'placeholder' => '-- Pilih Mata Kuliah --',
                    'required'    => true,
                ]) ?>

                <?= cc_input([
                    'id'          => 'nama_kelas',
                    'name'        => 'nama_kelas',
                    'label'       => 'Nama Kelas',
                    'value'       => $old['nama_kelas'] ?? ($item['nama_kelas'] ?? ''),
                    'error'       => $errors['nama_kelas'] ?? null,
                    'hint'        => 'Contoh: A, B, IF-A, Reguler Pagi.',
                    'placeholder' => 'Contoh: A',
                    'required'    => true,
                ]) ?>

                <?= cc_input([
                    'id'          => 'tahun_akademik',
                    'name'        => 'tahun_akademik',
                    'label'       => 'Tahun Akademik',
                    'value'       => $old['tahun_akademik'] ?? ($item['tahun_akademik'] ?? ''),
                    'error'       => $errors['tahun_akademik'] ?? null,
                    'hint'        => 'Format: YYYY/YYYY, contoh: 2026/2027.',
                    'placeholder' => '2026/2027',
                    'required'    => true,
                ]) ?>

                <?= cc_select([
                    'id'       => 'semester',
                    'name'     => 'semester',
                    'label'    => 'Semester',
                    'options'  => ['ganjil' => 'Ganjil', 'genap' => 'Genap'],
                    'selected' => $old['semester'] ?? ($item['semester'] ?? ''),
                    'error'    => $errors['semester'] ?? null,
                    'required' => true,
                    'placeholder' => '-- Pilih Semester --',
                ]) ?>

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
                        <?= $isEdit ? 'Simpan Perubahan' : 'Tambah Kelas' ?>
                    </button>
                    <a class="btn btn-outline-secondary" href="<?= site_url('admin/kelas') ?>">Batal</a>
                </div>
            </form>
        </div>

    </div>
</div>

<?= $this->endSection() ?>
