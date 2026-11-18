<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
$errors = session('errors') ?? [];
$old    = session('old') ?? [];
$error  = session()->getFlashdata('error');
?>

<section class="ah-auth">
    <div class="container">
        <div class="ah-auth__card ah-auth__card--wide">

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

            <h1 class="h3 mb-1">Buat Akun Mahasiswa</h1>
            <p class="text-muted-ah mb-4">Daftarkan diri Anda untuk mulai mengakses dan mengumpulkan tugas.</p>

            <?= cc_error_summary($errors) ?>
            <?= $error ? cc_alert('error', $error) : '' ?>

            <form action="<?= site_url('register') ?>" method="post" novalidate>
                <?= csrf_field() ?>

                <p class="ah-caption">Kolom bertanda <span aria-hidden="true">*</span><span class="visually-hidden">bintang</span> wajib diisi.</p>

                <?= cc_input([
                    'id'           => 'nama',
                    'name'         => 'nama',
                    'label'        => 'Nama lengkap',
                    'value'        => $old['nama'] ?? '',
                    'error'        => $errors['nama'] ?? null,
                    'autocomplete' => 'name',
                    'required'     => true,
                    'placeholder'  => 'Contoh: Budi Santoso',
                ]) ?>

                <?= cc_input([
                    'id'           => 'nim',
                    'name'         => 'nim',
                    'label'        => 'NIM',
                    'value'        => $old['nim'] ?? '',
                    'error'        => $errors['nim'] ?? null,
                    'hint'         => 'Hanya angka, 8 sampai 15 digit.',
                    'inputmode'    => 'numeric',
                    'autocomplete' => 'off',
                    'required'     => true,
                    'placeholder'  => 'Contoh: 20230001',
                ]) ?>

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
                    'placeholder'  => 'Contoh: nama@kampus.ac.id',
                ]) ?>

                <?= cc_input([
                    'id'           => 'password',
                    'name'         => 'password',
                    'label'        => 'Password',
                    'type'         => 'password',
                    'error'        => $errors['password'] ?? null,
                    'hint'         => 'Minimal 8 karakter, kombinasi huruf dan angka.',
                    'autocomplete' => 'new-password',
                    'required'     => true,
                ]) ?>

                <?= cc_input([
                    'id'           => 'password_confirm',
                    'name'         => 'password_confirm',
                    'label'        => 'Konfirmasi password',
                    'type'         => 'password',
                    'error'        => $errors['password_confirm'] ?? null,
                    'autocomplete' => 'new-password',
                    'required'     => true,
                ]) ?>

                <button type="submit" class="btn btn-primary btn-lg w-100 mt-3">
                    <i class="bi bi-person-check" aria-hidden="true"></i>
                    Daftar
                </button>
            </form>

            <p class="ah-auth__footer mb-0">
                Sudah punya akun? <a href="<?= site_url('login') ?>">Masuk di sini</a>
            </p>
        </div>
    </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/auth.js') ?>"></script>
<?= $this->endSection() ?>
