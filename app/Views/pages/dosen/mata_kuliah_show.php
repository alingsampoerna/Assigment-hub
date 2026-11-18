<?= $this->extend('layouts/dosen') ?>
<?= $this->section('content') ?>

<div class="ah-page">
    <div class="container">

        <div class="mb-4">
            <a class="ah-back" href="<?= site_url('dosen/mata-kuliah') ?>">
                <i class="bi bi-arrow-left" aria-hidden="true"></i> Kembali ke Mata Kuliah
            </a>
        </div>

        <div class="ah-admin-panel">
            <p class="ah-caption mb-1 font-monospace"><?= esc($item['kode_mata_kuliah']) ?></p>
            <h1 class="h3 mb-2"><?= esc($item['nama_mata_kuliah']) ?></h1>
            <span class="ah-badge ah-badge--<?= $item['status'] === 'aktif' ? 'active' : 'closed' ?> mb-3">
                <?= ucfirst($item['status']) ?>
            </span>
            <?php if (! empty($item['deskripsi'])): ?>
                <p class="mt-3 mb-0"><?= esc($item['deskripsi']) ?></p>
            <?php endif; ?>
        </div>

    </div>
</div>

<?= $this->endSection() ?>
