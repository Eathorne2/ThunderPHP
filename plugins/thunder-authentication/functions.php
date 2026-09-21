<?php

namespace ThunderAuthentication;

defined('ROOTPATH') or die('Direct script access denied');

function auth_db(): \Core\Database
{
    return new \Core\Database;
}

function auth_request(): \Core\Request
{
    return new \Core\Request;
}

function auth_session(): \Core\Session
{
    return new \Core\Session;
}

function auth_tables(): array
{
    return get_value('auth_tables') ?: [];
}

function auth_table(string $key): string
{
    $tables = auth_tables();
    return (string)($tables[$key] ?? $key);
}

function auth_setting_defaults(): array
{
    return [
        'registration_mode' => 'open',
        'default_role' => 'user',
        'auto_login_after_signup' => '1',
        'terms_required' => '0',
        'terms_url' => '',
        'allowed_email_domains' => '',
        'blocked_email_domains' => '',
        'login_identifier' => 'either',
        'remember_me_enabled' => '1',
        'remember_days' => '30',
        'login_redirect' => 'account',
        'logout_redirect' => 'login',
        'maximum_failed_attempts' => '5',
        'lockout_minutes' => '15',
        'single_session' => '0',
        'public_profiles' => '1',
        'allow_profile_images' => '1',
        'allow_username_change' => '0',
        'allow_email_change' => '1',
        'minimum_password_length' => '8',
        'password_require_uppercase' => '0',
        'password_require_lowercase' => '0',
        'password_require_number' => '0',
        'password_require_symbol' => '0',
        'active_look' => 'clean-card',
        'active_palette' => 'blue',
        'custom_primary' => '#2563eb',
        'custom_secondary' => '#64748b',
        'custom_background' => '#f8fafc',
        'custom_surface' => '#ffffff',
        'custom_text' => '#0f172a',
        'custom_muted_text' => '#64748b',
        'custom_border' => '#e2e8f0',
        'custom_success' => '#16a34a',
        'custom_warning' => '#d97706',
        'custom_danger' => '#dc2626',
        'form_width' => '460',
        'border_radius' => '18',
    ];
}

function auth_get_settings(bool $refresh = false): array
{
    if (!$refresh && isset($GLOBALS['thunder_auth_settings_cache']) && is_array($GLOBALS['thunder_auth_settings_cache'])) {
        return $GLOBALS['thunder_auth_settings_cache'];
    }

    $settings = auth_setting_defaults();
    $rows = auth_db()->query('SELECT setting_key, setting_value FROM ' . auth_table('settings'));

    if (is_array($rows)) {
        foreach ($rows as $row) {
            $settings[(string)$row->setting_key] = (string)($row->setting_value ?? '');
        }
    }

    $GLOBALS['thunder_auth_settings_cache'] = $settings;
    return $settings;
}

function auth_setting(string $key, mixed $default = null): mixed
{
    $settings = auth_get_settings();
    return $settings[$key] ?? $default;
}

function auth_save_setting(string $key, mixed $value): bool
{
    $db = auth_db();
    $db->query(
        'INSERT INTO ' . auth_table('settings') . ' (setting_key, setting_value, updated_at)
         VALUES (:setting_key, :setting_value, :updated_at)
         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = VALUES(updated_at)',
        [
            'setting_key' => $key,
            'setting_value' => is_array($value) ? json_encode($value) : (string)$value,
            'updated_at' => date('Y-m-d H:i:s'),
        ]
    );

    unset($GLOBALS['thunder_auth_settings_cache']);
    return !$db->has_error;
}

function auth_slug(string $value): string
{
    $value = strtolower(trim($value));
    $value = preg_replace('/[^a-z0-9_-]+/', '-', $value) ?: '';
    return trim($value, '-');
}

function auth_ip(): string
{
    return substr((string)($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45);
}

function auth_user_agent(): string
{
    return substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 500);
}

function auth_user_columns(bool $refresh = false): array
{
    if (!$refresh && isset($GLOBALS['thunder_auth_user_columns'])) {
        return $GLOBALS['thunder_auth_user_columns'];
    }

    $columns = [];
    $rows = auth_db()->query('SHOW COLUMNS FROM ' . auth_table('users'));
    if (is_array($rows)) {
        foreach ($rows as $row) {
            $columns[] = (string)($row->Field ?? '');
        }
    }

    $GLOBALS['thunder_auth_user_columns'] = array_values(array_filter($columns));
    return $GLOBALS['thunder_auth_user_columns'];
}

function auth_user_has_column(string $column): bool
{
    return in_array($column, auth_user_columns(), true);
}

function auth_user_count(): int
{
    $row = auth_db()->get_row('SELECT COUNT(*) AS total FROM ' . auth_table('users'));
    return is_object($row) ? max(0, (int)($row->total ?? 0)) : 0;
}

function auth_hydrate_user(object|false|null $user): object|false
{
    if (!is_object($user) || !isset($user->id) || (int)$user->id < 1) {
        return false;
    }

    $user->display_name = trim((string)($user->auth_display_name ?? $user->display_name ?? $user->username ?? ''));
    if ($user->display_name === '') {
        $user->display_name = (string)($user->username ?? 'User');
    }
    $user->bio = (string)($user->auth_bio ?? $user->bio ?? '');
    $user->image = (string)($user->auth_image ?? $user->image ?? '');
    $profileStatus = (string)($user->auth_status ?? '');
    $disabled = isset($user->disabled) && (int)$user->disabled === 1;
    $user->status = $disabled ? 'disabled' : ($profileStatus !== '' ? $profileStatus : 'active');
    $user->last_login = $user->auth_last_login ?? $user->last_login ?? null;
    $user->date_created = $user->auth_created_at ?? $user->date_created ?? $user->created_at ?? null;

    $displayPayload = do_filter('auth_user_display_name', ['name' => $user->display_name, 'user' => $user]);
    if (is_array($displayPayload) && array_key_exists('name', $displayPayload)) {
        $user->display_name = (string)$displayPayload['name'];
    }
    $avatarPayload = do_filter('auth_user_avatar', ['image' => $user->image, 'user' => $user]);
    if (is_array($avatarPayload) && array_key_exists('image', $avatarPayload)) {
        $user->image = (string)$avatarPayload['image'];
    }
    $dataPayload = do_filter('auth_user_data', ['data' => $user, 'user' => $user]);
    return is_array($dataPayload) && isset($dataPayload['data']) && is_object($dataPayload['data'])
        ? $dataPayload['data']
        : $user;
}

function auth_user_select(): string
{
    return 'SELECT u.*, p.display_name AS auth_display_name, p.bio AS auth_bio,
            p.image AS auth_image, p.status AS auth_status, p.last_login AS auth_last_login,
            p.created_at AS auth_created_at, p.updated_at AS auth_updated_at
            FROM ' . auth_table('users') . ' u
            LEFT JOIN ' . auth_table('profiles') . ' p ON p.user_id = u.id ';
}

function auth_get_user_by_id(int $id): object|false
{
    return auth_hydrate_user(auth_db()->get_row(auth_user_select() . ' WHERE u.id = :id LIMIT 1', ['id' => $id]));
}

