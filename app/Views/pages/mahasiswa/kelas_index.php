<?= $this->extend('layouts/student') ?>
<?= $this->section('content') ?>

<div class="ah-page">
    <div class="container">

        <?php
        $flashSuccess = session()->getFlashdata('success');
        $flashError   = session()->getFlashdata('error');
        ?>
        <?= $flashSuccess ? cc_alert('success', $flashSuccess) : '' ?>
        <?= $flashError   ? cc_alert('error',   $flashError)   : '' ?>

        <!-- ===== KELAS SAYA ===== -->
        <div class="ah-page__header">
            <div>
                <h1>Kelas Saya</h1>
                <p class="text-muted-ah mb-0">
                    Anda terdaftar di <strong><?= count($kelasSaya) ?></strong> kelas.
                </p>
            </div>
        </div>

        <?php if ($kelasSaya === []): ?>
            <div class="ah-panel mb-5">
                <div class="ah-empty">
                    <i class="bi bi-people ah-empty__icon" aria-hidden="true"></i>
                    <h3 class="mt-3">Belum terdaftar di kelas manapun</h3>
                    <p class="text-muted-ah mb-0">
                        Daftar ke kelas di bawah, atau hubungi admin jika tidak menemukan kelas yang sesuai.
                    </p>
                </div>
            </div>
        <?php else: ?>
            <div class="row g-4 mb-5">
                <?php foreach ($kelasSaya as $k): ?>
                    <div class="col-md-6 col-xl-4">
                        <div class="ah-feature-card h-100">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="ah-feature-icon mb-0">
                                    <i class="bi bi-journal-text" aria-hidden="true"></i>
                                </span>
                                <div class="d-flex gap-1 flex-wrap justify-content-end">
                                    <span class="ah-badge ah-badge--<?= $k['semester'] === 'ganjil' ? 'pending' : 'submitted' ?>">
                                        <?= ucfirst($k['semester']) ?>
                                    </span>
                                    <?php if ($k['status'] !== 'aktif'): ?>
                                        <span class="ah-badge ah-badge--closed">Nonaktif</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <p class="ah-caption mb-1"><?= esc($k['kode_mata_kuliah']) ?></p>
                            <h3 class="h5 mb-1"><?= esc($k['nama_mata_kuliah']) ?></h3>
                            <p class="text-muted-ah ah-caption mb-3">
                                Kelas <?= esc($k['nama_kelas']) ?> &bull; <?= esc($k['tahun_akademik']) ?>
                            </p>

                            <div class="d-flex gap-2 mt-auto">
                                <a class="btn btn-outline-primary btn-sm flex-grow-1"
                                   href="<?= site_url('mahasiswa/kelas/' . (int) $k['id']) ?>">
                                    <i class="bi bi-eye" aria-hidden="true"></i> Detail
                                </a>
                                <a class="btn btn-outline-primary btn-sm flex-grow-1"
                                   href="<?= site_url('mahasiswa/tugas?kelas=' . (int) $k['id']) ?>">
                                    <i class="bi bi-journal-text" aria-hidden="true"></i> Tugas
                                </a>
                            </div>

                            <!-- Tombol keluar kelas -->
                            <form action="<?= site_url('mahasiswa/kelas/' . (int) $k['id'] . '/keluar') ?>"
                                  method="post" class="mt-2"
                                  onsubmit="return confirm('Yakin ingin keluar dari kelas <?= esc($k['nama_kelas'], 'attr') ?>?')">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                    <i class="bi bi-box-arrow-right" aria-hidden="true"></i> Keluar Kelas
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- ===== KELAS TERSEDIA ===== -->
        <div class="ah-page__header">
            <div>
                <h2 class="h3 mb-1">Kelas Tersedia</h2>
                <p class="text-muted-ah mb-0">Kelas aktif yang bisa Anda ikuti.</p>
            </div>
        </div>

        <!-- Pencarian -->
        <form method="get" action="<?= site_url('mahasiswa/kelas') ?>" class="mb-4">
            <div class="input-group" style="max-width:400px;">
                <input type="search" name="q" class="form-control"
                       placeholder="Cari mata kuliah atau kelas…"
                       value="<?= esc($q) ?>">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search" aria-hidden="true"></i>
                </button>
                <?php if ($q !== ''): ?>
                    <a href="<?= site_url('mahasiswa/kelas') ?>" class="btn btn-outline-secondary">Reset</a>
                <?php endif; ?>
            </div>
        </form>

        <?php if ($kelasTersedia === []): ?>
            <div class="ah-panel">
                <div class="ah-empty">
                    <i class="bi bi-check-circle ah-empty__icon" aria-hidden="true"></i>
                    <h3 class="mt-3">
                        <?= $q !== '' ? 'Tidak ada kelas yang cocok' : 'Semua kelas aktif sudah Anda ikuti' ?>
                    </h3>
                    <?php if ($q !== ''): ?>
                        <p class="text-muted-ah mb-0">Coba kata kunci lain.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="ah-panel">
                <div class="table-responsive">
                    <table class="table ah-table">
                        <thead>
                            <tr>
                                <th scope="col">Mata Kuliah</th>
                                <th scope="col">Kelas</th>
                                <th scope="col">Tahun / Semester</th>
                                <th scope="col" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($kelasTersedia as $k): ?>
                                <tr>
                                    <td>
                                        <p class="ah-table__title mb-0"><?= esc($k['nama_mata_kuliah']) ?></p>
                                        <p class="ah-table__sub"><?= esc($k['kode_mata_kuliah']) ?></p>
                                    </td>
                                    <td><?= esc($k['nama_kelas']) ?></td>
                                    <td class="ah-caption">
                                        <?= esc($k['tahun_akademik']) ?> /
                                        <?= ucfirst($k['semester']) ?>
                                    </td>
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-primary"
                                           href="<?= site_url('mahasiswa/kelas/' . (int) $k['id'] . '/daftar') ?>">
                                            <i class="bi bi-person-plus" aria-hidden="true"></i> Daftar
                                        </a>
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
