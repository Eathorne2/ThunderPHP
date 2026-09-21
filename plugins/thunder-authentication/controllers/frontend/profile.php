<?php

namespace ThunderAuthentication;

defined('ROOTPATH') or die('Direct script access denied');

if (auth_setting('public_profiles', '1') !== '1') {
    auth_flash('fail', 'Public profiles are disabled.');
    redirect('');
    exit;
}

$username = trim((string)get_param('username'));
$user = auth_get_user_by_username($username);
if (!$user || (string)$user->status !== 'active') {
    auth_flash('fail', 'That profile could not be found.');
    redirect('');
    exit;
}

set_value([
    'page_title' => $user->display_name,
    'profile_user' => $user,
    'profile_roles' => auth_user_role_rows((int)$user->id),
    'profile_fields' => auth_fields_for_context('public', (int)$user->id),
]);