function auth_user_id_by_column(string $column, string $value): int
{
    if (!in_array($column, ['username', 'email'], true) || !auth_user_has_column($column)) {
        return 0;
    }

    $value = trim($value);
    if ($value === '') {
        return 0;
    }

    $row = auth_db()->get_row(
        'SELECT id FROM ' . auth_table('users') . ' WHERE LOWER(' . $column . ') = LOWER(:value) LIMIT 1',
        ['value' => $value]
    );

    return is_object($row) && isset($row->id) && (int)$row->id > 0
        ? (int)$row->id
        : 0;
}

function auth_get_user_by_username(string $username): object|false
{
    $userId = auth_user_id_by_column('username', $username);
    return $userId > 0 ? auth_get_user_by_id($userId) : false;
}

function auth_get_user_by_email(string $email): object|false
{
    $userId = auth_user_id_by_column('email', $email);
    return $userId > 0 ? auth_get_user_by_id($userId) : false;
}

function auth_get_user_by_identifier(string $identifier): object|false
{
    $mode = (string)auth_setting('login_identifier', 'either');
    if ($mode === 'username') {
        return auth_get_user_by_username($identifier);
    }
    if ($mode === 'email') {
        return auth_get_user_by_email($identifier);
    }

    if (auth_user_has_column('email')) {
        return auth_hydrate_user(auth_db()->get_row(
            auth_user_select() . ' WHERE LOWER(u.username) = LOWER(:identifier) OR LOWER(u.email) = LOWER(:identifier) LIMIT 1',
            ['identifier' => $identifier]
        ));
    }

    return auth_get_user_by_username($identifier);
}

function auth_ensure_profile(int $userId, array $data = []): bool
{
    if ($userId < 1) {
        return false;
    }

    $db = auth_db();
    $existing = $db->get_row('SELECT id FROM ' . auth_table('profiles') . ' WHERE user_id = :user_id LIMIT 1', ['user_id' => $userId]);
    $now = date('Y-m-d H:i:s');

    if ($existing) {
        if ($data === []) {
            return true;
        }
        $allowed = ['display_name', 'bio', 'image', 'status', 'last_login'];
        $sets = [];
        $params = ['user_id' => $userId, 'updated_at' => $now];
        foreach ($allowed as $key) {
            if (array_key_exists($key, $data)) {
                $sets[] = $key . ' = :' . $key;
                $params[$key] = $data[$key];
            }
        }
        if ($sets === []) {
            return true;
        }
        $sets[] = 'updated_at = :updated_at';
        $db->query('UPDATE ' . auth_table('profiles') . ' SET ' . implode(', ', $sets) . ' WHERE user_id = :user_id', $params);
        return !$db->has_error;
    }

    $db->query(
        'INSERT INTO ' . auth_table('profiles') . '
        (user_id, display_name, bio, image, status, last_login, created_at, updated_at)
        VALUES (:user_id, :display_name, :bio, :image, :status, :last_login, :created_at, :updated_at)',
        [
            'user_id' => $userId,
            'display_name' => (string)($data['display_name'] ?? ''),
            'bio' => (string)($data['bio'] ?? ''),
            'image' => (string)($data['image'] ?? ''),
            'status' => (string)($data['status'] ?? 'active'),
            'last_login' => $data['last_login'] ?? null,
            'created_at' => $now,
            'updated_at' => $now,
        ]
    );
    return !$db->has_error;
}

function auth_create_user(array $data): int
{
    $isFirstUser = auth_user_count() === 0;

    if ($isFirstUser) {
        $data['status'] = 'active';
        $data['primary_role'] = 'admin';
        $data['roles'] = ['admin'];
    }

    do_action('auth_before_user_create', ['data' => $data, 'is_first_user' => $isFirstUser]);
    $payload = do_filter('auth_before_user_create', ['data' => $data, 'is_first_user' => $isFirstUser]);
    if (is_array($payload) && isset($payload['data']) && is_array($payload['data'])) {
        $data = $payload['data'];
    }

    // The first account bootstraps an empty application and must remain an active administrator.
    if ($isFirstUser) {
        $data['status'] = 'active';
        $data['primary_role'] = 'admin';
        $data['roles'] = ['admin'];
    }

    $columns = auth_user_columns();
    foreach (['username', 'password'] as $required) {
        if (!in_array($required, $columns, true)) {
            throw new \RuntimeException('The ' . auth_table('users') . ' table must contain a ' . $required . ' column.');
        }
    }

    $insert = [
        'username' => trim((string)($data['username'] ?? '')),
        'password' => password_hash((string)($data['password'] ?? ''), PASSWORD_DEFAULT),
    ];
    if (in_array('email', $columns, true)) {
        $insert['email'] = trim((string)($data['email'] ?? ''));
    }
    if (in_array('role', $columns, true)) {
        $insert['role'] = (string)($data['primary_role'] ?? auth_setting('default_role', 'user'));
    }
    if (in_array('disabled', $columns, true)) {
        $insert['disabled'] = (($data['status'] ?? 'active') === 'disabled') ? 1 : 0;
    }
    foreach (['date_created', 'created_at'] as $dateColumn) {
        if (in_array($dateColumn, $columns, true)) {
            $insert[$dateColumn] = date('Y-m-d H:i:s');
        }
    }

    $names = array_keys($insert);
    $placeholders = array_map(static fn(string $key): string => ':' . $key, $names);
    $db = auth_db();
    $db->query('INSERT INTO ' . auth_table('users') . ' (' . implode(', ', $names) . ') VALUES (' . implode(', ', $placeholders) . ')', $insert);
    if ($db->has_error || $db->insert_id < 1) {
        throw new \RuntimeException($db->error ?: 'The user could not be created.');
    }

    $userId = (int)$db->insert_id;
    auth_ensure_profile($userId, [
        'display_name' => (string)($data['display_name'] ?? $data['username'] ?? ''),
        'bio' => (string)($data['bio'] ?? ''),
        'image' => (string)($data['image'] ?? ''),
        'status' => (string)($data['status'] ?? 'active'),
    ]);

    $roles = $data['roles'] ?? [(string)($data['primary_role'] ?? auth_setting('default_role', 'user'))];
    auth_set_user_roles($userId, is_array($roles) ? $roles : [$roles]);
    $user = auth_get_user_by_id($userId);
    do_action('auth_user_created', ['user' => $user]);
    return $userId;
}

function auth_update_user_core(int $userId, array $data): bool
{
    $user = auth_get_user_by_id($userId);
    if (!$user) {
        return false;
    }

    do_action('auth_before_user_update', ['user' => $user, 'data' => $data]);
    $payload = do_filter('auth_before_user_update', ['user' => $user, 'data' => $data]);
    if (is_array($payload) && isset($payload['data']) && is_array($payload['data'])) {
        $data = $payload['data'];
    }

    $allowed = ['username', 'email', 'password', 'role', 'disabled'];
    $sets = [];
    $params = ['id' => $userId];
    foreach ($allowed as $column) {
        if (!array_key_exists($column, $data) || !auth_user_has_column($column)) {
            continue;
        }
        $sets[] = $column . ' = :' . $column;
        $params[$column] = $column === 'password' ? password_hash((string)$data[$column], PASSWORD_DEFAULT) : $data[$column];
    }
    if (auth_user_has_column('updated_at')) {
        $sets[] = 'updated_at = :updated_at';
        $params['updated_at'] = date('Y-m-d H:i:s');
    }

    if ($sets !== []) {
        $db = auth_db();
        $db->query('UPDATE ' . auth_table('users') . ' SET ' . implode(', ', $sets) . ' WHERE id = :id', $params);
        if ($db->has_error) {
            return false;
        }
    }

    do_action('auth_user_updated', ['user' => auth_get_user_by_id($userId)]);
    return true;
}

