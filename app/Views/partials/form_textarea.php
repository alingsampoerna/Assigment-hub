<?php
/**
 * Komponen textarea CampusCare.
 * Wajib : id, name, label
 * Opsional: value, error, hint, rows, required, max (batas karakter)
 */
$value    = $value ?? '';
$error    = $error ?? null;
$hint     = $hint ?? null;
$rows     = $rows ?? 5;
$required = $required ?? true;
$max      = $max ?? null;

$describedBy = [];
if ($hint)  { $describedBy[] = $id . '-hint'; }
if ($max)   { $describedBy[] = $id . '-counter-status'; }
if ($error) { $describedBy[] = $id . '-error'; }
?>
<div class="mb-3">
    <label for="<?= esc($id, 'attr') ?>" class="form-label cc-label">
        <?= esc($label) ?>
        <?php if ($required): ?><span class="cc-required" aria-hidden="true">*</span><?php endif; ?>
    </label>

    <textarea
        class="form-control<?= $error ? ' is-invalid' : '' ?>"
        id="<?= esc($id, 'attr') ?>"
        name="<?= esc($name, 'attr') ?>"
        rows="<?= (int) $rows ?>"
        <?= $required ? 'required aria-required="true"' : '' ?>
        <?= $error ? 'aria-invalid="true"' : '' ?>
        <?= $describedBy ? 'aria-describedby="' . esc(implode(' ', $describedBy), 'attr') . '"' : '' ?>
    ><?= esc($value) ?></textarea>

    <?php if ($hint || $max): ?>
        <div class="d-flex justify-content-between align-items-start gap-3">
            <p id="<?= esc($id, 'attr') ?>-hint" class="cc-hint"><?= $hint ? esc($hint) : '' ?></p>
            <?php if ($max): ?>
                <p id="<?= esc($id, 'attr') ?>-counter" class="cc-counter" aria-hidden="true"
                   data-counter-for="#<?= esc($id, 'attr') ?>" data-max="<?= (int) $max ?>">0/<?= (int) $max ?></p>
            <?php endif; ?>
        </div>
        <?php if ($max): ?>
            <p id="<?= esc($id, 'attr') ?>-counter-status" class="visually-hidden" role="status" aria-live="polite"></p>
        <?php endif; ?>
    <?php endif; ?>

    <?php if ($error): ?>
        <p id="<?= esc($id, 'attr') ?>-error" class="cc-field-error">
            <i class="bi bi-exclamation-circle-fill" aria-hidden="true"></i>
            <span><span class="visually-hidden">Kesalahan: </span><?= esc($error) ?></span>
        </p>
    <?php endif; ?>
</div>