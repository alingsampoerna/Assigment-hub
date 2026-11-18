<?= $this->extend('layouts/dosen') ?>
<?= $this->section('content') ?>

<div class="ah-page">
    <div class="container">

        <div class="mb-4">
            <a class="ah-back" href="<?= site_url('dosen/kelas') ?>">
                <i class="bi bi-arrow-left" aria-hidden="true"></i> Kembali ke Kelas
            </a>
        </div>

        <!-- Info kelas -->
        <div class="ah-admin-panel mb-4">
            <div class="row g-3">
                <div class="col-md-8">
                    <p class="ah-caption mb-1"><?= esc($kelas['kode_mata_kuliah']) ?></p>
                    <h1 class="h3 mb-1"><?= esc($kelas['nama_mata_kuliah']) ?></h1>
                    <p class="text-muted-ah mb-0">
                        <i class="bi bi-people me-1" aria-hidden="true"></i>
                        Kelas <?= esc($kelas['nama_kelas']) ?> &bull;
                        <?= esc($kelas['tahun_akademik']) ?> &bull;
                        Semester <?= ucfirst($kelas['semester']) ?>
                    </p>
                </div>
                <div class="col-md-4 d-flex align-items-center justify-content-md-end gap-2">
                    <span class="ah-badge ah-badge--<?= $kelas['status'] === 'aktif' ? 'active' : 'closed' ?> fs-6">
                        <?= ucfirst($kelas['status']) ?>
                    </span>
                    <a class="btn btn-primary"
                       href="<?= site_url('dosen/tugas?kelas=' . (int) $kelas['id']) ?>">
                        <i class="bi bi-journal-text" aria-hidden="true"></i> Lihat Tugas
                    </a>
                </div>
            </div>
        </div>

        <!-- Daftar anggota -->
        <div class="ah-admin-panel">
            <h2 class="h5 mb-3">
                Anggota Kelas
                <span class="ah-badge ah-badge--submitted ms-2"><?= count($anggota) ?> mahasiswa</span>
            </h2>

            <?php if ($anggota === []): ?>
                <p class="text-muted-ah ah-caption text-center py-3 mb-0">Belum ada anggota di kelas ini.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table ah-table">
                        <caption class="visually-hidden">Daftar mahasiswa di kelas <?= esc($kelas['nama_kelas']) ?></caption>
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Nama</th>
                                <th scope="col">NIM</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($anggota as $no => $a): ?>
                                <tr>
                                    <td><?= $no + 1 ?></td>
                                    <td><?= esc($a['nama']) ?></td>
                                    <td class="font-monospace"><?= esc($a['nim']) ?></td>
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