function auth_delete_user(int $userId): bool
{
    $user = auth_get_user_by_id($userId);
    if (!$user || $userId === 1) {
        return false;
    }

    $payload = do_filter('auth_can_delete_user', ['allowed' => true, 'current_user' => auth_current_user(), 'target_user' => $user]);
    if (is_array($payload) && empty($payload['allowed'])) {
        return false;
    }

    do_action('auth_before_user_delete', ['user' => $user]);
    $db = auth_db();
    $pdo = $db->getConnection();
    try {
        if ($pdo && method_exists($pdo, 'beginTransaction')) {
            $pdo->beginTransaction();
        }
        foreach ([
            ['field_values', 'user_id'], ['user_roles', 'user_id'], ['remember_tokens', 'user_id'],
            ['user_sessions', 'user_id'], ['profiles', 'user_id'], ['audit_log', 'user_id']
        ] as [$table, $column]) {
            $db->query('DELETE FROM ' . auth_table($table) . ' WHERE ' . $column . ' = :user_id', ['user_id' => $userId]);
        }
        $db->query('DELETE FROM ' . auth_table('users') . ' WHERE id = :id', ['id' => $userId]);
        if ($db->has_error) {
            throw new \RuntimeException($db->error ?: 'Delete failed.');
        }
        if ($pdo && method_exists($pdo, 'inTransaction') && $pdo->inTransaction()) {
            $pdo->commit();
        }
    } catch (\Throwable $e) {
        if ($pdo && method_exists($pdo, 'inTransaction') && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        return false;
    }

    do_action('auth_user_deleted', ['user_id' => $userId]);
    return true;
}

function auth_current_user(): object|false
{
    $session = auth_session();
    if (!$session->is_logged_in()) {
        return false;
    }
    $id = (int)$session->user('id');
    return $id > 0 ? auth_get_user_by_id($id) : false;
}

function auth_current_user_id(): int
{
    $session = auth_session();
    return $session->is_logged_in() ? (int)$session->user('id') : 0;
}

function auth_profile_url(object $user): string
{
    $url = ROOT . '/profile/' . rawurlencode((string)($user->username ?? ''));
    $payload = do_filter('auth_user_profile_url', ['url' => $url, 'user' => $user]);
    return is_array($payload) && isset($payload['url']) ? (string)$payload['url'] : $url;
}

function auth_all_roles(): array
{
    $rows = auth_db()->query('SELECT r.*, COUNT(urm.user_id) AS user_count
        FROM ' . auth_table('roles') . ' r
        LEFT JOIN ' . auth_table('user_roles') . ' urm ON urm.role_id = r.id
        GROUP BY r.id ORDER BY r.name ASC');
    $roles = is_array($rows) ? $rows : [];
    $payload = do_filter('auth_available_roles', ['roles' => $roles]);
    return is_array($payload) && isset($payload['roles']) && is_array($payload['roles']) ? $payload['roles'] : $roles;
}

function auth_role_by_id(int $id): object|false
{
    return auth_db()->get_row('SELECT * FROM ' . auth_table('roles') . ' WHERE id = :id LIMIT 1', ['id' => $id]);
}

function auth_role_by_slug(string $slug): object|false
{
    return auth_db()->get_row('SELECT * FROM ' . auth_table('roles') . ' WHERE slug = :slug LIMIT 1', ['slug' => $slug]);
}

function auth_user_role_rows(int $userId): array
{
    $rows = auth_db()->query('SELECT r.* FROM ' . auth_table('roles') . ' r
        INNER JOIN ' . auth_table('user_roles') . ' urm ON urm.role_id = r.id
        WHERE urm.user_id = :user_id ORDER BY r.name ASC', ['user_id' => $userId]);
    $roles = is_array($rows) ? $rows : [];
    if ($userId > 0 && auth_user_has_column('role')) {
        $legacy = auth_db()->get_row('SELECT role FROM ' . auth_table('users') . ' WHERE id = :id LIMIT 1', ['id' => $userId]);
        $legacySlug = strtolower(trim((string)($legacy->role ?? '')));
        if ($legacySlug !== '' && !in_array($legacySlug, array_map(static fn(object $role): string => (string)$role->slug, $roles), true)) {
            $legacyRole = auth_role_by_slug($legacySlug);
            if ($legacyRole) {
                $roles[] = $legacyRole;
            }
        }
    }
    return $roles;
}

function auth_user_role_slugs(int $userId): array
{
    $roles = array_map(static fn(object $role): string => (string)$role->slug, auth_user_role_rows($userId));
    if ($userId > 0 && auth_user_has_column('role')) {
        $legacy = auth_db()->get_row('SELECT role FROM ' . auth_table('users') . ' WHERE id = :id LIMIT 1', ['id' => $userId]);
        $legacyRole = strtolower(trim((string)($legacy->role ?? '')));
        if ($legacyRole !== '') {
            $roles[] = $legacyRole;
        }
    }
    if ($userId === 1 && !in_array('admin', $roles, true)) {
        $roles[] = 'admin';
    }
    $roles = array_values(array_unique(array_filter($roles)));
    $payload = do_filter('auth_user_roles', ['roles' => $roles, 'user' => auth_get_user_by_id($userId)]);
    return is_array($payload) && isset($payload['roles']) && is_array($payload['roles'])
        ? array_values(array_unique(array_filter(array_map('strval', $payload['roles']))))
        : $roles;
}

function auth_set_user_roles(int $userId, array $roleIdentifiers): bool
{
    $ids = [];
    foreach ($roleIdentifiers as $identifier) {
        $role = is_numeric($identifier) ? auth_role_by_id((int)$identifier) : auth_role_by_slug((string)$identifier);
        if ($role) {
            $isAdminRole = (string)$role->slug === 'admin';
            $actorCanAssignAdmin = auth_current_user_id() === 1 || contains_role('admin');
            $allowed = $userId === 1 && $isAdminRole;
            if (!$allowed && $isAdminRole && !$actorCanAssignAdmin) {
                continue;
            }
            if (!$allowed) {
                $payload = do_filter('auth_can_assign_role', [
                    'allowed' => true, 'role' => $role, 'user' => auth_get_user_by_id($userId), 'actor' => auth_current_user()
                ]);
                $allowed = !is_array($payload) || !array_key_exists('allowed', $payload) || (bool)$payload['allowed'];
            }
            if ($allowed) {
                $ids[] = (int)$role->id;
            }
        }
    }
    $protectCurrentAdmin = $userId === auth_current_user_id() && in_array('admin', auth_user_role_slugs($userId), true);
    if ($userId === 1 || $protectCurrentAdmin) {
        $admin = auth_role_by_slug('admin');
        if ($admin) {
            $ids[] = (int)$admin->id;
        }
    }
    $ids = array_values(array_unique($ids));
    if ($ids === []) {
        $default = auth_role_by_slug((string)auth_setting('default_role', 'user'));
        if ($default) {
            $ids[] = (int)$default->id;
        }
    }

    $db = auth_db();
    $existing = auth_user_role_rows($userId);
    $old = array_map(static fn(object $r): int => (int)$r->id, $existing);
    $db->query('DELETE FROM ' . auth_table('user_roles') . ' WHERE user_id = :user_id', ['user_id' => $userId]);
    foreach ($ids as $roleId) {
        $db->query('INSERT IGNORE INTO ' . auth_table('user_roles') . ' (user_id, role_id, created_at)
            VALUES (:user_id, :role_id, :created_at)', [
                'user_id' => $userId, 'role_id' => $roleId, 'created_at' => date('Y-m-d H:i:s')
            ]);
    }

    $newRows = auth_user_role_rows($userId);
    foreach ($newRows as $role) {
        if (!in_array((int)$role->id, $old, true)) {
            do_action('auth_role_assigned', ['user' => auth_get_user_by_id($userId), 'role' => $role]);
        }
    }
    foreach ($existing as $role) {
        if (!in_array((int)$role->id, $ids, true)) {
            do_action('auth_role_removed', ['user' => auth_get_user_by_id($userId), 'role' => $role]);
        }
    }

    auth_sync_primary_role($userId);
    return !$db->has_error;
}

function auth_sync_primary_role(int $userId): void
{
    if (!auth_user_has_column('role')) {
        return;
    }
    $slugs = auth_user_role_slugs($userId);
    $primary = in_array('admin', $slugs, true) ? 'admin' : (string)($slugs[0] ?? auth_setting('default_role', 'user'));
    auth_db()->query('UPDATE ' . auth_table('users') . ' SET role = :role WHERE id = :id', ['role' => $primary, 'id' => $userId]);
}

function auth_user_permission_slugs(int $userId): array
{
    if ($userId === 1 || in_array('admin', auth_user_role_slugs($userId), true)) {
        $permissions = ['all'];
    } else {
        $rows = auth_db()->query('SELECT DISTINCT rp.permission_slug
            FROM ' . auth_table('role_permissions') . ' rp
            INNER JOIN ' . auth_table('user_roles') . ' urm ON urm.role_id = rp.role_id
            WHERE urm.user_id = :user_id', ['user_id' => $userId]);
        $permissions = is_array($rows)
            ? array_values(array_unique(array_map(static fn(object $row): string => (string)$row->permission_slug, $rows)))
            : [];
    }
    $payload = do_filter('auth_user_permissions', ['permissions' => $permissions, 'user' => auth_get_user_by_id($userId)]);
    return is_array($payload) && isset($payload['permissions']) && is_array($payload['permissions'])
        ? array_values(array_unique(array_filter(array_map('strval', $payload['permissions']))))
        : $permissions;
}

function auth_role_permission_slugs(int $roleId): array
{
    $rows = auth_db()->query('SELECT permission_slug FROM ' . auth_table('role_permissions') . ' WHERE role_id = :role_id', ['role_id' => $roleId]);
    return is_array($rows) ? array_map(static fn(object $row): string => (string)$row->permission_slug, $rows) : [];
}

function auth_set_role_permissions(int $roleId, array $permissions): bool
{
    $permissions = array_values(array_unique(array_filter(array_map('strval', $permissions))));
    $db = auth_db();
    $db->query('DELETE FROM ' . auth_table('role_permissions') . ' WHERE role_id = :role_id', ['role_id' => $roleId]);
    foreach ($permissions as $permission) {
        $db->query('INSERT IGNORE INTO ' . auth_table('role_permissions') . ' (role_id, permission_slug, created_at)
            VALUES (:role_id, :permission_slug, :created_at)', [
                'role_id' => $roleId, 'permission_slug' => $permission, 'created_at' => date('Y-m-d H:i:s')
            ]);
    }
    return !$db->has_error;
}

function auth_available_permissions(): array
{
    if (isset($GLOBALS['thunder_auth_available_permissions'])) {
        return $GLOBALS['thunder_auth_available_permissions'];
    }

    $permissions = [];
    $pluginRoot = dirname(rtrim(plugin_path(), '/\\'));
    foreach (glob($pluginRoot . '/*/config.json') ?: [] as $file) {
        $config = json_decode((string)file_get_contents($file), true);
        if (!is_array($config) || empty($config['permissions']) || !is_array($config['permissions'])) {
            continue;
        }
        $pluginId = (string)($config['id'] ?? basename(dirname($file)));
        $pluginName = (string)($config['name'] ?? $pluginId);
        foreach ($config['permissions'] as $item) {
            if (!is_array($item)) {
                continue;
            }
            $slug = trim((string)($item['slug'] ?? $item['key'] ?? ''));
            if ($slug === '') {
                continue;
            }
            $permissions[$slug] = (object)[
                'slug' => $slug,
                'name' => (string)($item['name'] ?? $item['label'] ?? $slug),
                'group' => (string)($item['group'] ?? $pluginName),
                'description' => (string)($item['description'] ?? ''),
                'plugin_id' => $pluginId,
                'plugin_name' => $pluginName,
            ];
        }
    }

    $filtered = do_filter('permissions', []);
    if (is_array($filtered)) {
        foreach ($filtered as $key => $item) {
            if (is_object($item)) {
                $item = (array)$item;
            }
            if (!is_array($item)) {
                continue;
            }
            $slug = trim((string)($item['slug'] ?? $item['key'] ?? (is_string($key) ? $key : '')));
            if ($slug === '') {
                continue;
            }
            $permissions[$slug] = (object)[
                'slug' => $slug,
                'name' => (string)($item['name'] ?? $item['label'] ?? $slug),
                'group' => (string)($item['group'] ?? 'Other'),
                'description' => (string)($item['description'] ?? ''),
                'plugin_id' => (string)($item['plugin_id'] ?? 'runtime'),
                'plugin_name' => (string)($item['plugin_name'] ?? 'Runtime'),
            ];
        }
    }

    ksort($permissions);
    $payload = do_filter('auth_available_permissions', ['permissions' => $permissions]);
    if (is_array($payload) && isset($payload['permissions']) && is_array($payload['permissions'])) {
        $permissions = $payload['permissions'];
    }
    $GLOBALS['thunder_auth_available_permissions'] = $permissions;
    return $permissions;
}

function auth_permission_groups(): array
{
    $groups = [];
    foreach (auth_available_permissions() as $permission) {
        $group = $permission->group ?: $permission->plugin_name;
        $groups[$group][] = $permission;
    }
    ksort($groups);
    return $groups;
}

function auth_bootstrap_primary_user(): void
{
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;
    $primary = auth_get_user_by_id(1);
    if (!$primary) {
        return;
    }
    if ((string)$primary->status !== 'active') {
        auth_ensure_profile(1, ['status' => 'active']);
        if (auth_user_has_column('disabled')) {
            auth_update_user_core(1, ['disabled' => 0]);
        }
    }
    $admin = auth_role_by_slug('admin');
    if ($admin) {
        $mapping = auth_db()->get_row('SELECT id FROM ' . auth_table('user_roles') . '
            WHERE user_id = :user_id AND role_id = :role_id LIMIT 1', [
                'user_id' => 1, 'role_id' => (int)$admin->id
            ]);
        if (!$mapping) {
            auth_db()->query('INSERT IGNORE INTO ' . auth_table('user_roles') . ' (user_id, role_id, created_at)
                VALUES (:user_id, :role_id, :created_at)', [
                    'user_id' => 1, 'role_id' => (int)$admin->id, 'created_at' => date('Y-m-d H:i:s')
                ]);
            auth_sync_primary_role(1);
        }
    }
}

