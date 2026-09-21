<?php

namespace ThunderAuthentication;

defined('ROOTPATH') or die('Direct script access denied');

function controller_user_validate(array $post, int $editingId = 0): array
{
    $errors = [];
    $username = trim((string)($post['username'] ?? ''));
    $email = trim((string)($post['email'] ?? ''));
    if (!preg_match('/^[A-Za-z0-9_.-]{3,80}$/', $username)) {
        $errors[] = 'Username must be 3–80 characters and may contain letters, numbers, dots, underscores, and hyphens.';
    }
    $existing = auth_get_user_by_username($username);
    if ($existing && (int)$existing->id !== $editingId) {
        $errors[] = 'That username is already in use.';
    }
    if (auth_user_has_column('email')) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Enter a valid email address.';
        } else {
            $existing = auth_get_user_by_email($email);
            if ($existing && (int)$existing->id !== $editingId) {
                $errors[] = 'That email address is already in use.';
            }
        }
    }
    return $errors;
}

if (is_route('auth.admin.users.create')) {
    auth_admin_guard('auth.create_users');
    set_value([
        'editing_user' => false,
        'available_roles' => auth_all_roles(),
        'admin_user_fields' => auth_fields_for_context('admin'),
    ]);
    return;
}

if (is_route('auth.admin.users.store')) {
    auth_admin_guard('auth.create_users');
    $post = auth_request()->post();
    if (!is_array($post) || !csrf_verify($post)) {
        auth_flash('fail', 'Your session expired. Please try again.');
        redirect('admin/auth/users/create');
        exit;
    }
    $errors = controller_user_validate($post);
    $password = (string)($post['password'] ?? '');
    $errors = array_merge($errors, auth_password_errors($password));
    $fields = auth_fields_for_context('admin');
    $errors = array_merge($errors, auth_validate_field_values($fields, $post));
    if ($errors !== []) {
        auth_flash('fail', implode('<br>', $errors));
        redirect('admin/auth/users/create');
        exit;
    }
    try {
        $canManageStatus = auth_current_user_id() === 1 || user_can('auth.disable_users');
        $userId = auth_create_user([
            'username' => trim((string)$post['username']), 'email' => trim((string)($post['email'] ?? '')),
            'display_name' => trim((string)($post['display_name'] ?? '')), 'bio' => trim((string)($post['bio'] ?? '')),
            'password' => $password, 'status' => $canManageStatus ? (string)($post['status'] ?? 'active') : 'active',
            'roles' => user_can('auth.assign_roles') || auth_current_user_id() === 1 ? (array)($post['roles'] ?? []) : [auth_setting('default_role', 'user')]
        ]);
        auth_save_field_values($userId, $fields, $post, 'admin');
        auth_audit('auth.admin_user_created', 'User created by an administrator.', $userId, ['actor' => auth_current_user_id()]);
    } catch (\Throwable $e) {
        auth_flash('fail', $e->getMessage());
        redirect('admin/auth/users/create');
        exit;
    }
    auth_flash('success', 'User created successfully.');
    redirect('admin/auth/users/' . $userId);
    exit;
}

$userId = (int)get_param('id');
$user = auth_get_user_by_id($userId);
if (!$user) {
    auth_flash('fail', 'User not found.');
    redirect('admin/auth/users');
    exit;
}

if (is_route('auth.admin.users.edit')) {
    auth_admin_guard('auth.view_users');
    set_value([
        'editing_user' => $user,
        'editing_user_roles' => auth_user_role_slugs($userId),
        'available_roles' => auth_all_roles(),
        'admin_user_fields' => auth_fields_for_context('admin', $userId, auth_current_user()),
    ]);
    return;
}

$post = auth_request()->post();
if (!is_array($post) || !csrf_verify($post)) {
    auth_flash('fail', 'Your session expired. Please try again.');
    redirect('admin/auth/users/' . $userId);
    exit;
}

