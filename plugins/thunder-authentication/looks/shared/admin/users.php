<?php

namespace ThunderAuthentication;

defined('ROOTPATH') or die('Direct script access denied');

$result = $users_result;
do_action('auth_admin_before_users', ['filters' => $user_filters]);
?>
<div class="th-auth-admin">
    <div class="th-auth-admin__header"><div><span class="th-auth-admin__eyebrow">Thunder Authentication</span><h1 class="th-auth-admin__title">Users</h1><p class="th-auth-admin__subtitle">Search accounts, review status, assign roles, and manage access.</p></div><div class="th-auth-admin__actions"><?php if (auth_current_user_id() === 1 || user_can('auth.export_users')): ?><a class="th-auth-admin__button th-auth-admin__button--secondary" href="<?=esc(ROOT . '/admin/auth/users/export?' . http_build_query($user_filters))?>"><i class="fa-solid fa-file-csv"></i> Export</a><?php endif; ?><?php if (auth_current_user_id() === 1 || user_can('auth.create_users')): ?><a class="th-auth-admin__button" href="<?=esc(ROOT . '/admin/auth/users/create')?>"><i class="fa-solid fa-user-plus"></i> Add user</a><?php endif; ?></div></div>
    <form class="th-auth-admin__filters" method="get" action="<?=esc(ROOT . '/admin/auth/users')?>">
        <div class="th-auth-admin__field th-auth-admin__field--grow"><label class="th-auth-admin__label" for="q">Search</label><input class="th-auth-admin__input" id="q" name="q" value="<?=esc(old_value('q', (string)$user_filters['q'], 'get'))?>" placeholder="Username, name, or email"></div>
        <div class="th-auth-admin__field"><label class="th-auth-admin__label" for="role">Role</label><select class="th-auth-admin__input" id="role" name="role"><option value="">All roles</option><?php foreach ($available_roles as $role): ?><option value="<?=esc((string)$role->slug)?>" <?=old_select('role',(string)$role->slug,$user_filters['role'],'get')?>><?=esc((string)$role->name)?></option><?php endforeach; ?></select></div>
        <div class="th-auth-admin__field"><label class="th-auth-admin__label" for="status">Status</label><select class="th-auth-admin__input" id="status" name="status"><option value="">All statuses</option><?php foreach (['active'=>'Active','pending'=>'Pending approval','disabled'=>'Disabled','suspended'=>'Suspended','archived'=>'Archived'] as $key=>$label): ?><option value="<?=esc($key)?>" <?=old_select('status', $key, (string)$user_filters['status'], 'get')?>><?=esc($label)?></option><?php endforeach; ?></select></div>
        <?php do_action('auth_admin_user_filters', ['filters' => $user_filters]); ?>
        <button class="th-auth-admin__button" type="submit"><i class="fa-solid fa-magnifying-glass"></i> Filter</button>
    </form>
    <div class="th-auth-admin__panel">
        <div class="th-auth-admin__panel-head"><strong><?=esc((string)$result['total'])?> users</strong><span>Page <?=esc((string)$result['page'])?> of <?=esc((string)$result['pages'])?></span></div>
        <div class="th-auth-admin__table-wrap"><table class="th-auth-admin__table"><thead><tr><th>User</th><th>Roles</th><th>Status</th><th>Joined</th><th>Last login</th><?php do_action('auth_admin_user_columns'); ?><th class="th-auth-admin__right">Actions</th></tr></thead><tbody>
            <?php if (!$result['users']): ?><tr><td colspan="7"><div class="th-auth-admin__empty">No users matched the current filters.</div></td></tr><?php endif; ?>
            <?php foreach ($result['users'] as $user): ?><tr>
                <td><div class="th-auth-admin__user"><img class="th-auth-admin__avatar" src="<?=esc(get_image((string)$user->image))?>" alt=""><div><a class="th-auth-admin__primary-link" href="<?=esc(ROOT . '/admin/auth/users/' . $user->id)?>"><?=esc((string)$user->display_name)?></a><div class="th-auth-admin__muted">@<?=esc((string)$user->username)?><?php if (!empty($user->email)): ?> · <?=esc((string)$user->email)?><?php endif; ?></div></div></div></td>
                <td><div class="th-auth-admin__badges"><?php foreach ($user->roles as $role): ?><span class="th-auth-admin__badge"><?=esc((string)$role->name)?></span><?php endforeach; ?></div></td>
                <td><span class="th-auth-admin__status th-auth-admin__status--<?=esc((string)$user->status)?>"><?=esc(ucfirst((string)$user->status))?></span></td>
                <td><?=esc($user->date_created ? get_date((string)$user->date_created) : '—')?></td><td><?=esc($user->last_login ? get_date((string)$user->last_login) : 'Never')?></td>
                <?php do_action('auth_admin_user_row_actions', ['user' => $user]); ?>
                <td class="th-auth-admin__right"><a class="th-auth-admin__icon-button" href="<?=esc(ROOT . '/admin/auth/users/' . $user->id)?>" title="Edit"><i class="fa-solid fa-pen"></i></a></td>
            </tr><?php endforeach; ?>
        </tbody></table></div>
        <?php if ($result['pages'] > 1): ?><nav class="th-auth-admin__pagination"><?php for ($i=1;$i<=$result['pages'];$i++): ?><a class="<?=$i===$result['page']?'is-active':''?>" href="<?=esc(ROOT . '/admin/auth/users?' . http_build_query(array_merge($user_filters,['page'=>$i])))?>"><?=$i?></a><?php endfor; ?></nav><?php endif; ?>
    </div>
</div>
<?php do_action('auth_admin_after_users', ['users' => $result['users']]); ?>