function auth_password_errors(string $password): array
{
    $errors = [];
    $minimum = max(6, (int)auth_setting('minimum_password_length', 8));
    if (strlen($password) < $minimum) {
        $errors[] = 'Password must be at least ' . $minimum . ' characters.';
    }
    if (auth_setting('password_require_uppercase', '0') === '1' && !preg_match('/[A-Z]/', $password)) {
        $errors[] = 'Password must include an uppercase letter.';
    }
    if (auth_setting('password_require_lowercase', '0') === '1' && !preg_match('/[a-z]/', $password)) {
        $errors[] = 'Password must include a lowercase letter.';
    }
    if (auth_setting('password_require_number', '0') === '1' && !preg_match('/[0-9]/', $password)) {
        $errors[] = 'Password must include a number.';
    }
    if (auth_setting('password_require_symbol', '0') === '1' && !preg_match('/[^A-Za-z0-9]/', $password)) {
        $errors[] = 'Password must include a symbol.';
    }
    return $errors;
}

function auth_email_domain_allowed(string $email): bool
{
    $domain = strtolower((string)substr(strrchr($email, '@') ?: '', 1));
    if ($domain === '') {
        return false;
    }
    $parse = static fn(string $value): array => array_values(array_filter(array_map(
        static fn(string $item): string => strtolower(trim($item)),
        preg_split('/[\s,;]+/', $value) ?: []
    )));
    $allowed = $parse((string)auth_setting('allowed_email_domains', ''));
    $blocked = $parse((string)auth_setting('blocked_email_domains', ''));
    if (in_array($domain, $blocked, true)) {
        return false;
    }
    return $allowed === [] || in_array($domain, $allowed, true);
}

