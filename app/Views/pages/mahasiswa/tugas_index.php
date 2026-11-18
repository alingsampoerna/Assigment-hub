<?= $this->extend('layouts/student') ?>
<?= $this->section('content') ?>

<div class="ah-page">
    <div class="container">

        <div class="ah-page__header">
            <div>
                <h1>Daftar Tugas</h1>
                <p class="text-muted-ah mb-0">Tugas dari kelas yang Anda ikuti.</p>
            </div>
        </div>

        <?php
        $flashSuccess = session()->getFlashdata('success');
        $flashError   = session()->getFlashdata('error');
        ?>
        <?= $flashSuccess ? cc_alert('success', $flashSuccess) : '' ?>
        <?= $flashError   ? cc_alert('error',   $flashError)   : '' ?>

        <!-- Filter -->
        <form method="get" action="<?= site_url('mahasiswa/tugas') ?>" class="ah-filter mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-sm-6 col-lg-5">
                    <label for="q" class="form-label fw-semibold">Cari</label>
                    <input type="search" id="q" name="q" class="form-control"
                           placeholder="Judul tugas…"
                           value="<?= esc($filter['q']) ?>">
                </div>
                <div class="col-sm-4 col-lg-3">
                    <label for="status" class="form-label fw-semibold">Status</label>
                    <select id="status" name="status" class="form-select">
                        <option value="">Semua</option>
                        <option value="belum"     <?= $filter['status'] === 'belum'     ? 'selected' : '' ?>>Belum Dikumpulkan</option>
                        <option value="sudah"     <?= $filter['status'] === 'sudah'     ? 'selected' : '' ?>>Sudah Dikumpulkan</option>
                        <option value="terlambat" <?= $filter['status'] === 'terlambat' ? 'selected' : '' ?>>Terlambat</option>
                    </select>
                </div>
                <div class="col-sm-auto">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search" aria-hidden="true"></i> Cari
                    </button>
                </div>
                <?php if ($query !== []): ?>
                    <div class="col-sm-auto">
                        <a href="<?= site_url('mahasiswa/tugas') ?>" class="btn btn-outline-secondary">Reset</a>
                    </div>
                <?php endif; ?>
            </div>
        </form>

        <div class="ah-panel">
            <?php if ($items === []): ?>
                <div class="ah-empty">
                    <i class="bi bi-journal-text ah-empty__icon" aria-hidden="true"></i>
                    <h3 class="mt-3">Tidak ada tugas</h3>
                    <p class="text-muted-ah">Tugas dari kelas yang Anda ikuti akan muncul di sini.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table ah-table">
                        <caption class="visually-hidden">Daftar tugas</caption>
                        <thead>
                            <tr>
                                <th scope="col">Tugas</th>
                                <th scope="col">Deadline</th>
                                <th scope="col">Status</th>
                                <th scope="col" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $t):
                                $lewat   = (bool) $t['sudah_lewat'];
                                $selisih = (int) round((strtotime($t['deadline']) - time()) / 86400);

                                if ($t['pengumpulan_id']) {
                                    $badgeClass = $t['status_pengumpulan'] === 'terlambat' ? 'ah-badge--overdue' : 'ah-badge--submitted';
                                    $badgeLabel = $t['status_pengumpulan'] === 'terlambat' ? 'Terlambat' : 'Sudah Dikumpulkan';
                                } else {
                                    $badgeClass = $lewat ? 'ah-badge--overdue' : 'ah-badge--pending';
                                    $badgeLabel = $lewat ? 'Melewati Deadline' : 'Belum Dikumpulkan';
                                }
                            ?>
                                <tr>
                                    <td>
                                        <p class="ah-table__title mb-0">
                                            <a href="<?= site_url('mahasiswa/tugas/' . (int) $t['id']) ?>">
                                                <?= esc($t['judul']) ?>
                                            </a>
                                        </p>
                                        <p class="ah-table__sub">
                                            <?= esc($t['kode_mata_kuliah'] . ' — ' . $t['nama_mata_kuliah']) ?>
                                        </p>
                                    </td>
                                    <td>
                                        <span class="ah-deadline ah-deadline--<?= $lewat ? 'passed' : ($selisih <= 1 ? 'urgent' : ($selisih <= 3 ? 'near' : 'ok')) ?>">
                                            <i class="bi bi-clock" aria-hidden="true"></i>
                                            <?= cc_datetime_id($t['deadline']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="ah-badge <?= $badgeClass ?>">
                                            <?= esc($badgeLabel) ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-outline-primary"
                                           href="<?= site_url('mahasiswa/tugas/' . (int) $t['id']) ?>">
                                            <i class="bi bi-eye" aria-hidden="true"></i> Detail
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

<?= $this->endSection() ?>
