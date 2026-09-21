<?php

namespace ThunderAuthentication;

defined('ROOTPATH') or die('Direct script access denied');

do_action('auth_before_signup_page');
?>
<section class="th-auth-page th-auth-page--signup">
    <div class="th-auth-shell">
        <aside class="th-auth-visual">
            <div class="th-auth-visual__content">
                <span class="th-auth-kicker">New account</span>
                <h1 class="th-auth-visual__title">Join <?=esc(defined('APP_NAME') ? APP_NAME : 'us')?></h1>
                <p class="th-auth-visual__text">Create your account and complete your profile in one place.</p>
            </div>
        </aside>
        <div class="th-auth-card th-auth-card--wide">
            <?php require plugin_path('looks/shared/frontend/_messages.php'); ?>
            <?php do_action('auth_before_signup_form'); ?>
            <form class="th-auth-form" method="post" action="<?=esc(ROOT . '/signup')?>" enctype="multipart/form-data">
                <?=csrf()?>
                <?php do_action('auth_signup_form_start'); ?>
                <div class="th-auth-heading">
                    <span class="th-auth-kicker">Registration</span>
                    <h2 class="th-auth-title">Create account</h2>
                    <p class="th-auth-subtitle">Fields marked with * are required.</p>
                </div>
                <div class="th-auth-grid th-auth-grid--two">
                    <div class="th-auth-field">
                        <label class="th-auth-label" for="th-auth-username">Username *</label>
                        <input class="th-auth-input" id="th-auth-username" type="text" name="username" value="<?=esc(old_value('username'))?>" autocomplete="username" required>
                    </div>
                    <div class="th-auth-field">
                        <label class="th-auth-label" for="th-auth-display-name">Display name</label>
                        <input class="th-auth-input" id="th-auth-display-name" type="text" name="display_name" value="<?=esc(old_value('display_name'))?>" autocomplete="name">
                    </div>
                </div>
                <?php if (auth_user_has_column('email')): ?>
                    <div class="th-auth-field">
                        <label class="th-auth-label" for="th-auth-email">Email address *</label>
                        <input class="th-auth-input" id="th-auth-email" type="email" name="email" value="<?=esc(old_value('email'))?>" autocomplete="email" required>
                    </div>
                <?php endif; ?>
                <div class="th-auth-grid th-auth-grid--two">
                    <div class="th-auth-field">
                        <label class="th-auth-label" for="th-auth-password">Password *</label>
                        <div class="th-auth-password-wrap"><input class="th-auth-input" id="th-auth-password" type="password" name="password" autocomplete="new-password" required><button class="th-auth-password-toggle" type="button" tabindex="-1" data-password-toggle="th-auth-password">Show</button></div>
                    </div>
                    <div class="th-auth-field">
                        <label class="th-auth-label" for="th-auth-password-confirmation">Confirm password *</label>
                        <input class="th-auth-input" id="th-auth-password-confirmation" type="password" name="password_confirmation" autocomplete="new-password" required>
                    </div>
                </div>
                <?php do_action('auth_signup_fields'); ?>
                <?php $field_context = 'user'; foreach ($signup_fields as $field): require plugin_path('looks/shared/frontend/_field.php'); endforeach; ?>
                <?php do_action('auth_after_signup_fields'); ?>
                <?php if ($terms_required): ?>
                    <input type="hidden" name="terms" value="0"><label class="th-auth-choice th-auth-choice--single"><input type="checkbox" name="terms" value="1" <?=old_checked('terms', '1')?> required> <span>I accept the <?=$terms_url ? '<a class="th-auth-link" href="' . esc($terms_url) . '" target="_blank" rel="noopener">terms and conditions</a>' : 'terms and conditions'?>.</span></label>
                <?php endif; ?>
                <button class="th-auth-button th-auth-button--primary" type="submit">Create account</button>
                <?php do_action('auth_signup_form_end'); ?>
            </form>
            <?php do_action('auth_after_signup_form'); ?>
            <p class="th-auth-switch">Already registered? <a class="th-auth-link" href="<?=esc(ROOT . '/login')?>">Log in</a></p>
        </div>
    </div>
</section>
<?php do_action('auth_after_signup_page'); ?>