function auth_login_is_locked(string $identifier): bool
{
    $minutes = max(1, (int)auth_setting('lockout_minutes', 15));
    $maximum = max(1, (int)auth_setting('maximum_failed_attempts', 5));
    $since = date('Y-m-d H:i:s', time() - ($minutes * 60));
    $row = auth_db()->get_row('SELECT COUNT(*) AS total FROM ' . auth_table('login_attempts') . '
        WHERE success = 0 AND attempted_at >= :since AND (identifier = :identifier OR ip_address = :ip)', [
            'since' => $since, 'identifier' => strtolower($identifier), 'ip' => auth_ip()
        ]);
    return $row && (int)$row->total >= $maximum;
}

function auth_record_login_attempt(string $identifier, bool $success, string $reason = ''): void
{
    auth_db()->query('INSERT INTO ' . auth_table('login_attempts') . '
        (identifier, ip_address, user_agent, success, reason, attempted_at)
        VALUES (:identifier, :ip_address, :user_agent, :success, :reason, :attempted_at)', [
            'identifier' => strtolower($identifier), 'ip_address' => auth_ip(), 'user_agent' => auth_user_agent(),
            'success' => $success ? 1 : 0, 'reason' => $reason, 'attempted_at' => date('Y-m-d H:i:s')
        ]);
}

function auth_clear_login_attempts(string $identifier): void
{
    auth_db()->query('DELETE FROM ' . auth_table('login_attempts') . ' WHERE identifier = :identifier OR ip_address = :ip', [
        'identifier' => strtolower($identifier), 'ip' => auth_ip()
    ]);
}

function auth_safe_redirect(string $target, string $fallback = ''): string
{
    $target = trim($target);
    if ($target === '' || str_contains($target, "\r") || str_contains($target, "\n")) {
        return trim($fallback, '/');
    }
    if (preg_match('#^https?://#i', $target)) {
        $parts = parse_url($target);
        $targetHost = $parts['host'] ?? '';
        $currentAuthority = (string)($_SERVER['HTTP_HOST'] ?? '');
        $currentParts = parse_url('http://' . $currentAuthority) ?: [];
        $currentHost = (string)($currentParts['host'] ?? '');
        $targetPort = $parts['port'] ?? null;
        $currentPort = $currentParts['port'] ?? null;
        if (
            $targetHost === '' || $currentHost === '' ||
            strcasecmp((string)$targetHost, $currentHost) !== 0 ||
            ($targetPort !== null && $currentPort !== null && (int)$targetPort !== (int)$currentPort)
        ) {
            return trim($fallback, '/');
        }
        $target = (string)($parts['path'] ?? '');
        if (!empty($parts['query'])) {
            $target .= '?' . $parts['query'];
        }
    }
    if (str_starts_with($target, '//')) {
        return trim($fallback, '/');
    }
    return ltrim($target, '/');
}

function auth_store_old_input(array $input): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        @session_start();
    }

    $safe = [];
    foreach ($input as $key => $value) {
        $name = (string)$key;
        if (preg_match('/password|csrf|token/i', $name)) {
            continue;
        }
        $safe[$name] = $value;
    }

    $_SESSION['thunder_auth_old_input'] = $safe;
}

function auth_restore_old_input(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        @session_start();
    }

    if (
        strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET')) !== 'GET' ||
        empty($_SESSION['thunder_auth_old_input']) ||
        !is_array($_SESSION['thunder_auth_old_input'])
    ) {
        return;
    }

    $_POST = array_merge($_POST, $_SESSION['thunder_auth_old_input']);
    unset($_SESSION['thunder_auth_old_input']);
}

function auth_flash(string $type, string $text): void
{
    if (
        $type === 'fail' &&
        strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET')) === 'POST' &&
        !empty($_POST)
    ) {
        auth_store_old_input($_POST);
    }

    message($type, $text);
}

function auth_can_access_admin(): bool
{
    if (!auth_session()->is_logged_in()) {
        return false;
    }

    return auth_current_user_id() === 1 ||
        contains_role('admin') ||
        user_can('view-admin-area');
}

function auth_audit(string $event, string $description = '', int $userId = 0, array $context = []): void
{
    auth_db()->query('INSERT INTO ' . auth_table('audit_log') . '
        (user_id, event_key, description, context_json, ip_address, created_at)
        VALUES (:user_id, :event_key, :description, :context_json, :ip_address, :created_at)', [
            'user_id' => $userId ?: auth_current_user_id(), 'event_key' => $event,
            'description' => $description, 'context_json' => json_encode($context),
            'ip_address' => auth_ip(), 'created_at' => date('Y-m-d H:i:s')
        ]);
}

function auth_remember_cookie_name(): string
{
    return 'thunder_auth_remember';
}

function auth_set_remember_cookie(int $userId): void
{
    if (auth_setting('remember_me_enabled', '1') !== '1') {
        return;
    }
    $selector = bin2hex(random_bytes(12));
    $validator = bin2hex(random_bytes(32));
    $days = max(1, min(365, (int)auth_setting('remember_days', 30)));
    $expires = time() + ($days * 86400);
    auth_db()->query('INSERT INTO ' . auth_table('remember_tokens') . '
        (user_id, selector, validator_hash, expires_at, created_at)
        VALUES (:user_id, :selector, :validator_hash, :expires_at, :created_at)', [
            'user_id' => $userId, 'selector' => $selector, 'validator_hash' => hash('sha256', $validator),
            'expires_at' => date('Y-m-d H:i:s', $expires), 'created_at' => date('Y-m-d H:i:s')
        ]);
    setcookie(auth_remember_cookie_name(), $selector . ':' . $validator, [
        'expires' => $expires, 'path' => '/', 'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true, 'samesite' => 'Lax'
    ]);
}

