<?php

namespace ThunderAuthentication;

defined('ROOTPATH') or die('Direct script access denied');

$user = $editing_user;
$isEdit = (bool)$user;
$action = $isEdit ? ROOT . '/admin/auth/users/' . $user->id : ROOT . '/admin/auth/users/create';
$selectedRoles = $isEdit ? $editing_user_roles : [];
$canManageStatus = auth_current_user_id() === 1 || user_can('auth.disable_users');
$canAssignRoles = auth_current_user_id() === 1 || user_can('auth.assign_roles');
$canAssignAdminRole = auth_current_user_id() === 1 || contains_role('admin');

do_action('auth_admin_before_user', ['user' => $user]);
?>
<div class="th-auth-admin">
    <div class="th-auth-admin__header">
        <div>
            <a class="th-auth-admin__back" href="<?=esc(ROOT . '/admin/auth/users')?>"><i class="fa-solid fa-arrow-left"></i> Users</a>
            <h1 class="th-auth-admin__title"><?=$isEdit ? 'Edit user' : 'Create user'?></h1>
            <p class="th-auth-admin__subtitle"><?=$isEdit ? 'Update account details, status, roles, and custom fields.' : 'Create a new account and assign its initial access.'?></p>
        </div>
        <?php if ($isEdit): ?>
            <a class="th-auth-admin__button th-auth-admin__button--secondary" href="<?=esc(auth_profile_url($user))?>" target="_blank">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> Public profile
            </a>
        <?php endif; ?>
    </div>

    <form class="th-auth-admin__form-layout" method="post" action="<?=esc($action)?>" enctype="multipart/form-data">
        <?=csrf()?>
        <main class="th-auth-admin__form-main">
            <section class="th-auth-admin__panel th-auth-admin__section">
                <div class="th-auth-admin__section-head">
                    <h2>Account</h2>
                    <p>Core login and profile information.</p>
                </div>

                <div class="th-auth-admin__grid th-auth-admin__grid--two">
                    <div class="th-auth-admin__field">
                        <label class="th-auth-admin__label" for="username">Username *</label>
                        <input class="th-auth-admin__input" id="username" name="username" value="<?=esc(old_value('username', (string)($user->username ?? '')))?>" required>
                    </div>
                    <div class="th-auth-admin__field">
                        <label class="th-auth-admin__label" for="display_name">Display name</label>
                        <input class="th-auth-admin__input" id="display_name" name="display_name" value="<?=esc(old_value('display_name', (string)($user->display_name ?? '')))?>">
                    </div>
                </div>

                <?php if (auth_user_has_column('email')): ?>
                    <div class="th-auth-admin__field">
                        <label class="th-auth-admin__label" for="email">Email address *</label>
                        <input class="th-auth-admin__input" id="email" type="email" name="email" value="<?=esc(old_value('email', (string)($user->email ?? '')))?>" required>
                    </div>
                <?php endif; ?>

                <div class="th-auth-admin__field">
                    <label class="th-auth-admin__label" for="bio">Biography</label>
                    <textarea class="th-auth-admin__input th-auth-admin__textarea" id="bio" name="bio"><?=esc(old_value('bio', (string)($user->bio ?? '')))?></textarea>
                </div>

                <div class="th-auth-admin__field">
                    <label class="th-auth-admin__label" for="password"><?=$isEdit ? 'New password' : 'Password *'?></label>
                    <input class="th-auth-admin__input" id="password" type="password" name="password" <?=$isEdit ? '' : 'required'?> autocomplete="new-password">
                    <div class="th-auth-admin__help"><?=$isEdit ? 'Leave blank to keep the existing password.' : 'The configured password policy applies.'?></div>
                </div>

                <?php do_action('auth_admin_user_fields', ['user' => $user]); ?>
            </section>

            <?php if ($admin_user_fields): ?>
                <section class="th-auth-admin__panel th-auth-admin__section">
                    <div class="th-auth-admin__section-head">
                        <h2>Extra fields</h2>
                        <p>Plugin-defined profile information.</p>
                    </div>
                    <?php
                    $field_context = 'admin';
                    foreach ($admin_user_fields as $field) {
                        require plugin_path('looks/shared/frontend/_field.php');
                    }
                    ?>
                </section>
            <?php endif; ?>

            <?php do_action('auth_admin_user_tabs', ['user' => $user]); ?>
        </main>

        <aside class="th-auth-admin__form-side">
            <section class="th-auth-admin__panel th-auth-admin__section">
                <div class="th-auth-admin__section-head"><h2>Status</h2></div>
                <div class="th-auth-admin__field">
                    <select class="th-auth-admin__input" name="status" <?=$canManageStatus ? '' : 'disabled'?>>
                        <?php foreach (['active' => 'Active', 'pending' => 'Pending approval', 'disabled' => 'Disabled', 'suspended' => 'Suspended', 'archived' => 'Archived'] as $key => $label): ?>
                            <option value="<?=esc($key)?>" <?=old_select('status', $key, (string)($user->status ?? 'active'))?> <?=($user && (int)$user->id === 1 && $key !== 'active') ? 'disabled' : ''?>><?=esc($label)?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </section>

            <section class="th-auth-admin__panel th-auth-admin__section">
                <div class="th-auth-admin__section-head">
                    <h2>Roles</h2>
                    <p>Permissions are combined across all selected roles.</p>
                </div>
                <input type="hidden" name="roles[]" value="">
                <div class="th-auth-admin__checks">
                    <?php foreach ($available_roles as $role): ?>
                        <?php $roleDefault = in_array((string)$role->slug, $selectedRoles, true) ? (string)$role->id : ''; ?>
                        <label class="th-auth-admin__check">
                            <input type="checkbox" name="roles[]" value="<?=esc((string)$role->id)?>" <?=old_checked('roles', (string)$role->id, $roleDefault)?> <?=$canAssignRoles && ((string)$role->slug !== 'admin' || $canAssignAdminRole) ? '' : 'disabled'?>>
                            <span>
                                <strong><?=esc((string)$role->name)?></strong>
                                <small><?=esc((string)$role->description)?></small>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </section>

            <?php do_action('auth_admin_user_sidebar', ['user' => $user]); ?>

            <button class="th-auth-admin__button th-auth-admin__button--block" type="submit">
                <i class="fa-solid fa-floppy-disk"></i> <?=$isEdit ? 'Save changes' : 'Create user'?>
            </button>

            <?php if ($isEdit && (int)$user->id !== 1): ?>
                <?php if (auth_current_user_id() === 1 || user_can('auth.disable_users')): ?>
                    <button class="th-auth-admin__button th-auth-admin__button--warning th-auth-admin__button--block" type="submit" formaction="<?=esc(ROOT . '/admin/auth/users/' . $user->id . '/toggle')?>">
                        <i class="fa-solid fa-ban"></i> <?=$user->status === 'disabled' ? 'Reactivate' : 'Disable'?> user
                    </button>
                <?php endif; ?>
                <?php if ((auth_current_user_id() === 1 || user_can('auth.delete_users')) && (int)$user->id !== auth_current_user_id()): ?>
                    <button class="th-auth-admin__button th-auth-admin__button--danger th-auth-admin__button--block" type="submit" formaction="<?=esc(ROOT . '/admin/auth/users/' . $user->id . '/delete')?>" data-confirm="Delete this user permanently?">
                        <i class="fa-solid fa-trash"></i> Delete user
                    </button>
                <?php endif; ?>
            <?php endif; ?>

            <?php do_action('auth_admin_user_actions', ['user' => $user]); ?>
        </aside>
    </form>
</div>
<?php do_action('auth_admin_after_user', ['user' => $user]); ?>
