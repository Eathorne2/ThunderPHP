<?php

namespace ThunderAuthentication;

defined('ROOTPATH') or die('Direct script access denied');

$s = $auth_settings;
$activeTab = old_value('active_tab', (string)($_GET['tab'] ?? 'general'));
$canEdit = (bool)$can_edit_settings;
$canLooks = (bool)$can_manage_looks;
$canPalettes = (bool)$can_manage_palettes;
$tabs = [
    'general' => 'General',
    'registration' => 'Registration',
    'login' => 'Login',
    'profiles' => 'Profiles',
    'passwords' => 'Passwords',
    'appearance' => 'Appearance',
];
$selectedLook = old_value('active_look', (string)$s['active_look']);
$selectedLook = isset($auth_looks[$selectedLook]) ? $selectedLook : (string)$s['active_look'];
$selectedManifest = auth_look_manifest($selectedLook);
?>
<div class="th-auth-admin">
    <div class="th-auth-admin__header">
        <div>
            <span class="th-auth-admin__eyebrow">Thunder Authentication</span>
            <h1 class="th-auth-admin__title">Settings</h1>
            <p class="th-auth-admin__subtitle">Configure registration, sessions, profiles, passwords, looks, and palettes.</p>
        </div>
    </div>

    <form method="post" action="<?=esc(ROOT . '/admin/auth/settings')?>">
        <?=csrf()?>
        <input type="hidden" name="active_tab" value="<?=esc($activeTab)?>" data-settings-active-tab>

        <nav class="th-auth-admin__settings-tabs">
            <?php foreach ($tabs as $key => $label): ?>
                <button type="button" class="<?=$activeTab === $key ? 'is-active' : ''?>" data-settings-tab="<?=esc($key)?>"><?=esc($label)?></button>
            <?php endforeach; ?>
        </nav>

        <div class="th-auth-admin__settings-panels">
            <section class="th-auth-admin__panel th-auth-admin__section <?=$activeTab === 'general' ? 'is-active' : ''?>" data-settings-panel="general">
                <div class="th-auth-admin__section-head">
                    <h2>General</h2>
                    <p>Default account behavior and role assignment.</p>
                </div>
                <div class="th-auth-admin__field">
                    <label class="th-auth-admin__label" for="default_role">Default role</label>
                    <select class="th-auth-admin__input" id="default_role" name="default_role">
                        <?php foreach ($auth_roles as $role): ?>
                            <option value="<?=esc((string)$role->slug)?>" <?=old_select('default_role', (string)$role->slug, (string)$s['default_role'])?>><?=esc((string)$role->name)?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="th-auth-admin__notice">Routes are registered in <code>config.json</code>: <code>/login</code>, <code>/signup</code>, <code>/account</code>, and <code>/profile/{username}</code>.</div>
                <?php do_action('auth_admin_settings_fields', ['tab' => 'general', 'settings' => $s]); ?>
            </section>

            <section class="th-auth-admin__panel th-auth-admin__section <?=$activeTab === 'registration' ? 'is-active' : ''?>" data-settings-panel="registration">
                <div class="th-auth-admin__section-head"><h2>Registration</h2></div>
                <div class="th-auth-admin__field">
                    <label class="th-auth-admin__label" for="registration_mode">Signup mode</label>
                    <select class="th-auth-admin__input" id="registration_mode" name="registration_mode">
                        <option value="disabled" <?=old_select('registration_mode', 'disabled', (string)$s['registration_mode'])?>>Disabled</option>
                        <option value="open" <?=old_select('registration_mode', 'open', (string)$s['registration_mode'])?>>Open registration</option>
                        <option value="approval" <?=old_select('registration_mode', 'approval', (string)$s['registration_mode'])?>>Administrator approval required</option>
                    </select>
                </div>

                <div class="th-auth-admin__checks">
                    <input type="hidden" name="auto_login_after_signup" value="0">
                    <label class="th-auth-admin__check">
                        <input type="checkbox" name="auto_login_after_signup" value="1" <?=old_checked('auto_login_after_signup', '1', (string)$s['auto_login_after_signup'])?>>
                        <span><strong>Log in immediately after open signup</strong></span>
                    </label>
                    <input type="hidden" name="terms_required" value="0">
                    <label class="th-auth-admin__check">
                        <input type="checkbox" name="terms_required" value="1" <?=old_checked('terms_required', '1', (string)$s['terms_required'])?>>
                        <span><strong>Require terms acceptance</strong></span>
                    </label>
                </div>

                <div class="th-auth-admin__field">
                    <label class="th-auth-admin__label" for="terms_url">Terms page URL</label>
                    <input class="th-auth-admin__input" id="terms_url" name="terms_url" value="<?=esc(old_value('terms_url', (string)$s['terms_url']))?>">
                </div>

                <div class="th-auth-admin__grid th-auth-admin__grid--two">
                    <div class="th-auth-admin__field">
                        <label class="th-auth-admin__label" for="allowed_email_domains">Allowed email domains</label>
                        <textarea class="th-auth-admin__input th-auth-admin__textarea" id="allowed_email_domains" name="allowed_email_domains"><?=esc(old_value('allowed_email_domains', (string)$s['allowed_email_domains']))?></textarea>
                        <div class="th-auth-admin__help">Comma or line separated. Leave blank to allow all.</div>
                    </div>
                    <div class="th-auth-admin__field">
                        <label class="th-auth-admin__label" for="blocked_email_domains">Blocked email domains</label>
                        <textarea class="th-auth-admin__input th-auth-admin__textarea" id="blocked_email_domains" name="blocked_email_domains"><?=esc(old_value('blocked_email_domains', (string)$s['blocked_email_domains']))?></textarea>
                    </div>
                </div>
                <?php do_action('auth_admin_settings_fields', ['tab' => 'registration', 'settings' => $s]); ?>
            </section>

            <section class="th-auth-admin__panel th-auth-admin__section <?=$activeTab === 'login' ? 'is-active' : ''?>" data-settings-panel="login">
                <div class="th-auth-admin__section-head"><h2>Login &amp; sessions</h2></div>

                <div class="th-auth-admin__grid th-auth-admin__grid--two">
                    <div class="th-auth-admin__field">
                        <label class="th-auth-admin__label" for="login_identifier">Login identifier</label>
                        <select class="th-auth-admin__input" id="login_identifier" name="login_identifier">
                            <option value="username" <?=old_select('login_identifier', 'username', (string)$s['login_identifier'])?>>Username only</option>
                            <option value="email" <?=old_select('login_identifier', 'email', (string)$s['login_identifier'])?>>Email only</option>
                            <option value="either" <?=old_select('login_identifier', 'either', (string)$s['login_identifier'])?>>Username or email</option>
                        </select>
                    </div>
                    <div class="th-auth-admin__field">
                        <label class="th-auth-admin__label" for="remember_days">Remember-me duration (days)</label>
                        <input class="th-auth-admin__input" id="remember_days" type="number" min="1" max="365" name="remember_days" value="<?=esc(old_value('remember_days', (string)$s['remember_days']))?>">
                    </div>
                </div>

                <div class="th-auth-admin__checks">
                    <input type="hidden" name="remember_me_enabled" value="0">
                    <label class="th-auth-admin__check">
                        <input type="checkbox" name="remember_me_enabled" value="1" <?=old_checked('remember_me_enabled', '1', (string)$s['remember_me_enabled'])?>>
                        <span><strong>Enable remember me</strong></span>
                    </label>
                    <input type="hidden" name="single_session" value="0">
                    <label class="th-auth-admin__check">
                        <input type="checkbox" name="single_session" value="1" <?=old_checked('single_session', '1', (string)$s['single_session'])?>>
                        <span><strong>Allow only one active session per user</strong></span>
                    </label>
                </div>

                <div class="th-auth-admin__grid th-auth-admin__grid--two">
                    <div class="th-auth-admin__field">
                        <label class="th-auth-admin__label" for="login_redirect">Login redirect</label>
                        <input class="th-auth-admin__input" id="login_redirect" name="login_redirect" value="<?=esc(old_value('login_redirect', (string)$s['login_redirect']))?>">
                    </div>
                    <div class="th-auth-admin__field">
                        <label class="th-auth-admin__label" for="logout_redirect">Logout redirect</label>
                        <input class="th-auth-admin__input" id="logout_redirect" name="logout_redirect" value="<?=esc(old_value('logout_redirect', (string)$s['logout_redirect']))?>">
                    </div>
                </div>

                <div class="th-auth-admin__grid th-auth-admin__grid--two">
                    <div class="th-auth-admin__field">
                        <label class="th-auth-admin__label" for="maximum_failed_attempts">Maximum failed attempts</label>
                        <input class="th-auth-admin__input" id="maximum_failed_attempts" type="number" min="1" max="100" name="maximum_failed_attempts" value="<?=esc(old_value('maximum_failed_attempts', (string)$s['maximum_failed_attempts']))?>">
                    </div>
                    <div class="th-auth-admin__field">
                        <label class="th-auth-admin__label" for="lockout_minutes">Lockout duration (minutes)</label>
                        <input class="th-auth-admin__input" id="lockout_minutes" type="number" min="1" max="1440" name="lockout_minutes" value="<?=esc(old_value('lockout_minutes', (string)$s['lockout_minutes']))?>">
                    </div>
                </div>
                <?php do_action('auth_admin_settings_fields', ['tab' => 'login', 'settings' => $s]); ?>
            </section>

            <section class="th-auth-admin__panel th-auth-admin__section <?=$activeTab === 'profiles' ? 'is-active' : ''?>" data-settings-panel="profiles">
                <div class="th-auth-admin__section-head"><h2>Profiles</h2></div>
                <div class="th-auth-admin__checks">
                    <?php foreach ([
                        'public_profiles' => 'Enable public profiles',
                        'allow_profile_images' => 'Allow profile-image uploads',
                        'allow_username_change' => 'Users may change usernames',
                        'allow_email_change' => 'Users may change email addresses',
                    ] as $key => $label): ?>
                        <input type="hidden" name="<?=esc($key)?>" value="0">
                        <label class="th-auth-admin__check">
                            <input type="checkbox" name="<?=esc($key)?>" value="1" <?=old_checked($key, '1', (string)$s[$key])?>>
                            <span><strong><?=esc($label)?></strong></span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <?php do_action('auth_admin_settings_fields', ['tab' => 'profiles', 'settings' => $s]); ?>
            </section>

            <section class="th-auth-admin__panel th-auth-admin__section <?=$activeTab === 'passwords' ? 'is-active' : ''?>" data-settings-panel="passwords">
                <div class="th-auth-admin__section-head"><h2>Password policy</h2></div>
                <div class="th-auth-admin__field">
                    <label class="th-auth-admin__label" for="minimum_password_length">Minimum length</label>
                    <input class="th-auth-admin__input" id="minimum_password_length" type="number" min="6" max="128" name="minimum_password_length" value="<?=esc(old_value('minimum_password_length', (string)$s['minimum_password_length']))?>">
                </div>
                <div class="th-auth-admin__checks">
                    <?php foreach ([
                        'password_require_uppercase' => 'Require uppercase letter',
                        'password_require_lowercase' => 'Require lowercase letter',
                        'password_require_number' => 'Require number',
                        'password_require_symbol' => 'Require symbol',
                    ] as $key => $label): ?>
                        <input type="hidden" name="<?=esc($key)?>" value="0">
                        <label class="th-auth-admin__check">
                            <input type="checkbox" name="<?=esc($key)?>" value="1" <?=old_checked($key, '1', (string)$s[$key])?>>
                            <span><strong><?=esc($label)?></strong></span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <?php do_action('auth_admin_settings_fields', ['tab' => 'passwords', 'settings' => $s]); ?>
            </section>

            <section class="th-auth-admin__panel th-auth-admin__section <?=$activeTab === 'appearance' ? 'is-active' : ''?>" data-settings-panel="appearance">
                <div class="th-auth-admin__section-head">
                    <h2>Appearance</h2>
                    <p>Authentication pages use their own look and palette, independently from Thunder Admin.</p>
                </div>

                <div class="th-auth-admin__look-grid">
                    <?php foreach ($auth_looks as $folder => $look): ?>
                        <label class="th-auth-admin__look">
                            <input type="radio" name="active_look" value="<?=esc($folder)?>" <?=$canLooks ? '' : 'disabled'?> <?=old_checked('active_look', $folder, (string)$s['active_look'])?> data-look-radio>
                            <span>
                                <strong><?=esc((string)($look['name'] ?? $folder))?></strong>
                                <small><?=esc((string)($look['description'] ?? ''))?></small>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>

                <div class="th-auth-admin__field">
                    <label class="th-auth-admin__label" for="active_palette">Palette</label>
                    <select class="th-auth-admin__input" id="active_palette" name="active_palette" <?=$canPalettes ? '' : 'disabled'?>>
                        <?php foreach ((array)($selectedManifest['palettes'] ?? []) as $key => $palette): ?>
                            <option value="<?=esc((string)$key)?>" <?=old_select('active_palette', (string)$key, (string)$s['active_palette'])?>><?=esc(ucwords(str_replace(['-', '_'], ' ', (string)$key)))?></option>
                        <?php endforeach; ?>
                        <option value="custom" <?=old_select('active_palette', 'custom', (string)$s['active_palette'])?>>Custom</option>
                    </select>
                </div>

                <div class="th-auth-admin__color-grid">
                    <?php foreach ([
                        'primary' => 'Primary',
                        'secondary' => 'Secondary',
                        'background' => 'Background',
                        'surface' => 'Surface',
                        'text' => 'Text',
                        'muted_text' => 'Muted text',
                        'border' => 'Border',
                        'success' => 'Success',
                        'warning' => 'Warning',
                        'danger' => 'Danger',
                    ] as $key => $label): ?>
                        <label class="th-auth-admin__color">
                            <span><?=esc($label)?></span>
                            <input type="color" name="custom_<?=esc($key)?>" <?=$canPalettes ? '' : 'disabled'?> value="<?=esc(old_value('custom_' . $key, (string)$s['custom_' . $key]))?>">
                        </label>
                    <?php endforeach; ?>
                </div>

                <div class="th-auth-admin__grid th-auth-admin__grid--two">
                    <div class="th-auth-admin__field">
                        <label class="th-auth-admin__label" for="form_width">Form width (px)</label>
                        <input class="th-auth-admin__input" id="form_width" type="number" <?=$canPalettes ? '' : 'disabled'?> min="320" max="760" name="form_width" value="<?=esc(old_value('form_width', (string)$s['form_width']))?>">
                    </div>
                    <div class="th-auth-admin__field">
                        <label class="th-auth-admin__label" for="border_radius">Border radius (px)</label>
                        <input class="th-auth-admin__input" id="border_radius" type="number" <?=$canPalettes ? '' : 'disabled'?> min="0" max="40" name="border_radius" value="<?=esc(old_value('border_radius', (string)$s['border_radius']))?>">
                    </div>
                </div>

                <?php do_action('auth_admin_settings_fields', ['tab' => 'appearance', 'settings' => $s]); ?>
            </section>
        </div>

        <?php if ($canEdit): ?>
            <div class="th-auth-admin__savebar">
                <button class="th-auth-admin__button" type="submit"><i class="fa-solid fa-floppy-disk"></i> Save settings</button>
            </div>
        <?php endif; ?>
    </form>

    <?php do_action('auth_admin_after_settings', ['settings' => $s]); ?>
</div>
