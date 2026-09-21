<?php

namespace ThunderAuthentication;

defined('ROOTPATH') or die('Direct script access denied');

do_action('auth_admin_before_roles');
?>
<div class="th-auth-admin"><div class="th-auth-admin__header"><div><span class="th-auth-admin__eyebrow">Users &amp; Access</span><h1 class="th-auth-admin__title">Roles &amp; Permissions</h1><p class="th-auth-admin__subtitle">Roles are editable; permissions remain owned by the plugins that register them.</p></div><?php if(auth_current_user_id()===1||user_can('auth.create_roles')):?><a class="th-auth-admin__button" href="<?=esc(ROOT . '/admin/auth/roles/create')?>"><i class="fa-solid fa-plus"></i> Create role</a><?php endif;?></div>
<div class="th-auth-admin__cards"><?php foreach($roles as $role):?><article class="th-auth-admin__role-card"><div class="th-auth-admin__role-icon"><i class="fa-solid <?=$role->slug==='admin'?'fa-user-shield':'fa-user-tag'?>"></i></div><div class="th-auth-admin__role-copy"><div class="th-auth-admin__role-title"><h2><?=esc((string)$role->name)?></h2><?php if((int)$role->is_protected===1):?><span class="th-auth-admin__badge">Protected</span><?php endif;?></div><code><?=esc((string)$role->slug)?></code><p><?=esc((string)$role->description)?></p><div class="th-auth-admin__role-meta"><span><i class="fa-solid fa-users"></i> <?=esc((string)$role->user_count)?> users</span><span><i class="fa-solid fa-key"></i> <?=count(auth_role_permission_slugs((int)$role->id))?> permissions</span></div></div><a class="th-auth-admin__icon-button" href="<?=esc(ROOT . '/admin/auth/roles/' . $role->id)?>"><i class="fa-solid fa-pen"></i></a></article><?php endforeach;?></div></div>
<?php do_action('auth_admin_after_roles', ['roles'=>$roles]); ?>
