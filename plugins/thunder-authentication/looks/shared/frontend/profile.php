<?php

namespace ThunderAuthentication;

defined('ROOTPATH') or die('Direct script access denied');

$user = $profile_user;
do_action('auth_before_profile', ['user' => $user]);
?>
<section class="th-auth-profile-page">
    <article class="th-auth-profile-card">
        <?php do_action('auth_profile_header', ['user' => $user]); ?>
        <div class="th-auth-profile-hero">
            <div class="th-auth-avatar th-auth-avatar--xl"><img src="<?=esc(get_image((string)$user->image))?>" alt=""></div>
            <div class="th-auth-profile-copy">
                <span class="th-auth-kicker">Public profile</span>
                <h1 class="th-auth-profile-title"><?=esc((string)$user->display_name)?></h1>
                <p class="th-auth-subtitle">@<?=esc((string)$user->username)?></p>
            </div>
            <?php if (auth_session()->is_logged_in()): ?>
                <div class="th-auth-profile-actions">
                    <?php if ((int)$user->id === auth_current_user_id()): ?>
                        <a class="th-auth-button th-auth-button--secondary" href="<?=esc(ROOT . '/account')?>"><i class="fa-solid fa-user-pen"></i> My account</a>
                    <?php endif; ?>
                    <?php if (auth_can_access_admin()): ?>
                        <a class="th-auth-button th-auth-button--secondary" href="<?=esc(ROOT . '/admin')?>"><i class="fa-solid fa-gauge-high"></i> Admin</a>
                    <?php endif; ?>
                    <a class="th-auth-button th-auth-button--secondary" href="<?=esc(ROOT . '/logout')?>"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
                </div>
            <?php endif; ?>
        </div>
        <div class="th-auth-profile-layout">
            <aside class="th-auth-profile-sidebar">
                <div class="th-auth-profile-meta"><span class="th-auth-label">Member since</span><strong><?=$user->date_created ? esc(get_date((string)$user->date_created)) : 'Not available'?></strong></div>
                <?php do_action('auth_profile_sidebar', ['user' => $user]); ?>
            </aside>
            <div class="th-auth-profile-details">
                <?php if ($user->bio): ?><section class="th-auth-profile-section"><h2 class="th-auth-panel-title">About</h2><p class="th-auth-profile-bio"><?=nl2br(esc((string)$user->bio))?></p></section><?php endif; ?>
                <?php do_action('auth_profile_details', ['user' => $user]); ?>
                <?php if ($profile_fields): ?>
                    <section class="th-auth-profile-section"><h2 class="th-auth-panel-title">Details</h2><dl class="th-auth-definition-list">
                        <?php foreach ($profile_fields as $field): ?>
                            <div class="th-auth-definition-row"><dt><?=esc((string)$field->label)?></dt><dd><?php $value=$field->value; echo esc(is_array($value) ? implode(', ', $value) : (string)$value); ?></dd></div>
                        <?php endforeach; ?>
                    </dl></section>
                <?php endif; ?>
                <?php do_action('auth_profile_sections', ['user' => $user]); ?>
            </div>
        </div>
    </article>
</section>
<?php do_action('auth_after_profile', ['user' => $user]); ?>
