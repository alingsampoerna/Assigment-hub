<?php
/**
 * Tabel laporan untuk admin (layar >= md) dan daftar kartu (layar kecil).
 * Parameter:
 *   $reports : array laporan (id, judul, lokasi, kategori, status, created_at, pelapor, nim)
 *   $caption : keterangan tabel untuk pembaca layar
 */
$caption = $caption ?? 'Daftar laporan';
?>

<!-- Tabel: layar sedang ke atas -->
<div class="d-none d-md-block">
    <div class="table-responsive">
        <table class="table cc-table">
            <caption class="visually-hidden"><?= esc($caption) ?></caption>
            <thead>
                <tr>
                    <th scope="col">Judul laporan</th>
                    <th scope="col">Pelapor</th>
                    <th scope="col">Kategori</th>
                    <th scope="col">Tanggal</th>
                    <th scope="col">Status</th>
                    <th scope="col"><span class="visually-hidden">Aksi</span></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reports as $report): ?>
                    <tr>
                        <td>
                            <p class="cc-table__title"><?= esc($report['judul']) ?></p>
                            <p class="cc-table__sub"><?= esc($report['lokasi']) ?></p>
                        </td>
                        <td>
                            <p class="mb-0"><?= esc($report['pelapor']) ?></p>
                            <p class="cc-table__sub">NIM <?= esc($report['nim']) ?></p>
                        </td>
                        <td><?= esc($report['kategori']) ?></td>
                        <td>
                            <time datetime="<?= esc(date('Y-m-d', strtotime($report['created_at'])), 'attr') ?>">
                                <?= esc(cc_date_id($report['created_at'])) ?>
                            </time>
                        </td>
                        <td><?= cc_status_badge($report['status']) ?></td>
                        <td class="text-end">
                            <a class="btn btn-outline-primary btn-sm"
                               href="<?= site_url('admin/laporan/' . (int) $report['id']) ?>">
                                Kelola<span class="visually-hidden"> laporan <?= esc($report['judul']) ?></span>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Daftar kartu: layar kecil -->
<ul class="cc-report-list d-md-none">
    <?php foreach ($reports as $report): ?>
        <li class="cc-report-item">
            <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                <p class="cc-report-item__title mb-0"><?= esc($report['judul']) ?></p>
                <?= cc_status_badge($report['status']) ?>
            </div>
            <p class="cc-report-item__meta">
                <?= esc($report['pelapor']) ?> (NIM <?= esc($report['nim']) ?>)<br>
                <?= esc($report['kategori']) ?> &middot; <?= esc($report['lokasi']) ?><br>
                <time datetime="<?= esc(date('Y-m-d', strtotime($report['created_at'])), 'attr') ?>">
                    <?= esc(cc_date_id($report['created_at'])) ?>
                </time>
            </p>
            <a class="btn btn-outline-primary btn-sm"
               href="<?= site_url('admin/laporan/' . (int) $report['id']) ?>">
                Kelola<span class="visually-hidden"> laporan <?= esc($report['judul']) ?></span>
            </a>
        </li>
    <?php endforeach; ?>
</ul>