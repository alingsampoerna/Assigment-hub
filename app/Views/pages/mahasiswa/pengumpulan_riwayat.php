<?= $this->extend('layouts/student') ?>
<?= $this->section('content') ?>

<div class="ah-page">
    <div class="container">

        <div class="mb-4">
            <a class="ah-back" href="<?= site_url('mahasiswa/pengumpulan/' . (int) $pengumpulan['id']) ?>">
                <i class="bi bi-arrow-left" aria-hidden="true"></i> Kembali ke Detail Pengumpulan
            </a>
        </div>

        <div class="ah-page__header">
            <div>
                <h1>Riwayat Revisi</h1>
                <p class="text-muted-ah mb-0"><?= esc($pengumpulan['judul_tugas']) ?></p>
            </div>
        </div>

        <div class="ah-admin-panel">
            <?php if ($riwayat === []): ?>
                <div class="ah-empty py-4">
                    <i class="bi bi-clock-history ah-empty__icon" aria-hidden="true"></i>
                    <p class="text-muted-ah mt-2 mb-0">Belum ada riwayat revisi.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table ah-table">
                        <thead>
                            <tr>
                                <th>Tanggal Revisi</th>
                                <th>File Lama</th>
                                <th>File Baru</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($riwayat as $r): ?>
                                <tr>
                                    <td><?= cc_datetime_id($r['tanggal_perubahan']) ?></td>
                                    <td class="ah-caption"><?= esc($r['nama_file_lama'] ?? $r['file_lama']) ?></td>
                                    <td class="ah-caption"><?= esc($r['nama_file_baru'] ?? $r['file_baru']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<?= $this->endSection() ?>
