<?php

namespace ThunderAuthentication;

defined('ROOTPATH') or die('Direct script access denied');

function controller_field_options_text(int $fieldId): string
{
    $lines = [];
    foreach (auth_field_options($fieldId) as $option) {
        $lines[] = $option->option_label . '|' . $option->option_value;
    }
    return implode("\n", $lines);
}

function controller_field_payload(array $post): array
{
    return [
        'label' => trim((string)($post['label'] ?? '')),
        'field_key' => auth_slug((string)($post['field_key'] ?? $post['label'] ?? '')),
        'field_type' => (string)($post['field_type'] ?? 'text'),
        'description' => trim((string)($post['description'] ?? '')),
        'placeholder' => trim((string)($post['placeholder'] ?? '')),
        'default_value' => (string)($post['default_value'] ?? ''),
        'validation_regex' => trim((string)($post['validation_regex'] ?? '')),
        'visibility' => (string)($post['visibility'] ?? 'user_admin'),
        'required' => !empty($post['required']) ? 1 : 0,
        'editable_user' => !empty($post['editable_user']) ? 1 : 0,
        'editable_admin' => !empty($post['editable_admin']) ? 1 : 0,
        'show_signup' => !empty($post['show_signup']) ? 1 : 0,
        'show_public' => !empty($post['show_public']) ? 1 : 0,
        'show_private' => !empty($post['show_private']) ? 1 : 0,
        'show_admin' => !empty($post['show_admin']) ? 1 : 0,
        'allowed_roles_json' => json_encode(array_values(array_filter((array)($post['allowed_roles'] ?? [])))),
        'active' => !empty($post['active']) ? 1 : 0,
        'options_text' => trim((string)($post['options_text'] ?? '')),
    ];
}

function controller_save_field_options(int $fieldId, string $text): void
{
    auth_db()->query('DELETE FROM ' . auth_table('field_options') . ' WHERE field_id = :field_id', ['field_id' => $fieldId]);
    $order = 0;
    foreach (preg_split('/\R/', $text) ?: [] as $line) {
        $line = trim($line);
        if ($line === '') continue;
        [$label, $value] = array_pad(array_map('trim', explode('|', $line, 2)), 2, '');
        if ($value === '') $value = auth_slug($label);
        auth_db()->query('INSERT INTO ' . auth_table('field_options') . ' (field_id, option_label, option_value, sort_order)
            VALUES (:field_id, :option_label, :option_value, :sort_order)', [
                'field_id' => $fieldId, 'option_label' => $label, 'option_value' => $value, 'sort_order' => $order
            ]);
        $order += 10;
    }
}

if (is_route('auth.admin.fields.create')) {
    auth_admin_guard('auth.create_user_fields');
    set_value([
        'editing_field' => false, 'field_types' => auth_field_types(), 'visibility_options' => auth_field_visibility_options(),
        'available_roles' => auth_all_roles(), 'field_allowed_roles' => [], 'field_options_text' => ''
    ]);
    return;
}

if (is_route('auth.admin.fields.store')) {
    auth_admin_guard('auth.create_user_fields');
    $post = auth_request()->post();
    if (!is_array($post) || !csrf_verify($post)) {
        auth_flash('fail', 'Your session expired. Please try again.');
        redirect('admin/auth/fields/create');
        exit;
    }
    $data = controller_field_payload($post);
    $errors = [];
    if ($data['label'] === '') $errors[] = 'Field label is required.';
    if ($data['field_key'] === '') $errors[] = 'Field key is required.';
    if (!array_key_exists($data['field_type'], auth_field_types())) $errors[] = 'Select a valid field type.';
    $existing = auth_db()->get_row('SELECT id FROM ' . auth_table('fields') . ' WHERE field_key = :field_key LIMIT 1', ['field_key' => $data['field_key']]);
    if ($existing) $errors[] = 'That field key is already in use.';
    if ($errors !== []) {
        auth_flash('fail', implode('<br>', $errors));
        redirect('admin/auth/fields/create');
        exit;
    }
    $orderRow = auth_db()->get_row('SELECT COALESCE(MAX(sort_order), 0) AS max_order FROM ' . auth_table('fields'));
    $db = auth_db();
    $db->query('INSERT INTO ' . auth_table('fields') . '
        (label, field_key, field_type, description, placeholder, default_value, validation_regex, visibility,
        required, editable_user, editable_admin, show_signup, show_public, show_private, show_admin,
        allowed_roles_json, sort_order, active, created_at, updated_at)
        VALUES (:label, :field_key, :field_type, :description, :placeholder, :default_value, :validation_regex, :visibility,
        :required, :editable_user, :editable_admin, :show_signup, :show_public, :show_private, :show_admin,
        :allowed_roles_json, :sort_order, :active, :created_at, :updated_at)', [
            'label' => $data['label'], 'field_key' => $data['field_key'], 'field_type' => $data['field_type'],
            'description' => $data['description'], 'placeholder' => $data['placeholder'], 'default_value' => $data['default_value'],
            'validation_regex' => $data['validation_regex'], 'visibility' => $data['visibility'], 'required' => $data['required'],
            'editable_user' => $data['editable_user'], 'editable_admin' => $data['editable_admin'], 'show_signup' => $data['show_signup'],
            'show_public' => $data['show_public'], 'show_private' => $data['show_private'], 'show_admin' => $data['show_admin'],
            'allowed_roles_json' => $data['allowed_roles_json'], 'sort_order' => (int)($orderRow->max_order ?? 0) + 10,
            'active' => $data['active'], 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')
        ]);
    if ($db->has_error || $db->insert_id < 1) {
        auth_flash('fail', $db->error ?: 'Field could not be created.');
        redirect('admin/auth/fields/create');
        exit;
    }
    $fieldId = (int)$db->insert_id;
    controller_save_field_options($fieldId, $data['options_text']);
    auth_flash('success', 'Extra field created.');
    redirect('admin/auth/fields/' . $fieldId);
    exit;
}

