<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php
$adaFilter = false;
foreach ($filter as $nilai) {
    if ($nilai !== '') {
        $adaFilter = true;
        break;
    }
}
$awal  = $total > 0 ? (($page - 1) * $perPage) + 1 : 0;
$akhir = min($page * $perPage, $total);
?>

<div class="cc-page">
    <div class="container-fluid px-3 px-lg-4">

        <div class="cc-page__header">
            <div>
                <h1>Semua Laporan</h1>
                <p class="text-muted-cc mb-0">Laporan kerusakan fasilitas dari seluruh mahasiswa.</p>
            </div>
        </div>

        <?php if ($totalSemua === 0): ?>

            <div class="cc-panel">
                <div class="cc-empty">
                    <i class="bi bi-inbox cc-empty__icon" aria-hidden="true"></i>
                    <h2 class="h3 mt-3">Belum ada laporan masuk</h2>
                    <p class="text-muted-cc mb-0">Laporan dari mahasiswa akan muncul di sini.</p>
                </div>
            </div>

        <?php else: ?>

            <!-- Pencarian dan filter -->
            <form class="cc-filter" action="<?= site_url('admin/laporan') ?>" method="get"
                  role="search" aria-labelledby="judul-filter">
                <h2 id="judul-filter" class="visually-hidden">Cari dan saring laporan</h2>

                <div class="row g-3 align-items-end">
                    <div class="col-12 col-lg-6">
                        <label for="q" class="form-label cc-label">Cari laporan</label>
                        <input type="search" class="form-control" id="q" name="q"
                               value="<?= esc($filter['q'], 'attr') ?>" maxlength="100"
                               placeholder="Judul, lokasi, nama pelapor, atau NIM">
                    </div>

                    <div class="col-6 col-lg-3">
                        <label for="status" class="form-label cc-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Semua status</option>
                            <?php foreach ($statusList as $value => $label): ?>
                                <option value="<?= esc($value, 'attr') ?>"<?= $filter['status'] === $value ? ' selected' : '' ?>>
                                    <?= esc($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-6 col-lg-3">
                        <label for="kategori" class="form-label cc-label">Kategori</label>
                        <select class="form-select" id="kategori" name="kategori">
                            <option value="">Semua kategori</option>
                            <?php foreach ($kategori as $id => $nama): ?>
                                <option value="<?= (int) $id ?>"<?= $filter['kategori'] === (string) $id ? ' selected' : '' ?>>
                                    <?= esc($nama) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-6 col-lg-3">
                        <label for="dari" class="form-label cc-label">Dari tanggal</label>
                        <input type="date" class="form-control" id="dari" name="dari"
                               value="<?= esc($filter['dari'], 'attr') ?>">
                    </div>

                    <div class="col-6 col-lg-3">
                        <label for="sampai" class="form-label cc-label">Sampai tanggal</label>
                        <input type="date" class="form-control" id="sampai" name="sampai"
                               value="<?= esc($filter['sampai'], 'attr') ?>">
                    </div>

                    <div class="col-12 col-lg-6 d-flex flex-wrap gap-2 justify-content-lg-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search" aria-hidden="true"></i> Terapkan
                        </button>
                        <?php if ($adaFilter): ?>
                            <a class="btn btn-outline-primary" href="<?= site_url('admin/laporan') ?>">Atur ulang</a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>

            <section aria-labelledby="judul-daftar">
                <h2 id="judul-daftar" class="visually-hidden">Daftar laporan</h2>

                <div class="cc-result-info">
                    <p class="cc-caption mb-0" role="status">
                        <?php if ($total === 0): ?>
                            Tidak ada laporan yang cocok.
                        <?php else: ?>
                            Menampilkan <?= (int) $awal ?>&ndash;<?= (int) $akhir ?> dari <?= (int) $total ?> laporan<?= $adaFilter ? ' (hasil filter)' : '' ?>.
                        <?php endif; ?>
                    </p>
                </div>

                <div class="cc-panel">
                    <?php if ($total === 0): ?>
                        <div class="cc-empty">
                            <i class="bi bi-search cc-empty__icon" aria-hidden="true"></i>
                            <h3 class="mt-3">Laporan tidak ditemukan</h3>
                            <p class="text-muted-cc">Coba kata kunci lain atau ubah filter yang dipakai.</p>
                            <a class="btn btn-outline-primary" href="<?= site_url('admin/laporan') ?>">Atur ulang filter</a>
                        </div>
                    <?php else: ?>
                        <?= $this->setVar('reports', $reports)
                                 ->setVar('caption', 'Daftar seluruh laporan mahasiswa')
                                 ->include('partials/admin_report_table') ?>
                    <?php endif; ?>
                </div>

                <?php if ($totalPages > 1): ?>
                    <div class="mt-4">
                        <?= $this->setVar('page', $page)
                                ->setVar('totalPages', $totalPages)
                                ->setVar('query', $query)
                                ->setVar('baseUrl', site_url('admin/laporan'))
                                ->include('partials/pagination') ?>
                    </div>
                <?php endif; ?>
            </section>

        <?php endif; ?>

    </div>
</div>
<?= $this->endSection() ?>