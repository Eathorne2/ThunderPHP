<?php

namespace ThunderAuthentication;

defined('ROOTPATH') or die('Direct script access denied');

$key = 'field_' . (int)$field->id;
$value = $field->value ?? $field->default_value ?? '';
$type = (string)$field->field_type;
$disabled = isset($field_context) && $field_context === 'user' && (int)$field->editable_user !== 1;
$disabled = $disabled || (isset($field_context) && $field_context === 'admin' && (int)$field->editable_admin !== 1);
$required = (int)$field->required === 1;
$scalarDefault = is_scalar($value) ? (string)$value : '';
?>
<div class="th-auth-field <?=in_array($type, ['hidden'], true) ? 'th-auth-field--hidden' : ''?>">
    <?php if ($type !== 'hidden'): ?>
        <label class="th-auth-label" for="<?=esc($key)?>">
            <?=esc((string)$field->label)?><?=$required ? ' *' : ''?>
        </label>
    <?php endif; ?>

    <?php if ($type === 'textarea'): ?>
        <textarea class="th-auth-input th-auth-textarea" id="<?=esc($key)?>" name="<?=esc($key)?>" placeholder="<?=esc((string)$field->placeholder)?>" <?=$required ? 'required' : ''?> <?=$disabled ? 'disabled' : ''?>><?=esc(old_value($key, $scalarDefault))?></textarea>
    <?php elseif ($type === 'select'): ?>
        <select class="th-auth-input th-auth-select" id="<?=esc($key)?>" name="<?=esc($key)?>" <?=$required ? 'required' : ''?> <?=$disabled ? 'disabled' : ''?>>
            <option value="">Select an option</option>
            <?php foreach ($field->options as $option): ?>
                <option value="<?=esc((string)$option->option_value)?>" <?=old_select($key, (string)$option->option_value, $scalarDefault)?>><?=esc((string)$option->option_label)?></option>
            <?php endforeach; ?>
        </select>
    <?php elseif ($type === 'multiselect'): ?>
        <?php $selected = is_array($value) ? $value : (json_decode((string)$value, true) ?: []); ?>
        <input type="hidden" name="<?=esc($key)?>[]" value="">
        <select class="th-auth-input th-auth-select" id="<?=esc($key)?>" name="<?=esc($key)?>[]" multiple <?=$required ? 'required' : ''?> <?=$disabled ? 'disabled' : ''?>>
            <?php foreach ($field->options as $option): ?>
                <?php $optionValue = (string)$option->option_value; ?>
                <option value="<?=esc($optionValue)?>" <?=old_select($key, $optionValue, in_array($optionValue, $selected, true) ? $optionValue : '')?>><?=esc((string)$option->option_label)?></option>
            <?php endforeach; ?>
        </select>
    <?php elseif ($type === 'radio'): ?>
        <div class="th-auth-options">
            <?php foreach ($field->options as $option): ?>
                <?php $optionValue = (string)$option->option_value; ?>
                <label class="th-auth-choice"><input type="radio" name="<?=esc($key)?>" value="<?=esc($optionValue)?>" <?=old_checked($key, $optionValue, $scalarDefault)?> <?=$required ? 'required' : ''?> <?=$disabled ? 'disabled' : ''?>> <span><?=esc((string)$option->option_label)?></span></label>
            <?php endforeach; ?>
        </div>
    <?php elseif ($type === 'checkbox'): ?>
        <?php $checked = is_array($value) ? $value : (json_decode((string)$value, true) ?: []); ?>
        <input type="hidden" name="<?=esc($key)?>[]" value="">
        <div class="th-auth-options">
            <?php foreach ($field->options as $option): ?>
                <?php $optionValue = (string)$option->option_value; ?>
                <label class="th-auth-choice"><input type="checkbox" name="<?=esc($key)?>[]" value="<?=esc($optionValue)?>" <?=old_checked($key, $optionValue, in_array($optionValue, $checked, true) ? $optionValue : '')?> <?=$disabled ? 'disabled' : ''?>> <span><?=esc((string)$option->option_label)?></span></label>
            <?php endforeach; ?>
        </div>
    <?php elseif ($type === 'toggle'): ?>
        <input type="hidden" name="<?=esc($key)?>" value="0">
        <label class="th-auth-toggle"><input type="checkbox" id="<?=esc($key)?>" name="<?=esc($key)?>" value="1" <?=old_checked($key, '1', $scalarDefault)?> <?=$disabled ? 'disabled' : ''?>><span class="th-auth-toggle__track"></span><span><?=esc((string)$field->label)?></span></label>
    <?php elseif (in_array($type, ['image', 'file'], true)): ?>
        <?php if ($value): ?><div class="th-auth-current-file"><?=esc(basename((string)$value))?></div><?php endif; ?>
        <input class="th-auth-input" type="file" id="<?=esc($key)?>" name="<?=esc($key)?>" <?=$type === 'image' ? 'accept="image/*"' : ''?> <?=$required && !$value ? 'required' : ''?> <?=$disabled ? 'disabled' : ''?>>
    <?php elseif ($type === 'readonly'): ?>
        <input class="th-auth-input" type="text" id="<?=esc($key)?>" value="<?=esc(old_value($key, $scalarDefault))?>" readonly>
    <?php else: ?>
        <?php $htmlType = in_array($type, ['text','email','number','tel','url','date','time','hidden'], true) ? $type : 'text'; ?>
        <input class="th-auth-input" type="<?=esc($htmlType)?>" id="<?=esc($key)?>" name="<?=esc($key)?>" value="<?=esc(old_value($key, $scalarDefault))?>" placeholder="<?=esc((string)$field->placeholder)?>" <?=$required ? 'required' : ''?> <?=$disabled ? 'disabled' : ''?>>
    <?php endif; ?>

    <?php if ($type !== 'hidden' && !empty($field->description)): ?>
        <div class="th-auth-help"><?=esc((string)$field->description)?></div>
    <?php endif; ?>
</div>
