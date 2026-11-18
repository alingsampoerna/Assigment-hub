<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="ah-page">
    <div class="container-fluid px-3 px-lg-4">

        <div class="mb-4">
            <a class="ah-back" href="<?= site_url('admin/dosen') ?>">
                <i class="bi bi-arrow-left" aria-hidden="true"></i> Kembali ke Dosen
            </a>
        </div>

        <?php
        $errors = session()->getFlashdata('errors') ?? [];
        $old    = session()->getFlashdata('old') ?? [];
        ?>
        <?= cc_error_summary($errors) ?>

        <div class="ah-admin-panel" style="max-width:560px;">
            <h1 class="h4 mb-4">Tambah Akun Dosen</h1>

            <form action="<?= site_url('admin/dosen') ?>" method="post" novalidate>
                <?= csrf_field() ?>

                <p class="ah-caption mb-3">Kolom bertanda <span aria-hidden="true">*</span> wajib diisi.</p>

                <?= cc_input([
                    'id'    => 'nama',
                    'name'  => 'nama',
                    'label' => 'Nama Lengkap',
                    'value' => $old['nama'] ?? '',
                    'error' => $errors['nama'] ?? null,
                    'required' => true,
                    'autocomplete' => 'name',
                ]) ?>

                <?= cc_input([
                    'id'           => 'email',
                    'name'         => 'email',
                    'label'        => 'Email',
                    'type'         => 'email',
                    'value'        => $old['email'] ?? '',
                    'error'        => $errors['email'] ?? null,
                    'required'     => true,
                    'autocomplete' => 'email',
                    'inputmode'    => 'email',
                ]) ?>

                <?= cc_input([
                    'id'           => 'password',
                    'name'         => 'password',
                    'label'        => 'Password',
                    'type'         => 'password',
                    'error'        => $errors['password'] ?? null,
                    'hint'         => 'Minimal 8 karakter, kombinasi huruf dan angka.',
                    'required'     => true,
                    'autocomplete' => 'new-password',
                ]) ?>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-person-plus" aria-hidden="true"></i> Buat Akun Dosen
                    </button>
                    <a href="<?= site_url('admin/dosen') ?>" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>

    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/auth.js') ?>"></script>
<?= $this->endSection() ?>
