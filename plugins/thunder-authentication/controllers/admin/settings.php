<?php

namespace ThunderAuthentication;

defined('ROOTPATH') or die('Direct script access denied');

if (is_route('auth.admin.settings')) {
    auth_admin_guard('auth.view_settings');
    set_value([
        'auth_settings' => auth_get_settings(),
        'auth_looks' => auth_available_looks(),
        'auth_roles' => auth_all_roles(),
        'can_edit_settings' => auth_current_user_id() === 1 || user_can('auth.edit_settings'),
        'can_manage_looks' => auth_current_user_id() === 1 || user_can('auth.manage_looks'),
        'can_manage_palettes' => auth_current_user_id() === 1 || user_can('auth.manage_palettes'),
    ]);
    return;
}

auth_admin_guard('auth.edit_settings');
$post = auth_request()->post();
if (!is_array($post) || !csrf_verify($post)) {
    auth_flash('fail', 'Your session expired. Please try again.');
    redirect('admin/auth/settings');
    exit;
}

$keys = [
    'registration_mode','default_role','auto_login_after_signup','terms_required','terms_url','allowed_email_domains','blocked_email_domains',
    'login_identifier','remember_me_enabled','remember_days','login_redirect','logout_redirect','maximum_failed_attempts','lockout_minutes','single_session',
    'public_profiles','allow_profile_images','allow_username_change','allow_email_change',
    'minimum_password_length','password_require_uppercase','password_require_lowercase','password_require_number','password_require_symbol',
    'active_look','active_palette','custom_primary','custom_secondary','custom_background','custom_surface','custom_text','custom_muted_text',
    'custom_border','custom_success','custom_warning','custom_danger','form_width','border_radius'
];
$checkboxes = [
    'auto_login_after_signup','terms_required','remember_me_enabled','single_session','public_profiles','allow_profile_images',
    'allow_username_change','allow_email_change','password_require_uppercase','password_require_lowercase','password_require_number','password_require_symbol'
];
foreach ($checkboxes as $key) {
    $post[$key] = !empty($post[$key]) ? '1' : '0';
}
$currentSettings = auth_get_settings();
$canManageLooks = auth_current_user_id() === 1 || user_can('auth.manage_looks');
$canManagePalettes = auth_current_user_id() === 1 || user_can('auth.manage_palettes');
$looks = auth_available_looks();
if (!$canManageLooks) {
    $post['active_look'] = $currentSettings['active_look'];
} elseif (!isset($looks[(string)($post['active_look'] ?? '')])) {
    $post['active_look'] = 'clean-card';
}
$manifest = auth_look_manifest((string)$post['active_look']);
$validPalettes = array_keys((array)($manifest['palettes'] ?? []));
$validPalettes[] = 'custom';
if (!$canManagePalettes) {
    foreach ([
        'active_palette','custom_primary','custom_secondary','custom_background','custom_surface','custom_text',
        'custom_muted_text','custom_border','custom_success','custom_warning','custom_danger','form_width','border_radius'
    ] as $appearanceKey) {
        $post[$appearanceKey] = $currentSettings[$appearanceKey] ?? '';
    }
} elseif (!in_array((string)($post['active_palette'] ?? ''), $validPalettes, true)) {
    $post['active_palette'] = (string)($manifest['default_palette'] ?? 'blue');
}
foreach ($keys as $key) {
    auth_save_setting($key, $post[$key] ?? '');
}
auth_audit('auth.settings_updated', 'Authentication settings updated.', auth_current_user_id());
do_action('auth_admin_after_settings', ['settings' => auth_get_settings(true)]);
auth_flash('success', 'Authentication settings saved.');
redirect('admin/auth/settings?tab=' . rawurlencode((string)($post['active_tab'] ?? 'general')));
exit;
