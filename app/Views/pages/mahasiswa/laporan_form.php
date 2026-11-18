<?= $this->extend('layouts/student') ?>

<?= $this->section('content') ?>
<?php
$errors    = session('errors') ?? [];
$old       = session('old') ?? [];
$fotoReset = (bool) session('foto_reset');
$flashError = session()->getFlashdata('error');
?>

<div class="cc-page">
    <div class="container">

        <div class="cc-page__header">
            <div>
                <h1>Buat Laporan Kerusakan</h1>
                <p class="text-muted-cc mb-0">Ceritakan fasilitas yang rusak agar dapat segera ditindaklanjuti.</p>
            </div>
        </div>

        <div class="cc-form-card">
            <?= $flashError ? cc_alert('error', $flashError) : '' ?>
            <?= cc_error_summary($errors) ?>

            <form id="form-laporan" action="<?= site_url('mahasiswa/laporan') ?>" method="post"
                enctype="multipart/form-data" novalidate>
                <?= csrf_field() ?>

                <p class="cc-caption">Kolom bertanda <span aria-hidden="true">*</span><span class="visually-hidden">bintang</span> wajib diisi.</p>

                <?= cc_input([
                    'id'     => 'judul',
                    'name'   => 'judul',
                    'label'  => 'Judul laporan',
                    'value'  => $old['judul'] ?? '',
                    'error'  => $errors['judul'] ?? null,
                    'hint'   => 'Ringkas dan jelas, contoh: AC tidak dingin di ruang 204.',
                ]) ?>

                <?= cc_select([
                    'id'          => 'kategori_id',
                    'name'        => 'kategori_id',
                    'label'       => 'Kategori fasilitas',
                    'options'     => $kategori,
                    'selected'    => $old['kategori_id'] ?? '',
                    'error'       => $errors['kategori_id'] ?? null,
                    'placeholder' => 'Pilih kategori',
                ]) ?>

                <?= cc_input([
                    'id'     => 'lokasi',
                    'name'   => 'lokasi',
                    'label'  => 'Lokasi kerusakan',
                    'value'  => $old['lokasi'] ?? '',
                    'error'  => $errors['lokasi'] ?? null,
                    'hint'   => 'Sebutkan gedung, lantai, dan ruangan, contoh: Gedung A, Lantai 2, R.204.',
                ]) ?>

                <?= cc_textarea([
                    'id'    => 'deskripsi',
                    'name'  => 'deskripsi',
                    'label' => 'Deskripsi kerusakan',
                    'value' => $old['deskripsi'] ?? '',
                    'error' => $errors['deskripsi'] ?? null,
                    'hint'  => 'Jelaskan apa yang rusak dan sejak kapan.',
                    'max'   => 500,
                    'rows'  => 5,
                ]) ?>

                <!-- Foto kerusakan (opsional) -->
                <div class="mb-3">
                    <label for="foto" class="form-label cc-label">
                        Foto kerusakan <span class="text-muted-cc fw-normal">(opsional)</span>
                    </label>

                    <input class="form-control<?= isset($errors['foto']) ? ' is-invalid' : '' ?>"
                           type="file" id="foto" name="foto"
                           accept="image/jpeg,image/png"
                           data-max-bytes="<?= (int) $maxFotoKb * 1024 ?>"
                           <?= isset($errors['foto']) ? 'aria-invalid="true"' : '' ?>
                           aria-describedby="foto-hint<?= isset($errors['foto']) ? ' foto-error' : '' ?> foto-client-error">

                    <p id="foto-hint" class="cc-hint">
                        Format JPG atau PNG, maksimal <?= (int) ($maxFotoKb / 1024) ?> MB.
                        <?php if ($fotoReset): ?>
                            Browser tidak menyimpan file yang sebelumnya dipilih, silakan pilih foto lagi.
                        <?php endif; ?>
                    </p>

                    <?php if (isset($errors['foto'])): ?>
                        <p id="foto-error" class="cc-field-error">
                            <i class="bi bi-exclamation-circle-fill" aria-hidden="true"></i>
                            <span><span class="visually-hidden">Kesalahan: </span><?= esc($errors['foto']) ?></span>
                        </p>
                    <?php endif; ?>

                    <!-- Error dari pemeriksaan di browser (diisi oleh JavaScript) -->
                    <p id="foto-client-error" class="cc-field-error d-none" role="alert"></p>

                    <div id="foto-preview" class="cc-photo-preview d-none">
                        <img id="foto-preview-img" alt="Pratinjau foto yang akan diunggah">
                        <button type="button" id="foto-remove" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-trash" aria-hidden="true"></i> Hapus foto
                        </button>
                    </div>
                </div>

                <?= cc_input([
                    'id'       => 'foto_alt',
                    'name'     => 'foto_alt',
                    'label'    => 'Deskripsi foto (untuk pengguna pembaca layar)',
                    'value'    => $old['foto_alt'] ?? '',
                    'error'    => $errors['foto_alt'] ?? null,
                    'hint'     => 'Opsional. Contoh: Unit AC terlihat berembun dan menetes ke lantai.',
                    'required' => false,
                ]) ?>

                <div class="cc-form-actions">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-send" aria-hidden="true"></i>
                        <span class="cc-submit-label">Kirim Laporan</span>
                    </button>
                    <a class="btn btn-outline-primary btn-lg" href="<?= site_url('mahasiswa/dashboard') ?>">Batal</a>
                </div>
            </form>
        </div>

    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/laporan-form.js') ?>"></script>
<?= $this->endSection() ?>