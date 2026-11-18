<?= $this->extend('layouts/student') ?>
<?= $this->section('content') ?>

<div class="ah-page">
    <div class="container">

        <div class="mb-4">
            <a class="ah-back" href="<?= site_url('mahasiswa/kelas') ?>">
                <i class="bi bi-arrow-left" aria-hidden="true"></i> Kembali ke Kelas
            </a>
        </div>

        <div class="ah-admin-panel" style="max-width:520px;margin:0 auto;">

            <div class="text-center mb-4">
                <div class="mx-auto mb-3" style="width:72px;height:72px;background:var(--ah-primary-soft);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:2rem;color:var(--ah-primary);">
                    <i class="bi bi-person-plus" aria-hidden="true"></i>
                </div>
                <h1 class="h4 mb-1">Daftar ke Kelas</h1>
                <p class="text-muted-ah">Konfirmasi pendaftaran Anda ke kelas berikut.</p>
            </div>

            <div class="p-3 mb-4" style="background:var(--ah-surface-2);border-radius:var(--ah-radius-md);border:1px solid var(--ah-border);">
                <dl class="ah-dl mb-0">
                    <dt>Mata Kuliah</dt>
                    <dd><?= esc($kelas['kode_mata_kuliah'] . ' — ' . $kelas['nama_mata_kuliah']) ?></dd>

                    <dt>Kelas</dt>
                    <dd><?= esc($kelas['nama_kelas']) ?></dd>

                    <dt>Tahun Akademik</dt>
                    <dd><?= esc($kelas['tahun_akademik']) ?></dd>

                    <dt>Semester</dt>
                    <dd><?= ucfirst($kelas['semester']) ?></dd>
                </dl>
            </div>

            <div class="d-flex gap-3">
                <form action="<?= site_url('mahasiswa/kelas/' . (int) $kelas['id'] . '/daftar') ?>"
                      method="post" class="flex-grow-1">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-check-circle" aria-hidden="true"></i> Ya, Daftar Sekarang
                    </button>
                </form>
                <a href="<?= site_url('mahasiswa/kelas') ?>" class="btn btn-outline-secondary flex-grow-1">
                    Batal
                </a>
            </div>

        </div>
    </div>
</div>

<?= $this->endSection() ?>
