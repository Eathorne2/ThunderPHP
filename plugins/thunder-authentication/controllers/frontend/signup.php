<?php

namespace ThunderAuthentication;

defined('ROOTPATH') or die('Direct script access denied');

$mode = (string)auth_setting('registration_mode', 'open');
$enabledPayload = do_filter('auth_registration_enabled', ['enabled' => $mode !== 'disabled']);
$enabled = (bool)($enabledPayload['enabled'] ?? ($mode !== 'disabled'));

if (is_route('auth.signup')) {
    if (auth_session()->is_logged_in()) {
        redirect('account');
        exit;
    }
    if (!$enabled) {
        auth_flash('fail', 'Public registration is currently disabled.');
        redirect('login');
        exit;
    }
    set_value([
        'page_title' => 'Create account',
        'signup_fields' => auth_fields_for_context('signup'),
        'terms_required' => auth_setting('terms_required', '0') === '1',
        'terms_url' => (string)auth_setting('terms_url', ''),
    ]);
    return;
}

if (!is_route('auth.signup.submit')) {
    return;
}
if (!$enabled) {
    auth_flash('fail', 'Public registration is currently disabled.');
    redirect('login');
    exit;
}

$request = auth_request();
$post = $request->post();
if (!is_array($post) || !csrf_verify($post)) {
    auth_flash('fail', 'Your session expired. Please try again.');
    redirect('signup');
    exit;
}

$username = trim((string)($post['username'] ?? ''));
$email = trim((string)($post['email'] ?? ''));
$displayName = trim((string)($post['display_name'] ?? $username));
$password = (string)($post['password'] ?? '');
$confirm = (string)($post['password_confirmation'] ?? '');
$fields = auth_fields_for_context('signup');
$errors = [];

if (!preg_match('/^[A-Za-z0-9_.-]{3,80}$/', $username)) {
    $errors[] = 'Username must be 3–80 characters and may contain letters, numbers, dots, underscores, and hyphens.';
}
if (auth_get_user_by_username($username)) {
    $errors[] = 'That username is already in use.';
}
if (auth_user_has_column('email')) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid email address.';
    } elseif (!auth_email_domain_allowed($email)) {
        $errors[] = 'That email domain is not allowed.';
    } elseif (auth_get_user_by_email($email)) {
        $errors[] = 'That email address is already in use.';
    }
}
$errors = array_merge($errors, auth_password_errors($password));
if ($password !== $confirm) {
    $errors[] = 'Password confirmation does not match.';
}
if (auth_setting('terms_required', '0') === '1' && empty($post['terms'])) {
    $errors[] = 'You must accept the terms before creating an account.';
}
$errors = array_merge($errors, auth_validate_field_values($fields, $post));
$validationPayload = do_filter('auth_registration_validation_rules', [
    'rules' => [],
    'data' => ['username' => $username, 'email' => $email, 'display_name' => $displayName],
    'errors' => $errors,
]);
if (is_array($validationPayload) && isset($validationPayload['errors']) && is_array($validationPayload['errors'])) {
    $errors = $validationPayload['errors'];
}

$registration = do_filter('auth_registration_data', ['data' => [
    'username' => $username, 'email' => $email, 'display_name' => $displayName, 'password' => $password
], 'errors' => $errors]);
if (is_array($registration)) {
    $errors = is_array($registration['errors'] ?? null) ? $registration['errors'] : $errors;
    $registrationData = is_array($registration['data'] ?? null) ? $registration['data'] : [];
} else {
    $registrationData = [];
}

if ($errors !== []) {
    auth_flash('fail', implode('<br>', $errors));
    redirect('signup');
    exit;
}

$isFirstUser = auth_user_count() === 0;
$approvalPayload = do_filter('auth_registration_requires_approval', [
    'required' => !$isFirstUser && $mode === 'approval',
    'data' => $registrationData,
    'is_first_user' => $isFirstUser,
]);
$requiresApproval = !$isFirstUser && (bool)($approvalPayload['required'] ?? ($mode === 'approval'));
$rolePayload = do_filter('auth_default_role', [
    'role' => $isFirstUser ? 'admin' : auth_setting('default_role', 'user'),
    'is_first_user' => $isFirstUser,
]);
$role = $isFirstUser
    ? 'admin'
    : (string)($rolePayload['role'] ?? auth_setting('default_role', 'user'));
$data = array_merge($registrationData, [
    'username' => $username, 'email' => $email, 'display_name' => $displayName, 'password' => $password,
    'status' => $requiresApproval ? 'pending' : 'active', 'roles' => [$role], 'primary_role' => $role
]);
do_action('auth_before_registration', ['data' => $data, 'is_first_user' => $isFirstUser]);

try {
    $userId = auth_create_user($data);
    auth_save_field_values($userId, $fields, $post, 'user');
} catch (\Throwable $e) {
    auth_flash('fail', 'The account could not be created.<br>' . esc($e->getMessage()));
    redirect('signup');
    exit;
}

$user = auth_get_user_by_id($userId);
auth_audit('auth.registration', 'Account registered.', $userId, [
    'approval_required' => $requiresApproval,
    'is_first_user' => $isFirstUser,
]);
do_action('auth_user_registered', ['user' => $user]);

if ($requiresApproval) {
    do_action('auth_registration_pending', ['user' => $user]);
    auth_flash('success', 'Your account was created and is waiting for administrator approval.');
    redirect('login');
    exit;
}

if (auth_setting('auto_login_after_signup', '1') === '1') {
    session_regenerate_id(true);
    auth_session()->auth($user);
    auth_register_session($userId, true);
    do_action('auth_login_success', ['user' => $user, 'registered' => true]);
}

auth_flash('success', 'Your account was created successfully.');
$redirectPayload = do_filter('auth_registration_redirect', [
    'url' => auth_setting('auto_login_after_signup', '1') === '1' ? 'account' : 'login', 'user' => $user
]);
redirect(auth_safe_redirect((string)($redirectPayload['url'] ?? 'account'), 'account'));
exit;
