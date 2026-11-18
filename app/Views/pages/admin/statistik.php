<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php
$cards = [
    ['label' => 'Total laporan',          'value' => $total,                  'icon' => 'bi-collection',        'modifier' => 'total'],
    ['label' => 'Menunggu',               'value' => $perStatus['menunggu'],  'icon' => 'bi-clock',             'modifier' => 'waiting'],
    ['label' => 'Tingkat penyelesaian',   'value' => $tingkatSelesai . '%',   'icon' => 'bi-check-circle-fill', 'modifier' => 'done'],
    ['label' => 'Ditolak',                'value' => $perStatus['ditolak'],   'icon' => 'bi-x-circle-fill',     'modifier' => 'rejected'],
];
?>

<div class="cc-page">
    <div class="container-fluid px-3 px-lg-4">

        <div class="cc-page__header">
            <div>
                <h1>Statistik Pengaduan</h1>
                <p class="text-muted-cc mb-0">Periode: <?= esc($labelPeriode) ?>.</p>
            </div>
        </div>

        <!-- Filter periode -->
        <form class="cc-filter" action="<?= site_url('admin/statistik') ?>" method="get"
              aria-labelledby="judul-filter-periode">
            <h2 id="judul-filter-periode" class="visually-hidden">Pilih periode statistik</h2>
            <div class="row g-3 align-items-end">
                <div class="col-12 col-sm-6 col-lg-4">
                    <label for="periode" class="form-label cc-label">Periode</label>
                    <select class="form-select" id="periode" name="periode">
                        <?php foreach ($opsiPeriode as $nilai => $label): ?>
                            <option value="<?= esc((string) $nilai, 'attr') ?>"<?= (string) $nilai === $periode ? ' selected' : '' ?>>
                                <?= esc($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel" aria-hidden="true"></i> Terapkan
                    </button>
                </div>
            </div>
        </form>

        <?php if ($total === 0): ?>

            <div class="cc-panel">
                <div class="cc-empty">
                    <i class="bi bi-bar-chart-line cc-empty__icon" aria-hidden="true"></i>
                    <h2 class="h3 mt-3">Tidak ada laporan pada periode ini</h2>
                    <p class="text-muted-cc mb-0">Pilih periode lain untuk melihat statistik.</p>
                </div>
            </div>

        <?php else: ?>

            <!-- Ringkasan -->
            <section aria-labelledby="judul-ringkasan" class="mb-5">
                <h2 id="judul-ringkasan" class="h3 mb-3">Ringkasan</h2>
                <ul class="list-unstyled row g-3 mb-0">
                    <?php foreach ($cards as $card): ?>
                        <li class="col-6 col-xl-3">
                            <div class="cc-stat cc-stat--<?= esc($card['modifier'], 'attr') ?>">
                                <span class="cc-stat__icon" aria-hidden="true">
                                    <i class="bi <?= esc($card['icon'], 'attr') ?>"></i>
                                </span>
                                <div>
                                    <p class="cc-stat__label"><?= esc($card['label']) ?></p>
                                    <p class="cc-stat__value"><?= esc((string) $card['value']) ?></p>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>

            <div class="row g-4 mb-4">
                <!-- Per kategori -->
                <div class="col-xl-7">
                    <section class="cc-admin-panel" aria-labelledby="judul-kategori">
                        <h2 id="judul-kategori" class="h3 mb-1">Laporan per Kategori</h2>
                        <p class="cc-caption mb-3">Periode: <?= esc($labelPeriode) ?>.</p>
                        <?= $this->setVar('items', $itemKategori)
                                 ->setVar('total', $total)
                                 ->setVar('kolomLabel', 'Kategori')
                                 ->setVar('caption', 'Jumlah laporan per kategori pada periode ' . $labelPeriode)
                                 ->include('partials/bar_chart') ?>
                    </section>
                </div>

                <!-- Per status -->
                <div class="col-xl-5">
                    <section class="cc-admin-panel" aria-labelledby="judul-status">
                        <h2 id="judul-status" class="h3 mb-1">Laporan per Status</h2>
                        <p class="cc-caption mb-3">Periode: <?= esc($labelPeriode) ?>.</p>
                        <?= $this->setVar('items', $itemStatus)
                                 ->setVar('total', $total)
                                 ->setVar('kolomLabel', 'Status')
                                 ->setVar('caption', 'Jumlah laporan per status pada periode ' . $labelPeriode)
                                 ->include('partials/bar_chart') ?>
                    </section>
                </div>
            </div>

            <!-- Tren per bulan -->
            <section class="cc-admin-panel" aria-labelledby="judul-tren">
                <h2 id="judul-tren" class="h3 mb-1">Laporan per Bulan</h2>
                <p class="cc-caption mb-3">Seluruh periode, tidak terpengaruh filter di atas.</p>
                <?= $this->setVar('items', $itemTren)
                         ->setVar('total', $totalSemua)
                         ->setVar('kolomLabel', 'Bulan')
                         ->setVar('caption', 'Jumlah laporan per bulan')
                         ->include('partials/bar_chart') ?>
            </section>

        <?php endif; ?>

    </div>
</div>
<?= $this->endSection() ?>