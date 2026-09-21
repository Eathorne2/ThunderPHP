<?php

namespace ThunderAuthentication;

defined('ROOTPATH') or die('Direct script access denied');

auth_account_guard();
$userId = auth_current_user_id();

if (is_route('auth.account')) {
    $user = auth_get_user_by_id($userId);
    set_value([
        'page_title' => 'My account',
        'account_user' => $user,
        'account_roles' => auth_user_role_rows($userId),
        'account_fields' => auth_fields_for_context('private', $userId, $user),
        'allow_username_change' => auth_setting('allow_username_change', '0') === '1',
        'allow_email_change' => auth_setting('allow_email_change', '1') === '1',
        'allow_profile_images' => auth_setting('allow_profile_images', '1') === '1',
    ]);
    return;
}

$request = auth_request();
$post = $request->post();
if (!is_array($post) || !csrf_verify($post)) {
    auth_flash('fail', 'Your session expired. Please try again.');
    redirect('account');
    exit;
}

$user = auth_get_user_by_id($userId);
if (!$user) {
    auth_session()->logout();
    redirect('login');
    exit;
}

if (is_route('auth.account.profile')) {
    $username = auth_setting('allow_username_change', '0') === '1'
        ? trim((string)($post['username'] ?? $user->username))
        : (string)$user->username;
    $email = auth_setting('allow_email_change', '1') === '1'
        ? trim((string)($post['email'] ?? ($user->email ?? '')))
        : (string)($user->email ?? '');
    $displayName = trim((string)($post['display_name'] ?? $user->display_name));
    $bio = trim((string)($post['bio'] ?? $user->bio));
    $errors = [];

    if (!preg_match('/^[A-Za-z0-9_.-]{3,80}$/', $username)) {
        $errors[] = 'Username must be 3–80 characters and may contain letters, numbers, dots, underscores, and hyphens.';
    }
    $existingUsername = auth_get_user_by_username($username);
    if ($existingUsername && (int)$existingUsername->id !== $userId) {
        $errors[] = 'That username is already in use.';
    }
    if (auth_user_has_column('email')) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Enter a valid email address.';
        } elseif (!auth_email_domain_allowed($email)) {
            $errors[] = 'That email domain is not allowed.';
        } else {
            $existingEmail = auth_get_user_by_email($email);
            if ($existingEmail && (int)$existingEmail->id !== $userId) {
                $errors[] = 'That email address is already in use.';
            }
        }
    }
    $fields = auth_fields_for_context('private', $userId, $user);
    $errors = array_merge($errors, auth_validate_field_values($fields, $post));
    if ($errors !== []) {
        auth_flash('fail', implode('<br>', $errors));
        redirect('account');
        exit;
    }

    do_action('auth_before_profile_update', ['user' => $user, 'data' => $post]);
    $core = ['username' => $username];
    if (auth_user_has_column('email')) {
        $core['email'] = $email;
    }
    auth_update_user_core($userId, $core);
    $profile = ['display_name' => $displayName, 'bio' => $bio];
    try {
        $avatar = auth_handle_avatar_upload();
        if ($avatar !== null) {
            $profile['image'] = $avatar;
            do_action('auth_profile_image_updated', ['user' => $user, 'image' => $avatar]);
        }
        auth_ensure_profile($userId, $profile);
        auth_save_field_values($userId, $fields, $post, 'user');
    } catch (\Throwable $e) {
        auth_flash('fail', $e->getMessage());
        redirect('account');
        exit;
    }

    $fresh = auth_get_user_by_id($userId);
    auth_session()->auth($fresh);
    auth_audit('auth.profile_updated', 'Profile updated.', $userId);
    do_action('auth_profile_updated', ['user' => $fresh]);
    auth_flash('success', 'Your profile was updated.');
    redirect('account');
    exit;
}

if (is_route('auth.account.password')) {
    $current = (string)($post['current_password'] ?? '');
    $password = (string)($post['new_password'] ?? '');
    $confirm = (string)($post['new_password_confirmation'] ?? '');
    $errors = [];
    if (!password_verify($current, (string)$user->password)) {
        $errors[] = 'Your current password is incorrect.';
    }
    $errors = array_merge($errors, auth_password_errors($password));
    if ($password !== $confirm) {
        $errors[] = 'New password confirmation does not match.';
    }
    if ($errors !== []) {
        auth_flash('fail', implode('<br>', $errors));
        redirect('account#password');
        exit;
    }
    auth_update_user_core($userId, ['password' => $password]);
    auth_db()->query('DELETE FROM ' . auth_table('remember_tokens') . ' WHERE user_id = :user_id', ['user_id' => $userId]);
    auth_audit('auth.password_changed', 'Password changed.', $userId);
    do_action('auth_password_changed', ['user' => auth_get_user_by_id($userId)]);
    auth_flash('success', 'Your password was changed.');
    redirect('account#password');
    exit;
}
