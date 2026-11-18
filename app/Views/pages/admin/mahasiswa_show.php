<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="ah-page">
    <div class="container-fluid px-3 px-lg-4">

        <div class="mb-4">
            <a class="ah-back" href="<?= site_url('admin/mahasiswa') ?>">
                <i class="bi bi-arrow-left" aria-hidden="true"></i> Kembali ke Mahasiswa
            </a>
        </div>

        <?php
        $flashSuccess = session()->getFlashdata('success');
        $flashError   = session()->getFlashdata('error');
        ?>
        <?= $flashSuccess ? cc_alert('success', $flashSuccess) : '' ?>
        <?= $flashError   ? cc_alert('error',   $flashError)   : '' ?>

        <div class="row g-4">

            <!-- Info akun -->
            <div class="col-lg-4">
                <div class="ah-admin-panel">
                    <div class="text-center mb-4">
                        <div class="mx-auto mb-3" style="width:72px;height:72px;background:var(--ah-primary-soft);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:2rem;color:var(--ah-primary);">
                            <i class="bi bi-person-circle" aria-hidden="true"></i>
                        </div>
                        <h2 class="h4 mb-1"><?= esc($mhs['nama']) ?></h2>
                        <p class="ah-caption mb-2"><?= esc($mhs['email']) ?></p>
                        <?php if ($mhs['status'] === 'aktif'): ?>
                            <span class="ah-badge ah-badge--active">Aktif</span>
                        <?php else: ?>
                            <span class="ah-badge ah-badge--closed">Nonaktif</span>
                        <?php endif; ?>
                    </div>

                    <dl class="ah-dl">
                        <dt>NIM</dt>
                        <dd class="font-monospace"><?= esc($mhs['nim']) ?></dd>
                        <dt>Terdaftar</dt>
                        <dd><?= cc_date_id($mhs['created_at'] ?? '') ?></dd>
                    </dl>

                    <form action="<?= site_url('admin/mahasiswa/' . (int) $mhs['id'] . '/status') ?>" method="post">
                        <?= csrf_field() ?>
                        <button type="submit"
                                class="btn w-100 <?= $mhs['status'] === 'aktif' ? 'btn-outline-danger' : 'btn-success' ?>">
                            <?= $mhs['status'] === 'aktif' ? 'Nonaktifkan Akun' : 'Aktifkan Akun' ?>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Kelas yang diikuti -->
            <div class="col-lg-8">
                <div class="ah-admin-panel">
                    <h2 class="h5 mb-3">
                        Kelas yang Diikuti
                        <span class="ah-badge ah-badge--submitted ms-2"><?= count($kelasList) ?></span>
                    </h2>

                    <?php if ($kelasList === []): ?>
                        <p class="text-muted-ah ah-caption text-center py-3 mb-0">
                            Mahasiswa ini belum terdaftar di kelas manapun.
                        </p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table ah-table">
                                <thead>
                                    <tr>
                                        <th scope="col">Mata Kuliah</th>
                                        <th scope="col">Kelas</th>
                                        <th scope="col">Tahun / Sem</th>
                                        <th scope="col" class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($kelasList as $k): ?>
                                        <tr>
                                            <td>
                                                <p class="ah-table__title mb-0"><?= esc($k['nama_mata_kuliah']) ?></p>
                                                <p class="ah-table__sub"><?= esc($k['kode_mata_kuliah']) ?></p>
                                            </td>
                                            <td><?= esc($k['nama_kelas']) ?></td>
                                            <td><?= esc($k['tahun_akademik']) ?> / <?= ucfirst($k['semester']) ?></td>
                                            <td class="text-end">
                                                <a class="btn btn-sm btn-outline-secondary"
                                                   href="<?= site_url('admin/kelas/' . (int) $k['id'] . '/anggota') ?>">
                                                    <i class="bi bi-people" aria-hidden="true"></i> Anggota
                                                </a>
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
    </div>
</div>

<?= $this->endSection() ?>
