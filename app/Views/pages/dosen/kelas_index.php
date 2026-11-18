<?= $this->extend('layouts/dosen') ?>
<?= $this->section('content') ?>

<div class="ah-page">
    <div class="container">

        <div class="ah-page__header">
            <div>
                <h1>Daftar Kelas</h1>
                <p class="text-muted-ah mb-0">Kelas yang tersedia di Assignment Hub.</p>
            </div>
        </div>

        <?php $flash = session()->getFlashdata('success'); ?>
        <?= $flash ? cc_alert('success', $flash) : '' ?>

        <?php if ($kelasList === []): ?>
            <div class="ah-panel">
                <div class="ah-empty">
                    <i class="bi bi-people ah-empty__icon" aria-hidden="true"></i>
                    <h3 class="mt-3">Belum ada kelas</h3>
                    <p class="text-muted-ah mb-0">Belum ada kelas yang terdaftar di sistem.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($kelasList as $k): ?>
                    <div class="col-md-6 col-xl-4">
                        <div class="ah-admin-panel h-100">
                            <p class="ah-caption mb-1"><?= esc($k['kode_mata_kuliah']) ?></p>
                            <h3 class="h5 mb-1"><?= esc($k['nama_mata_kuliah']) ?></h3>
                            <p class="text-muted-ah ah-caption mb-3">
                                Kelas <?= esc($k['nama_kelas']) ?> &bull;
                                <?= esc($k['tahun_akademik']) ?> &bull;
                                <?= ucfirst($k['semester']) ?>
                            </p>
                            <div class="d-flex gap-2">
                                <a class="btn btn-sm btn-outline-primary"
                                   href="<?= site_url('dosen/kelas/' . (int) $k['id']) ?>">
                                    <i class="bi bi-eye" aria-hidden="true"></i> Detail
                                </a>
                                <a class="btn btn-sm btn-outline-secondary"
                                   href="<?= site_url('dosen/kelas/' . (int) $k['id'] . '/anggota') ?>">
                                    <i class="bi bi-people" aria-hidden="true"></i> Anggota
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</div>

<?= $this->endSection() ?>
