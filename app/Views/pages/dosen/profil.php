<?= $this->extend('layouts/dosen') ?>

<?= $this->section('content') ?>
<?php
$formError = session('form_error');      // 'profil' atau 'password'
$errors    = session('errors') ?? [];
$old       = session('old') ?? [];
$success   = session()->getFlashdata('success');

$errProfil = $formError === 'profil' ? $errors : [];
$errPass   = $formError === 'password' ? $errors : [];

$nilaiNama  = $formError === 'profil' ? ($old['nama'] ?? '') : $profil['nama'];
$nilaiEmail = $formError === 'profil' ? ($old['email'] ?? '') : $profil['email'];
?>

<div class="cc-page">
    <div class="container">

        <div class="cc-page__header">
            <div>
                <h1>Profil Saya</h1>
                <p class="text-muted-cc mb-0">Kelola data akun dan keamanan password Anda.</p>
            </div>
        </div>

        <?= $success ? cc_alert('success', $success) : '' ?>

        <!-- Data profil -->
        <section class="cc-form-card" aria-labelledby="judul-data-profil">
            <h2 id="judul-data-profil" class="h3 mb-3">Data Profil</h2>

            <?= cc_error_summary($errProfil) ?>

            <form action="<?= site_url('dosen/profil') ?>" method="post" novalidate>
                <?= csrf_field() ?>

                <p class="cc-caption">Kolom bertanda <span aria-hidden="true">*</span><span class="visually-hidden">bintang</span> wajib diisi.</p>

                <?= cc_input([
                    'id'           => 'nama',
                    'name'         => 'nama',
                    'label'        => 'Nama lengkap',
                    'value'        => $nilaiNama,
                    'error'        => $errProfil['nama'] ?? null,
                    'autocomplete' => 'name',
                ]) ?>

                <?= cc_input([
                    'id'           => 'email',
                    'name'         => 'email',
                    'label'        => 'Email',
                    'type'         => 'email',
                    'value'        => $nilaiEmail,
                    'error'        => $errProfil['email'] ?? null,
                    'autocomplete' => 'email',
                    'inputmode'    => 'email',
                ]) ?>

                <div class="cc-form-actions">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-lg" aria-hidden="true"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </section>

        <!-- Ubah password -->
        <section class="cc-form-card" aria-labelledby="judul-password">
            <h2 id="judul-password" class="h3 mb-3">Ubah Password</h2>

            <?= cc_error_summary($errPass) ?>

            <form action="<?= site_url('dosen/profil/password') ?>" method="post" novalidate>
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

    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- auth.js: tombol lihat password dan fokus ke ringkasan error -->
<script src="<?= base_url('assets/js/auth.js') ?>"></script>
<?= $this->endSection() ?>