$fieldId = (int)get_param('id');
$field = auth_db()->get_row('SELECT * FROM ' . auth_table('fields') . ' WHERE id = :id LIMIT 1', ['id' => $fieldId]);
if (!$field) {
    auth_flash('fail', 'Field not found.');
    redirect('admin/auth/fields');
    exit;
}

if (is_route('auth.admin.fields.edit')) {
    auth_admin_guard('auth.view_user_fields');
    set_value([
        'editing_field' => $field, 'field_types' => auth_field_types(), 'visibility_options' => auth_field_visibility_options(),
        'available_roles' => auth_all_roles(),
        'field_allowed_roles' => json_decode((string)$field->allowed_roles_json, true) ?: [],
        'field_options_text' => controller_field_options_text($fieldId),
    ]);
    return;
}

$post = auth_request()->post();
if (!is_array($post) || !csrf_verify($post)) {
    auth_flash('fail', 'Your session expired. Please try again.');
    redirect('admin/auth/fields/' . $fieldId);
    exit;
}

if (is_route('auth.admin.fields.update')) {
    auth_admin_guard('auth.edit_user_fields');
    $data = controller_field_payload($post);
    $existing = auth_db()->get_row('SELECT id FROM ' . auth_table('fields') . ' WHERE field_key = :field_key AND id != :id LIMIT 1', [
        'field_key' => $data['field_key'], 'id' => $fieldId
    ]);
    $errors = [];
    if ($data['label'] === '') $errors[] = 'Field label is required.';
    if ($data['field_key'] === '') $errors[] = 'Field key is required.';
    if ($existing) $errors[] = 'That field key is already in use.';
    if ($errors !== []) {
        auth_flash('fail', implode('<br>', $errors));
        redirect('admin/auth/fields/' . $fieldId);
        exit;
    }
    auth_db()->query('UPDATE ' . auth_table('fields') . ' SET
        label = :label, field_key = :field_key, field_type = :field_type, description = :description,
        placeholder = :placeholder, default_value = :default_value, validation_regex = :validation_regex,
        visibility = :visibility, required = :required, editable_user = :editable_user, editable_admin = :editable_admin,
        show_signup = :show_signup, show_public = :show_public, show_private = :show_private, show_admin = :show_admin,
        allowed_roles_json = :allowed_roles_json, active = :active, updated_at = :updated_at WHERE id = :id', [
            'label' => $data['label'], 'field_key' => $data['field_key'], 'field_type' => $data['field_type'],
            'description' => $data['description'], 'placeholder' => $data['placeholder'], 'default_value' => $data['default_value'],
            'validation_regex' => $data['validation_regex'], 'visibility' => $data['visibility'], 'required' => $data['required'],
            'editable_user' => $data['editable_user'], 'editable_admin' => $data['editable_admin'], 'show_signup' => $data['show_signup'],
            'show_public' => $data['show_public'], 'show_private' => $data['show_private'], 'show_admin' => $data['show_admin'],
            'allowed_roles_json' => $data['allowed_roles_json'], 'active' => $data['active'],
            'updated_at' => date('Y-m-d H:i:s'), 'id' => $fieldId
        ]);
    controller_save_field_options($fieldId, $data['options_text']);
    auth_flash('success', 'Extra field updated.');
    redirect('admin/auth/fields/' . $fieldId);
    exit;
}

if (is_route('auth.admin.fields.move')) {
    auth_admin_guard('auth.reorder_user_fields');
    $direction = (string)($post['direction'] ?? 'up');
    $operator = $direction === 'down' ? '>' : '<';
    $order = $direction === 'down' ? 'ASC' : 'DESC';
    $neighbor = auth_db()->get_row('SELECT id, sort_order FROM ' . auth_table('fields') . '
        WHERE sort_order ' . $operator . ' :sort_order ORDER BY sort_order ' . $order . ' LIMIT 1', ['sort_order' => (int)$field->sort_order]);
    if ($neighbor) {
        auth_db()->query('UPDATE ' . auth_table('fields') . ' SET sort_order = :sort_order WHERE id = :id', [
            'sort_order' => (int)$neighbor->sort_order, 'id' => $fieldId
        ]);
        auth_db()->query('UPDATE ' . auth_table('fields') . ' SET sort_order = :sort_order WHERE id = :id', [
            'sort_order' => (int)$field->sort_order, 'id' => (int)$neighbor->id
        ]);
    }
    redirect('admin/auth/fields');
    exit;
}

if (is_route('auth.admin.fields.delete')) {
    auth_admin_guard('auth.delete_user_fields');
    auth_db()->query('DELETE FROM ' . auth_table('field_values') . ' WHERE field_id = :field_id', ['field_id' => $fieldId]);
    auth_db()->query('DELETE FROM ' . auth_table('field_options') . ' WHERE field_id = :field_id', ['field_id' => $fieldId]);
    auth_db()->query('DELETE FROM ' . auth_table('fields') . ' WHERE id = :id', ['id' => $fieldId]);
    auth_flash('success', 'Extra field deleted.');
    redirect('admin/auth/fields');
    exit;
}
