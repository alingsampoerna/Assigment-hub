<?php
/**
 * Grafik batang horizontal (HTML + CSS) dengan tabel alternatif.
 * Parameter:
 *   $items      : array of ['label' => string, 'jumlah' => int, 'fill' => ''|success|warning|danger]
 *   $total      : total untuk menghitung persentase di tabel
 *   $kolomLabel : judul kolom pertama tabel alternatif
 *   $caption    : keterangan tabel alternatif (untuk pembaca layar)
 */
$items      = $items ?? [];
$total      = $total ?? 0;
$kolomLabel = $kolomLabel ?? 'Kategori';
$caption    = $caption ?? 'Data grafik';

$counts = array_column($items, 'jumlah');
$max    = $counts === [] ? 1 : max(1, ...$counts);
?>
<ul class="cc-bars">
    <?php foreach ($items as $item): ?>
        <?php
        $persen = (int) round($item['jumlah'] / $max * 100);
        if ($item['jumlah'] > 0 && $persen < 4) {
            $persen = 4; // agar batang kecil tetap terlihat
        }
        $fill = $item['fill'] ?? '';
        ?>
        <li class="cc-bars__row">
            <span class="cc-bars__label"><?= esc($item['label']) ?></span>
            <span class="cc-bars__track" aria-hidden="true">
                <span class="cc-bars__fill<?= $fill !== '' ? ' cc-bars__fill--' . esc($fill, 'attr') : '' ?>"
                      style="width: <?= $persen ?>%;"></span>
            </span>
            <span class="cc-bars__value">
                <?= (int) $item['jumlah'] ?><span class="visually-hidden"> laporan</span>
            </span>
        </li>
    <?php endforeach; ?>
</ul>

<details class="cc-details mt-3">
    <summary>Lihat data dalam bentuk tabel</summary>
    <div class="table-responsive">
        <table class="table cc-table">
            <caption class="visually-hidden"><?= esc($caption) ?></caption>
            <thead>
                <tr>
                    <th scope="col"><?= esc($kolomLabel) ?></th>
                    <th scope="col" class="text-end">Jumlah laporan</th>
                    <th scope="col" class="text-end">Persentase</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <th scope="row" class="fw-normal"><?= esc($item['label']) ?></th>
                        <td class="text-end"><?= (int) $item['jumlah'] ?></td>
                        <td class="text-end"><?= $total > 0 ? (int) round($item['jumlah'] / $total * 100) : 0 ?>%</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</details>