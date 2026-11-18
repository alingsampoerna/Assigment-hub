<?php
/**
 * Parameter: errors (array [nama_field => pesan])
 * Id kolom sama dengan nama field, sehingga tautan #field mengarah ke input yang salah.
 */
?>
<div id="ringkasan-error" class="cc-alert cc-alert--danger"
     role="group" aria-labelledby="ringkasan-error-judul" tabindex="-1">
    <i class="bi bi-exclamation-triangle-fill cc-alert__icon" aria-hidden="true"></i>
    <div>
        <h2 id="ringkasan-error-judul" class="cc-alert__title">
            Periksa kembali isian Anda (<?= count($errors) ?> kesalahan)
        </h2>
        <ul>
            <?php foreach ($errors as $field => $message): ?>
                <li><a href="#<?= esc($field, 'attr') ?>"><?= esc($message) ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>