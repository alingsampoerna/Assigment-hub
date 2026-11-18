<?php
/**
 * Navigasi halaman.
 * Parameter:
 *   $page       : halaman aktif
 *   $totalPages : jumlah halaman
 *   $query      : filter aktif (tanpa 'page'), array
 *   $baseUrl    : alamat dasar, contoh site_url('mahasiswa/laporan')
 */
$linkTo = static function (int $target) use ($baseUrl, $query): string {
    return $baseUrl . '?' . http_build_query(array_merge($query, ['page' => $target]));
};

// Tampilkan maksimal 5 nomor di sekitar halaman aktif
$start = max(1, $page - 2);
$end   = min($totalPages, $page + 2);
?>
<nav aria-label="Navigasi halaman laporan">
    <ul class="pagination justify-content-center flex-wrap mb-2">

        <li class="page-item<?= $page <= 1 ? ' disabled' : '' ?>">
            <?php if ($page > 1): ?>
                <a class="page-link" href="<?= esc($linkTo($page - 1), 'attr') ?>">
                    <span aria-hidden="true">&laquo;</span>
                    <span class="visually-hidden">Halaman sebelumnya</span>
                </a>
            <?php else: ?>
                <span class="page-link" aria-disabled="true">
                    <span aria-hidden="true">&laquo;</span>
                    <span class="visually-hidden">Halaman sebelumnya (tidak tersedia)</span>
                </span>
            <?php endif; ?>
        </li>

        <?php for ($n = $start; $n <= $end; $n++): ?>
            <?php if ($n === $page): ?>
                <li class="page-item active">
                    <span class="page-link" aria-current="page">
                        <span class="visually-hidden">Halaman </span><?= $n ?>
                    </span>
                </li>
            <?php else: ?>
                <li class="page-item">
                    <a class="page-link" href="<?= esc($linkTo($n), 'attr') ?>">
                        <span class="visually-hidden">Halaman </span><?= $n ?>
                    </a>
                </li>
            <?php endif; ?>
        <?php endfor; ?>

        <li class="page-item<?= $page >= $totalPages ? ' disabled' : '' ?>">
            <?php if ($page < $totalPages): ?>
                <a class="page-link" href="<?= esc($linkTo($page + 1), 'attr') ?>">
                    <span aria-hidden="true">&raquo;</span>
                    <span class="visually-hidden">Halaman berikutnya</span>
                </a>
            <?php else: ?>
                <span class="page-link" aria-disabled="true">
                    <span aria-hidden="true">&raquo;</span>
                    <span class="visually-hidden">Halaman berikutnya (tidak tersedia)</span>
                </span>
            <?php endif; ?>
        </li>

    </ul>
    <p class="cc-caption text-center mb-0">Halaman <?= (int) $page ?> dari <?= (int) $totalPages ?></p>
</nav>