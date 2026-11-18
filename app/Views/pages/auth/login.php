<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
$errors  = session('errors') ?? [];
$old     = session('old') ?? [];
$success = session()->getFlashdata('success');
$info    = session()->getFlashdata('info');
$error   = session()->getFlashdata('error');
?>

<section class="ah-auth">
    <div class="container">
        <div class="ah-auth__card">

            <!-- Logo -->
            <div class="ah-auth__logo">
                <span class="ah-auth__logo-icon">
                    <i class="bi bi-journal-bookmark-fill" aria-hidden="true"></i>
                </span>
                <div>
                    <div class="ah-auth__logo-text"><?= esc(cc_app_name()) ?></div>
                    <div class="ah-auth__logo-sub">Platform Tugas Akademik</div>
                </div>
            </div>

            <h1 class="h3 mb-1">Masuk ke Akun</h1>
            <p class="text-muted-ah mb-4">Gunakan akun yang sudah terdaftar untuk melanjutkan.</p>

            <?= $success ? cc_alert('success', $success) : '' ?>
            <?= $info    ? cc_alert('info', $info)       : '' ?>
            <?= $error   ? cc_alert('error', $error)     : '' ?>
            <?= cc_error_summary($errors) ?>

            <form action="<?= site_url('login') ?>" method="post" novalidate>
                <?= csrf_field() ?>

                <p class="ah-caption">Kolom bertanda <span aria-hidden="true">*</span><span class="visually-hidden">bintang</span> wajib diisi.</p>

                <?= cc_input([
                    'id'           => 'email',
                    'name'         => 'email',
                    'label'        => 'Email',
                    'type'         => 'email',
                    'value'        => $old['email'] ?? '',
                    'error'        => $errors['email'] ?? null,
                    'autocomplete' => 'email',
                    'inputmode'    => 'email',
                    'required'     => true,
                ]) ?>

                <?= cc_input([
                    'id'           => 'password',
                    'name'         => 'password',
                    'label'        => 'Password',
                    'type'         => 'password',
                    'error'        => $errors['password'] ?? null,
                    'autocomplete' => 'current-password',
                    'required'     => true,
                ]) ?>

                <button type="submit" class="btn btn-primary btn-lg w-100 mt-3">
                    <i class="bi bi-box-arrow-in-right" aria-hidden="true"></i>
                    Masuk
                </button>
            </form>

            <p class="ah-auth__footer mb-0">
                Belum punya akun? <a href="<?= site_url('register') ?>">Daftar sebagai mahasiswa</a>
            </p>
        </div>
    </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/auth.js') ?>"></script>
<?= $this->endSection() ?>