function auth_clear_remember_cookie(): void
{
    $cookie = (string)($_COOKIE[auth_remember_cookie_name()] ?? '');
    if (str_contains($cookie, ':')) {
        [$selector] = explode(':', $cookie, 2);
        auth_db()->query('DELETE FROM ' . auth_table('remember_tokens') . ' WHERE selector = :selector', ['selector' => $selector]);
    }
    setcookie(auth_remember_cookie_name(), '', [
        'expires' => time() - 3600, 'path' => '/', 'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true, 'samesite' => 'Lax'
    ]);
    unset($_COOKIE[auth_remember_cookie_name()]);
}

function auth_auto_login(): void
{
    $session = auth_session();
    if ($session->is_logged_in() || auth_setting('remember_me_enabled', '1') !== '1') {
        return;
    }
    $cookie = (string)($_COOKIE[auth_remember_cookie_name()] ?? '');
    if (!str_contains($cookie, ':')) {
        return;
    }
    [$selector, $validator] = explode(':', $cookie, 2);
    $token = auth_db()->get_row('SELECT * FROM ' . auth_table('remember_tokens') . '
        WHERE selector = :selector AND expires_at > :now LIMIT 1', ['selector' => $selector, 'now' => date('Y-m-d H:i:s')]);
    if (!$token || !hash_equals((string)$token->validator_hash, hash('sha256', $validator))) {
        auth_clear_remember_cookie();
        return;
    }
    $user = auth_get_user_by_id((int)$token->user_id);
    if (!$user || $user->status !== 'active') {
        auth_clear_remember_cookie();
        return;
    }
    session_regenerate_id(true);
    $session->auth($user);
    auth_db()->query('DELETE FROM ' . auth_table('remember_tokens') . ' WHERE id = :id', ['id' => (int)$token->id]);
    auth_set_remember_cookie((int)$user->id);
    auth_register_session((int)$user->id, true);
    do_action('auth_login_success', ['user' => $user, 'remembered' => true]);
}

function auth_register_session(int $userId, bool $replaceOthers = true): void
{
    $sessionId = session_id();
    if ($sessionId === '') {
        return;
    }
    $db = auth_db();
    if ($replaceOthers && auth_setting('single_session', '0') === '1') {
        $db->query('DELETE FROM ' . auth_table('user_sessions') . ' WHERE user_id = :user_id', ['user_id' => $userId]);
    }
    $token = hash('sha256', $sessionId);
    $db->query('INSERT INTO ' . auth_table('user_sessions') . '
        (user_id, session_token, ip_address, user_agent, last_seen, created_at)
        VALUES (:user_id, :session_token, :ip_address, :user_agent, :last_seen, :created_at)
        ON DUPLICATE KEY UPDATE ip_address = VALUES(ip_address), user_agent = VALUES(user_agent), last_seen = VALUES(last_seen)', [
            'user_id' => $userId, 'session_token' => $token, 'ip_address' => auth_ip(),
            'user_agent' => auth_user_agent(), 'last_seen' => date('Y-m-d H:i:s'), 'created_at' => date('Y-m-d H:i:s')
        ]);
    $_SESSION['thunder_auth_session_token'] = $token;
}

function auth_delete_current_session_record(): void
{
    if (session_id() === '') {
        return;
    }
    auth_db()->query('DELETE FROM ' . auth_table('user_sessions') . ' WHERE session_token = :token', [
        'token' => hash('sha256', session_id())
    ]);
    unset($_SESSION['thunder_auth_session_token']);
}

function auth_validate_current_session(): void
{
    $session = auth_session();
    if (!$session->is_logged_in()) {
        return;
    }
    $userId = (int)$session->user('id');
    $user = auth_get_user_by_id($userId);
    if (!$user || $user->status !== 'active') {
        auth_clear_remember_cookie();
        $session->logout();
        return;
    }
    if (auth_setting('single_session', '0') !== '1' || session_id() === '') {
        return;
    }
    $token = hash('sha256', session_id());
    $row = auth_db()->get_row('SELECT id FROM ' . auth_table('user_sessions') . ' WHERE user_id = :user_id AND session_token = :token LIMIT 1', [
        'user_id' => $userId, 'token' => $token
    ]);
    if (!$row) {
        if ((string)($_SESSION['thunder_auth_session_token'] ?? '') === $token) {
            auth_clear_remember_cookie();
            $session->logout();
            return;
        }
        auth_register_session($userId, true);
        return;
    }
    $_SESSION['thunder_auth_session_token'] = $token;
    auth_db()->query('UPDATE ' . auth_table('user_sessions') . ' SET last_seen = :last_seen WHERE id = :id', [
        'last_seen' => date('Y-m-d H:i:s'), 'id' => (int)$row->id
    ]);
}

function auth_account_guard(): void
{
    if (!auth_session()->is_logged_in()) {
        auth_flash('fail', 'Please log in to continue.');
        redirect('login');
        exit;
    }
}

function auth_admin_guard(string $permission): void
{
    $session = auth_session();
    if (!$session->is_logged_in()) {
        auth_flash('fail', 'Please log in to continue.');
        redirect('login');
        exit;
    }
    if ((int)$session->user('id') !== 1 && !contains_role('admin') && !user_can($permission)) {
        auth_flash('fail', 'You do not have permission to access that page.');
        redirect('admin');
        exit;
    }
}

function auth_active_look(): string
{
    $look = auth_slug((string)auth_setting('active_look', 'clean-card'));
    return is_file(plugin_path('looks/' . $look . '/look.json')) ? $look : 'clean-card';
}

function auth_view_path(string $path): string
{
    $look = auth_active_look();
    $candidate = plugin_path('looks/' . $look . '/' . ltrim($path, '/'));
    if (is_file($candidate)) {
        return $candidate;
    }
    return plugin_path('looks/shared/' . ltrim($path, '/'));
}

function auth_asset_url(string $path): string
{
    return plugin_http_path('looks/' . auth_active_look() . '/assets/' . ltrim($path, '/'));
}

function auth_look_manifest(?string $look = null): array
{
    $look = $look ?: auth_active_look();
    $file = plugin_path('looks/' . $look . '/look.json');
    $data = is_file($file) ? json_decode((string)file_get_contents($file), true) : [];
    return is_array($data) ? $data : [];
}

function auth_available_looks(): array
{
    $looks = [];
    foreach (glob(plugin_path('looks/*/look.json')) ?: [] as $file) {
        $folder = basename(dirname($file));
        $manifest = json_decode((string)file_get_contents($file), true);
        if (is_array($manifest)) {
            $looks[$folder] = $manifest;
        }
    }
    return $looks;
}

function auth_palette(): array
{
    $manifest = auth_look_manifest();
    $paletteKey = (string)auth_setting('active_palette', $manifest['default_palette'] ?? 'blue');
    if ($paletteKey === 'custom') {
        return [
            'primary' => (string)auth_setting('custom_primary'), 'secondary' => (string)auth_setting('custom_secondary'),
            'background' => (string)auth_setting('custom_background'), 'surface' => (string)auth_setting('custom_surface'),
            'text' => (string)auth_setting('custom_text'), 'muted_text' => (string)auth_setting('custom_muted_text'),
            'border' => (string)auth_setting('custom_border'), 'success' => (string)auth_setting('custom_success'),
            'warning' => (string)auth_setting('custom_warning'), 'danger' => (string)auth_setting('custom_danger'),
        ];
    }
    $palettes = $manifest['palettes'] ?? [];
    return is_array($palettes[$paletteKey] ?? null) ? $palettes[$paletteKey] : (array)($palettes[$manifest['default_palette'] ?? ''] ?? []);
}

