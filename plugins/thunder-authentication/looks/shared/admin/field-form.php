<?php

namespace ThunderAuthentication;

defined('ROOTPATH') or die('Direct script access denied');

$field = $editing_field;
$isEdit = (bool)$field;
$action = $isEdit ? ROOT . '/admin/auth/fields/' . $field->id : ROOT . '/admin/auth/fields/create';
?>
<div class="th-auth-admin">
    <div class="th-auth-admin__header">
        <div>
            <a class="th-auth-admin__back" href="<?=esc(ROOT . '/admin/auth/fields')?>"><i class="fa-solid fa-arrow-left"></i> Extra fields</a>
            <h1 class="th-auth-admin__title"><?=$isEdit ? 'Edit field' : 'Create field'?></h1>
            <p class="th-auth-admin__subtitle">Control where the field appears, who sees it, and who can edit it.</p>
        </div>
    </div>

    <form class="th-auth-admin__form-layout" method="post" action="<?=esc($action)?>">
        <?=csrf()?>
        <main class="th-auth-admin__form-main">
            <section class="th-auth-admin__panel th-auth-admin__section">
                <div class="th-auth-admin__section-head"><h2>Field definition</h2></div>

                <div class="th-auth-admin__grid th-auth-admin__grid--two">
                    <div class="th-auth-admin__field">
                        <label class="th-auth-admin__label" for="label">Label *</label>
                        <input class="th-auth-admin__input" id="label" name="label" value="<?=esc(old_value('label', (string)($field->label ?? '')))?>" required data-slug-source>
                    </div>
                    <div class="th-auth-admin__field">
                        <label class="th-auth-admin__label" for="field_key">Field key *</label>
                        <input class="th-auth-admin__input" id="field_key" name="field_key" value="<?=esc(old_value('field_key', (string)($field->field_key ?? '')))?>" required data-slug-target>
                    </div>
                </div>

                <div class="th-auth-admin__grid th-auth-admin__grid--two">
                    <div class="th-auth-admin__field">
                        <label class="th-auth-admin__label" for="field_type">Type</label>
                        <select class="th-auth-admin__input" id="field_type" name="field_type" data-field-type>
                            <?php foreach ($field_types as $key => $label): ?>
                                <option value="<?=esc($key)?>" <?=old_select('field_type', $key, (string)($field->field_type ?? 'text'))?>><?=esc($label)?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="th-auth-admin__field">
                        <label class="th-auth-admin__label" for="visibility">Visibility</label>
                        <select class="th-auth-admin__input" id="visibility" name="visibility">
                            <?php foreach ($visibility_options as $key => $label): ?>
                                <option value="<?=esc($key)?>" <?=old_select('visibility', $key, (string)($field->visibility ?? 'user_admin'))?>><?=esc($label)?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="th-auth-admin__field">
                    <label class="th-auth-admin__label" for="description">Description</label>
                    <textarea class="th-auth-admin__input th-auth-admin__textarea" id="description" name="description"><?=esc(old_value('description', (string)($field->description ?? '')))?></textarea>
                </div>

                <div class="th-auth-admin__grid th-auth-admin__grid--two">
                    <div class="th-auth-admin__field">
                        <label class="th-auth-admin__label" for="placeholder">Placeholder</label>
                        <input class="th-auth-admin__input" id="placeholder" name="placeholder" value="<?=esc(old_value('placeholder', (string)($field->placeholder ?? '')))?>">
                    </div>
                    <div class="th-auth-admin__field">
                        <label class="th-auth-admin__label" for="default_value">Default value</label>
                        <input class="th-auth-admin__input" id="default_value" name="default_value" value="<?=esc(old_value('default_value', (string)($field->default_value ?? '')))?>">
                    </div>
                </div>

                <div class="th-auth-admin__field">
                    <label class="th-auth-admin__label" for="validation_regex">Validation regular expression</label>
                    <input class="th-auth-admin__input" id="validation_regex" name="validation_regex" value="<?=esc(old_value('validation_regex', (string)($field->validation_regex ?? '')))?>" placeholder="/^...$/">
                    <div class="th-auth-admin__help">Optional PHP regular expression.</div>
                </div>

                <div class="th-auth-admin__field" data-options-field>
                    <label class="th-auth-admin__label" for="options_text">Options</label>
                    <textarea class="th-auth-admin__input th-auth-admin__textarea" id="options_text" name="options_text" rows="7"><?=esc(old_value('options_text', (string)$field_options_text))?></textarea>
                    <div class="th-auth-admin__help">One option per line: Label|value. Used by select, multi-select, radio, and checkbox fields.</div>
                </div>
            </section>
        </main>

        <aside class="th-auth-admin__form-side">
            <section class="th-auth-admin__panel th-auth-admin__section">
                <div class="th-auth-admin__section-head"><h2>Behaviour</h2></div>
                <div class="th-auth-admin__checks">
                    <?php
                    $behaviour = [
                        'active' => ['Active', !$field || (int)$field->active === 1],
                        'required' => ['Required', (bool)($field->required ?? false)],
                        'editable_user' => ['User can edit', !$field || (int)$field->editable_user === 1],
                        'editable_admin' => ['Administrator can edit', !$field || (int)$field->editable_admin === 1],
                    ];
                    foreach ($behaviour as $key => [$label, $defaultChecked]):
                    ?>
                        <input type="hidden" name="<?=esc($key)?>" value="0">
                        <label class="th-auth-admin__check">
                            <input type="checkbox" name="<?=esc($key)?>" value="1" <?=old_checked($key, '1', $defaultChecked ? '1' : '')?>>
                            <span><strong><?=esc($label)?></strong></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="th-auth-admin__panel th-auth-admin__section">
                <div class="th-auth-admin__section-head"><h2>Display locations</h2></div>
                <div class="th-auth-admin__checks">
                    <?php foreach (['show_signup' => 'Signup', 'show_public' => 'Public profile', 'show_private' => 'Private account', 'show_admin' => 'User administration'] as $key => $label): ?>
                        <?php $defaultChecked = (bool)($field->$key ?? ($key === 'show_private' || $key === 'show_admin')); ?>
                        <input type="hidden" name="<?=esc($key)?>" value="0">
                        <label class="th-auth-admin__check">
                            <input type="checkbox" name="<?=esc($key)?>" value="1" <?=old_checked($key, '1', $defaultChecked ? '1' : '')?>>
                            <span><strong><?=esc($label)?></strong></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="th-auth-admin__panel th-auth-admin__section">
                <div class="th-auth-admin__section-head">
                    <h2>Allowed roles</h2>
                    <p>Leave all unchecked to show it for every role.</p>
                </div>
                <input type="hidden" name="allowed_roles[]" value="">
                <div class="th-auth-admin__checks">
                    <?php foreach ($available_roles as $role): ?>
                        <?php $roleDefault = in_array((string)$role->slug, $field_allowed_roles, true) ? (string)$role->slug : ''; ?>
                        <label class="th-auth-admin__check">
                            <input type="checkbox" name="allowed_roles[]" value="<?=esc((string)$role->slug)?>" <?=old_checked('allowed_roles', (string)$role->slug, $roleDefault)?>>
                            <span><strong><?=esc((string)$role->name)?></strong></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </section>

            <button class="th-auth-admin__button th-auth-admin__button--block" type="submit"><i class="fa-solid fa-floppy-disk"></i> Save field</button>
            <?php if ($isEdit && (auth_current_user_id() === 1 || user_can('auth.delete_user_fields'))): ?>
                <button class="th-auth-admin__button th-auth-admin__button--danger th-auth-admin__button--block" type="submit" formaction="<?=esc(ROOT . '/admin/auth/fields/' . $field->id . '/delete')?>" data-confirm="Delete this field and every saved value?"><i class="fa-solid fa-trash"></i> Delete field</button>
            <?php endif; ?>
        </aside>
    </form>
</div>
