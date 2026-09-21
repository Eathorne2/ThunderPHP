<?php

namespace ThunderAdmin;

$vars = get_value();
extract(is_array($vars) ? $vars : [], EXTR_SKIP);

$admin_available_looks = $admin_available_looks ?? [];
$admin_active_look = $admin_active_look ?? active_admin_look();
$admin_look_settings = $admin_look_settings ?? [];
$admin_palette_fields = $admin_palette_fields ?? palette_fields();
?>
<form class="ta-admin-settings" method="post" action="<?= e(admin_url('settings')) ?>" data-ta-admin-settings>
    <?= function_exists('csrf') ? \csrf('thunder_admin_settings') : '' ?>

    <section class="ta-card ta-admin-settings__section">
        <div class="ta-card__header">
            <div class="ta-card__heading-wrap">
                <h2 class="ta-card__title">Admin look</h2>
                <p class="ta-card__subtitle">Choose the layout Thunder Admin uses. Installed look folders are discovered automatically.</p>
            </div>
        </div>

        <div class="ta-card__body ta-card__body--spacious">
            <div class="ta-look-grid">
                <?php foreach ($admin_available_looks as $lookId => $look): ?>
                    <label class="ta-look-option<?= $lookId === $admin_active_look ? ' ta-look-option--selected' : '' ?>" data-ta-look-option>
                        <input
                            class="ta-look-option__input"
                            type="radio"
                            name="active_look"
                            value="<?= e($lookId) ?>"
                            <?= $lookId === $admin_active_look ? 'checked' : '' ?>
                            data-ta-look-input
                        >
                        <span class="ta-look-option__preview">
                            <?php if (($look['thumbnail'] ?? '') !== ''): ?>
                                <img class="ta-look-option__image" src="<?= e(admin_look_http($look['thumbnail'], $lookId)) ?>" alt="">
                            <?php else: ?>
                                <span class="ta-look-option__placeholder"><i class="fa-solid fa-table-columns" aria-hidden="true"></i></span>
                            <?php endif; ?>
                        </span>
                        <span class="ta-look-option__copy">
                            <strong class="ta-look-option__name"><?= e($look['name'] ?? $lookId) ?></strong>
                            <span class="ta-look-option__description"><?= e($look['description'] ?? '') ?></span>
                            <?php if (($look['version'] ?? '') !== ''): ?>
                                <span class="ta-look-option__version">Version <?= e($look['version']) ?></span>
                            <?php endif; ?>
                        </span>
                        <span class="ta-look-option__check"><i class="fa-solid fa-check" aria-hidden="true"></i></span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="ta-card ta-admin-settings__section">
        <div class="ta-card__header">
            <div class="ta-card__heading-wrap">
                <h2 class="ta-card__title">Colour palette</h2>
                <p class="ta-card__subtitle">Each look defines its own presets. Choose Custom to edit every available colour.</p>
            </div>
        </div>

        <div class="ta-card__body ta-card__body--spacious">
            <?php foreach ($admin_available_looks as $lookId => $look): ?>
                <?php
                $selectedPalette = (string) ($admin_look_settings[$lookId]['palette'] ?? ($look['default_palette'] ?? 'custom'));
                $customColors = is_array($admin_look_settings[$lookId]['custom'] ?? null)
                    ? $admin_look_settings[$lookId]['custom']
                    : [];
                $palettes = is_array($look['palettes'] ?? null) ? $look['palettes'] : [];
                ?>
                <div class="ta-look-palette-group" data-ta-look-palette-group="<?= e($lookId) ?>" <?= $lookId === $admin_active_look ? '' : 'hidden' ?>>
                    <div class="ta-palette-grid">
                        <?php foreach ($palettes as $paletteId => $palette): ?>
                            <?php $colors = is_array($palette['colors'] ?? null) ? $palette['colors'] : []; ?>
                            <label class="ta-palette-option<?= $selectedPalette === $paletteId ? ' ta-palette-option--selected' : '' ?>" data-ta-palette-option>
                                <input
                                    class="ta-palette-option__input"
                                    type="radio"
                                    name="palette[<?= e($lookId) ?>]"
                                    value="<?= e($paletteId) ?>"
                                    <?= $selectedPalette === $paletteId ? 'checked' : '' ?>
                                    data-ta-palette-input
                                >
                                <span class="ta-palette-option__swatches" aria-hidden="true">
                                    <span style="background:<?= e($colors['primary'] ?? '#000000') ?>"></span>
                                    <span style="background:<?= e($colors['sidebar_bg'] ?? '#000000') ?>"></span>
                                    <span style="background:<?= e($colors['page_bg'] ?? '#ffffff') ?>"></span>
                                    <span style="background:<?= e($colors['surface'] ?? '#ffffff') ?>"></span>
                                </span>
                                <strong class="ta-palette-option__name"><?= e($palette['name'] ?? $paletteId) ?></strong>
                                <span class="ta-palette-option__description"><?= e($palette['description'] ?? '') ?></span>
                            </label>
                        <?php endforeach; ?>

                        <label class="ta-palette-option<?= $selectedPalette === 'custom' ? ' ta-palette-option--selected' : '' ?>" data-ta-palette-option>
                            <input
                                class="ta-palette-option__input"
                                type="radio"
                                name="palette[<?= e($lookId) ?>]"
                                value="custom"
                                <?= $selectedPalette === 'custom' ? 'checked' : '' ?>
                                data-ta-palette-input
                            >
                            <span class="ta-palette-option__custom-icon"><i class="fa-solid fa-palette" aria-hidden="true"></i></span>
                            <strong class="ta-palette-option__name">Custom</strong>
                            <span class="ta-palette-option__description">Set the individual colours for this look.</span>
                        </label>
                    </div>

                    <div class="ta-custom-palette" data-ta-custom-palette <?= $selectedPalette === 'custom' ? '' : 'hidden' ?>>
                        <div class="ta-custom-palette__header">
                            <strong class="ta-custom-palette__title">Custom colours</strong>
                            <span class="ta-custom-palette__description">These values are saved separately for <?= e($look['name'] ?? $lookId) ?>.</span>
                        </div>

                        <div class="ta-color-grid">
                            <?php foreach ($admin_palette_fields as $colorKey => $label): ?>
                                <label class="ta-color-field">
                                    <span class="ta-color-field__label"><?= e($label) ?></span>
                                    <span class="ta-color-field__control">
                                        <input
                                            class="ta-color-field__picker"
                                            type="color"
                                            name="custom[<?= e($lookId) ?>][<?= e($colorKey) ?>]"
                                            value="<?= e($customColors[$colorKey] ?? '#000000') ?>"
                                        >
                                        <span class="ta-color-field__value"><?= e($customColors[$colorKey] ?? '#000000') ?></span>
                                    </span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <div class="ta-admin-settings__actions">
        <button class="ta-button ta-button--secondary" type="button" data-ta-reset-sidebar>
            <i class="fa-solid fa-arrows-rotate" aria-hidden="true"></i>
            Reset navigation state
        </button>
        <button class="ta-button ta-button--primary" type="submit">
            <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>
            Save admin settings
        </button>
    </div>
</form>
