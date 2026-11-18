<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="ah-page">
    <div class="container-fluid px-3 px-lg-4">

        <!-- Header -->
        <div class="mb-4">
            <a class="ah-back" href="<?= site_url('admin/kelas') ?>">
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

        <div class="row g-4">

            <!-- Daftar Anggota -->
            <div class="col-lg-7">
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
                                        <th scope="col" class="text-end">Aksi</th>
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
                                            <td>
                                                <div class="ah-row-actions">
                                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                                            data-bs-toggle="modal" data-bs-target="#modal-hapus-anggota"
                                                            data-hapus-url="<?= site_url('admin/kelas/' . (int) $kelas['id'] . '/anggota/' . (int) $a['id'] . '/hapus') ?>"
                                                            data-hapus-nama="<?= esc($a['nama'], 'attr') ?>">
                                                        <i class="bi bi-person-dash" aria-hidden="true"></i> Hapus
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Tambah Anggota -->
            <div class="col-lg-5">
                <div class="ah-admin-panel">
                    <h2 class="h5 mb-3">Tambah Mahasiswa</h2>

                    <!-- Pencarian calon -->
                    <form method="get" action="<?= site_url('admin/kelas/' . (int) $kelas['id'] . '/anggota') ?>" class="mb-4">
                        <div class="input-group">
                            <input type="search" name="cari" class="form-control"
                                   placeholder="Cari nama atau NIM…"
                                   value="<?= esc($q) ?>">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="bi bi-search" aria-hidden="true"></i>
                            </button>
                        </div>
                    </form>

                    <?php if ($calon === []): ?>
                        <p class="text-muted-ah ah-caption text-center py-3">
                            <?= $q !== '' ? 'Tidak ada mahasiswa yang cocok dengan pencarian.' : 'Semua mahasiswa aktif sudah terdaftar di kelas ini.' ?>
                        </p>
                    <?php else: ?>
                        <ul class="list-unstyled mb-0">
                            <?php foreach ($calon as $c): ?>
                                <li class="d-flex align-items-center justify-content-between gap-2 py-2"
                                    style="border-bottom:1px solid var(--ah-border);">
                                    <div>
                                        <p class="fw-semibold mb-0"><?= esc($c['nama']) ?></p>
                                        <p class="ah-caption mb-0"><?= esc($c['nim']) ?> &bull; <?= esc($c['email']) ?></p>
                                    </div>
                                    <form action="<?= site_url('admin/kelas/' . (int) $kelas['id'] . '/anggota') ?>" method="post">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="mahasiswa_id" value="<?= (int) $c['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            <i class="bi bi-person-plus" aria-hidden="true"></i> Tambah
                                        </button>
                                    </form>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <p class="ah-caption mt-2 mb-0">Menampilkan maks. 20 hasil.</p>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal hapus anggota -->
<div class="modal fade" id="modal-hapus-anggota" tabindex="-1"
     aria-labelledby="modal-hapus-anggota-judul" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="#" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h2 class="modal-title h5" id="modal-hapus-anggota-judul">Hapus Anggota</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">
                        Hapus <strong data-hapus-nama></strong> dari kelas ini?
                        Tindakan ini tidak dapat dibatalkan.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-person-dash" aria-hidden="true"></i> Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.getElementById('modal-hapus-anggota').addEventListener('show.bs.modal', function (e) {
    var btn  = e.relatedTarget;
    var form = this.querySelector('form');
    form.action = btn.dataset.hapusUrl;
    this.querySelector('[data-hapus-nama]').textContent = btn.dataset.hapusNama;
});
</script>
<?= $this->endSection() ?>
