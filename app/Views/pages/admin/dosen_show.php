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
        $flashSuccess = session()->getFlashdata('success');
        $flashError   = session()->getFlashdata('error');
        $errors       = session()->getFlashdata('errors') ?? [];
        $formError    = session()->getFlashdata('form_error');
        ?>
        <?= $flashSuccess ? cc_alert('success', $flashSuccess) : '' ?>
        <?= $flashError   ? cc_alert('error',   $flashError)   : '' ?>

        <div class="row g-4">

            <!-- Info akun -->
            <div class="col-lg-4">
                <div class="ah-admin-panel">
                    <div class="text-center mb-4">
                        <div class="mx-auto mb-3" style="width:72px;height:72px;background:var(--ah-warning-soft);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:2rem;color:var(--ah-warning);">
                            <i class="bi bi-person-workspace" aria-hidden="true"></i>
                        </div>
                        <h2 class="h4 mb-1"><?= esc($dosen['nama']) ?></h2>
                        <p class="ah-caption mb-2"><?= esc($dosen['email']) ?></p>
                        <span class="ah-badge me-1" style="background:var(--ah-warning-soft);color:var(--ah-warning-text);border:1px solid color-mix(in srgb,var(--ah-warning) 25%,transparent);">Dosen</span>
                        <?php if ($dosen['status'] === 'aktif'): ?>
                            <span class="ah-badge ah-badge--active">Aktif</span>
                        <?php else: ?>
                            <span class="ah-badge ah-badge--closed">Nonaktif</span>
                        <?php endif; ?>
                    </div>

                    <dl class="ah-dl">
                        <dt>Terdaftar</dt>
                        <dd><?= cc_date_id($dosen['created_at'] ?? '') ?></dd>
                    </dl>

                    <form action="<?= site_url('admin/dosen/' . (int) $dosen['id'] . '/status') ?>" method="post">
                        <?= csrf_field() ?>
                        <button type="submit"
                                class="btn w-100 <?= $dosen['status'] === 'aktif' ? 'btn-outline-danger' : 'btn-success' ?>">
                            <?= $dosen['status'] === 'aktif' ? 'Nonaktifkan Akun' : 'Aktifkan Akun' ?>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Reset password -->
            <div class="col-lg-8">
                <div class="ah-admin-panel">
                    <h2 class="h5 mb-3">Reset Password</h2>
                    <p class="text-muted-ah ah-caption mb-4">
                        Gunakan fitur ini jika dosen lupa password. Password baru harus diberitahu secara langsung kepada dosen.
                    </p>

                    <?php if ($formError === 'password'): ?>
                        <?= cc_error_summary($errors) ?>
                    <?php endif; ?>

                    <form action="<?= site_url('admin/dosen/' . (int) $dosen['id'] . '/reset-password') ?>"
                          method="post" novalidate style="max-width:400px;">
                        <?= csrf_field() ?>

                        <?= cc_input([
                            'id'           => 'password_baru',
                            'name'         => 'password_baru',
                            'label'        => 'Password Baru',
                            'type'         => 'password',
                            'error'        => ($formError === 'password' ? ($errors['password_baru'] ?? null) : null),
                            'hint'         => 'Minimal 8 karakter, kombinasi huruf dan angka.',
                            'required'     => true,
                            'autocomplete' => 'new-password',
                        ]) ?>

                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-key" aria-hidden="true"></i> Reset Password
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/auth.js') ?>"></script>
<?= $this->endSection() ?>
