<?php

namespace ThunderAdmin;

use Throwable;

const PLUGIN_ID = 'thunder-admin';

function e(mixed $value): string
{
    if (function_exists('esc')) {
        return (string) \esc((string) $value);
    }

    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/**
 * Escape a flash message while preserving simple <br> formatting.
 */
function flash_message_html(mixed $message): string
{
    $escaped = e($message);

    return preg_replace('~&lt;br\s*/?&gt;~i', '<br>', $escaped) ?? $escaped;
}

function app_name(): string
{
    if (defined('APP_NAME') && APP_NAME !== '') {
        return (string) APP_NAME;
    }

    return 'ThunderPHP';
}

function app_description(): string
{
    if (defined('APP_DESCRIPTION') && APP_DESCRIPTION !== '') {
        return (string) APP_DESCRIPTION;
    }

    return '';
}

function app_logo(): string
{
    if (!defined('APP_LOGO') || APP_LOGO === '') {
        return '';
    }

    $logo = trim((string) APP_LOGO);

    if ($logo === '') {
        return '';
    }

    if (preg_match('~^(?:https?:)?//|^(?:data|blob):~i', $logo) === 1) {
        return $logo;
    }

    return root_url() . '/' . ltrim($logo, '/');
}

function root_url(): string
{
    if (defined('ROOT') && ROOT !== '') {
        return rtrim((string) ROOT, '/');
    }

    if (function_exists('base_url')) {
        return rtrim((string) \base_url(), '/');
    }

    return '';
}

function admin_url(string $path = ''): string
{
    $url = root_url() . '/admin';
    $path = trim($path, '/');

    return $path === '' ? $url : $url . '/' . $path;
}

function slugify(string $value): string
{
    $value = trim(strtolower($value));
    $value = preg_replace('/[^a-z0-9_-]+/', '-', $value) ?? '';

    return trim($value, '-');
}

function current_path(): string
{
    $url = '';

    if (function_exists('current_url')) {
        $url = (string) \current_url();
    } elseif (isset($_SERVER['REQUEST_URI'])) {
        $url = (string) $_SERVER['REQUEST_URI'];
    }

    $path = parse_url($url, PHP_URL_PATH);

    return '/' . trim((string) $path, '/');
}

function comparable_path(string $url): string
{
    $path = parse_url($url, PHP_URL_PATH);

    if ($path === null || $path === false || $path === '') {
        $path = $url;
    }

    return '/' . trim((string) $path, '/');
}

/**
 * Convert plugin link groups into a predictable array of objects.
 *
 * @param array<string, array<int, array|object>> $groups
 * @return array<string, array<int, object>>
 */
function normalize_links(array $groups): array
{
    $normalized = [];

    foreach ($groups as $pluginId => $items) {
        if (!is_array($items)) {
            continue;
        }

        $pluginId = trim((string) $pluginId);
        if ($pluginId === '') {
            $pluginId = 'unknown-plugin';
        }

        $seen = [];

        foreach ($items as $index => $item) {
            if (!is_array($item) && !is_object($item)) {
                continue;
            }

            $item = (array) $item;
            $title = trim((string) ($item['title'] ?? ''));
            $slug = trim((string) ($item['slug'] ?? ''));

            if ($slug === '') {
                $slug = slugify($title);
            }

            if ($title === '' || $slug === '' || isset($seen[$slug])) {
                continue;
            }


            $seen[$slug] = true;

            $normalized[$pluginId][] = (object) [
                'plugin_id'  => $pluginId,
                'key'        => $pluginId . ':' . $slug,
                'title'      => $title,
                'link'       => trim((string) ($item['link'] ?? '#')) ?: '#',
                'icon'       => trim((string) ($item['icon'] ?? 'fa-solid fa-circle')),
                'parent'     => trim((string) ($item['parent'] ?? '')),
                'slug'       => $slug,
                'order'      => (int) ($item['order'] ?? (($index + 1) * 10)),
                'badge'      => trim((string) ($item['badge'] ?? '')),
                'target'     => trim((string) ($item['target'] ?? '_self')) ?: '_self',
                'active'     => false,
                'expanded'   => false,
            ];
        }

        if (!isset($normalized[$pluginId])) {
            continue;
        }

        usort($normalized[$pluginId], static function (object $a, object $b): int {
            $order = $a->order <=> $b->order;
            return $order !== 0 ? $order : strcasecmp($a->title, $b->title);
        });

        repair_parents($normalized[$pluginId]);
    }

    return $normalized;
}

/** @param array<int, object> $items */
function repair_parents(array &$items): void
{
    $bySlug = [];
    foreach ($items as $item) {
        $bySlug[$item->slug] = $item;
    }

    foreach ($items as $item) {
        if ($item->parent === '' || !isset($bySlug[$item->parent]) || $item->parent === $item->slug) {
            $item->parent = '';
            continue;
        }

        $visited = [$item->slug => true];
        $parent = $item->parent;

        while ($parent !== '' && isset($bySlug[$parent])) {
            if (isset($visited[$parent])) {
                $item->parent = '';
                break;
            }

            $visited[$parent] = true;
            $parent = $bySlug[$parent]->parent;
        }
    }
}

/**
 * @param array<string, array<int, object>> $groups
 */
function mark_active_links(array &$groups): ?object
{
    $current = rtrim(current_path(), '/') ?: '/';
    $adminRoot = rtrim(comparable_path(admin_url()), '/') ?: '/';
    $best = null;
    $bestLength = -1;

    foreach ($groups as $items) {
        foreach ($items as $item) {
            if ($item->link === '#' || str_starts_with(strtolower($item->link), 'javascript:')) {
                continue;
            }

            $path = rtrim(comparable_path($item->link), '/') ?: '/';
            $exact = $current === $path;
            $child = $path !== '/' && $path !== $adminRoot && str_starts_with($current . '/', $path . '/');

            if (($exact || $child) && strlen($path) > $bestLength) {
                $best = $item;
                $bestLength = strlen($path);
            }
        }
    }

    if ($best === null) {
        return null;
    }

    $best->active = true;
    $group = &$groups[$best->plugin_id];
    $bySlug = [];

    foreach ($group as $item) {
        $bySlug[$item->slug] = $item;
    }

    $parent = $best->parent;
    $guard = [];

    while ($parent !== '' && isset($bySlug[$parent]) && !isset($guard[$parent])) {
        $guard[$parent] = true;
        $bySlug[$parent]->expanded = true;
        $parent = $bySlug[$parent]->parent;
    }

    return $best;
}

/**
 * @param array<string, array<int, object>> $groups
 * @return array<int, object>
 */
function build_breadcrumbs(array $groups, ?object $active): array
{
    $breadcrumbs = [
        (object) [
            'title' => 'Dashboard',
            'link'  => admin_url(),
        ],
    ];

    if ($active === null || $active->slug === 'dashboard') {
        return $breadcrumbs;
    }

    $items = $groups[$active->plugin_id] ?? [];
    $bySlug = [];
    foreach ($items as $item) {
        $bySlug[$item->slug] = $item;
    }

    $chain = [$active];
    $parent = $active->parent;
    $guard = [];

    while ($parent !== '' && isset($bySlug[$parent]) && !isset($guard[$parent])) {
        $guard[$parent] = true;
        array_unshift($chain, $bySlug[$parent]);
        $parent = $bySlug[$parent]->parent;
    }

    foreach ($chain as $item) {
        if ($item->slug === 'dashboard') {
            continue;
        }

        $breadcrumbs[] = (object) [
            'title' => $item->title,
            'link'  => $item->link,
        ];
    }

    return $breadcrumbs;
}

/**
 * @param array<string, array<int, object>> $groups
 * @return array<string, array{roots: array<int, object>, children: array<string, array<int, object>>}>
 */
function build_menu(array $groups): array
{
    $menu = [];

    foreach ($groups as $pluginId => $items) {
        $children = [];
        $roots = [];

        foreach ($items as $item) {
            if ($item->parent === '') {
                $roots[] = $item;
            } else {
                $children[$item->parent][] = $item;
            }
        }

        $menu[$pluginId] = [
            'roots'    => $roots,
            'children' => $children,
        ];
    }

    return $menu;
}

/**
 * @param array<int, object> $items
 * @param array<string, array<int, object>> $children
 * @param array<string, bool> $visited
 */
function render_menu_items(array $items, array $children, int $level = 0, array $visited = []): void
{
    foreach ($items as $item) {
        if (isset($visited[$item->slug])) {
            continue;
        }

        $branchVisited = $visited;
        $branchVisited[$item->slug] = true;
        $childItems = $children[$item->slug] ?? [];
        $hasChildren = $childItems !== [];
        $isOpen = $item->expanded || $item->active;
        $itemClass = 'ta-nav__item';

        if ($item->active) {
            $itemClass .= ' ta-nav__item--active';
        }
        if ($isOpen) {
            $itemClass .= ' ta-nav__item--open';
        }

        echo '<div class="' . e($itemClass) . '" data-ta-nav-item data-ta-level="' . e($level) . '">';
        echo '<div class="ta-nav__row">';

        $labelClass = 'ta-nav__link';
        if ($item->active) {
            $labelClass .= ' ta-nav__link--active';
        }

        if ($hasChildren && ($item->link === '#' || $item->link === '')) {
            echo '<button class="' . e($labelClass) . ' ta-nav__link--button" type="button" title="' . e($item->title) . '" data-ta-submenu-toggle aria-expanded="' . ($isOpen ? 'true' : 'false') . '">';
        } else {
            echo '<a class="' . e($labelClass) . '" href="' . e($item->link) . '" target="' . e($item->target) . '" title="' . e($item->title) . '"' . ($item->active ? ' aria-current="page"' : '') . '>';
        }

        echo '<span class="ta-nav__icon-wrap">';
        echo '<i class="ta-nav__icon ' . e($item->icon) . '" aria-hidden="true"></i>';
        echo '<span class="ta-nav__icon-fallback" aria-hidden="true"></span>';
        echo '</span>';
        echo '<span class="ta-nav__title">' . e($item->title) . '</span>';

        if ($item->badge !== '') {
            echo '<span class="ta-nav__badge">' . e($item->badge) . '</span>';
        }

        if ($hasChildren && ($item->link === '#' || $item->link === '')) {
            echo '<i class="ta-nav__chevron fa-solid fa-chevron-down" aria-hidden="true"></i>';
            echo '</button>';
        } else {
            echo '</a>';

            if ($hasChildren) {
                echo '<button class="ta-nav__toggle" type="button" data-ta-submenu-toggle aria-label="Toggle ' . e($item->title) . ' submenu" aria-expanded="' . ($isOpen ? 'true' : 'false') . '">';
                echo '<i class="ta-nav__chevron fa-solid fa-chevron-down" aria-hidden="true"></i>';
                echo '</button>';
            }
        }

        echo '</div>';

        if ($hasChildren) {
            echo '<div class="ta-nav__submenu" data-ta-submenu' . ($isOpen ? '' : ' hidden') . '>';
            render_menu_items($childItems, $children, $level + 1, $branchVisited);
            echo '</div>';
        }

        echo '</div>';
    }
}


/**
 * Resolve whether the current request may enter the admin area.
 *
 * Access requires an authenticated session and at least one of:
 * - user ID 1
 * - the view-admin-area permission
 * - the admin role
 *
 * @return array{logged_in: bool, allowed: bool, user_id: int}
 */
function admin_access_status(): array
{
    $status = [
        'logged_in' => false,
        'allowed'   => false,
        'user_id'   => 0,
    ];

    try {
        if (!class_exists('\\Core\\Session')) {
            return $status;
        }

        $session = new \Core\Session();
        $status['logged_in'] = $session->is_logged_in();

        if (!$status['logged_in']) {
            return $status;
        }

        $status['user_id'] = (int) $session->user('id');

        $hasPermission = function_exists('user_can')
            && \user_can('view-admin-area');

        $hasAdminRole = function_exists('contains_role')
            && \contains_role('admin');

        // Fallback for applications that have not enabled the role helpers.
        if (!$hasAdminRole) {
            $user = $session->user();
            $user = is_object($user) ? (array) $user : (is_array($user) ? $user : []);
            $roles = $user['roles'] ?? $user['role'] ?? $user['user_role'] ?? [];
            $roles = is_array($roles) ? $roles : preg_split('/[,|]/', (string) $roles);

            foreach ($roles ?: [] as $role) {
                if (strcasecmp(trim((string) $role), 'admin') === 0) {
                    $hasAdminRole = true;
                    break;
                }
            }
        }

        $status['allowed'] = $status['user_id'] === 1
            || $hasPermission
            || $hasAdminRole;
    } catch (Throwable) {
        return $status;
    }

    return $status;
}

function deny_admin_access(bool $loggedIn): void
{
    if ($loggedIn && function_exists('message')) {
        \message('fail', 'You do not have permission to access the admin area.');
    }

    $destination = $loggedIn
        ? (root_url() ?: '/')
        : root_url() . '/login';

    if (!headers_sent()) {
        header('Location: ' . $destination);
    }

    exit;
}


/** @return array<string, string> */
function palette_fields(): array
{
    return [
        'primary'         => 'Primary',
        'primary_dark'    => 'Primary dark',
        'primary_soft'    => 'Primary soft',
        'sidebar_bg'      => 'Sidebar background',
        'sidebar_text'    => 'Sidebar text',
        'sidebar_heading' => 'Sidebar heading',
        'page_bg'         => 'Page background',
        'surface'         => 'Surface',
        'border'          => 'Border',
        'text'            => 'Main text',
        'muted'           => 'Muted text',
        'success'         => 'Success',
        'danger'          => 'Danger',
    ];
}

function admin_settings_table(): string
{
    return 'thunder_admin_settings';
}

/** @return array<string, string> */
function admin_settings(bool $refresh = false): array
{
    static $cache = null;

    if ($cache !== null && !$refresh) {
        return $cache;
    }

    $cache = [];

    try {
        if (!class_exists('\\Core\\Database')) {
            return $cache;
        }

        $db = new \Core\Database();
        $rows = $db->query(
            'SELECT setting_key, setting_value FROM ' . admin_settings_table()
        );

        if (!is_array($rows)) {
            return $cache;
        }

        foreach ($rows as $row) {
            $row = is_object($row) ? (array) $row : (is_array($row) ? $row : []);
            $key = trim((string) ($row['setting_key'] ?? ''));

            if ($key !== '') {
                $cache[$key] = (string) ($row['setting_value'] ?? '');
            }
        }
    } catch (Throwable) {
        // The migration may not have been run yet. Defaults keep the admin usable.
    }

    return $cache;
}

function save_admin_setting(string $key, string $value): bool
{
    $key = trim($key);

    if ($key === '' || strlen($key) > 120 || !class_exists('\\Core\\Database')) {
        return false;
    }

    try {
        $db = new \Core\Database();
        $table = admin_settings_table();
        $existing = $db->get_row(
            "SELECT id FROM {$table} WHERE setting_key = :setting_key LIMIT 1",
            ['setting_key' => $key]
        );

        if (!empty($existing->id)) {
            $db->query(
                "UPDATE {$table}
                 SET setting_value = :setting_value, date_updated = :date_updated
                 WHERE setting_key = :setting_key",
                [
                    'setting_key'   => $key,
                    'setting_value' => $value,
                    'date_updated'  => date('Y-m-d H:i:s'),
                ]
            );
        } else {
            $db->query(
                "INSERT INTO {$table} (setting_key, setting_value, date_created)
                 VALUES (:setting_key, :setting_value, :date_created)",
                [
                    'setting_key'   => $key,
                    'setting_value' => $value,
                    'date_created'  => date('Y-m-d H:i:s'),
                ]
            );
        }

        return !$db->has_error;
    } catch (Throwable) {
        return false;
    }
}

/** @param array<string, string> $values */
function save_admin_settings(array $values): bool
{
    foreach ($values as $key => $value) {
        if (!save_admin_setting((string) $key, (string) $value)) {
            return false;
        }
    }

    admin_settings(true);
    return true;
}

/** @return array<string, array<string, mixed>> */
function available_admin_looks(): array
{
    static $looks = null;

    if ($looks !== null) {
        return $looks;
    }

    $looks = [];
    $root = __DIR__ . '/looks';

    if (!is_dir($root)) {
        return $looks;
    }

    foreach (scandir($root) ?: [] as $folder) {
        if ($folder === '.' || $folder === '..' || !is_dir($root . '/' . $folder)) {
            continue;
        }

        $definitionFile = $root . '/' . $folder . '/look.json';
        if (!is_file($definitionFile)) {
            continue;
        }

        $definition = json_decode((string) file_get_contents($definitionFile), true);
        if (!is_array($definition)) {
            continue;
        }

        $palettes = is_array($definition['palettes'] ?? null)
            ? $definition['palettes']
            : [];

        $looks[$folder] = [
            'id'              => $folder,
            'name'            => trim((string) ($definition['name'] ?? $folder)) ?: $folder,
            'description'     => trim((string) ($definition['description'] ?? '')),
            'version'         => trim((string) ($definition['version'] ?? '')),
            'thumbnail'       => trim((string) ($definition['thumbnail'] ?? '')),
            'default_palette' => trim((string) ($definition['default_palette'] ?? array_key_first($palettes) ?? '')),
            'palettes'        => $palettes,
        ];
    }

    ksort($looks);
    return $looks;
}

/** @param array<string, string>|null $settings */
function active_admin_look(?array $settings = null): string
{
    $looks = available_admin_looks();
    $settings ??= admin_settings();
    $selected = trim((string) ($settings['active_look'] ?? ''));

    if ($selected !== '' && isset($looks[$selected])) {
        return $selected;
    }

    if (isset($looks['classic-sidebar'])) {
        return 'classic-sidebar';
    }

    return (string) (array_key_first($looks) ?? 'classic-sidebar');
}

function admin_look_path(string $path = '', ?string $look = null): string
{
    $look ??= active_admin_look();
    return __DIR__ . '/looks/' . $look . '/' . ltrim($path, '/');
}

function admin_look_http(string $path = '', ?string $look = null): string
{
    $look ??= active_admin_look();

    if (!function_exists('plugin_http_path')) {
        return '';
    }

    return (string) \plugin_http_path('looks/' . $look . '/' . ltrim($path, '/'));
}

function normalize_hex_color(mixed $value, string $fallback): string
{
    $value = trim((string) $value);
    $fallback = trim($fallback);

    if (preg_match('/^#[0-9a-f]{6}$/i', $value) === 1) {
        return strtolower($value);
    }

    if (preg_match('/^#[0-9a-f]{3}$/i', $value) === 1) {
        return strtolower($value);
    }

    return preg_match('/^#[0-9a-f]{3}(?:[0-9a-f]{3})?$/i', $fallback) === 1
        ? strtolower($fallback)
        : '#000000';
}

/**
 * @param array<string, mixed> $colors
 * @param array<string, string> $fallback
 * @return array<string, string>
 */
function sanitize_palette_colors(array $colors, array $fallback): array
{
    $sanitized = [];

    foreach (palette_fields() as $key => $label) {
        $default = (string) ($fallback[$key] ?? '#000000');
        $sanitized[$key] = normalize_hex_color($colors[$key] ?? '', $default);
    }

    return $sanitized;
}

/** @param array<string, string>|null $settings */
function admin_palette_id(string $look, ?array $settings = null): string
{
    $settings ??= admin_settings();
    $looks = available_admin_looks();
    $definition = $looks[$look] ?? [];
    $palettes = is_array($definition['palettes'] ?? null) ? $definition['palettes'] : [];
    $selected = trim((string) ($settings['palette_' . $look] ?? ''));

    if ($selected === 'custom' || isset($palettes[$selected])) {
        return $selected;
    }

    $default = trim((string) ($definition['default_palette'] ?? ''));
    return isset($palettes[$default]) ? $default : (string) (array_key_first($palettes) ?? 'custom');
}

/** @param array<string, string>|null $settings
 *  @return array<string, string>
 */
function custom_palette_colors(string $look, ?array $settings = null): array
{
    $settings ??= admin_settings();
    $looks = available_admin_looks();
    $definition = $looks[$look] ?? [];
    $palettes = is_array($definition['palettes'] ?? null) ? $definition['palettes'] : [];
    $defaultId = trim((string) ($definition['default_palette'] ?? ''));
    $defaultPalette = is_array($palettes[$defaultId]['colors'] ?? null)
        ? $palettes[$defaultId]['colors']
        : (is_array(($palettes[array_key_first($palettes)] ?? [])['colors'] ?? null)
            ? $palettes[array_key_first($palettes)]['colors']
            : []);

    $stored = json_decode((string) ($settings['custom_palette_' . $look] ?? ''), true);
    $stored = is_array($stored) ? $stored : [];

    return sanitize_palette_colors($stored, $defaultPalette);
}

/** @param array<string, string>|null $settings
 *  @return array{id: string, name: string, custom: bool, colors: array<string, string>}
 */
function resolved_admin_palette(string $look, ?array $settings = null): array
{
    $settings ??= admin_settings();
    $looks = available_admin_looks();
    $definition = $looks[$look] ?? [];
    $palettes = is_array($definition['palettes'] ?? null) ? $definition['palettes'] : [];
    $paletteId = admin_palette_id($look, $settings);

    if ($paletteId === 'custom') {
        return [
            'id'     => 'custom',
            'name'   => 'Custom',
            'custom' => true,
            'colors' => custom_palette_colors($look, $settings),
        ];
    }

    $palette = is_array($palettes[$paletteId] ?? null) ? $palettes[$paletteId] : [];
    $colors = is_array($palette['colors'] ?? null) ? $palette['colors'] : [];

    return [
        'id'     => $paletteId,
        'name'   => trim((string) ($palette['name'] ?? $paletteId)) ?: $paletteId,
        'custom' => false,
        'colors' => sanitize_palette_colors($colors, $colors),
    ];
}

/** @param array<string, string> $colors */
function palette_css_variables(array $colors): string
{
    $variables = [
        'primary'         => '--ta-primary',
        'primary_dark'    => '--ta-primary-dark',
        'primary_soft'    => '--ta-primary-soft',
        'sidebar_bg'      => '--ta-sidebar-bg',
        'sidebar_text'    => '--ta-sidebar-text',
        'sidebar_heading' => '--ta-sidebar-heading',
        'page_bg'         => '--ta-page-bg',
        'surface'         => '--ta-surface',
        'border'          => '--ta-border',
        'text'            => '--ta-text',
        'muted'           => '--ta-muted',
        'success'         => '--ta-success',
        'danger'          => '--ta-danger',
    ];

    $parts = [];
    foreach ($variables as $key => $variable) {
        if (isset($colors[$key])) {
            $parts[] = $variable . ':' . normalize_hex_color($colors[$key], '#000000');
        }
    }

    return implode(';', $parts);
}

function redirect_admin_settings(): void
{
    if (function_exists('redirect')) {
        \redirect('admin/settings');
    }

    if (!headers_sent()) {
        header('Location: ' . admin_url('settings'));
    }

    exit;
}

function initials(string $name): string
{
    $parts = preg_split('/\s+/', trim($name)) ?: [];
    $initials = '';

    foreach (array_slice($parts, 0, 2) as $part) {
        $initials .= strtoupper(substr($part, 0, 1));
    }

    return $initials !== '' ? $initials : 'U';
}

/** @return array{name: string, email: string, role: string, image: string, initials: string, links: array<int, array<string, string>>} */
function current_user_info(): array
{
    $data = [
        'name'     => 'Administrator',
        'email'    => '',
        'role'     => 'Admin',
        'image'    => '',
        'initials' => 'A',
        'links'    => [],
    ];

    $candidate = null;

    try {
        if (class_exists('\\Core\\Session')) {
            $session = new \Core\Session();

            foreach (['user', 'get_user', 'current_user'] as $method) {
                if (method_exists($session, $method)) {
                    $candidate = $session->{$method}();
                    if ($candidate) {
                        break;
                    }
                }
            }
        }
    } catch (Throwable) {
        $candidate = null;
    }

    if (!$candidate && isset($_SESSION)) {
        foreach (['USER', 'user', 'auth_user'] as $key) {
            if (isset($_SESSION[$key])) {
                $candidate = $_SESSION[$key];
                break;
            }
        }
    }

    if (is_object($candidate)) {
        $candidate = (array) $candidate;
    }

    if (is_array($candidate)) {
        $first = trim((string) ($candidate['firstname'] ?? $candidate['first_name'] ?? ''));
        $last = trim((string) ($candidate['lastname'] ?? $candidate['last_name'] ?? ''));
        $full = trim((string) ($candidate['name'] ?? $candidate['username'] ?? trim($first . ' ' . $last)));

        $data['name'] = $full !== '' ? $full : $data['name'];
        $data['email'] = trim((string) ($candidate['email'] ?? ''));
        $data['role'] = trim((string) ($candidate['role'] ?? $candidate['user_role'] ?? $data['role']));
        $data['image'] = trim((string) ($candidate['image'] ?? $candidate['avatar'] ?? $candidate['profile_image'] ?? ''));
    }

    $data['initials'] = initials($data['name']);

    return $data;
}

/** @param array<int, array|object> $items */
function normalize_notifications(array $items): array
{
    $notifications = [];

    foreach ($items as $item) {
        if (!is_array($item) && !is_object($item)) {
            continue;
        }

        $item = (array) $item;
        $title = trim((string) ($item['title'] ?? ''));

        if ($title === '') {
            continue;
        }

        $notifications[] = (object) [
            'title'      => $title,
            'message'    => trim((string) ($item['message'] ?? '')),
            'link'       => trim((string) ($item['link'] ?? '#')) ?: '#',
            'icon'       => trim((string) ($item['icon'] ?? 'fa-solid fa-bell')),
            'type'       => trim((string) ($item['type'] ?? 'info')),
            'created_at' => trim((string) ($item['created_at'] ?? '')),
            'read'       => (bool) ($item['read'] ?? false),
        ];
    }

    usort($notifications, static function (object $a, object $b): int {
        if ($a->read !== $b->read) {
            return $a->read <=> $b->read;
        }

        return strcmp($b->created_at, $a->created_at);
    });

    return $notifications;
}

function notification_time(string $date): string
{
    if ($date === '') {
        return '';
    }

    if (function_exists('get_date')) {
        return (string) \get_date($date);
    }

    $timestamp = strtotime($date);

    return $timestamp ? date('M j, Y g:i A', $timestamp) : $date;
}
