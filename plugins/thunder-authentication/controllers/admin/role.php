<?php

namespace ThunderAuthentication;

defined('ROOTPATH') or die('Direct script access denied');

function controller_role_payload(array $post): array
{
    return [
        'name' => trim((string)($post['name'] ?? '')),
        'slug' => auth_slug((string)($post['slug'] ?? $post['name'] ?? '')),
        'description' => trim((string)($post['description'] ?? '')),
        'permissions' => array_values(array_filter((array)($post['permissions'] ?? []))),
    ];
}

if (is_route('auth.admin.roles.create')) {
    auth_admin_guard('auth.create_roles');
    set_value(['editing_role' => false, 'permission_groups' => auth_permission_groups(), 'role_permissions' => []]);
    return;
}

if (is_route('auth.admin.roles.store')) {
    auth_admin_guard('auth.create_roles');
    $post = auth_request()->post();
    if (!is_array($post) || !csrf_verify($post)) {
        auth_flash('fail', 'Your session expired. Please try again.');
        redirect('admin/auth/roles/create');
        exit;
    }
    $data = controller_role_payload($post);
    $errors = [];
    if ($data['name'] === '') $errors[] = 'Role name is required.';
    if ($data['slug'] === '') $errors[] = 'Role key is required.';
    if (auth_role_by_slug($data['slug'])) $errors[] = 'That role key is already in use.';
    if ($errors !== []) {
        auth_flash('fail', implode('<br>', $errors));
        redirect('admin/auth/roles/create');
        exit;
    }
    $db = auth_db();
    $db->query('INSERT INTO ' . auth_table('roles') . ' (name, slug, description, is_protected, created_at, updated_at)
        VALUES (:name, :slug, :description, 0, :created_at, :updated_at)', [
            'name' => $data['name'], 'slug' => $data['slug'], 'description' => $data['description'],
            'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')
        ]);
    if ($db->has_error || $db->insert_id < 1) {
        auth_flash('fail', $db->error ?: 'Role could not be created.');
        redirect('admin/auth/roles/create');
        exit;
    }
    $roleId = (int)$db->insert_id;
    if (auth_current_user_id() === 1 || user_can('auth.assign_permissions')) {
        auth_set_role_permissions($roleId, $data['permissions']);
    }
    $role = auth_role_by_id($roleId);
    do_action('auth_role_created', ['role' => $role]);
    auth_flash('success', 'Role created.');
    redirect('admin/auth/roles/' . $roleId);
    exit;
}

$roleId = (int)get_param('id');
$role = auth_role_by_id($roleId);
if (!$role) {
    auth_flash('fail', 'Role not found.');
    redirect('admin/auth/roles');
    exit;
}

if (is_route('auth.admin.roles.edit')) {
    auth_admin_guard('auth.view_roles');
    set_value([
        'editing_role' => $role,
        'permission_groups' => auth_permission_groups(),
        'role_permissions' => auth_role_permission_slugs($roleId),
    ]);
    return;
}

$post = auth_request()->post();
if (!is_array($post) || !csrf_verify($post)) {
    auth_flash('fail', 'Your session expired. Please try again.');
    redirect('admin/auth/roles/' . $roleId);
    exit;
}

if (is_route('auth.admin.roles.update')) {
    auth_admin_guard('auth.edit_roles');
    $data = controller_role_payload($post);
    $existing = auth_role_by_slug($data['slug']);
    $errors = [];
    if ($data['name'] === '') $errors[] = 'Role name is required.';
    if ($data['slug'] === '') $errors[] = 'Role key is required.';
    if ($existing && (int)$existing->id !== $roleId) $errors[] = 'That role key is already in use.';
    if ((int)$role->is_protected === 1) {
        $data['slug'] = (string)$role->slug;
    }
    if ($errors !== []) {
        auth_flash('fail', implode('<br>', $errors));
        redirect('admin/auth/roles/' . $roleId);
        exit;
    }
    auth_db()->query('UPDATE ' . auth_table('roles') . ' SET name = :name, slug = :slug, description = :description, updated_at = :updated_at WHERE id = :id', [
        'name' => $data['name'], 'slug' => $data['slug'], 'description' => $data['description'],
        'updated_at' => date('Y-m-d H:i:s'), 'id' => $roleId
    ]);
    if (auth_current_user_id() === 1 || user_can('auth.assign_permissions')) {
        $available = array_keys(auth_available_permissions());
        $unavailableAssigned = array_values(array_diff(auth_role_permission_slugs($roleId), $available));
        auth_set_role_permissions($roleId, array_merge($data['permissions'], $unavailableAssigned));
    }
    do_action('auth_role_updated', ['role' => auth_role_by_id($roleId)]);
    auth_flash('success', 'Role updated.');
    redirect('admin/auth/roles/' . $roleId);
    exit;
}

if (is_route('auth.admin.roles.duplicate')) {
    auth_admin_guard('auth.create_roles');
    $base = auth_slug((string)$role->slug . '-copy');
    $slug = $base;
    $counter = 2;
    while (auth_role_by_slug($slug)) {
        $slug = $base . '-' . $counter++;
    }
    $db = auth_db();
    $db->query('INSERT INTO ' . auth_table('roles') . ' (name, slug, description, is_protected, created_at, updated_at)
        VALUES (:name, :slug, :description, 0, :created_at, :updated_at)', [
            'name' => (string)$role->name . ' Copy', 'slug' => $slug, 'description' => (string)$role->description,
            'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')
        ]);
    $newId = (int)$db->insert_id;
    if (auth_current_user_id() === 1 || user_can('auth.assign_permissions')) {
        auth_set_role_permissions($newId, auth_role_permission_slugs($roleId));
    }
    do_action('auth_role_created', ['role' => auth_role_by_id($newId), 'duplicated_from' => $role]);
    auth_flash('success', 'Role duplicated.');
    redirect('admin/auth/roles/' . $newId);
    exit;
}

if (is_route('auth.admin.roles.delete')) {
    auth_admin_guard('auth.delete_roles');
    if ((int)$role->is_protected === 1) {
        auth_flash('fail', 'Protected roles cannot be deleted.');
        redirect('admin/auth/roles/' . $roleId);
        exit;
    }
    $count = auth_db()->get_row('SELECT COUNT(*) AS total FROM ' . auth_table('user_roles') . ' WHERE role_id = :role_id', ['role_id' => $roleId]);
    if ($count && (int)$count->total > 0) {
        auth_flash('fail', 'Remove this role from all users before deleting it.');
        redirect('admin/auth/roles/' . $roleId);
        exit;
    }
    do_action('auth_before_role_delete', ['role' => $role]);
    auth_db()->query('DELETE FROM ' . auth_table('role_permissions') . ' WHERE role_id = :role_id', ['role_id' => $roleId]);
    auth_db()->query('DELETE FROM ' . auth_table('roles') . ' WHERE id = :id', ['id' => $roleId]);
    do_action('auth_role_deleted', ['role_id' => $roleId]);
    auth_flash('success', 'Role deleted.');
    redirect('admin/auth/roles');
    exit;
}