function auth_palette_css(): string
{
    $palette = auth_palette();
    $map = [
        'primary' => '--th-auth-primary', 'secondary' => '--th-auth-secondary', 'background' => '--th-auth-background',
        'surface' => '--th-auth-surface', 'text' => '--th-auth-text', 'muted_text' => '--th-auth-muted',
        'border' => '--th-auth-border', 'success' => '--th-auth-success', 'warning' => '--th-auth-warning', 'danger' => '--th-auth-danger'
    ];
    $css = ':root{';
    foreach ($map as $key => $var) {
        $css .= $var . ':' . ($palette[$key] ?? '#000000') . ';';
    }
    $css .= '--th-auth-form-width:' . max(320, min(760, (int)auth_setting('form_width', 460))) . 'px;';
    $css .= '--th-auth-radius:' . max(0, min(40, (int)auth_setting('border_radius', 18))) . 'px;}';
    return $css;
}

function auth_render_view(string $path, array $extra = []): void
{
    $vars = array_merge(get_value() ?: [], $extra);
    extract($vars, EXTR_SKIP);
    require_once auth_view_path($path);
}

function auth_render_frontend(string $view, string $title, array $extra = []): void
{
    ob_start();
    auth_render_view('frontend/' . $view, $extra);
    $content = (string)ob_get_clean();
    $payload = do_filter('auth_render_page', [
        'handled' => false, 'title' => $title, 'content' => $content, 'look' => auth_active_look(), 'view' => $view
    ]);
    if (is_array($payload) && !empty($payload['handled'])) {
        echo (string)($payload['content'] ?? '');
        return;
    }
    ob_start();
    do_action('foundation_render_auth_page', [
        'title' => $title, 'content' => $content, 'look' => auth_active_look(), 'view' => $view
    ]);
    $foundation = (string)ob_get_clean();
    echo $foundation !== '' ? $foundation : $content;
}

function auth_field_options(int $fieldId): array
{
    $rows = auth_db()->query('SELECT * FROM ' . auth_table('field_options') . ' WHERE field_id = :field_id ORDER BY sort_order ASC, id ASC', ['field_id' => $fieldId]);
    return is_array($rows) ? $rows : [];
}

function auth_field_value(int $fieldId, int $userId): mixed
{
    $row = auth_db()->get_row('SELECT field_value FROM ' . auth_table('field_values') . ' WHERE field_id = :field_id AND user_id = :user_id LIMIT 1', [
        'field_id' => $fieldId, 'user_id' => $userId
    ]);
    if (!$row) {
        return null;
    }
    $decoded = json_decode((string)$row->field_value, true);
    return json_last_error() === JSON_ERROR_NONE ? $decoded : (string)$row->field_value;
}

function auth_fields_for_context(string $context, int $userId = 0, ?object $viewer = null): array
{
    $columnMap = [
        'signup' => 'show_signup', 'public' => 'show_public', 'private' => 'show_private', 'admin' => 'show_admin'
    ];
    $column = $columnMap[$context] ?? 'show_private';
    $rows = auth_db()->query('SELECT * FROM ' . auth_table('fields') . ' WHERE active = 1 AND ' . $column . ' = 1 ORDER BY sort_order ASC, id ASC');
    if (!is_array($rows)) {
        return [];
    }
    $userRoles = $userId > 0 ? auth_user_role_slugs($userId) : [];
    $viewer = $viewer ?: auth_current_user();
    $isAdmin = $viewer && (
        (int)$viewer->id === 1 || contains_role('admin') ||
        user_can('auth.view_private_user_fields') || user_can('auth.edit_private_user_fields')
    );
    $isSelf = $viewer && $userId > 0 && (int)$viewer->id === $userId;
    $result = [];
    foreach ($rows as $field) {
        $allowedRoles = json_decode((string)($field->allowed_roles_json ?? '[]'), true);
        if (is_array($allowedRoles) && $allowedRoles !== [] && $userId > 0 && array_intersect($allowedRoles, $userRoles) === []) {
            continue;
        }
        $visible = $context === 'signup' ? true : match ((string)$field->visibility) {
            'public' => true,
            'authenticated' => (bool)$viewer,
            'user_admin' => $isSelf || $isAdmin,
            'admin' => $isAdmin,
            default => $isSelf || $isAdmin,
        };
        $payload = do_filter('auth_user_field_visibility', [
            'visible' => $visible, 'field' => $field, 'viewer' => $viewer, 'user' => $userId > 0 ? auth_get_user_by_id($userId) : false
        ]);
        if (is_array($payload)) {
            $visible = (bool)($payload['visible'] ?? $visible);
        }
        if (!$visible) {
            continue;
        }
        $field->options = auth_field_options((int)$field->id);
        $field->value = $userId > 0 ? auth_field_value((int)$field->id, $userId) : $field->default_value;
        $result[] = $field;
    }
    $payload = do_filter('auth_user_fields', ['fields' => $result, 'context' => $context, 'user_id' => $userId]);
    return is_array($payload) && isset($payload['fields']) && is_array($payload['fields']) ? $payload['fields'] : $result;
}

function auth_validate_field_values(array $fields, array $input): array
{
    $errors = [];
    foreach ($fields as $field) {
        $key = 'field_' . (int)$field->id;
        $value = $input[$key] ?? null;
        if (is_array($value)) {
            $value = array_values(array_filter(
                $value,
                static fn(mixed $item): bool => $item !== null && $item !== ''
            ));
        }
        $isEmpty = $value === null || $value === '' || $value === [];
        if ((string)$field->field_type === 'toggle') {
            $isEmpty = (string)$value !== '1';
        }
        if ((int)$field->required === 1 && $isEmpty) {
            $errors[] = (string)$field->label . ' is required.';
            continue;
        }
        if ($isEmpty) {
            continue;
        }
        if ($field->field_type === 'email' && !filter_var((string)$value, FILTER_VALIDATE_EMAIL)) {
            $errors[] = (string)$field->label . ' must be a valid email address.';
        }
        if ($field->field_type === 'url' && !filter_var((string)$value, FILTER_VALIDATE_URL)) {
            $errors[] = (string)$field->label . ' must be a valid URL.';
        }
        if ($field->field_type === 'number' && !is_numeric($value)) {
            $errors[] = (string)$field->label . ' must be numeric.';
        }
        if (!empty($field->validation_regex) && @preg_match((string)$field->validation_regex, '') !== false && !preg_match((string)$field->validation_regex, (string)$value)) {
            $errors[] = (string)$field->label . ' is not in the required format.';
        }
        $payload = do_filter('auth_user_field_validation', [
            'errors' => $errors, 'field' => $field, 'value' => $value, 'input' => $input
        ]);
        if (is_array($payload) && isset($payload['errors']) && is_array($payload['errors'])) {
            $errors = $payload['errors'];
        }
    }
    return array_values(array_unique(array_map('strval', $errors)));
}

