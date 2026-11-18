<?php
/**
 * Parameter: type (success|error|warning|info), message, title (opsional)
 */
$config = [
    'success' => ['cc-alert--success', 'bi-check-circle-fill',         'status', 'Berhasil'],
    'error'   => ['cc-alert--danger',  'bi-exclamation-triangle-fill', 'alert',  'Terjadi kesalahan'],
    'warning' => ['cc-alert--warning', 'bi-exclamation-circle-fill',   'status', 'Peringatan'],
    'info'    => ['cc-alert--info',    'bi-info-circle-fill',          'status', 'Informasi'],
];

[$class, $icon, $role, $label] = $config[$type] ?? $config['info'];
?>
<div class="cc-alert <?= $class ?>" role="<?= $role ?>">
    <i class="bi <?= $icon ?> cc-alert__icon" aria-hidden="true"></i>
    <div>
        <span class="visually-hidden"><?= $label ?>: </span>
        <?php if (! empty($title)): ?>
            <p class="cc-alert__title"><?= esc($title) ?></p>
        <?php endif; ?>
        <p><?= esc($message) ?></p>
    </div>
</div>