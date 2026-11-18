<?= $this->extend('layouts/dosen') ?>
<?= $this->section('content') ?>

<div class="ah-page">
    <div class="container">

        <div class="ah-page__header">
            <div>
                <h1>Mata Kuliah</h1>
                <p class="text-muted-ah mb-0">Mata kuliah yang aktif di Assignment Hub.</p>
            </div>
        </div>

        <?php if ($items === []): ?>
            <div class="ah-panel">
                <div class="ah-empty">
                    <i class="bi bi-book ah-empty__icon" aria-hidden="true"></i>
                    <h3 class="mt-3">Belum ada mata kuliah</h3>
                    <p class="text-muted-ah mb-0">Belum ada mata kuliah yang terdaftar di sistem.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="ah-panel">
                <div class="table-responsive">
                    <table class="table ah-table">
                        <caption class="visually-hidden">Daftar mata kuliah</caption>
                        <thead>
                            <tr>
                                <th scope="col">Kode</th>
                                <th scope="col">Nama Mata Kuliah</th>
                                <th scope="col">Deskripsi</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $mk): ?>
                                <tr>
                                    <td class="font-monospace"><?= esc($mk['kode_mata_kuliah']) ?></td>
                                    <td class="fw-semibold"><?= esc($mk['nama_mata_kuliah']) ?></td>
                                    <td class="ah-caption"><?= esc($mk['deskripsi'] ?? '-') ?></td>
                                    <td>
                                        <span class="ah-badge ah-badge--<?= $mk['status'] === 'aktif' ? 'active' : 'closed' ?>">
                                            <?= ucfirst($mk['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<?= $this->endSection() ?>
