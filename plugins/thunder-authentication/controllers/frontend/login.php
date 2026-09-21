<?php

namespace ThunderAuthentication;

defined('ROOTPATH') or die('Direct script access denied');

$session = auth_session();

if (is_route('auth.login')) {
    if ($session->is_logged_in()) {
        redirect(auth_safe_redirect((string)auth_setting('login_redirect', 'account'), 'account'));
        exit;
    }
    set_value([
        'page_title' => 'Login',
        'login_identifier_mode' => auth_setting('login_identifier', 'either'),
        'remember_enabled' => auth_setting('remember_me_enabled', '1') === '1',
        'registration_enabled' => auth_setting('registration_mode', 'open') !== 'disabled',
    ]);
    return;
}

if (!is_route('auth.login.submit')) {
    return;
}

$request = auth_request();
$post = $request->post();
if (!is_array($post) || !csrf_verify($post)) {
    auth_flash('fail', 'Your session expired. Please try again.');
    redirect('login');
    exit;
}

$identifier = trim((string)($post['identifier'] ?? ''));
$password = (string)($post['password'] ?? '');
$remember = !empty($post['remember']);
$identifierPayload = do_filter('auth_login_identifier', ['identifier' => $identifier]);
if (is_array($identifierPayload) && array_key_exists('identifier', $identifierPayload)) {
    $identifier = trim((string)$identifierPayload['identifier']);
}
$credentials = do_filter('auth_login_credentials', [
    'identifier' => $identifier, 'password' => $password, 'remember' => $remember
]);
if (is_array($credentials)) {
    $identifier = trim((string)($credentials['identifier'] ?? $identifier));
    $password = (string)($credentials['password'] ?? $password);
    $remember = (bool)($credentials['remember'] ?? $remember);
}
do_action('auth_before_login_attempt', ['identifier' => $identifier]);

if ($identifier === '' || $password === '') {
    auth_record_login_attempt($identifier, false, 'missing_credentials');
    do_action('auth_login_failed', ['identifier' => $identifier, 'reason' => 'missing_credentials']);
    auth_flash('fail', 'Enter your login details.');
    redirect('login');
    exit;
}

if (auth_login_is_locked($identifier)) {
    do_action('auth_login_failed', ['identifier' => $identifier, 'reason' => 'throttled']);
    auth_flash('fail', 'Too many failed attempts. Please try again later.');
    redirect('login');
    exit;
}

$user = auth_get_user_by_identifier($identifier);
$valid = $user && isset($user->password) && password_verify($password, (string)$user->password);
if (!$valid) {
    auth_record_login_attempt($identifier, false, 'invalid_credentials');
    do_action('auth_login_failed', ['identifier' => $identifier, 'reason' => 'invalid_credentials']);
    $payload = do_filter('auth_login_error', ['message' => 'The username, email, or password is incorrect.', 'reason' => 'invalid_credentials']);
    auth_flash('fail', (string)($payload['message'] ?? 'The username, email, or password is incorrect.'));
    redirect('login');
    exit;
}

if ((string)$user->status !== 'active') {
    auth_record_login_attempt($identifier, false, 'account_' . $user->status);
    do_action('auth_login_failed', ['identifier' => $identifier, 'reason' => 'account_' . $user->status]);
    $message = $user->status === 'pending'
        ? 'Your account is waiting for administrator approval.'
        : 'This account is not currently active.';
    auth_flash('fail', $message);
    redirect('login');
    exit;
}

$canLogin = do_filter('auth_can_login', ['allowed' => true, 'user' => $user]);
if (is_array($canLogin) && empty($canLogin['allowed'])) {
    auth_record_login_attempt($identifier, false, 'blocked_by_plugin');
    do_action('auth_login_failed', ['identifier' => $identifier, 'reason' => 'blocked_by_plugin']);
    auth_flash('fail', (string)($canLogin['message'] ?? 'Login is not available for this account.'));
    redirect('login');
    exit;
}

session_regenerate_id(true);
$session->auth($user);
auth_clear_login_attempts($identifier);
auth_ensure_profile((int)$user->id, ['last_login' => date('Y-m-d H:i:s')]);
if (auth_user_has_column('last_login')) {
    auth_db()->query('UPDATE ' . auth_table('users') . ' SET last_login = :last_login WHERE id = :id', [
        'last_login' => date('Y-m-d H:i:s'), 'id' => (int)$user->id
    ]);
}
auth_register_session((int)$user->id, true);
if ($remember) {
    auth_set_remember_cookie((int)$user->id);
}
auth_record_login_attempt($identifier, true, 'success');
auth_audit('auth.login', 'User logged in.', (int)$user->id);
do_action('auth_login_success', ['user' => auth_get_user_by_id((int)$user->id), 'remembered' => $remember]);

$redirectPayload = do_filter('auth_login_redirect', [
    'url' => auth_safe_redirect((string)($post['return_to'] ?? auth_setting('login_redirect', 'account')), 'account'),
    'user' => $user
]);
redirect(auth_safe_redirect((string)($redirectPayload['url'] ?? 'account'), 'account'));
exit;
