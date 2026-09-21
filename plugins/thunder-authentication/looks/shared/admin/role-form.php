<?php

namespace ThunderAuthentication;

defined('ROOTPATH') or die('Direct script access denied');

$role = $editing_role;
$isEdit = (bool)$role;
$action = $isEdit ? ROOT . '/admin/auth/roles/' . $role->id : ROOT . '/admin/auth/roles/create';
?>
<div class="th-auth-admin">
    <div class="th-auth-admin__header">
        <div>
            <a class="th-auth-admin__back" href="<?=esc(ROOT . '/admin/auth/roles')?>"><i class="fa-solid fa-arrow-left"></i> Roles</a>
            <h1 class="th-auth-admin__title"><?=$isEdit ? 'Edit role' : 'Create role'?></h1>
            <p class="th-auth-admin__subtitle">Assign only permissions registered by installed plugins.</p>
        </div>
    </div>

    <form class="th-auth-admin__role-layout" method="post" action="<?=esc($action)?>">
        <?=csrf()?>
        <aside class="th-auth-admin__panel th-auth-admin__section">
            <div class="th-auth-admin__field">
                <label class="th-auth-admin__label" for="name">Role name *</label>
                <input class="th-auth-admin__input" id="name" name="name" value="<?=esc(old_value('name', (string)($role->name ?? '')))?>" required data-slug-source>
            </div>
            <div class="th-auth-admin__field">
                <label class="th-auth-admin__label" for="slug">Role key *</label>
                <input class="th-auth-admin__input" id="slug" name="slug" value="<?=esc(old_value('slug', (string)($role->slug ?? '')))?>" required data-slug-target <?=$isEdit && (int)$role->is_protected === 1 ? 'readonly' : ''?>>
                <div class="th-auth-admin__help">Used by contains_role(). Protected role keys cannot change.</div>
            </div>
            <div class="th-auth-admin__field">
                <label class="th-auth-admin__label" for="description">Description</label>
                <textarea class="th-auth-admin__input th-auth-admin__textarea" id="description" name="description"><?=esc(old_value('description', (string)($role->description ?? '')))?></textarea>
            </div>
            <button class="th-auth-admin__button th-auth-admin__button--block" type="submit"><i class="fa-solid fa-floppy-disk"></i> Save role</button>
            <?php if ($isEdit): ?>
                <button class="th-auth-admin__button th-auth-admin__button--secondary th-auth-admin__button--block" type="submit" formaction="<?=esc(ROOT . '/admin/auth/roles/' . $role->id . '/duplicate')?>"><i class="fa-solid fa-copy"></i> Duplicate role</button>
            <?php endif; ?>
        </aside>

        <main class="th-auth-admin__panel th-auth-admin__section">
            <div class="th-auth-admin__permission-head">
                <div>
                    <h2>Permissions</h2>
                    <p>Search and select permission groups.</p>
                </div>
                <input class="th-auth-admin__input th-auth-admin__permission-search" type="search" placeholder="Search permissions" data-permission-search>
            </div>

            <?php do_action('auth_admin_role_fields', ['role' => $role]); ?>
            <input type="hidden" name="permissions[]" value="">

            <div class="th-auth-admin__permission-groups">
                <?php foreach ($permission_groups as $group => $permissions): ?>
                    <section class="th-auth-admin__permission-group" data-permission-group>
                        <header>
                            <div>
                                <h3><?=esc((string)$group)?></h3>
                                <span><?=count($permissions)?> permissions</span>
                            </div>
                            <div>
                                <button type="button" data-group-check>All</button>
                                <button type="button" data-group-clear>None</button>
                            </div>
                        </header>
                        <div class="th-auth-admin__permission-list">
                            <?php foreach ($permissions as $permission): ?>
                                <?php $permissionDefault = in_array((string)$permission->slug, $role_permissions, true) ? (string)$permission->slug : ''; ?>
                                <label class="th-auth-admin__permission" data-permission-item data-search="<?=esc(strtolower($permission->name . ' ' . $permission->slug . ' ' . $permission->description))?>">
                                    <input type="checkbox" name="permissions[]" value="<?=esc((string)$permission->slug)?>" <?=old_checked('permissions', (string)$permission->slug, $permissionDefault)?> <?=auth_current_user_id() === 1 || user_can('auth.assign_permissions') ? '' : 'disabled'?>>
                                    <span>
                                        <strong><?=esc((string)$permission->name)?></strong>
                                        <code><?=esc((string)$permission->slug)?></code>
                                        <small><?=esc((string)$permission->description)?></small>
                                    </span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endforeach; ?>
            </div>
            <?php do_action('auth_admin_permission_groups', ['role' => $role]); ?>
        </main>
    </form>

    <?php if ($isEdit && (int)$role->is_protected !== 1 && (auth_current_user_id() === 1 || user_can('auth.delete_roles'))): ?>
        <form class="th-auth-admin__danger-zone" method="post" action="<?=esc(ROOT . '/admin/auth/roles/' . $role->id . '/delete')?>" data-confirm="Delete this role? It must not be assigned to any users.">
            <?=csrf()?>
            <button class="th-auth-admin__button th-auth-admin__button--danger" type="submit"><i class="fa-solid fa-trash"></i> Delete role</button>
        </form>
    <?php endif; ?>
</div>
