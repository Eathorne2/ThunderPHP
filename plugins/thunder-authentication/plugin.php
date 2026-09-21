<?php

namespace ThunderAuthentication;

defined('ROOTPATH') or die('Direct script access denied');

require_once plugin_path('functions.php');

set_value([
    'plugin_route' => 'auth',
    'admin_route' => 'admin',
    'auth_tables' => [
        'users' => 'auth_users',
        'profiles' => 'auth_user_profiles',
        'roles' => 'auth_roles',
        'user_roles' => 'auth_user_roles_map',
        'role_permissions' => 'auth_role_permissions',
        'fields' => 'auth_user_fields',
        'field_options' => 'auth_user_field_options',
        'field_values' => 'auth_user_field_values',
        'settings' => 'auth_settings',
        'remember_tokens' => 'auth_remember_tokens',
        'login_attempts' => 'auth_login_attempts',
        'user_sessions' => 'auth_user_sessions',
        'audit_log' => 'auth_audit_log',
    ],
]);

add_action('before_controller', function (): void {
    auth_restore_old_input();
    auth_bootstrap_primary_user();
    auth_auto_login();
    auth_validate_current_session();
}, 1);

add_filter('roles', function (array $roles): array {
    foreach (auth_all_roles() as $role) {
        $roles[(string)$role->slug] = [
            'name' => (string)$role->name,
            'slug' => (string)$role->slug,
            'description' => (string)$role->description,
        ];
    }
    return $roles;
});

add_filter('user_roles', function (array $roles): array {
    return array_values(array_unique(array_merge($roles, auth_user_role_slugs(auth_current_user_id()))));
});

add_filter('user_permissions', function (array $permissions): array {
    return array_values(array_unique(array_merge($permissions, auth_user_permission_slugs(auth_current_user_id()))));
});

add_filter('admin_before_links', function (array $links): array {
    $items = [];
    $canUsers = user_can('auth.view_users') || user_can('auth.create_users') || user_can('auth.edit_users');
    $canRoles = user_can('auth.view_roles') || user_can('auth.edit_roles');
    $canFields = user_can('auth.view_user_fields') || user_can('auth.edit_user_fields');
    $canSettings = user_can('auth.view_settings') || user_can('auth.edit_settings');
    if (!$canUsers && !$canRoles && !$canFields && !$canSettings && auth_current_user_id() !== 1) {
        return $links;
    }
    $items[] = (object)[
        'title' => 'Users & Access', 'slug' => 'thunder-authentication', 'link' => ROOT . '/admin/auth/users',
        'icon' => 'fa-solid fa-users-gear', 'parent' => '', 'order' => 30
    ];
    if ($canUsers || auth_current_user_id() === 1) {
        $items[] = (object)['title' => 'Users', 'slug' => 'auth-users', 'link' => ROOT . '/admin/auth/users', 'icon' => 'fa-solid fa-users', 'parent' => 'thunder-authentication', 'order' => 10];
    }
    if ($canRoles || auth_current_user_id() === 1) {
        $items[] = (object)['title' => 'Roles & Permissions', 'slug' => 'auth-roles', 'link' => ROOT . '/admin/auth/roles', 'icon' => 'fa-solid fa-user-shield', 'parent' => 'thunder-authentication', 'order' => 20];
    }
    if ($canFields || auth_current_user_id() === 1) {
        $items[] = (object)['title' => 'Extra Fields', 'slug' => 'auth-fields', 'link' => ROOT . '/admin/auth/fields', 'icon' => 'fa-solid fa-list-check', 'parent' => 'thunder-authentication', 'order' => 30];
    }
    if ($canSettings || auth_current_user_id() === 1) {
        $items[] = (object)['title' => 'Authentication Settings', 'slug' => 'auth-settings', 'link' => ROOT . '/admin/auth/settings', 'icon' => 'fa-solid fa-shield-halved', 'parent' => 'thunder-authentication', 'order' => 40];
    }
    $links[plugin_id()] = $items;
    return $links;
});

add_filter('admin_user_info', function (array $user): array {
    if (auth_session()->is_logged_in()) {
        $user['links'][] = ['title' => 'My Account', 'link' => ROOT . '/account', 'icon' => 'fa-solid fa-id-card'];
        $current = auth_current_user();
        if ($current) {
            $user['links'][] = ['title' => 'Public Profile', 'link' => auth_profile_url($current), 'icon' => 'fa-solid fa-user'];
        }
    }
    return $user;
});

add_filter('admin_notifications', function (array $notifications): array {
    if (auth_current_user_id() !== 1 && !user_can('auth.edit_users')) {
        return $notifications;
    }
    $row = auth_db()->get_row('SELECT COUNT(*) AS total FROM ' . auth_table('profiles') . ' WHERE status = :status', ['status' => 'pending']);
    $count = $row ? (int)$row->total : 0;
    if ($count > 0) {
        $notifications[] = (object)[
            'title' => 'Registration approval', 'message' => $count . ' account' . ($count === 1 ? '' : 's') . ' waiting for approval.',
            'link' => ROOT . '/admin/auth/users?status=pending', 'icon' => 'fa-solid fa-user-clock', 'type' => 'warning',
            'created_at' => date('Y-m-d H:i:s'), 'read' => false
        ];
    }
    return $notifications;
});

