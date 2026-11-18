<?php
/**
 * Komponen select CampusCare.
 * Wajib : id, name, label, options (array nilai => teks)
 * Opsional: selected, error, hint, placeholder, required
 */
$selected    = (string) ($selected ?? '');
$error       = $error ?? null;
$hint        = $hint ?? null;
$placeholder = $placeholder ?? 'Pilih salah satu';
$required    = $required ?? true;

$describedBy = [];
if ($hint)  { $describedBy[] = $id . '-hint'; }
if ($error) { $describedBy[] = $id . '-error'; }
?>
<div class="mb-3">
    <label for="<?= esc($id, 'attr') ?>" class="form-label cc-label">
        <?= esc($label) ?>
        <?php if ($required): ?><span class="cc-required" aria-hidden="true">*</span><?php endif; ?>
    </label>

    <select
        class="form-select<?= $error ? ' is-invalid' : '' ?>"
        id="<?= esc($id, 'attr') ?>"
        name="<?= esc($name, 'attr') ?>"
        <?= $required ? 'required aria-required="true"' : '' ?>
        <?= $error ? 'aria-invalid="true"' : '' ?>
        <?= $describedBy ? 'aria-describedby="' . esc(implode(' ', $describedBy), 'attr') . '"' : '' ?>
    >
        <option value=""><?= esc($placeholder) ?></option>
        <?php foreach ($options as $value => $text): ?>
            <option value="<?= esc((string) $value, 'attr') ?>"<?= (string) $value === $selected ? ' selected' : '' ?>>
                <?= esc($text) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <?php if ($hint): ?>
        <p id="<?= esc($id, 'attr') ?>-hint" class="cc-hint"><?= esc($hint) ?></p>
    <?php endif; ?>

    <?php if ($error): ?>
        <p id="<?= esc($id, 'attr') ?>-error" class="cc-field-error">
            <i class="bi bi-exclamation-circle-fill" aria-hidden="true"></i>
            <span><span class="visually-hidden">Kesalahan: </span><?= esc($error) ?></span>
        </p>
    <?php endif; ?>
</div>