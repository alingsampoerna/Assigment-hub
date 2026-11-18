<?= $this->extend('layouts/dosen') ?>
<?= $this->section('content') ?>

<div class="ah-page">
    <div class="container-fluid px-3 px-lg-4">

        <!-- Header -->
        <div class="mb-4">
            <a class="ah-back" href="<?= site_url('dosen/kelas') ?>">
                <i class="bi bi-arrow-left" aria-hidden="true"></i> Kembali ke Kelas
            </a>
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mt-1">
                <div>
                    <h1 class="mb-1">Anggota Kelas — <?= esc($kelas['nama_kelas']) ?></h1>
                    <p class="text-muted-ah mb-0">
                        <?= esc($kelas['kode_mata_kuliah'] . ' — ' . $kelas['nama_mata_kuliah']) ?> &bull;
                        <?= esc($kelas['tahun_akademik']) ?> (<?= ucfirst($kelas['semester']) ?>)
                    </p>
                </div>
                <span class="ah-badge ah-badge--<?= $kelas['status'] === 'aktif' ? 'active' : 'closed' ?> fs-6">
                    <?= ucfirst($kelas['status']) ?>
                </span>
            </div>
        </div>

        <!-- Flash -->
        <?php
        $flashSuccess = session()->getFlashdata('success');
        $flashError   = session()->getFlashdata('error');
        ?>
        <?= $flashSuccess ? cc_alert('success', $flashSuccess) : '' ?>
        <?= $flashError   ? cc_alert('error',   $flashError)   : '' ?>

        <!-- Daftar Anggota -->
        <div class="ah-admin-panel">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h5 mb-0">
                    Daftar Anggota
                    <span class="ah-badge ah-badge--submitted ms-2"><?= count($anggota) ?></span>
                </h2>
            </div>

            <?php if ($anggota === []): ?>
                <div class="ah-empty py-4">
                    <i class="bi bi-people ah-empty__icon" aria-hidden="true"></i>
                    <p class="mt-3 mb-0 text-muted-ah">Belum ada mahasiswa di kelas ini.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table ah-table">
                        <caption class="visually-hidden">Daftar mahasiswa anggota kelas <?= esc($kelas['nama_kelas']) ?></caption>
                        <thead>
                            <tr>
                                <th scope="col">Mahasiswa</th>
                                <th scope="col">NIM</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($anggota as $a): ?>
                                <tr>
                                    <td>
                                        <p class="ah-table__title mb-0"><?= esc($a['nama']) ?></p>
                                        <p class="ah-table__sub"><?= esc($a['email']) ?></p>
                                    </td>
                                    <td class="font-monospace"><?= esc($a['nim']) ?></td>
                                    <td>
                                        <?php if ($a['status'] === 'aktif'): ?>
                                            <span class="ah-badge ah-badge--active">Aktif</span>
                                        <?php else: ?>
                                            <span class="ah-badge ah-badge--closed">Nonaktif</span>
                                        <?php endif; ?>
                                    </td>
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
