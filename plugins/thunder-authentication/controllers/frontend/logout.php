<?php

namespace ThunderAuthentication;

defined('ROOTPATH') or die('Direct script access denied');

$session = auth_session();
$user = auth_current_user();
do_action('auth_before_logout', ['user' => $user]);
if ($user) {
    auth_audit('auth.logout', 'User logged out.', (int)$user->id);
}
auth_clear_remember_cookie();
auth_delete_current_session_record();
$session->logout();
do_action('auth_after_logout', ['user' => $user]);
auth_flash('success', 'You have been logged out.');
$payload = do_filter('auth_logout_redirect', ['url' => auth_setting('logout_redirect', 'login'), 'user' => $user]);
redirect(auth_safe_redirect((string)($payload['url'] ?? 'login'), 'login'));
exit;
