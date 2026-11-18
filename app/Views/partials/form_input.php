<?php
/**
 * Komponen input CampusCare.
 * Wajib : id, name, label
 * Opsional: type, value, error, hint, autocomplete, inputmode, placeholder, required
 */
$type         = $type ?? 'text';
$value        = ($type === 'password') ? '' : ($value ?? '');   // password tidak pernah diisi ulang
$error        = $error ?? null;
$hint         = $hint ?? null;
$required     = $required ?? true;
$autocomplete = $autocomplete ?? null;
$inputmode    = $inputmode ?? null;
$placeholder  = $placeholder ?? null;

$describedBy = [];
if ($hint)  { $describedBy[] = $id . '-hint'; }
if ($error) { $describedBy[] = $id . '-error'; }
?>
<div class="mb-3">
    <label for="<?= esc($id, 'attr') ?>" class="form-label cc-label">
        <?= esc($label) ?>
        <?php if ($required): ?><span class="cc-required" aria-hidden="true">*</span><?php endif; ?>
    </label>

    <?php if ($type === 'password'): ?><div class="input-group"><?php endif; ?>

    <input
        class="form-control<?= $error ? ' is-invalid' : '' ?>"
        type="<?= esc($type, 'attr') ?>"
        id="<?= esc($id, 'attr') ?>"
        name="<?= esc($name, 'attr') ?>"
        value="<?= esc($value, 'attr') ?>"
        <?= $required ? 'required aria-required="true"' : '' ?>
        <?= $error ? 'aria-invalid="true"' : '' ?>
        <?= $describedBy ? 'aria-describedby="' . esc(implode(' ', $describedBy), 'attr') . '"' : '' ?>
        <?= $autocomplete ? 'autocomplete="' . esc($autocomplete, 'attr') . '"' : '' ?>
        <?= $inputmode ? 'inputmode="' . esc($inputmode, 'attr') . '"' : '' ?>
        <?= $placeholder ? 'placeholder="' . esc($placeholder, 'attr') . '"' : '' ?>
    >

    <?php if ($type === 'password'): ?>
        <button type="button" class="btn cc-toggle-password"
                data-toggle-password="#<?= esc($id, 'attr') ?>"
                aria-label="Tampilkan password" aria-pressed="false">
            <i class="bi bi-eye" aria-hidden="true"></i>
        </button>
    </div>
    <?php endif; ?>

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