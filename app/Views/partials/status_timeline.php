<?php
/**
 * Timeline riwayat status.
 * Parameter:
 *   $riwayat : array [status, catatan, waktu], urut dari yang paling awal
 *   $status  : status laporan saat ini
 */
$langkahBerikut = [
    'menunggu' => 'Menunggu ditinjau oleh admin',
    'diproses' => 'Menunggu penyelesaian oleh petugas',
];
$jumlahRiwayat = count($riwayat);
?>
<?php if ($riwayat === []): ?>
    <p class="text-muted-cc mb-0">Belum ada riwayat.</p>
<?php else: ?>
    <ol class="cc-timeline">
        <?php foreach ($riwayat as $i => $item): ?>
            <?php $terbaru = ($i === $jumlahRiwayat - 1); ?>
            <li class="cc-timeline__item"<?= $terbaru ? ' aria-current="step"' : '' ?>>
                <span class="cc-timeline__dot" aria-hidden="true"></span>
                <?= cc_status_badge($item['status']) ?>
                <?php if ($terbaru): ?>
                    <span class="cc-timeline__current">Status saat ini</span>
                <?php endif; ?>
                <p class="cc-timeline__time">
                    <time datetime="<?= esc(date('c', strtotime($item['waktu'])), 'attr') ?>">
                        <?= esc(cc_datetime_id($item['waktu'])) ?>
                    </time>
                </p>
                <?php if (! empty($item['catatan'])): ?>
                    <p class="cc-timeline__note"><?= esc($item['catatan']) ?></p>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>

        <?php if (isset($langkahBerikut[$status])): ?>
            <li class="cc-timeline__item is-pending">
                <span class="cc-timeline__dot" aria-hidden="true"></span>
                <p class="cc-timeline__pending">
                    Tahap berikutnya: <?= esc($langkahBerikut[$status]) ?>
                </p>
            </li>
        <?php endif; ?>
    </ol>
<?php endif; ?>