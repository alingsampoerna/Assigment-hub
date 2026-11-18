<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php
$formError = session('form_error');      // 'aplikasi' atau 'profil'
$errors    = session('errors') ?? [];
$old       = session('old') ?? [];
$success   = session()->getFlashdata('success');

$errApp    = $formError === 'aplikasi' ? $errors : [];
$errProfil = $formError === 'profil' ? $errors : [];
$errPass   = $formError === 'password' ? $errors : [];

$nilaiNamaApp = $formError === 'aplikasi' ? ($old['nama_aplikasi'] ?? '') : $nilai['nama_aplikasi'];
$nilaiUpload  = $formError === 'aplikasi' ? ($old['maks_upload_mb'] ?? '') : $nilai['maks_upload_mb'];
$nilaiAdmin   = $formError === 'profil' ? ($old['admin_nama'] ?? '') : $nilai['admin_nama'];
$nilaiEmail   = $formError === 'profil' ? ($old['admin_email'] ?? '') : $nilai['admin_email'];
?>

        <!-- Ubah password admin -->
        <section class="cc-form-card" aria-labelledby="judul-password-admin">
            <h2 id="judul-password-admin" class="h3 mb-3">Ubah Password</h2>

            <?= cc_error_summary($errPass) ?>

            <form action="<?= site_url('admin/pengaturan/password') ?>" method="post" novalidate>
                <?= csrf_field() ?>

                <?= cc_input([
                    'id'           => 'password_lama',
                    'name'         => 'password_lama',
                    'label'        => 'Password lama',
                    'type'         => 'password',
                    'error'        => $errPass['password_lama'] ?? null,
                    'autocomplete' => 'current-password',
                ]) ?>

                <?= cc_input([
                    'id'           => 'password_baru',
                    'name'         => 'password_baru',
                    'label'        => 'Password baru',
                    'type'         => 'password',
                    'error'        => $errPass['password_baru'] ?? null,
                    'hint'         => 'Minimal 8 karakter, kombinasi huruf dan angka.',
                    'autocomplete' => 'new-password',
                ]) ?>

                <?= cc_input([
                    'id'           => 'password_konfirmasi',
                    'name'         => 'password_konfirmasi',
                    'label'        => 'Konfirmasi password baru',
                    'type'         => 'password',
                    'error'        => $errPass['password_konfirmasi'] ?? null,
                    'autocomplete' => 'new-password',
                ]) ?>

                <div class="cc-form-actions">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-shield-lock" aria-hidden="true"></i> Ubah Password
                    </button>
                </div>
            </form>
        </section>

<div class="cc-page">
    <div class="container-fluid px-3 px-lg-4">

        <div class="cc-page__header">
            <div>
                <h1>Pengaturan</h1>
                <p class="text-muted-cc mb-0">Atur identitas aplikasi dan profil admin.</p>
            </div>
        </div>

        <?= $success ? cc_alert('success', $success) : '' ?>

        <!-- Pengaturan aplikasi -->
        <section class="cc-form-card" aria-labelledby="judul-aplikasi">
            <h2 id="judul-aplikasi" class="h3 mb-3">Pengaturan Aplikasi</h2>

            <?= cc_error_summary($errApp) ?>

            <form action="<?= site_url('admin/pengaturan') ?>" method="post" novalidate>
                <?= csrf_field() ?>

                <p class="cc-caption">Kolom bertanda <span aria-hidden="true">*</span><span class="visually-hidden">bintang</span> wajib diisi.</p>

                <?= cc_input([
                    'id'    => 'nama_aplikasi',
                    'name'  => 'nama_aplikasi',
                    'label' => 'Nama aplikasi',
                    'value' => $nilaiNamaApp,
                    'error' => $errApp['nama_aplikasi'] ?? null,
                    'hint'  => 'Tampil di judul halaman dan navigasi.',
                ]) ?>

                <?= cc_select([
                    'id'          => 'maks_upload_mb',
                    'name'        => 'maks_upload_mb',
                    'label'       => 'Batas ukuran foto laporan',
                    'options'     => $opsiUpload,
                    'selected'    => (string) $nilaiUpload,
                    'error'       => $errApp['maks_upload_mb'] ?? null,
                    'placeholder' => 'Pilih batas ukuran',
                    'hint'        => 'Pastikan tidak melebihi batas upload_max_filesize di php.ini.',
                ]) ?>

                <div class="cc-form-actions">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-lg" aria-hidden="true"></i> Simpan Pengaturan
                    </button>
                </div>
            </form>
        </section>

        <!-- Profil admin -->
        <section class="cc-form-card" aria-labelledby="judul-profil-admin">
            <h2 id="judul-profil-admin" class="h3 mb-3">Profil Admin</h2>

            <?= cc_error_summary($errProfil) ?>

            <form action="<?= site_url('admin/pengaturan/profil') ?>" method="post" novalidate>
                <?= csrf_field() ?>

                <?= cc_input([
                    'id'           => 'admin_nama',
                    'name'         => 'admin_nama',
                    'label'        => 'Nama admin',
                    'value'        => $nilaiAdmin,
                    'error'        => $errProfil['admin_nama'] ?? null,
                    'autocomplete' => 'name',
                ]) ?>

                <?= cc_input([
                    'id'           => 'admin_email',
                    'name'         => 'admin_email',
                    'label'        => 'Email admin',
                    'type'         => 'email',
                    'value'        => $nilaiEmail,
                    'error'        => $errProfil['admin_email'] ?? null,
                    'autocomplete' => 'email',
                    'inputmode'    => 'email',
                ]) ?>

                <div class="cc-form-actions">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-lg" aria-hidden="true"></i> Simpan Profil
                    </button>
                </div>
            </form>
        </section>

    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/admin-crud.js') ?>"></script>
<script src="<?= base_url('assets/js/auth.js') ?>"></script>
<?= $this->endSection() ?>
