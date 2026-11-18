<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<?php
$errors       = session('errors') ?? [];
$old          = session('old') ?? [];
$isEdit       = $isEdit ?? false;
$tugas        = $tugas ?? null;
$kelasDipilih = $kelasDipilih ?? [];
?>

<div class="ah-page">
    <div class="container-fluid px-3 px-lg-4">

        <div class="mb-4">
            <a class="ah-back" href="<?= site_url('admin/tugas') ?>">
                <i class="bi bi-arrow-left" aria-hidden="true"></i> Kembali ke Tugas
            </a>
            <h1><?= $isEdit ? 'Edit Tugas' : 'Buat Tugas Baru' ?></h1>
        </div>

        <?= cc_error_summary($errors) ?>

        <form action="<?= site_url($isEdit ? 'admin/tugas/' . (int) $tugas['id'] : 'admin/tugas') ?>"
              method="post" enctype="multipart/form-data" novalidate>
            <?= csrf_field() ?>

            <div class="row g-4">

                <!-- Kolom kiri: info tugas -->
                <div class="col-lg-8">
                    <div class="ah-form-card">
                        <h2 class="h5 mb-4">Informasi Tugas</h2>

                        <?= cc_input([
                            'id'          => 'judul',
                            'name'        => 'judul',
                            'label'       => 'Judul Tugas',
                            'value'       => $old['judul'] ?? ($tugas['judul'] ?? ''),
                            'error'       => $errors['judul'] ?? null,
                            'placeholder' => 'Contoh: Laporan Praktikum Pemrograman Web',
                            'required'    => true,
                        ]) ?>

                        <?= cc_select([
                            'id'          => 'mata_kuliah_id',
                            'name'        => 'mata_kuliah_id',
                            'label'       => 'Mata Kuliah',
                            'options'     => array_column($mataKuliah, 'nama_mata_kuliah', 'id'),
                            'selected'    => $old['mata_kuliah_id'] ?? ($tugas['mata_kuliah_id'] ?? ''),
                            'error'       => $errors['mata_kuliah_id'] ?? null,
                            'placeholder' => '-- Pilih Mata Kuliah --',
                            'required'    => true,
                        ]) ?>

                        <?= cc_textarea([
                            'id'       => 'deskripsi',
                            'name'     => 'deskripsi',
                            'label'    => 'Instruksi / Deskripsi',
                            'value'    => $old['deskripsi'] ?? ($tugas['deskripsi'] ?? ''),
                            'error'    => $errors['deskripsi'] ?? null,
                            'hint'     => 'Opsional. Jelaskan instruksi pengerjaan tugas.',
                            'rows'     => 6,
                            'required' => false,
                        ]) ?>

                        <!-- Upload lampiran -->
                        <div class="mb-3">
                            <label for="file_lampiran" class="form-label cc-label">
                                File Lampiran
                            </label>
                            <?php if ($isEdit && ! empty($tugas['file_lampiran'])): ?>
                                <div class="ah-file-preview mb-2">
                                    <i class="bi bi-paperclip ah-file-preview__icon" aria-hidden="true"></i>
                                    <div>
                                        <p class="ah-file-preview__name"><?= esc($tugas['nama_lampiran_asli'] ?? 'Lampiran') ?></p>
                                        <p class="ah-file-preview__size">File saat ini</p>
                                    </div>
                                    <a class="btn btn-sm btn-outline-primary ms-auto"
                                       href="<?= site_url('admin/tugas/' . (int) $tugas['id'] . '/lampiran') ?>">
                                        <i class="bi bi-download" aria-hidden="true"></i> Unduh
                                    </a>
                                </div>
                                <p class="ah-caption mb-2">Upload file baru untuk mengganti yang lama. Kosongkan jika tidak ingin mengganti.</p>
                            <?php endif; ?>

                            <input type="file" id="file_lampiran" name="file_lampiran"
                                   class="form-control<?= isset($errors['file_lampiran']) ? ' is-invalid' : '' ?>"
                                   accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip">

                            <?php if (isset($errors['file_lampiran'])): ?>
                                <p class="cc-field-error mt-2">
                                    <i class="bi bi-exclamation-circle-fill" aria-hidden="true"></i>
                                    <?= esc($errors['file_lampiran']) ?>
                                </p>
                            <?php endif; ?>
                            <p class="ah-caption mt-1">Format: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, ZIP. Maks. 50 MB.</p>
                        </div>
                    </div>
                </div>

                <!-- Kolom kanan: pengaturan -->
                <div class="col-lg-4">

                    <!-- Waktu -->
                    <div class="ah-form-card mb-4">
                        <h2 class="h5 mb-4">Waktu</h2>

                        <div class="mb-3">
                            <label for="tanggal_mulai" class="form-label cc-label">
                                Tanggal Mulai <span class="cc-required" aria-hidden="true">*</span>
                            </label>
                            <input type="datetime-local" id="tanggal_mulai" name="tanggal_mulai"
                                   class="form-control<?= isset($errors['tanggal_mulai']) ? ' is-invalid' : '' ?>"
                                   value="<?= esc($old['tanggal_mulai'] ?? ($tugas['tanggal_mulai'] ?? '')) ?>"
                                   required>
                            <?php if (isset($errors['tanggal_mulai'])): ?>
                                <p class="cc-field-error mt-1"><i class="bi bi-exclamation-circle-fill"></i> <?= esc($errors['tanggal_mulai']) ?></p>
                            <?php endif; ?>
                            <p class="ah-caption mt-1">Kapan tugas mulai terlihat oleh mahasiswa.</p>
                        </div>

                        <div class="mb-3">
                            <label for="deadline" class="form-label cc-label">
                                Deadline <span class="cc-required" aria-hidden="true">*</span>
                            </label>
                            <input type="datetime-local" id="deadline" name="deadline"
                                   class="form-control<?= isset($errors['deadline']) ? ' is-invalid' : '' ?>"
                                   value="<?= esc($old['deadline'] ?? ($tugas['deadline'] ?? '')) ?>"
                                   required>
                            <?php if (isset($errors['deadline'])): ?>
                                <p class="cc-field-error mt-1"><i class="bi bi-exclamation-circle-fill"></i> <?= esc($errors['deadline']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Ketentuan -->
                    <div class="ah-form-card mb-4">
                        <h2 class="h5 mb-4">Ketentuan</h2>

                        <div class="mb-3">
                            <label for="batas_ukuran_file_mb" class="form-label cc-label">
                                Batas Ukuran File (MB) <span class="cc-required" aria-hidden="true">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" id="batas_ukuran_file_mb" name="batas_ukuran_file_mb"
                                       class="form-control<?= isset($errors['batas_ukuran_file']) ? ' is-invalid' : '' ?>"
                                       value="<?= (int) ($old['batas_ukuran_file_mb'] ?? (isset($tugas['batas_ukuran_file']) ? round($tugas['batas_ukuran_file'] / 1024) : 10)) ?>"
                                       min="1" max="100" required>
                                <span class="input-group-text">MB</span>
                            </div>
                            <?php if (isset($errors['batas_ukuran_file'])): ?>
                                <p class="cc-field-error mt-1"><i class="bi bi-exclamation-circle-fill"></i> <?= esc($errors['batas_ukuran_file']) ?></p>
                            <?php endif; ?>
                            <p class="ah-caption mt-1">Ukuran file maksimum yang boleh dikumpulkan mahasiswa.</p>
                            <!-- Field hidden: nilai dalam KB yang dikirim ke server -->
                            <input type="hidden" id="batas_ukuran_file" name="batas_ukuran_file"
                                   value="<?= (int) ($old['batas_ukuran_file'] ?? (isset($tugas['batas_ukuran_file']) ? (int)$tugas['batas_ukuran_file'] : 10240)) ?>">
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="izin_revisi" name="izin_revisi"
                                       value="1" <?= ($old['izin_revisi'] ?? $tugas['izin_revisi'] ?? 1) ? 'checked' : '' ?>>
                                <label class="form-check-label fw-semibold" for="izin_revisi">
                                    Izinkan revisi sebelum deadline
                                </label>
                            </div>
                            <p class="ah-caption mt-1 ms-4">Mahasiswa boleh memperbarui pengumpulan selama deadline belum lewat.</p>
                        </div>

                        <?= cc_select([
                            'id'       => 'status',
                            'name'     => 'status',
                            'label'    => 'Status Tugas',
                            'options'  => ['draft' => 'Draft', 'aktif' => 'Aktif', 'ditutup' => 'Ditutup'],
                            'selected' => $old['status'] ?? ($tugas['status'] ?? 'aktif'),
                            'error'    => $errors['status'] ?? null,
                            'required' => true,
                            'hint'     => 'Draft: tersembunyi dari mahasiswa. Aktif: mahasiswa bisa melihat dan mengumpulkan.',
                        ]) ?>
                    </div>

                    <!-- Kelas Penerima -->
                    <div class="ah-form-card">
                        <h2 class="h5 mb-1">Kelas Penerima <span class="cc-required" aria-hidden="true">*</span></h2>
                        <p class="ah-caption mb-3">Pilih minimal satu kelas yang akan menerima tugas ini.</p>

                        <?php if (isset($errors['kelas_ids'])): ?>
                            <p class="cc-field-error mb-3">
                                <i class="bi bi-exclamation-circle-fill"></i>
                                <?= esc($errors['kelas_ids']) ?>
                            </p>
                        <?php endif; ?>

                        <?php if ($kelasList === []): ?>
                            <p class="text-muted-ah ah-caption">
                                Belum ada kelas aktif.
                                <a href="<?= site_url('admin/kelas/baru') ?>">Buat kelas terlebih dahulu.</a>
                            </p>
                        <?php else: ?>
                            <?php
                            // Kelompokkan kelas per mata kuliah
                            $kelasByMK = [];
                            foreach ($kelasList as $k) {
                                $kelasByMK[$k['nama_mata_kuliah']][] = $k;
                            }
                            ?>
                            <div style="max-height:300px;overflow-y:auto;">
                                <?php foreach ($kelasByMK as $nmk => $kelas): ?>
                                    <p class="ah-caption fw-bold mt-2 mb-1 text-uppercase" style="letter-spacing:.05em;">
                                        <?= esc($nmk) ?>
                                    </p>
                                    <?php foreach ($kelas as $k): ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                   id="kelas_<?= (int) $k['id'] ?>"
                                                   name="kelas_ids[]"
                                                   value="<?= (int) $k['id'] ?>"
                                                   <?= in_array((int) $k['id'], array_map('intval', (array) ($old['kelas_ids'] ?? $kelasDipilih)), true) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="kelas_<?= (int) $k['id'] ?>">
                                                Kelas <?= esc($k['nama_kelas']) ?>
                                                <span class="ah-caption">(<?= esc($k['tahun_akademik']) ?> / <?= ucfirst($k['semester']) ?>)</span>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>

            <!-- Tombol aksi -->
            <div class="ah-form-actions mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-<?= $isEdit ? 'check-circle' : 'plus-circle' ?>" aria-hidden="true"></i>
                    <?= $isEdit ? 'Simpan Perubahan' : 'Buat Tugas' ?>
                </button>
                <a class="btn btn-outline-secondary" href="<?= site_url('admin/tugas') ?>">Batal</a>
            </div>

        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function () {
    var mbInput = document.getElementById('batas_ukuran_file_mb');
    var kbInput = document.getElementById('batas_ukuran_file');

    function syncKb() {
        var mb = parseFloat(mbInput.value) || 1;
        kbInput.value = Math.round(mb * 1024);
    }

    // Sync saat halaman load
    syncKb();

    // Sync setiap kali nilai MB berubah
    mbInput.addEventListener('input', syncKb);
    mbInput.addEventListener('change', syncKb);
})();
</script>
<?= $this->endSection() ?>