if (is_route('auth.admin.users.update')) {
    auth_admin_guard('auth.edit_users');
    $editPayload = do_filter('auth_can_edit_user', [
        'allowed' => true, 'current_user' => auth_current_user(), 'target_user' => $user
    ]);
    if (is_array($editPayload) && empty($editPayload['allowed'])) {
        auth_flash('fail', (string)($editPayload['message'] ?? 'That user cannot be edited.'));
        redirect('admin/auth/users/' . $userId);
        exit;
    }
    $errors = controller_user_validate($post, $userId);
    $fields = auth_fields_for_context('admin', $userId, auth_current_user());
    $errors = array_merge($errors, auth_validate_field_values($fields, $post));
    $newPassword = (string)($post['password'] ?? '');
    if ($newPassword !== '') {
        if (auth_current_user_id() !== 1 && !user_can('auth.reset_user_passwords')) {
            $errors[] = 'You do not have permission to reset user passwords.';
        } else {
            $errors = array_merge($errors, auth_password_errors($newPassword));
        }
    }
    if ($errors !== []) {
        auth_flash('fail', implode('<br>', $errors));
        redirect('admin/auth/users/' . $userId);
        exit;
    }

    $core = ['username' => trim((string)$post['username'])];
    if (auth_user_has_column('email')) {
        $core['email'] = trim((string)($post['email'] ?? ''));
    }
    if ($newPassword !== '') {
        $core['password'] = $newPassword;
    }
    $canManageStatus = auth_current_user_id() === 1 || user_can('auth.disable_users');
    $status = $canManageStatus ? (string)($post['status'] ?? $user->status) : (string)$user->status;
    if ($userId === 1) {
        $status = 'active';
    }
    if (auth_user_has_column('disabled')) {
        $core['disabled'] = $status === 'disabled' ? 1 : 0;
    }
    auth_update_user_core($userId, $core);
    auth_ensure_profile($userId, [
        'display_name' => trim((string)($post['display_name'] ?? '')),
        'bio' => trim((string)($post['bio'] ?? '')), 'status' => $status,
    ]);
    if (auth_current_user_id() === 1 || user_can('auth.assign_roles')) {
        auth_set_user_roles($userId, (array)($post['roles'] ?? []));
    }
    try {
        auth_save_field_values($userId, $fields, $post, 'admin');
    } catch (\Throwable $e) {
        auth_flash('fail', $e->getMessage());
        redirect('admin/auth/users/' . $userId);
        exit;
    }
    if ($status === 'active' && $user->status === 'pending') {
        do_action('auth_registration_approved', ['user' => auth_get_user_by_id($userId)]);
    }
    if ($status === 'disabled' && $user->status !== 'disabled') {
        do_action('auth_user_disabled', ['user' => auth_get_user_by_id($userId)]);
    }
    if ($status === 'active' && $user->status === 'disabled') {
        do_action('auth_user_enabled', ['user' => auth_get_user_by_id($userId)]);
    }
    auth_audit('auth.admin_user_updated', 'User updated by an administrator.', $userId, ['actor' => auth_current_user_id()]);
    auth_flash('success', 'User updated successfully.');
    redirect('admin/auth/users/' . $userId);
    exit;
}

if (is_route('auth.admin.users.toggle')) {
    auth_admin_guard('auth.disable_users');
    if ($userId === 1 || $userId === auth_current_user_id()) {
        auth_flash('fail', 'That account cannot be disabled here.');
        redirect('admin/auth/users/' . $userId);
        exit;
    }
    $status = $user->status === 'disabled' ? 'active' : 'disabled';
    auth_ensure_profile($userId, ['status' => $status]);
    if (auth_user_has_column('disabled')) {
        auth_update_user_core($userId, ['disabled' => $status === 'disabled' ? 1 : 0]);
    }
    auth_db()->query('DELETE FROM ' . auth_table('user_sessions') . ' WHERE user_id = :user_id', ['user_id' => $userId]);
    auth_db()->query('DELETE FROM ' . auth_table('remember_tokens') . ' WHERE user_id = :user_id', ['user_id' => $userId]);
    do_action($status === 'disabled' ? 'auth_user_disabled' : 'auth_user_enabled', ['user' => auth_get_user_by_id($userId)]);
    auth_flash('success', $status === 'disabled' ? 'User disabled.' : 'User reactivated.');
    redirect('admin/auth/users/' . $userId);
    exit;
}

if (is_route('auth.admin.users.delete')) {
    auth_admin_guard('auth.delete_users');
    if ($userId === auth_current_user_id()) {
        auth_flash('fail', 'You cannot delete your own account.');
        redirect('admin/auth/users/' . $userId);
        exit;
    }
    if (!auth_delete_user($userId)) {
        auth_flash('fail', 'The user could not be deleted. Another plugin may have blocked the deletion.');
        redirect('admin/auth/users/' . $userId);
        exit;
    }
    auth_flash('success', 'User deleted.');
    redirect('admin/auth/users');
    exit;
}
