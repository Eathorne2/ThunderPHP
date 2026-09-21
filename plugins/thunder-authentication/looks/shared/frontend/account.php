<?php

namespace ThunderAuthentication;

defined('ROOTPATH') or die('Direct script access denied');

$user = $account_user;
?>
<section class="th-auth-account-page">
    <div class="th-auth-account-shell">
        <?php require plugin_path('looks/shared/frontend/_messages.php'); ?>
        <header class="th-auth-account-header">
            <div class="th-auth-avatar th-auth-avatar--large"><img src="<?=esc(get_image((string)$user->image))?>" alt=""></div>
            <div>
                <span class="th-auth-kicker">My account</span>
                <h1 class="th-auth-account-title"><?=esc((string)$user->display_name)?></h1>
                <p class="th-auth-subtitle">@<?=esc((string)$user->username)?> · <?=esc(ucfirst((string)$user->status))?></p>
            </div>
            <div class="th-auth-profile-actions">
                <a class="th-auth-button th-auth-button--secondary" href="<?=esc(auth_profile_url($user))?>">View public profile</a>
                <?php if (auth_can_access_admin()): ?>
                    <a class="th-auth-button th-auth-button--secondary" href="<?=esc(ROOT . '/admin')?>"><i class="fa-solid fa-gauge-high"></i> Admin</a>
                <?php endif; ?>
                <a class="th-auth-button th-auth-button--secondary" href="<?=esc(ROOT . '/logout')?>"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
            </div>
        </header>
        <nav class="th-auth-tabs" aria-label="Account sections">
            <button class="th-auth-tab is-active" type="button" data-auth-tab="profile">Profile</button>
            <button class="th-auth-tab" type="button" data-auth-tab="password">Password</button>
            <?php do_action('auth_account_tabs', ['user' => $user]); ?>
        </nav>
        <div class="th-auth-account-layout">
            <aside class="th-auth-account-side">
                <div class="th-auth-side-card">
                    <span class="th-auth-label">Roles</span>
                    <div class="th-auth-badges"><?php foreach ($account_roles as $role): ?><span class="th-auth-badge"><?=esc((string)$role->name)?></span><?php endforeach; ?></div>
                </div>
                <?php do_action('auth_account_menu', ['user' => $user]); ?>
            </aside>
            <main class="th-auth-account-main">
                <?php do_action('auth_before_account_content', ['user' => $user]); ?>
                <section class="th-auth-panel is-active" data-auth-panel="profile">
                    <div class="th-auth-panel-heading"><h2 class="th-auth-panel-title">Profile details</h2><p class="th-auth-subtitle">Update the information attached to your account.</p></div>
                    <form class="th-auth-form" method="post" action="<?=esc(ROOT . '/account/profile')?>" enctype="multipart/form-data">
                        <?=csrf()?>
                        <?php if ($allow_profile_images): ?>
                            <div class="th-auth-field"><label class="th-auth-label" for="profile_image">Profile image</label><input class="th-auth-input" id="profile_image" type="file" name="profile_image" accept="image/jpeg,image/png,image/webp"><div class="th-auth-help">JPEG, PNG, or WebP. Maximum 5 MB.</div></div>
                        <?php endif; ?>
                        <div class="th-auth-grid th-auth-grid--two">
                            <div class="th-auth-field"><label class="th-auth-label" for="username">Username</label><input class="th-auth-input" id="username" type="text" name="username" value="<?=esc(old_value('username', (string)$user->username))?>" <?=$allow_username_change ? '' : 'readonly'?>></div>
                            <div class="th-auth-field"><label class="th-auth-label" for="display_name">Display name</label><input class="th-auth-input" id="display_name" type="text" name="display_name" value="<?=esc(old_value('display_name', (string)$user->display_name))?>"></div>
                        </div>
                        <?php if (auth_user_has_column('email')): ?><div class="th-auth-field"><label class="th-auth-label" for="email">Email address</label><input class="th-auth-input" id="email" type="email" name="email" value="<?=esc(old_value('email', (string)($user->email ?? '')))?>" <?=$allow_email_change ? '' : 'readonly'?>></div><?php endif; ?>
                        <div class="th-auth-field"><label class="th-auth-label" for="bio">Biography</label><textarea class="th-auth-input th-auth-textarea" id="bio" name="bio" rows="5"><?=esc(old_value('bio', (string)$user->bio))?></textarea></div>
                        <?php do_action('auth_profile_details', ['user' => $user]); ?>
                        <?php $field_context = 'user'; foreach ($account_fields as $field): require plugin_path('looks/shared/frontend/_field.php'); endforeach; ?>
                        <?php do_action('auth_profile_sections', ['user' => $user]); ?>
                        <button class="th-auth-button th-auth-button--primary" type="submit">Save profile</button>
                    </form>
                </section>
                <section class="th-auth-panel" data-auth-panel="password" id="password">
                    <div class="th-auth-panel-heading"><h2 class="th-auth-panel-title">Change password</h2><p class="th-auth-subtitle">Use a strong password you do not reuse elsewhere.</p></div>
                    <?php do_action('auth_before_password_form', ['user' => $user]); ?>
                    <form class="th-auth-form" method="post" action="<?=esc(ROOT . '/account/password')?>">
                        <?=csrf()?>
                        <div class="th-auth-field"><label class="th-auth-label" for="current_password">Current password</label><input class="th-auth-input" id="current_password" type="password" name="current_password" autocomplete="current-password" required></div>
                        <?php do_action('auth_password_fields', ['user' => $user]); ?>
                        <div class="th-auth-grid th-auth-grid--two">
                            <div class="th-auth-field"><label class="th-auth-label" for="new_password">New password</label><input class="th-auth-input" id="new_password" type="password" name="new_password" autocomplete="new-password" required></div>
                            <div class="th-auth-field"><label class="th-auth-label" for="new_password_confirmation">Confirm new password</label><input class="th-auth-input" id="new_password_confirmation" type="password" name="new_password_confirmation" autocomplete="new-password" required></div>
                        </div>
                        <?php do_action('auth_after_password_form', ['user' => $user]); ?>
                        <button class="th-auth-button th-auth-button--primary" type="submit">Change password</button>
                    </form>
                </section>
                <?php do_action('auth_account_content', ['user' => $user]); ?>
                <?php do_action('auth_after_account_content', ['user' => $user]); ?>
            </main>
        </div>
    </div>
</section>