function auth_uploaded_field_file(string $key, string $type): ?string
{
    if (empty($_FILES[$key]['name'])) {
        return null;
    }
    $request = auth_request();
    $request->upload_folder = 'uploads/thunder-authentication/fields';
    $request->upload_max_size = 20;
    if ($type === 'image') {
        $request->upload_file_types = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    }
    $uploaded = $request->upload_files($key);
    if ($request->upload_error_code || $request->upload_errors) {
        throw new \RuntimeException(implode('<br>', (array)$request->upload_errors));
    }
    if (is_array($uploaded)) {
        $uploaded = reset($uploaded);
    }
    return is_string($uploaded) && $uploaded !== '' ? $uploaded : null;
}

function auth_save_field_values(int $userId, array $fields, array $input, string $actor = 'user'): bool
{
    $db = auth_db();
    foreach ($fields as $field) {
        if ($actor === 'user' && (int)$field->editable_user !== 1) {
            continue;
        }
        if ($actor === 'admin' && (int)$field->editable_admin !== 1) {
            continue;
        }
        $key = 'field_' . (int)$field->id;
        $value = $input[$key] ?? null;
        if (in_array((string)$field->field_type, ['image', 'file'], true)) {
            $uploaded = auth_uploaded_field_file($key, (string)$field->field_type);
            if ($uploaded === null) {
                continue;
            }
            $value = $uploaded;
        } elseif ($field->field_type === 'toggle') {
            $value = !empty($input[$key]) ? '1' : '0';
        }
        if (is_array($value)) {
            $value = array_values(array_filter(
                $value,
                static fn(mixed $item): bool => $item !== null && $item !== ''
            ));
            $value = json_encode($value);
        }
        $payload = do_filter('auth_user_field_value', ['value' => $value, 'field' => $field, 'user' => auth_get_user_by_id($userId)]);
        if (is_array($payload) && array_key_exists('value', $payload)) {
            $value = $payload['value'];
        }
        $db->query('INSERT INTO ' . auth_table('field_values') . '
            (user_id, field_id, field_value, updated_at)
            VALUES (:user_id, :field_id, :field_value, :updated_at)
            ON DUPLICATE KEY UPDATE field_value = VALUES(field_value), updated_at = VALUES(updated_at)', [
                'user_id' => $userId, 'field_id' => (int)$field->id,
                'field_value' => (string)$value, 'updated_at' => date('Y-m-d H:i:s')
            ]);
    }
    return !$db->has_error;
}

function auth_handle_avatar_upload(string $key = 'profile_image'): ?string
{
    if (auth_setting('allow_profile_images', '1') !== '1' || empty($_FILES[$key]['name'])) {
        return null;
    }
    $request = auth_request();
    $request->upload_folder = 'uploads/thunder-authentication/avatars';
    $request->upload_max_size = 5;
    $request->upload_file_types = ['image/jpeg', 'image/png', 'image/webp'];
    $uploaded = $request->upload_files($key);
    if ($request->upload_error_code || $request->upload_errors) {
        throw new \RuntimeException(implode('<br>', (array)$request->upload_errors));
    }
    if (is_array($uploaded)) {
        $uploaded = reset($uploaded);
    }
    if (is_string($uploaded) && $uploaded !== '') {
        try {
            (new \Core\Image)->resize($uploaded, 700);
        } catch (\Throwable) {
        }
        return $uploaded;
    }
    return null;
}

function auth_field_types(): array
{
    return [
        'text' => 'Text', 'email' => 'Email', 'number' => 'Number', 'tel' => 'Telephone', 'url' => 'URL',
        'date' => 'Date', 'time' => 'Time', 'textarea' => 'Textarea', 'select' => 'Select',
        'multiselect' => 'Multi-select', 'radio' => 'Radio buttons', 'checkbox' => 'Checkboxes',
        'toggle' => 'Toggle', 'image' => 'Image', 'file' => 'File', 'hidden' => 'Hidden', 'readonly' => 'Read only'
    ];
}

function auth_field_visibility_options(): array
{
    return [
        'private' => 'Private', 'user_admin' => 'User and administrators', 'authenticated' => 'Authenticated users',
        'public' => 'Public', 'admin' => 'Administrators only'
    ];
}

function auth_list_users(array $filters = [], int $page = 1, int $perPage = 25): array
{
    $where = ['1=1'];
    $params = [];
    if (($filters['q'] ?? '') !== '') {
        $parts = ['u.username LIKE :q'];
        if (auth_user_has_column('email')) {
            $parts[] = 'u.email LIKE :q';
        }
        $parts[] = 'p.display_name LIKE :q';
        $where[] = '(' . implode(' OR ', $parts) . ')';
        $params['q'] = '%' . $filters['q'] . '%';
    }
    if (($filters['status'] ?? '') !== '') {
        $statusExpression = auth_user_has_column('disabled')
            ? "CASE WHEN u.disabled = 1 THEN 'disabled' ELSE COALESCE(p.status, 'active') END"
            : "COALESCE(p.status, 'active')";
        $where[] = $statusExpression . ' = :status';
        $params['status'] = $filters['status'];
    }
    if (($filters['role'] ?? '') !== '') {
        $where[] = 'EXISTS (SELECT 1 FROM ' . auth_table('user_roles') . ' xurm
            INNER JOIN ' . auth_table('roles') . ' xr ON xr.id = xurm.role_id
            WHERE xurm.user_id = u.id AND xr.slug = :role)';
        $params['role'] = $filters['role'];
    }
    $whereSql = implode(' AND ', $where);
    $count = auth_db()->get_row('SELECT COUNT(*) AS total FROM ' . auth_table('users') . ' u LEFT JOIN ' . auth_table('profiles') . ' p ON p.user_id = u.id WHERE ' . $whereSql, $params);
    $total = $count ? (int)$count->total : 0;
    $page = max(1, $page);
    $perPage = max(1, min(100, $perPage));
    $offset = ($page - 1) * $perPage;
    $rows = auth_db()->query(auth_user_select() . ' WHERE ' . $whereSql . ' ORDER BY u.id DESC LIMIT ' . $perPage . ' OFFSET ' . $offset, $params);
    $users = [];
    if (is_array($rows)) {
        foreach ($rows as $row) {
            $row = auth_hydrate_user($row);
            $row->roles = auth_user_role_rows((int)$row->id);
            $users[] = $row;
        }
    }
    return ['users' => $users, 'total' => $total, 'page' => $page, 'per_page' => $perPage, 'pages' => max(1, (int)ceil($total / $perPage))];
}

function auth_is_frontend_route(): bool
{
    foreach (['auth.login','auth.login.submit','auth.signup','auth.signup.submit','auth.account','auth.account.profile','auth.account.password','auth.profile'] as $route) {
        if (is_route($route)) {
            return true;
        }
    }
    return false;
}

function auth_is_admin_route(): bool
{
    foreach ([
        'auth.admin.users','auth.admin.users.create','auth.admin.users.store','auth.admin.users.edit','auth.admin.users.update','auth.admin.users.toggle','auth.admin.users.delete',
        'auth.admin.roles','auth.admin.roles.create','auth.admin.roles.store','auth.admin.roles.edit','auth.admin.roles.update','auth.admin.roles.duplicate','auth.admin.roles.delete',
        'auth.admin.fields','auth.admin.fields.create','auth.admin.fields.store','auth.admin.fields.edit','auth.admin.fields.update','auth.admin.fields.move','auth.admin.fields.delete',
        'auth.admin.settings','auth.admin.settings.update'
    ] as $route) {
        if (is_route($route)) {
            return true;
        }
    }
    return false;
}
