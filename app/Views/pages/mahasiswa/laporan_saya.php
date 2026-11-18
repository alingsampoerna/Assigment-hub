<?= $this->extend('layouts/student') ?>

<?= $this->section('content') ?>
<?php
$adaFilter = $filter['q'] !== '' || $filter['status'] !== '' || $filter['kategori'] !== '';
$dari      = $total > 0 ? (($page - 1) * $perPage) + 1 : 0;
$sampai    = min($page * $perPage, $total);
?>

<div class="cc-page">
    <div class="container">

        <div class="cc-page__header">
            <div>
                <h1>Laporan Saya</h1>
                <p class="text-muted-cc mb-0">Semua laporan kerusakan yang pernah Anda kirim.</p>
            </div>
            <a class="btn btn-primary btn-lg" href="<?= site_url('mahasiswa/laporan/baru') ?>">
                <i class="bi bi-plus-circle" aria-hidden="true"></i>
                Buat Laporan
            </a>
        </div>

        <?php if ($totalSemua === 0): ?>

            <div class="cc-panel">
                <div class="cc-empty">
                    <i class="bi bi-inbox cc-empty__icon" aria-hidden="true"></i>
                    <h2 class="h3 mt-3">Belum ada laporan</h2>
                    <p class="text-muted-cc">Temukan fasilitas yang rusak? Buat laporan pertama Anda.</p>
                    <a class="btn btn-primary" href="<?= site_url('mahasiswa/laporan/baru') ?>">Buat Laporan</a>
                </div>
            </div>

        <?php else: ?>

            <!-- Pencarian dan filter -->
            <form class="cc-filter" action="<?= site_url('mahasiswa/laporan') ?>" method="get"
                  role="search" aria-labelledby="judul-filter">
                <h2 id="judul-filter" class="visually-hidden">Cari dan saring laporan</h2>

                <div class="row g-3 align-items-end">
                    <div class="col-12 col-lg-4">
                        <label for="q" class="form-label cc-label">Cari laporan</label>
                        <input type="search" class="form-control" id="q" name="q"
                               value="<?= esc($filter['q'], 'attr') ?>" maxlength="100"
                               placeholder="Judul atau lokasi">
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

                    <div class="col-12 col-lg-2 d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search" aria-hidden="true"></i> Terapkan
                        </button>
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
                            Menampilkan <?= (int) $dari ?>&ndash;<?= (int) $sampai ?> dari <?= (int) $total ?> laporan<?= $adaFilter ? ' (hasil filter)' : '' ?>.
                        <?php endif; ?>
                    </p>
                    <?php if ($adaFilter): ?>
                        <a href="<?= site_url('mahasiswa/laporan') ?>">
                            <i class="bi bi-x-circle" aria-hidden="true"></i> Atur ulang filter
                        </a>
                    <?php endif; ?>
                </div>

                <div class="cc-panel">
                    <?php if ($total === 0): ?>
                        <div class="cc-empty">
                            <i class="bi bi-search cc-empty__icon" aria-hidden="true"></i>
                            <h3 class="mt-3">Laporan tidak ditemukan</h3>
                            <p class="text-muted-cc">Coba kata kunci lain atau ubah filter status dan kategori.</p>
                            <a class="btn btn-outline-primary" href="<?= site_url('mahasiswa/laporan') ?>">Atur ulang filter</a>
                        </div>
                    <?php else: ?>
                        <?= $this->setVar('reports', $reports)
                                 ->setVar('caption', 'Daftar laporan Anda')
                                 ->include('partials/report_table') ?>
                    <?php endif; ?>
                </div>

                <?php if ($totalPages > 1): ?>
                    <div class="mt-4">
                        <?= $this->setVar('page', $page)
                                 ->setVar('totalPages', $totalPages)
                                 ->setVar('query', $query)
                                 ->setVar('baseUrl', site_url('mahasiswa/laporan'))
                                 ->include('partials/pagination') ?>
                    </div>
                <?php endif; ?>
            </section>

        <?php endif; ?>

    </div>
</div>
<?= $this->endSection() ?>