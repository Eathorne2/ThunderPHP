<?php

namespace ThunderAuthentication;

defined('ROOTPATH') or die('Direct script access denied');

do_action('auth_before_login_page');
?>
<section class="th-auth-page th-auth-page--login">
    <div class="th-auth-shell">
        <aside class="th-auth-visual">
            <div class="th-auth-visual__content">
                <span class="th-auth-kicker">Secure access</span>
                <h1 class="th-auth-visual__title">Welcome back</h1>
                <p class="th-auth-visual__text">Sign in to continue to <?=esc(defined('APP_NAME') ? APP_NAME : 'your account')?>.</p>
            </div>
        </aside>
        <div class="th-auth-card">
            <?php require plugin_path('looks/shared/frontend/_messages.php'); ?>
            <?php do_action('auth_before_login_form'); ?>
            <form class="th-auth-form" method="post" action="<?=esc(ROOT . '/login')?>">
                <?=csrf()?>
                <input type="hidden" name="return_to" value="<?=esc(old_value('return_to', (string)($_GET['return'] ?? '')))?>">
                <?php do_action('auth_login_form_start'); ?>
                <div class="th-auth-heading">
                    <span class="th-auth-kicker">Account access</span>
                    <h2 class="th-auth-title">Log in</h2>
                    <p class="th-auth-subtitle">Use your <?=esc($login_identifier_mode === 'either' ? 'username or email address' : $login_identifier_mode)?>.</p>
                </div>
                <?php do_action('auth_login_fields'); ?>
                <div class="th-auth-field">
                    <label class="th-auth-label" for="th-auth-identifier"><?=esc($login_identifier_mode === 'either' ? 'Username or email' : ucfirst((string)$login_identifier_mode))?></label>
                    <input class="th-auth-input" id="th-auth-identifier" type="text" name="identifier" value="<?=esc(old_value('identifier'))?>" autocomplete="username" required autofocus>
                </div>
                <div class="th-auth-field">
                    <label class="th-auth-label" for="th-auth-password">Password</label>
                    <div class="th-auth-password-wrap">
                        <input class="th-auth-input" id="th-auth-password" type="password" name="password" autocomplete="current-password" required>
                        <button class="th-auth-password-toggle" type="button" tabindex="-1" data-password-toggle="th-auth-password" aria-label="Show password">Show</button>
                    </div>
                </div>
                <?php do_action('auth_after_login_fields'); ?>
                <?php if ($remember_enabled): ?>
                    <input type="hidden" name="remember" value="0">
                    <label class="th-auth-choice th-auth-choice--single"><input type="checkbox" name="remember" value="1" <?=old_checked('remember', '1')?>> <span>Keep me signed in</span></label>
                <?php endif; ?>
                <button class="th-auth-button th-auth-button--primary" type="submit">Log in</button>
                <?php do_action('auth_login_form_end'); ?>
            </form>
            <?php do_action('auth_after_login_form'); ?>
            <?php if ($registration_enabled): ?>
                <p class="th-auth-switch">No account yet? <a class="th-auth-link" href="<?=esc(ROOT . '/signup')?>">Create one</a></p>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php do_action('auth_after_login_page'); ?>