add_action('admin_dashboard_widgets', function (): void {
    if (auth_current_user_id() !== 1 && !user_can('auth.view_users')) {
        return;
    }
    $total = auth_db()->get_row('SELECT COUNT(*) AS total FROM ' . auth_table('users'));
    $pending = auth_db()->get_row('SELECT COUNT(*) AS total FROM ' . auth_table('profiles') . ' WHERE status = :status', ['status' => 'pending']);
    ?>
    <section class="ta-widget th-auth-dashboard-widget">
        <h2 class="ta-widget__title">Users &amp; Access</h2>
        <p class="ta-widget__text"><strong><?=esc((string)($total->total ?? 0))?></strong> users · <strong><?=esc((string)($pending->total ?? 0))?></strong> pending approval</p>
        <a href="<?=esc(ROOT . '/admin/auth/users')?>">Manage users</a>
    </section>
    <?php
});

add_filter('admin_page_title', function (string $title): string {
    $titles = [
        'auth.admin.users' => 'Users', 'auth.admin.users.create' => 'Create User', 'auth.admin.users.edit' => 'Edit User',
        'auth.admin.roles' => 'Roles & Permissions', 'auth.admin.roles.create' => 'Create Role', 'auth.admin.roles.edit' => 'Edit Role',
        'auth.admin.fields' => 'Extra Fields', 'auth.admin.fields.create' => 'Create Extra Field', 'auth.admin.fields.edit' => 'Edit Extra Field',
        'auth.admin.settings' => 'Authentication Settings'
    ];
    foreach ($titles as $route => $value) {
        if (is_route($route)) {
            return $value;
        }
    }
    return $title;
});

$controllers = [
    'auth.login' => 'controllers/frontend/login.php', 'auth.login.submit' => 'controllers/frontend/login.php',
    'auth.logout' => 'controllers/frontend/logout.php',
    'auth.signup' => 'controllers/frontend/signup.php', 'auth.signup.submit' => 'controllers/frontend/signup.php',
    'auth.account' => 'controllers/frontend/account.php', 'auth.account.profile' => 'controllers/frontend/account.php',
    'auth.account.password' => 'controllers/frontend/account.php', 'auth.profile' => 'controllers/frontend/profile.php',
    'auth.admin.users' => 'controllers/admin/users.php', 'auth.admin.users.export' => 'controllers/admin/users.php',
    'auth.admin.users.create' => 'controllers/admin/user.php', 'auth.admin.users.store' => 'controllers/admin/user.php',
    'auth.admin.users.edit' => 'controllers/admin/user.php', 'auth.admin.users.update' => 'controllers/admin/user.php',
    'auth.admin.users.toggle' => 'controllers/admin/user.php', 'auth.admin.users.delete' => 'controllers/admin/user.php',
    'auth.admin.roles' => 'controllers/admin/roles.php', 'auth.admin.roles.create' => 'controllers/admin/role.php',
    'auth.admin.roles.store' => 'controllers/admin/role.php', 'auth.admin.roles.edit' => 'controllers/admin/role.php',
    'auth.admin.roles.update' => 'controllers/admin/role.php', 'auth.admin.roles.duplicate' => 'controllers/admin/role.php',
    'auth.admin.roles.delete' => 'controllers/admin/role.php',
    'auth.admin.fields' => 'controllers/admin/fields.php', 'auth.admin.fields.create' => 'controllers/admin/field.php',
    'auth.admin.fields.store' => 'controllers/admin/field.php', 'auth.admin.fields.edit' => 'controllers/admin/field.php',
    'auth.admin.fields.update' => 'controllers/admin/field.php', 'auth.admin.fields.move' => 'controllers/admin/field.php',
    'auth.admin.fields.delete' => 'controllers/admin/field.php',
    'auth.admin.settings' => 'controllers/admin/settings.php', 'auth.admin.settings.update' => 'controllers/admin/settings.php',
];
foreach ($controllers as $route => $file) {
    add_action('controller', function () use ($file): void {
        require_once plugin_path($file);
    }, 10, $route);
}

add_action('view', function (): void {
    auth_render_frontend('login.php', 'Login');
}, 10, 'auth.login');
add_action('view', function (): void {
    auth_render_frontend('signup.php', 'Create account');
}, 10, 'auth.signup');
add_action('view', function (): void {
    auth_render_frontend('account.php', 'My account');
}, 10, 'auth.account');
add_action('view', function (): void {
    auth_render_frontend('profile.php', (string)(get_value('page_title') ?: 'Profile'));
}, 10, 'auth.profile');

$adminViews = [
    'auth.admin.users' => 'users.php', 'auth.admin.users.create' => 'user-form.php', 'auth.admin.users.edit' => 'user-form.php',
    'auth.admin.roles' => 'roles.php', 'auth.admin.roles.create' => 'role-form.php', 'auth.admin.roles.edit' => 'role-form.php',
    'auth.admin.fields' => 'fields.php', 'auth.admin.fields.create' => 'field-form.php', 'auth.admin.fields.edit' => 'field-form.php',
    'auth.admin.settings' => 'settings.php',
];
foreach ($adminViews as $route => $view) {
    add_action('admin_main_content', function () use ($view): void {
        auth_render_view('admin/' . $view);
    }, 10, $route);
}
