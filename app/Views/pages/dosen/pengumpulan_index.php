<?= $this->extend('layouts/dosen') ?>
<?= $this->section('content') ?>

<div class="ah-page">
    <div class="container-fluid px-3 px-lg-4">

        <div class="mb-4">
            <a class="ah-back" href="<?= site_url('dosen/tugas/' . (int) $tugas['id']) ?>">
                <i class="bi bi-arrow-left" aria-hidden="true"></i> Kembali ke Detail Tugas
            </a>
        </div>

        <div class="ah-page__header">
            <div>
                <h1>Pengumpulan</h1>
                <p class="text-muted-ah mb-0"><?= esc($tugas['judul']) ?></p>
            </div>
        </div>

        <?php
        $flashSuccess = session()->getFlashdata('success');
        $flashError   = session()->getFlashdata('error');
        ?>
        <?= $flashSuccess ? cc_alert('success', $flashSuccess) : '' ?>
        <?= $flashError   ? cc_alert('error',   $flashError)   : '' ?>

        <!-- Statistik -->
        <div class="row g-3 mb-4">
            <?php
            $statCards = [
                ['label' => 'Total Harus Kumpul', 'value' => $statistik['total_mahasiswa'], 'icon' => 'bi-people',              'color' => '--ah-primary'],
                ['label' => 'Sudah Dikumpulkan',  'value' => $statistik['sudah_kumpul'],    'icon' => 'bi-cloud-upload-fill',   'color' => '--ah-success'],
                ['label' => 'Belum Dikumpulkan',  'value' => $statistik['belum_kumpul'],    'icon' => 'bi-hourglass-split',     'color' => '--ah-warning'],
                ['label' => 'Terlambat',          'value' => $statistik['terlambat'],       'icon' => 'bi-clock-history',       'color' => '--ah-danger'],
                ['label' => 'Sudah Dinilai',      'value' => $statistik['dinilai'],         'icon' => 'bi-award-fill',          'color' => '--ah-info'],
            ];
            foreach ($statCards as $c):
            ?>
                <div class="col-6 col-xl">
                    <div class="ah-admin-panel text-center p-3">
                        <i class="bi <?= $c['icon'] ?> fs-4 mb-1" style="color:var(<?= $c['color'] ?>);" aria-hidden="true"></i>
                        <p class="h4 fw-bold mb-0"><?= (int) $c['value'] ?></p>
                        <p class="ah-caption mb-0"><?= esc($c['label']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Tabel pengumpulan -->
        <div class="ah-panel">
            <?php if ($pengumpulan === []): ?>
                <div class="ah-empty">
                    <i class="bi bi-cloud-upload ah-empty__icon" aria-hidden="true"></i>
                    <h3 class="mt-3">Belum ada pengumpulan</h3>
                    <p class="text-muted-ah mb-0">Mahasiswa belum mengumpulkan tugas ini.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table ah-table">
                        <caption class="visually-hidden">Daftar pengumpulan mahasiswa</caption>
                        <thead>
                            <tr>
                                <th scope="col">Mahasiswa</th>
                                <th scope="col">Waktu Kumpul</th>
                                <th scope="col">Status</th>
                                <th scope="col">Nilai</th>
                                <th scope="col" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pengumpulan as $p):
                                $badgeClass = $p['status'] === 'terlambat' ? 'ah-badge--overdue' : 'ah-badge--submitted';
                                $badgeLabel = $p['status'] === 'terlambat' ? 'Terlambat' : 'Tepat Waktu';
                            ?>
                                <tr>
                                    <td>
                                        <p class="ah-table__title mb-0"><?= esc($p['nama']) ?></p>
                                        <p class="ah-table__sub font-monospace"><?= esc($p['nim']) ?></p>
                                    </td>
                                    <td class="ah-caption"><?= cc_datetime_id($p['tanggal_pengumpulan']) ?></td>
                                    <td>
                                        <span class="ah-badge <?= $badgeClass ?>"><?= $badgeLabel ?></span>
                                    </td>
                                    <td>
                                        <?php if ($p['nilai'] !== null): ?>
                                            <span class="fw-bold"><?= (int) $p['nilai'] ?></span>
                                        <?php else: ?>
                                            <span class="text-muted-ah ah-caption">Belum dinilai</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-primary"
                                           href="<?= site_url('dosen/pengumpulan/' . (int) $p['id']) ?>">
                                            <i class="bi bi-eye" aria-hidden="true"></i> Periksa
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
