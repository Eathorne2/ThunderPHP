<?php

namespace ThunderBlog;

defined('ROOTPATH') or die('Direct script access denied');

use Core\Database;
use Throwable;

function blog_tables(): array
{
    return [
        'posts' => 'thunder_blog_posts',
        'categories' => 'thunder_blog_categories',
        'tags' => 'thunder_blog_tags',
        'post_tags' => 'thunder_blog_post_tags',
        'settings' => 'thunder_blog_settings',
        'menus' => 'thunder_blog_menus',
        'menu_items' => 'thunder_blog_menu_items',
    ];
}

function blog_db(): Database
{
    return new Database();
}

function blog_identifier(string $identifier, bool $allowEmpty = false): string
{
    $identifier = trim($identifier);
    if ($allowEmpty && $identifier === '') {
        return '';
    }

    if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $identifier)) {
        throw new \InvalidArgumentException('Invalid database identifier: ' . $identifier);
    }

    return $identifier;
}


function blog_database_tables(): array
{
    try {
        $pdo = blog_db()->getConnection();
        $statement = $pdo->query('SHOW TABLES');
        $tables = $statement ? $statement->fetchAll(\PDO::FETCH_COLUMN) : [];
        $tables = array_values(array_filter(array_map('strval', is_array($tables) ? $tables : [])));
        sort($tables, SORT_NATURAL | SORT_FLAG_CASE);
        return $tables;
    } catch (Throwable) {
        return [];
    }
}

function blog_table_columns(string $table): array
{
    try {
        $table = blog_identifier($table);
        $pdo = blog_db()->getConnection();
        $statement = $pdo->query("SHOW COLUMNS FROM `{$table}`");
        $rows = $statement ? $statement->fetchAll(\PDO::FETCH_ASSOC) : [];
        $columns = [];
        foreach (is_array($rows) ? $rows : [] as $row) {
            $name = (string) ($row['Field'] ?? '');
            if ($name === '') {
                continue;
            }
            $columns[] = [
                'name' => $name,
                'type' => (string) ($row['Type'] ?? ''),
                'nullable' => strtoupper((string) ($row['Null'] ?? '')) === 'YES',
                'key' => (string) ($row['Key'] ?? ''),
            ];
        }
        return $columns;
    } catch (Throwable) {
        return [];
    }
}

function blog_column_names(string $table): array
{
    return array_values(array_map(static fn (array $column): string => (string) $column['name'], blog_table_columns($table)));
}

function blog_row_value(object|array $row, string $key, mixed $default = ''): mixed
{
    if ($key === '') {
        return $default;
    }
    if (is_object($row) && property_exists($row, $key)) {
        return $row->{$key};
    }
    if (is_array($row) && array_key_exists($key, $row)) {
        return $row[$key];
    }
    return $default;
}

function blog_author_mapping_preview(): array
{
    $id = blog_current_user_id();
    $author = blog_author($id);
    return [
        'current_user_id' => $id,
        'author' => $author,
        'table' => (string) blog_setting('user_table', 'auth_users'),
        'primary_key' => (string) blog_setting('user_primary_key', 'id'),
        'display_column' => (string) blog_setting('user_display_column', 'username'),
    ];
}

function blog_settings(bool $refresh = false): array
{
    static $cache = null;
    if (!$refresh && is_array($cache)) {
        return $cache;
    }

    $defaults = [
        'look' => 'editorial',
        'palette' => 'indigo',
        'custom_palette' => '',
        'posts_per_page' => '9',
        'show_author' => '1',
        'show_date' => '1',
        'show_category' => '1',
        'show_views' => '1',
        'blog_page_eyebrow' => 'Stories, ideas and updates',
        'blog_page_title' => 'The Blog',
        'blog_page_subtitle' => (defined('APP_DESCRIPTION') && APP_DESCRIPTION !== '') ? (string) APP_DESCRIPTION : 'Explore our latest articles and updates.',
        'search_page_eyebrow' => 'Stories, ideas and updates',
        'search_page_title' => 'Search the Blog',
        'search_page_subtitle' => 'Find articles, topics and stories from the blog.',
        'user_table' => 'auth_users',
        'user_primary_key' => 'id',
        'user_display_column' => 'username',
        'user_email_column' => 'email',
        'user_avatar_column' => '',
        'user_profile_column' => 'username',
        'active_header_id' => '0',
        'active_footer_id' => '0',
    ];

    try {
        $rows = blog_db()->query('SELECT setting_key, setting_value FROM ' . blog_tables()['settings']);
        if (is_array($rows)) {
            foreach ($rows as $row) {
                $defaults[(string) $row->setting_key] = (string) $row->setting_value;
            }
        }
    } catch (Throwable) {
        // Allows the plugin settings screen to render a useful default before migration.
    }

    $cache = $defaults;
    return $cache;
}

function blog_setting(string $key, mixed $default = null): mixed
{
    $settings = blog_settings();
    return $settings[$key] ?? $default;
}

function blog_save_setting(string $key, string $value): bool
{
    $db = blog_db();
    $table = blog_tables()['settings'];
    $exists = $db->get_row("SELECT id FROM {$table} WHERE setting_key = :setting_key LIMIT 1", [
        'setting_key' => $key,
    ]);

    if ($exists) {
        $ok = $db->query("UPDATE {$table} SET setting_value = :setting_value, updated_at = CURRENT_TIMESTAMP WHERE setting_key = :setting_key", [
            'setting_key' => $key,
            'setting_value' => $value,
        ]);
    } else {
        $ok = $db->query("INSERT INTO {$table} (setting_key, setting_value, created_at, updated_at) VALUES (:setting_key, :setting_value, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)", [
            'setting_key' => $key,
            'setting_value' => $value,
        ]);
    }

    blog_settings(true);
    return (bool) $ok;
}

function blog_active_look(): string
{
    $look = preg_replace('/[^a-z0-9_-]/i', '', (string) blog_setting('look', 'editorial')) ?: 'editorial';
    if (!is_dir(plugin_path('looks/' . $look))) {
        return 'editorial';
    }

    return $look;
}

function blog_look_path(string $path = ''): string
{
    return plugin_path('looks/' . blog_active_look() . '/' . ltrim($path, '/'));
}

function blog_look_http(string $path = ''): string
{
    return plugin_http_path('looks/' . blog_active_look() . '/' . ltrim($path, '/'));
}

function blog_admin_view(string $view, array $vars = []): void
{
    $file = blog_look_path('admin/' . ltrim($view, '/'));
    if (!is_file($file)) {
        echo '<div class="tb-admin__notice tb-admin__notice--danger">The selected blog look is missing the requested administration view.</div>';
        return;
    }

    extract($vars, EXTR_SKIP);
    require $file;
}

function blog_capture_view(string $view, array $vars = []): string
{
    $file = blog_look_path('frontend/' . ltrim($view, '/'));
    if (!is_file($file)) {
        return '<div class="thunder-blog__notice">The selected blog look is incomplete.</div>';
    }

    extract($vars, EXTR_SKIP);
    ob_start();
    require $file;
    return (string) ob_get_clean();
}

function blog_render_frontend(string $view, array $vars = []): void
{
    $content = blog_capture_view($view, $vars);
    $payload = [
        'title' => (string) ($vars['page_title'] ?? APP_NAME),
        'content' => $content,
        'look' => blog_active_look(),
        'view' => $view,
        'palette' => $vars['palette'] ?? blog_global_palette(),
        'handled' => false,
        'data' => $vars,
    ];

    $payload = do_filter('thunder_blog_render_page', $payload);
    if (!empty($payload['handled'])) {
        echo (string) ($payload['content'] ?? '');
        return;
    }

    ob_start();
    do_action('foundation_render_blog_page', $payload);
    $foundationOutput = trim((string) ob_get_clean());
    if ($foundationOutput !== '') {
        echo $foundationOutput;
        return;
    }

    $layout = blog_look_path('frontend/layout.php');
    if (is_file($layout)) {
        extract($payload, EXTR_SKIP);
        require $layout;
        return;
    }

    echo (string) $payload['content'];
}

function blog_can(string $permission): bool
{
    if (function_exists('user_can')) {
        return (bool) user_can($permission);
    }

    $id = blog_session_user_value('id');
    $role = strtolower((string) blog_session_user_value('role'));
    return (string) $id === '1' || $role === 'admin';
}

function blog_require_permission(string $permission): void
{
    if (blog_can($permission)) {
        return;
    }

    if (function_exists('message')) {
        message('fail', 'You do not have permission to access that blog area.');
    }
    redirect('admin');
    exit;
}

function blog_require_any_permission(array $permissions): void
{
    foreach ($permissions as $permission) {
        if (blog_can((string) $permission)) {
            return;
        }
    }

    if (function_exists('message')) {
        message('fail', 'You do not have permission to access that blog area.');
    }
    redirect('admin');
    exit;
}

function blog_session_user_value(string $key): mixed
{
    try {
        if (class_exists('Core\\Session')) {
            $session = new \Core\Session();
            if (method_exists($session, 'user')) {
                $value = $session->user($key);
                if ($value !== null && $value !== '' && $value !== false) {
                    return $value;
                }
            }
        }
    } catch (Throwable) {
        // Fall through to direct session inspection for compatibility.
    }

    foreach (['USER', 'user', 'auth_user'] as $sessionKey) {
        if (!isset($_SESSION[$sessionKey])) {
            continue;
        }
        $user = $_SESSION[$sessionKey];
        if (is_array($user) && array_key_exists($key, $user)) {
            return $user[$key];
        }
        if (is_object($user) && property_exists($user, $key)) {
            return $user->{$key};
        }
    }

    return null;
}

function blog_current_user_id(): string
{
    $primaryKey = (string) blog_setting('user_primary_key', 'id');
    $value = blog_session_user_value($primaryKey);
    if ($value === null || $value === '') {
        $value = blog_session_user_value('id');
    }

    return (string) ($value ?? '');
}

function blog_author(string|int|null $authorId): array
{
    static $cache = [];
    $id = trim((string) ($authorId ?? ''));
    $cacheKey = implode('|', [
        $id,
        (string) blog_setting('user_table', 'auth_users'),
        (string) blog_setting('user_primary_key', 'id'),
        (string) blog_setting('user_display_column', 'username'),
        (string) blog_setting('user_email_column', 'email'),
        (string) blog_setting('user_avatar_column', ''),
        (string) blog_setting('user_profile_column', 'username'),
    ]);
    if (isset($cache[$cacheKey])) {
        return $cache[$cacheKey];
    }

    $fallback = [
        'id' => $id,
        'display_name' => $id !== '' ? 'Author ' . $id : 'Unknown author',
        'email' => '',
        'avatar' => '',
        'profile_url' => '',
        'raw' => null,
    ];
    if ($id === '') {
        return $cache[$cacheKey] = blog_filter_author($fallback);
    }

    try {
        $table = blog_identifier((string) blog_setting('user_table', 'auth_users'));
        $pk = blog_identifier((string) blog_setting('user_primary_key', 'id'));
        $display = blog_identifier((string) blog_setting('user_display_column', 'username'));
        $email = blog_identifier((string) blog_setting('user_email_column', 'email'), true);
        $avatar = blog_identifier((string) blog_setting('user_avatar_column', ''), true);
        $profile = blog_identifier((string) blog_setting('user_profile_column', 'username'), true);

        $columns = blog_column_names($table);
        if (!in_array($pk, $columns, true) || !in_array($display, $columns, true)) {
            return $cache[$cacheKey] = blog_filter_author($fallback);
        }

        $row = blog_db()->get_row(
            "SELECT * FROM `{$table}` WHERE `{$pk}` = :author_id LIMIT 1",
            ['author_id' => $id]
        );
        if (!$row) {
            return $cache[$cacheKey] = blog_filter_author($fallback);
        }

        $displayName = trim((string) blog_row_value($row, $display, ''));
        $emailValue = $email !== '' ? trim((string) blog_row_value($row, $email, '')) : '';
        if ($displayName === '') {
            $displayName = $emailValue !== '' ? $emailValue : 'Author ' . $id;
        }

        $avatarValue = $avatar !== '' ? trim((string) blog_row_value($row, $avatar, '')) : '';
        if ($avatarValue !== '' && !preg_match('#^(https?://|/)#i', $avatarValue) && function_exists('get_image')) {
            $avatarValue = (string) get_image($avatarValue);
        }

        $profileValue = $profile !== '' ? trim((string) blog_row_value($row, $profile, '')) : '';
        $profileUrl = '';
        if ($profileValue !== '') {
            $profileUrl = preg_match('#^https?://#i', $profileValue)
                ? $profileValue
                : ROOT . '/profile/' . rawurlencode($profileValue);
        }

        $author = [
            'id' => (string) blog_row_value($row, $pk, $id),
            'display_name' => $displayName,
            'email' => $emailValue,
            'avatar' => $avatarValue,
            'profile_url' => $profileUrl,
            'raw' => $row,
        ];

        $author['display_name'] = blog_apply_auth_filter('auth_user_display_name', $author['display_name'], $row, 'display_name');
        $author['profile_url'] = blog_apply_auth_filter('auth_user_profile_url', $author['profile_url'], $row, 'profile_url');
        $author['avatar'] = blog_apply_auth_filter('auth_user_avatar', $author['avatar'], $row, 'avatar');

        return $cache[$cacheKey] = blog_filter_author($author);
    } catch (Throwable) {
        return $cache[$cacheKey] = blog_filter_author($fallback);
    }
}

function blog_filter_author(array $author): array
{
    try {
        $filtered = do_filter('thunder_blog_author', $author);
        return is_array($filtered) ? array_replace($author, $filtered) : $author;
    } catch (Throwable) {
        return $author;
    }
}

function blog_apply_auth_filter(string $hook, string $value, object $user, string $resultKey): string
{
    try {
        $filtered = do_filter($hook, [
            'user' => $user,
            'value' => $value,
            $resultKey => $value,
        ]);

        if (is_string($filtered)) {
            return $filtered;
        }
        if (is_array($filtered)) {
            return (string) ($filtered[$resultKey] ?? $filtered['value'] ?? $value);
        }
    } catch (Throwable) {
    }

    return $value;
}

function blog_slugify(string $value): string
{
    $value = trim($value);
    if (function_exists('iconv')) {
        $transliterated = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
        if (is_string($transliterated)) {
            $value = $transliterated;
        }
    }
    $value = strtolower($value);
    $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';
    return trim($value, '-') ?: 'post';
}

function blog_unique_slug(string $slug, ?int $ignoreId = null, string $postType = 'post'): string
{
    $postType = in_array($postType, ['post', 'page', 'header', 'footer'], true) ? $postType : 'post';
    $base = blog_slugify($slug);
    $candidate = $base;
    $number = 2;
    $table = blog_tables()['posts'];

    while (true) {
        $sql = "SELECT id FROM {$table} WHERE slug = :slug AND post_type = :post_type";
        $data = ['slug' => $candidate, 'post_type' => $postType];
        if ($ignoreId !== null) {
            $sql .= ' AND id != :ignore_id';
            $data['ignore_id'] = $ignoreId;
        }
        $sql .= ' LIMIT 1';
        if (!blog_db()->get_row($sql, $data)) return $candidate;
        $candidate = $base . '-' . $number++;
    }
}

function blog_palette_presets(): array
{
    return [
        'indigo' => [
            'name' => 'Indigo Editorial',
            'colors' => [
                'primary' => '#4f46e5', 'primary-hover' => '#4338ca', 'secondary' => '#0f172a',
                'accent' => '#f59e0b', 'background' => '#f8fafc', 'surface' => '#ffffff',
                'text' => '#172033', 'muted' => '#64748b', 'border' => '#dbe3ef',
                'success' => '#15803d', 'warning' => '#b45309', 'danger' => '#b91c1c',
            ],
        ],
        'forest' => [
            'name' => 'Forest Journal',
            'colors' => [
                'primary' => '#166534', 'primary-hover' => '#14532d', 'secondary' => '#14291f',
                'accent' => '#d97706', 'background' => '#f5f7f2', 'surface' => '#ffffff',
                'text' => '#17251d', 'muted' => '#617066', 'border' => '#d9e1d8',
                'success' => '#15803d', 'warning' => '#a16207', 'danger' => '#b91c1c',
            ],
        ],
        'sunset' => [
            'name' => 'Copper Sunset',
            'colors' => [
                'primary' => '#c2410c', 'primary-hover' => '#9a3412', 'secondary' => '#431407',
                'accent' => '#f59e0b', 'background' => '#fff8f1', 'surface' => '#ffffff',
                'text' => '#3b2118', 'muted' => '#80665b', 'border' => '#ead7cb',
                'success' => '#15803d', 'warning' => '#b45309', 'danger' => '#b91c1c',
            ],
        ],
        'ocean' => [
            'name' => 'Deep Ocean',
            'colors' => [
                'primary' => '#0369a1', 'primary-hover' => '#075985', 'secondary' => '#082f49',
                'accent' => '#06b6d4', 'background' => '#f0f9ff', 'surface' => '#ffffff',
                'text' => '#0c2a3a', 'muted' => '#587586', 'border' => '#cce5f2',
                'success' => '#15803d', 'warning' => '#b45309', 'danger' => '#b91c1c',
            ],
        ],
        'charcoal' => [
            'name' => 'Charcoal',
            'colors' => [
                'primary' => '#a78bfa', 'primary-hover' => '#8b5cf6', 'secondary' => '#f8fafc',
                'accent' => '#fbbf24', 'background' => '#111827', 'surface' => '#1f2937',
                'text' => '#f8fafc', 'muted' => '#aeb9c8', 'border' => '#374151',
                'success' => '#4ade80', 'warning' => '#fbbf24', 'danger' => '#f87171',
            ],
        ],
    ];
}

function blog_sanitize_color(string $value, string $fallback): string
{
    $value = trim($value);
    $patterns = [
        '/^#[0-9a-f]{3,8}$/i',
        '/^rgba?\([0-9.% ,]+\)$/i',
        '/^hsla?\([0-9.% ,]+\)$/i',
    ];
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $value)) {
            return $value;
        }
    }
    return $fallback;
}

function blog_validate_palette(array $palette): array
{
    $defaults = blog_palette_presets()['indigo']['colors'];
    $result = [];
    foreach ($defaults as $key => $fallback) {
        $result[$key] = blog_sanitize_color((string) ($palette[$key] ?? $fallback), $fallback);
    }
    return $result;
}

function blog_global_palette(): array
{
    $presetKey = (string) blog_setting('palette', 'indigo');
    $presets = blog_palette_presets();
    $palette = $presets[$presetKey]['colors'] ?? $presets['indigo']['colors'];

    if ($presetKey === 'custom') {
        $custom = json_decode((string) blog_setting('custom_palette', ''), true);
        if (is_array($custom)) {
            $palette = $custom;
        }
    }

    return blog_validate_palette($palette);
}

function blog_post_palette(object|array|null $post = null): array
{
    $json = is_object($post) ? ($post->palette_json ?? '') : (is_array($post) ? ($post['palette_json'] ?? '') : '');
    $palette = json_decode((string) $json, true);
    return is_array($palette) ? blog_validate_palette($palette) : blog_global_palette();
}

function blog_palette_variables(array $palette): string
{
    $palette = blog_validate_palette($palette);
    $pairs = [];
    foreach ($palette as $key => $value) {
        $pairs[] = '--tb-color-' . $key . ':' . $value;
    }
    return implode(';', $pairs) . ';';
}

function blog_block_index(): array
{
    $file = plugin_path('blocks/index.json');
    $blocks = [];
    if (is_file($file)) {
        $decoded = json_decode((string) file_get_contents($file), true);
        $blocks = is_array($decoded) ? $decoded : [];
    }

    $blocks = do_filter('thunder_blog_block_index', $blocks);
    return array_values(array_filter($blocks, static fn ($block): bool => is_array($block) && !empty($block['slug'])));
}

function blog_block_manifest(string $slug): ?array
{
    $slug = preg_replace('/[^a-z0-9_-]/i', '', $slug) ?: '';
    if ($slug === '') {
        return null;
    }

    $file = plugin_path('blocks/' . $slug . '/block.json');
    if (!is_file($file)) {
        return null;
    }

    $manifest = json_decode((string) file_get_contents($file), true);
    if (!is_array($manifest) || ($manifest['slug'] ?? '') !== $slug) {
        return null;
    }

    return $manifest;
}

function blog_blocks_page(int $page = 1, int $perPage = 12, string $category = '', string $search = '', string $context = 'post'): array
{
    $page = max(1, $page);
    $perPage = max(1, min(48, $perPage));
    $category = trim($category);
    $search = strtolower(trim($search));
    $context = in_array($context, ['post', 'page', 'header', 'footer'], true) ? $context : 'post';
    $all = blog_block_index();

    $available = array_values(array_filter($all, static function (array $block) use ($context): bool {
        $contexts = array_values(array_filter(array_map('strval', (array) ($block['contexts'] ?? ['post', 'page']))));
        return in_array($context, $contexts, true);
    }));

    $filtered = array_values(array_filter($available, static function (array $block) use ($category, $search): bool {
        if ($category !== '' && strcasecmp((string) ($block['category'] ?? ''), $category) !== 0) {
            return false;
        }
        if ($search === '') {
            return true;
        }
        $haystack = strtolower(implode(' ', [
            (string) ($block['name'] ?? ''),
            (string) ($block['description'] ?? ''),
            (string) ($block['category'] ?? ''),
            implode(' ', (array) ($block['keywords'] ?? [])),
        ]));
        return str_contains($haystack, $search);
    }));

    $total = count($filtered);
    $pages = max(1, (int) ceil($total / $perPage));
    $page = min($page, $pages);
    $items = array_slice($filtered, ($page - 1) * $perPage, $perPage);
    $categories = array_values(array_unique(array_filter(array_map(static fn (array $b): string => (string) ($b['category'] ?? ''), $available))));
    sort($categories, SORT_NATURAL | SORT_FLAG_CASE);
    $category_counts = [];
    foreach ($available as $block) {
        $blockCategory = trim((string) ($block['category'] ?? ''));
        if ($blockCategory === '') continue;
        $category_counts[$blockCategory] = ($category_counts[$blockCategory] ?? 0) + 1;
    }
    $available_total = count($available);

    return compact('items', 'total', 'pages', 'page', 'perPage', 'categories', 'category_counts', 'available_total', 'context');
}

function blog_image_library_path(string $path = ''): string
{
    return plugin_path('assets/images/library/' . ltrim($path, '/'));
}

function blog_image_library_http(string $path = ''): string
{
    return plugin_http_path('assets/images/library/' . ltrim($path, '/'));
}

function blog_image_library(): array
{
    $root = blog_image_library_path();
    if (!is_dir($root)) {
        return [];
    }

    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'];
    $images = [];
    $categories = new \DirectoryIterator($root);

    foreach ($categories as $categoryDir) {
        if ($categoryDir->isDot() || !$categoryDir->isDir() || $categoryDir->isLink()) {
            continue;
        }

        $categorySlug = $categoryDir->getFilename();
        if (!preg_match('/^[A-Za-z0-9_-]+$/', $categorySlug)) {
            continue;
        }
        $categoryName = ucwords(str_replace(['-', '_'], ' ', $categorySlug));

        foreach (new \DirectoryIterator($categoryDir->getPathname()) as $file) {
            if ($file->isDot() || !$file->isFile() || $file->isLink()) {
                continue;
            }

            $extension = strtolower($file->getExtension());
            if (!in_array($extension, $allowed, true)) {
                continue;
            }

            $filename = $file->getFilename();
            $relative = $categorySlug . '/' . rawurlencode($filename);
            $title = pathinfo($filename, PATHINFO_FILENAME);
            $title = ucwords(str_replace(['-', '_'], ' ', $title));
            $width = 0;
            $height = 0;
            if ($extension !== 'svg') {
                $size = @getimagesize($file->getPathname());
                if (is_array($size)) {
                    $width = (int) ($size[0] ?? 0);
                    $height = (int) ($size[1] ?? 0);
                }
            }

            $images[] = [
                'name' => $title,
                'filename' => $filename,
                'category' => $categoryName,
                'category_slug' => $categorySlug,
                'url' => blog_image_library_http($relative),
                'width' => $width,
                'height' => $height,
            ];
        }
    }

    usort($images, static fn (array $a, array $b): int => strnatcasecmp($a['category'] . ' ' . $a['name'], $b['category'] . ' ' . $b['name']));
    return $images;
}

function blog_images_page(int $page = 1, int $perPage = 24, string $category = '', string $search = ''): array
{
    $page = max(1, $page);
    $perPage = max(1, min(60, $perPage));
    $category = trim($category);
    $search = strtolower(trim($search));
    $all = blog_image_library();

    $filtered = array_values(array_filter($all, static function (array $image) use ($category, $search): bool {
        if ($category !== '' && strcasecmp((string) ($image['category_slug'] ?? ''), $category) !== 0) {
            return false;
        }
        if ($search === '') {
            return true;
        }
        return str_contains(strtolower(implode(' ', [
            (string) ($image['name'] ?? ''),
            (string) ($image['filename'] ?? ''),
            (string) ($image['category'] ?? ''),
        ])), $search);
    }));

    $total = count($filtered);
    $pages = max(1, (int) ceil($total / $perPage));
    $page = min($page, $pages);
    $items = array_slice($filtered, ($page - 1) * $perPage, $perPage);
    $categoryMap = [];
    foreach ($all as $image) {
        $slug = (string) ($image['category_slug'] ?? '');
        if ($slug !== '') {
            $categoryMap[$slug] = (string) ($image['category'] ?? $slug);
        }
    }
    natcasesort($categoryMap);
    $categories = [];
    foreach ($categoryMap as $slug => $name) {
        $categories[] = ['slug' => $slug, 'name' => $name];
    }

    return compact('items', 'total', 'pages', 'page', 'perPage', 'categories');
}

function blog_sanitize_richtext(string $value): string
{
    $value = (string) preg_replace('#<(script|style)[^>]*>.*?</\1>#is', '', $value);
    $value = strip_tags($value, '<p><br><strong><b><em><i><u><a><ul><ol><li><span>');
    return (string) preg_replace_callback('/<(p|br|strong|b|em|i|u|a|ul|ol|li|span)([^>]*)>/i', static function (array $match): string {
        $tag = strtolower($match[1]);
        if ($tag !== 'a') {
            return '<' . $tag . '>';
        }

        $href = '';
        if (preg_match('/href\s*=\s*(["\'])(.*?)\1/i', $match[2], $hrefMatch)) {
            $candidate = trim($hrefMatch[2]);
            if (preg_match('#^(https?://|/|mailto:)#i', $candidate)) {
                $href = ' href="' . htmlspecialchars($candidate, ENT_QUOTES, 'UTF-8') . '"';
            }
        }
        return '<a' . $href . '>';
    }, $value);
}

function blog_property_value(array $field, mixed $value): string
{
    $type = (string) ($field['type'] ?? 'text');
    $value = is_scalar($value) ? (string) $value : '';

    if ($type === 'url' || $type === 'image') {
        $value = trim($value);
        if ($value !== '' && !preg_match('~^(https?://|/|#|mailto:|tel:|\{\{root\}\}(?:/|$)|data:image/(png|jpeg|gif|webp);base64,)~i', $value)) {
            $value = '';
        }
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    if ($type === 'menu') {
        $menuId = max(0, (int) $value);
        return (string) $menuId;
    }

    if ($type === 'color') {
        return blog_sanitize_color($value, '#000000');
    }

    if ($type === 'number' || $type === 'range') {
        $number = is_numeric($value) ? (float) $value : (float) ($field['default'] ?? 0);
        $min = isset($field['min']) ? (float) $field['min'] : $number;
        $max = isset($field['max']) ? (float) $field['max'] : $number;
        if (isset($field['min'])) {
            $number = max($min, $number);
        }
        if (isset($field['max'])) {
            $number = min($max, $number);
        }
        return (string) $number;
    }

    if ($type === 'select') {
        $allowed = array_map('strval', array_keys((array) ($field['options'] ?? [])));
        if ($allowed !== [] && !in_array($value, $allowed, true)) {
            $value = (string) ($field['default'] ?? $allowed[0]);
        }
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    if ($type === 'richtext') {
        return blog_sanitize_richtext($value);
    }

    if ($type === 'html') {
        return str_replace("\0", '', $value);
    }

    if ($type === 'shortcode') {
        return str_replace(['<', '>', "\0"], '', trim($value));
    }

    if ($type === 'textarea') {
        return nl2br(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
    }

    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function blog_raw_property_value(array $field, mixed $value): string
{
    $type = (string) ($field['type'] ?? 'text');
    $value = is_scalar($value) ? str_replace("\0", '', (string) $value) : '';

    if ($type === 'url' || $type === 'image') {
        $value = trim($value);
        return $value === '' || preg_match('~^(https?://|/|#|mailto:|tel:|\{\{root\}\}(?:/|$)|data:image/(png|jpeg|gif|webp);base64,)~i', $value) ? $value : '';
    }
    if ($type === 'menu') {
        return (string) max(0, (int) $value);
    }
    if ($type === 'color') {
        return blog_sanitize_color($value, (string) ($field['default'] ?? '#000000'));
    }
    if ($type === 'number' || $type === 'range') {
        $number = is_numeric($value) ? (float) $value : (float) ($field['default'] ?? 0);
        if (isset($field['min'])) $number = max((float) $field['min'], $number);
        if (isset($field['max'])) $number = min((float) $field['max'], $number);
        return (string) $number;
    }
    if ($type === 'select') {
        $allowed = array_map('strval', array_keys((array) ($field['options'] ?? [])));
        if ($allowed !== [] && !in_array($value, $allowed, true)) {
            return (string) ($field['default'] ?? $allowed[0]);
        }
        return $value;
    }
    if ($type === 'richtext') {
        return blog_sanitize_richtext($value);
    }
    if ($type === 'html') {
        return str_replace("\0", '', $value);
    }
    if ($type === 'shortcode') {
        return str_replace(['<', '>', "\0"], '', trim($value));
    }
    return trim($value);
}

function blog_manifest_settings(array $manifest): array
{
    $settings = $manifest['settings'] ?? [];
    return is_array($settings) ? $settings : [];
}

function blog_manifest_elements(array $manifest): array
{
    return array_values(array_filter((array) ($manifest['elements'] ?? []), static fn ($definition): bool => is_array($definition) && !empty($definition['key'])));
}


function blog_palette_token_names(): array
{
    return array_keys(blog_palette_presets()['indigo']['colors']);
}

function blog_normalize_palette_token(mixed $value): string
{
    $token = is_scalar($value) ? strtolower(trim((string) $value)) : '';
    return in_array($token, blog_palette_token_names(), true) ? $token : '';
}

function blog_normalize_font_size(mixed $value): string
{
    if (!is_scalar($value)) return '';
    $raw = strtolower(trim((string) $value));
    if ($raw === '') return '';
    $raw = preg_replace('/px$/i', '', $raw) ?? '';
    if (!is_numeric($raw)) return '';
    $number = max(6.0, min(240.0, (float) $raw));
    $formatted = rtrim(rtrim(number_format($number, 1, '.', ''), '0'), '.');
    return $formatted . 'px';
}

function blog_element_appearance_definition(array $definition): array
{
    $appearance = $definition['appearance'] ?? [];
    return is_array($appearance) ? $appearance : [];
}

function blog_normalize_element_appearance(array $definition, mixed $input): array
{
    $appearanceDefinition = blog_element_appearance_definition($definition);
    $input = is_array($input) ? $input : [];
    $defaults = is_array($appearanceDefinition['defaults'] ?? null) ? $appearanceDefinition['defaults'] : [];
    $result = [];
    if (!empty($appearanceDefinition['color'])) {
        $result['color'] = blog_normalize_palette_token($input['color'] ?? ($defaults['color'] ?? ''));
    }
    if (!empty($appearanceDefinition['background_color'])) {
        $result['background_color'] = blog_normalize_palette_token($input['background_color'] ?? ($defaults['background_color'] ?? ''));
    }
    $result['font_size'] = blog_normalize_font_size($input['font_size'] ?? ($defaults['font_size'] ?? ''));
    return $result;
}

function blog_inject_element_appearance(string $markup, array $definition, array $item): string
{
    $appearance = blog_normalize_element_appearance($definition, $item['appearance'] ?? []);
    $attributes = [];
    $style = [];
    $color = (string) ($appearance['color'] ?? '');
    $background = (string) ($appearance['background_color'] ?? '');
    $fontSize = (string) ($appearance['font_size'] ?? '');
    if ($color !== '') {
        $attributes[] = 'data-tb-element-color="' . htmlspecialchars($color, ENT_QUOTES, 'UTF-8') . '"';
        $style[] = '--tb-element-color:var(--tb-color-' . $color . ')';
    }
    if ($background !== '') {
        $attributes[] = 'data-tb-element-background="' . htmlspecialchars($background, ENT_QUOTES, 'UTF-8') . '"';
        $style[] = '--tb-element-background:var(--tb-color-' . $background . ')';
    }
    if ($fontSize !== '') {
        $attributes[] = 'data-tb-element-font-size="' . htmlspecialchars($fontSize, ENT_QUOTES, 'UTF-8') . '"';
        $style[] = '--tb-element-font-size:' . htmlspecialchars($fontSize, ENT_QUOTES, 'UTF-8');
    }
    $cloneGroup = preg_replace('/[^a-z0-9_-]/i', '', (string) ($item['clone_group'] ?? '')) ?: '';
    if ($cloneGroup !== '') {
        $attributes[] = 'data-tb-clone-group="' . htmlspecialchars($cloneGroup, ENT_QUOTES, 'UTF-8') . '"';
    }
    if ($style !== []) {
        $attributes[] = 'style="' . htmlspecialchars(implode(';', $style), ENT_QUOTES, 'UTF-8') . '"';
    }
    if ($attributes === []) return $markup;
    return (string) preg_replace('/^(\s*<[a-zA-Z][\w:-]*)(?=[\s>])/', '$1 ' . implode(' ', $attributes), $markup, 1);
}

function blog_element_palette_css(): string
{
    return '[data-tb-element-color]{color:var(--tb-element-color)!important}[data-tb-element-color] *{color:inherit!important}[data-tb-element-background]{background-color:var(--tb-element-background)!important;background-image:none!important}[data-tb-element-font-size],[data-tb-element-font-size] *{font-size:var(--tb-element-font-size)!important}';
}

function blog_default_element_items(array $definition, string $instanceId): array
{
    $defaults = is_array($definition['defaults'] ?? null) ? $definition['defaults'] : [];
    $source = is_array($definition['default_items'] ?? null) && $definition['default_items'] !== []
        ? array_values($definition['default_items'])
        : [$defaults];
    $minimum = isset($definition['min']) ? max(0, (int) $definition['min']) : 1;
    $maximum = isset($definition['max']) ? max($minimum, (int) $definition['max']) : count($source);
    $items = [];
    foreach (array_slice($source, 0, max(1, $maximum)) as $position => $values) {
        $items[] = [
            'id' => $instanceId . '-' . preg_replace('/[^a-z0-9_-]/i', '', (string) $definition['key']) . '-' . ($position + 1),
            'values' => array_merge($defaults, is_array($values) ? $values : []),
            'appearance' => blog_normalize_element_appearance($definition, []),
            'clone_group' => '',
        ];
    }
    while (count($items) < $minimum) {
        $items[] = [
            'id' => $instanceId . '-' . preg_replace('/[^a-z0-9_-]/i', '', (string) $definition['key']) . '-' . (count($items) + 1),
            'values' => $defaults,
            'appearance' => blog_normalize_element_appearance($definition, []),
            'clone_group' => '',
        ];
    }
    if (empty($definition['repeatable'])) {
        $items = array_slice($items, 0, 1);
    }
    return $items;
}

function blog_normalize_block_document(array $manifest, array $block, string $instanceId): array
{
    $settingsDefinition = blog_manifest_settings($manifest);
    $settingsInput = is_array($block['settings'] ?? null) ? $block['settings'] : [];
    $settings = [];
    foreach ((array) ($settingsDefinition['schema'] ?? []) as $field) {
        if (!is_array($field) || empty($field['key'])) continue;
        $key = (string) $field['key'];
        $fallback = $settingsDefinition['defaults'][$key] ?? ($field['default'] ?? '');
        $settings[$key] = blog_raw_property_value($field, $settingsInput[$key] ?? $fallback);
    }

    $elementInput = is_array($block['elements'] ?? null) ? $block['elements'] : [];
    $elements = [];
    foreach (blog_manifest_elements($manifest) as $definition) {
        $key = (string) $definition['key'];
        $items = is_array($elementInput[$key] ?? null) ? array_values($elementInput[$key]) : blog_default_element_items($definition, $instanceId);
        $minimum = isset($definition['min']) ? max(0, (int) $definition['min']) : 1;
        $maximum = isset($definition['max']) ? max($minimum, (int) $definition['max']) : PHP_INT_MAX;
        if (empty($definition['repeatable'])) $maximum = 1;
        $normalizedItems = [];
        foreach (array_slice($items, 0, $maximum) as $position => $item) {
            if (!is_array($item)) continue;
            $itemId = preg_replace('/[^a-z0-9_-]/i', '', (string) ($item['id'] ?? '')) ?: ($instanceId . '-' . $key . '-' . ($position + 1));
            $inputValues = is_array($item['values'] ?? null) ? $item['values'] : [];
            $values = [];
            foreach ((array) ($definition['schema'] ?? []) as $field) {
                if (!is_array($field) || empty($field['key'])) continue;
                $fieldKey = (string) $field['key'];
                $fallback = $definition['defaults'][$fieldKey] ?? ($field['default'] ?? '');
                $values[$fieldKey] = blog_raw_property_value($field, $inputValues[$fieldKey] ?? $fallback);
            }
            $normalizedItems[] = [
                'id' => $itemId,
                'values' => $values,
                'appearance' => blog_normalize_element_appearance($definition, $item['appearance'] ?? []),
                'clone_group' => preg_replace('/[^a-z0-9_-]/i', '', (string) ($item['clone_group'] ?? '')) ?: '',
            ];
        }
        while (count($normalizedItems) < $minimum) {
            $defaults = blog_default_element_items($definition, $instanceId);
            $candidate = $defaults[count($normalizedItems)] ?? $defaults[0] ?? [
                'id' => $instanceId . '-' . $key . '-' . (count($normalizedItems) + 1),
                'values' => [],
                'appearance' => blog_normalize_element_appearance($definition, []),
                'clone_group' => '',
            ];
            $candidate['id'] = $instanceId . '-' . $key . '-' . (count($normalizedItems) + 1);
            $normalizedItems[] = $candidate;
        }
        $elements[$key] = $normalizedItems;
    }

    $cloneCounts = [];
    foreach ($elements as $items) {
        foreach ($items as $item) {
            $cloneGroup = (string) ($item['clone_group'] ?? '');
            if ($cloneGroup !== '') $cloneCounts[$cloneGroup] = ($cloneCounts[$cloneGroup] ?? 0) + 1;
        }
    }
    $cloneState = [];
    foreach ($elements as &$items) {
        foreach ($items as &$item) {
            $cloneGroup = (string) ($item['clone_group'] ?? '');
            if ($cloneGroup === '' || ($cloneCounts[$cloneGroup] ?? 0) < 2) {
                $item['clone_group'] = '';
                continue;
            }
            if (!isset($cloneState[$cloneGroup])) {
                $cloneState[$cloneGroup] = [
                    'values' => $item['values'],
                    'appearance' => $item['appearance'],
                ];
                continue;
            }
            $item['values'] = $cloneState[$cloneGroup]['values'];
            $item['appearance'] = $cloneState[$cloneGroup]['appearance'];
        }
        unset($item);
    }
    unset($items);

    return [
        'id' => $instanceId,
        'slug' => (string) ($manifest['slug'] ?? ''),
        'settings' => $settings,
        'elements' => $elements,
    ];
}

function blog_replace_tokens(string $template, array $replace): string
{
    return strtr($template, $replace);
}

function blog_render_block(array $manifest, array $block, string $instanceId): array
{
    $normalized = blog_normalize_block_document($manifest, $block, $instanceId);
    $settingsDefinition = blog_manifest_settings($manifest);
    $replace = [
        '{{instance_id}}' => htmlspecialchars($instanceId, ENT_QUOTES, 'UTF-8'),
        '{{app_name}}' => htmlspecialchars(defined('APP_NAME') ? (string) APP_NAME : 'Website', ENT_QUOTES, 'UTF-8'),
        '{{app_description}}' => htmlspecialchars(defined('APP_DESCRIPTION') ? (string) APP_DESCRIPTION : '', ENT_QUOTES, 'UTF-8'),
        '{{root}}' => htmlspecialchars(defined('ROOT') ? (string) ROOT : '', ENT_QUOTES, 'UTF-8'),
        '{{app_logo}}' => htmlspecialchars(defined('APP_LOGO') && APP_LOGO && function_exists('get_image') ? (string) get_image(APP_LOGO) : '', ENT_QUOTES, 'UTF-8'),
        '{{year}}' => date('Y'),
    ];
    foreach ((array) ($settingsDefinition['schema'] ?? []) as $field) {
        if (!is_array($field) || empty($field['key'])) continue;
        $key = (string) $field['key'];
        $replace['{{' . $key . '}}'] = blog_property_value($field, $normalized['settings'][$key] ?? '');
    }

    foreach (blog_manifest_elements($manifest) as $definition) {
        $key = (string) $definition['key'];
        $parts = [];
        foreach ((array) ($normalized['elements'][$key] ?? []) as $item) {
            $local = ['{{element_id}}' => htmlspecialchars((string) ($item['id'] ?? ''), ENT_QUOTES, 'UTF-8')];
            foreach ((array) ($definition['schema'] ?? []) as $field) {
                if (!is_array($field) || empty($field['key'])) continue;
                $fieldKey = (string) $field['key'];
                $local['{{' . $fieldKey . '}}'] = blog_property_value($field, $item['values'][$fieldKey] ?? '');
            }
            $markup = blog_replace_tokens((string) ($definition['template'] ?? ''), $local);
            $parts[] = blog_inject_element_appearance($markup, $definition, $item);
        }
        $replace['{{elements:' . $key . '}}'] = implode('', $parts);
    }

    foreach ((array) ($manifest['menu_sources'] ?? []) as $source) {
        if (!is_array($source)) continue;
        $token = preg_replace('/[^a-z0-9_-]/i', '', (string) ($source['token'] ?? 'menu')) ?: 'menu';
        $setting = (string) ($source['setting'] ?? 'menu_id');
        $menuId = max(0, (int) ($normalized['settings'][$setting] ?? 0));
        $replace['{{menu:' . $token . '}}'] = blog_render_manifest_menu($source, $menuId, $instanceId);
    }

    $html = blog_replace_tokens((string) ($manifest['html'] ?? ''), $replace);
    $renderMode = (string) ($manifest['render_mode'] ?? '');
    if (in_array($renderMode, ['html', 'shortcode'], true)) {
        $field = $renderMode === 'html' ? 'code' : 'shortcode';
        $firstDefinition = blog_manifest_elements($manifest)[0] ?? [];
        $firstKey = (string) ($firstDefinition['key'] ?? 'content');
        $firstItem = $normalized['elements'][$firstKey][0] ?? [];
        $html = (string) ($firstItem['values'][$field] ?? '');
        if ($renderMode === 'shortcode') {
            $html = str_replace(['<', '>'], '', trim($html));
        } else {
            $html = blog_replace_tokens($html, $replace);
        }
    }

    $html = blog_replace_tokens($html, [
        '{{app_name}}' => htmlspecialchars(defined('APP_NAME') ? (string) APP_NAME : 'Website', ENT_QUOTES, 'UTF-8'),
        '{{app_description}}' => htmlspecialchars(defined('APP_DESCRIPTION') ? (string) APP_DESCRIPTION : '', ENT_QUOTES, 'UTF-8'),
        '{{root}}' => htmlspecialchars(defined('ROOT') ? (string) ROOT : '', ENT_QUOTES, 'UTF-8'),
        '{{app_logo}}' => htmlspecialchars(defined('APP_LOGO') && APP_LOGO && function_exists('get_image') ? (string) get_image(APP_LOGO) : '', ENT_QUOTES, 'UTF-8'),
        '{{year}}' => date('Y'),
    ]);
    $css = blog_replace_tokens((string) ($manifest['css'] ?? ''), $replace);
    $js = strtr((string) ($manifest['js'] ?? ''), ['{{instance_id}}' => addslashes($instanceId)]);
    return compact('html', 'css', 'js', 'normalized');
}

function blog_compile_blocks(array $blocks, string $context = 'post'): array
{
    $context = in_array($context, ['post', 'page', 'header', 'footer'], true) ? $context : 'post';
    $html = [];
    $css = [];
    $js = [];
    $normalized = [];

    foreach ($blocks as $position => $block) {
        if (!is_array($block)) continue;
        $slug = preg_replace('/[^a-z0-9_-]/i', '', (string) ($block['slug'] ?? '')) ?: '';
        $manifest = blog_block_manifest($slug);
        if (!$manifest) continue;
        $contexts = array_values(array_filter(array_map('strval', (array) ($manifest['contexts'] ?? ['post', 'page']))));
        if (!in_array($context, $contexts, true)) continue;
        $instanceId = preg_replace('/[^a-z0-9_-]/i', '', (string) ($block['id'] ?? '')) ?: ('block-' . ($position + 1));
        $rendered = blog_render_block($manifest, $block, $instanceId);
        $html[] = '<section class="tb-content-block tb-content-block--' . htmlspecialchars($slug, ENT_QUOTES, 'UTF-8') . '" data-tb-instance="' . htmlspecialchars($instanceId, ENT_QUOTES, 'UTF-8') . '">' . $rendered['html'] . '</section>';
        $css[$slug] = $rendered['css'];
        if (trim($rendered['js']) !== '') $js[$slug] = $rendered['js'];
        $normalized[] = $rendered['normalized'];
    }

    $compiled = [
        'blocks' => $normalized,
        'html' => implode("\n", $html),
        'css' => blog_element_palette_css() . "\n" . implode("\n", $css),
        'js' => implode("\n", $js),
    ];
    return do_filter('thunder_blog_compiled_post', $compiled);
}

function blog_block_preview_document(array $manifest, array $palette): string
{
    $preview = blog_normalize_block_document($manifest, [], 'preview');
    $rendered = blog_render_block($manifest, $preview, 'preview');
    $style = blog_palette_variables($palette);
    $script = str_ireplace('</script', '<\/script', $rendered['js']);
    return '<!doctype html><html class="tb-preview-root"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><style>' . blog_element_palette_css() . '.tb-preview-root{background:var(--tb-color-background);color:var(--tb-color-text);font-family:Arial,sans-serif}.tb-preview-body{margin:0;padding:12px;box-sizing:border-box}' . $rendered['css'] . '</style></head><body class="tb-preview-body" style="' . htmlspecialchars($style, ENT_QUOTES, 'UTF-8') . '">' . $rendered['html'] . '<script>' . $script . '</script></body></html>';
}

function blog_get_post_by_id(int $id): object|false
{
    $tables = blog_tables();
    return blog_db()->get_row(
        "SELECT p.*, c.name AS category_name, c.slug AS category_slug FROM {$tables['posts']} p LEFT JOIN {$tables['categories']} c ON c.id = p.category_id WHERE p.id = :id LIMIT 1",
        ['id' => $id]
    );
}

function blog_get_post_by_slug(string $slug, bool $admin = false, string $postType = 'post'): object|false
{
    $tables = blog_tables();
    $postType = in_array($postType, ['post', 'page', 'header', 'footer'], true) ? $postType : 'post';
    $sql = "SELECT p.*, c.name AS category_name, c.slug AS category_slug FROM {$tables['posts']} p LEFT JOIN {$tables['categories']} c ON c.id = p.category_id WHERE p.slug = :slug AND p.post_type = :post_type";
    $data = ['slug' => $slug, 'post_type' => $postType];
    if (!$admin) {
        $sql .= " AND (p.status = 'published' OR (p.status = 'scheduled' AND p.published_at <= CURRENT_TIMESTAMP)) AND (p.published_at IS NULL OR p.published_at <= CURRENT_TIMESTAMP)";
    }
    return blog_db()->get_row($sql . ' LIMIT 1', $data);
}

function blog_get_posts(array $options = []): array
{
    $tables = blog_tables();
    $defaults = [
        'page' => 1,
        'per_page' => (int) blog_setting('posts_per_page', 9),
        'status' => 'published',
        'post_type' => 'post',
        'category_slug' => '',
        'tag_slug' => '',
        'search' => '',
        'admin' => false,
        'order' => 'latest',
        'featured' => false,
    ];
    $options = array_merge($defaults, do_filter('thunder_blog_post_query', array_merge($defaults, $options)));
    $page = max(1, (int) $options['page']);
    $perPage = max(1, min(100, (int) $options['per_page']));
    $postType = in_array((string) $options['post_type'], ['post', 'page', 'header', 'footer'], true) ? (string) $options['post_type'] : 'post';
    $where = ['p.post_type = :post_type'];
    $data = ['post_type' => $postType];
    $joins = " LEFT JOIN {$tables['categories']} c ON c.id = p.category_id";

    if (!$options['admin']) {
        $where[] = "(p.status = 'published' OR (p.status = 'scheduled' AND p.published_at <= CURRENT_TIMESTAMP))";
        $where[] = '(p.published_at IS NULL OR p.published_at <= CURRENT_TIMESTAMP)';
    } elseif ((string) $options['status'] !== '' && (string) $options['status'] !== 'all') {
        $where[] = 'p.status = :status';
        $data['status'] = (string) $options['status'];
    }

    if (!empty($options['featured'])) {
        $where[] = 'p.is_featured = 1';
    }
    if ((string) $options['category_slug'] !== '') {
        $where[] = 'c.slug = :category_slug';
        $data['category_slug'] = (string) $options['category_slug'];
    }
    if ((string) $options['tag_slug'] !== '') {
        $joins .= " INNER JOIN {$tables['post_tags']} pt ON pt.post_id = p.id INNER JOIN {$tables['tags']} t ON t.id = pt.tag_id";
        $where[] = 't.slug = :tag_slug';
        $data['tag_slug'] = (string) $options['tag_slug'];
    }
    if ((string) $options['search'] !== '') {
        $where[] = '(p.title LIKE :search OR p.excerpt LIKE :search OR p.slug LIKE :search)';
        $data['search'] = '%' . (string) $options['search'] . '%';
    }

    $order = (string) $options['order'];
    if ($order === 'popular') {
        $where[] = 'COALESCE(p.published_at, p.created_at) >= DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 30 DAY)';
        $orderSql = 'p.view_count DESC, COALESCE(p.published_at, p.created_at) DESC, p.id DESC';
    } elseif ($order === 'popular_week') {
        $where[] = 'COALESCE(p.published_at, p.created_at) >= DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 7 DAY)';
        $orderSql = 'p.view_count DESC, COALESCE(p.published_at, p.created_at) DESC, p.id DESC';
    } elseif ($order === 'most_viewed') {
        $orderSql = 'p.view_count DESC, COALESCE(p.published_at, p.created_at) DESC, p.id DESC';
    } elseif ($order === 'featured') {
        $where[] = 'p.is_featured = 1';
        $orderSql = 'COALESCE(p.published_at, p.created_at) DESC, p.id DESC';
    } elseif ($order === 'oldest') {
        $orderSql = 'COALESCE(p.published_at, p.created_at) ASC, p.id ASC';
    } else {
        $orderSql = 'COALESCE(p.published_at, p.created_at) DESC, p.id DESC';
    }

    $whereSql = ' WHERE ' . implode(' AND ', $where);
    $countRow = blog_db()->get_row("SELECT COUNT(DISTINCT p.id) AS total FROM {$tables['posts']} p {$joins} {$whereSql}", $data);
    $total = (int) ($countRow->total ?? 0);
    $pages = max(1, (int) ceil($total / $perPage));
    $page = min($page, $pages);
    $offset = ($page - 1) * $perPage;

    $rows = blog_db()->query(
        "SELECT DISTINCT p.*, c.name AS category_name, c.slug AS category_slug FROM {$tables['posts']} p {$joins} {$whereSql} ORDER BY {$orderSql} LIMIT {$perPage} OFFSET {$offset}",
        $data
    );

    return [
        'items' => is_array($rows) ? $rows : [],
        'total' => $total,
        'pages' => $pages,
        'page' => $page,
        'per_page' => $perPage,
    ];
}

function blog_categories(): array
{
    $table = blog_tables()['categories'];
    $rows = blog_db()->query("SELECT c.*, (SELECT COUNT(*) FROM " . blog_tables()['posts'] . " p WHERE p.category_id = c.id AND p.post_type = 'post') AS post_count FROM {$table} c ORDER BY c.name ASC");
    return is_array($rows) ? $rows : [];
}

function blog_tags_for_post(int $postId): array
{
    $tables = blog_tables();
    $rows = blog_db()->query("SELECT t.* FROM {$tables['tags']} t INNER JOIN {$tables['post_tags']} pt ON pt.tag_id = t.id WHERE pt.post_id = :post_id ORDER BY t.name ASC", ['post_id' => $postId]);
    return is_array($rows) ? $rows : [];
}

function blog_tags_text(int $postId): string
{
    return implode(', ', array_map(static fn (object $tag): string => (string) $tag->name, blog_tags_for_post($postId)));
}

function blog_normalize_datetime(string $value): ?string
{
    $value = trim($value);
    if ($value === '') {
        return null;
    }
    $timestamp = strtotime($value);
    return $timestamp === false ? null : date('Y-m-d H:i:s', $timestamp);
}

function blog_resize_featured_image(string $filename, int $maxSize = 1000): void
{
    if (!class_exists('Core\\Image')) {
        throw new \RuntimeException('ThunderPHP\'s Image class is unavailable.');
    }

    $image = new \Core\Image();
    if (method_exists($image, 'allow_upscale')) {
        $image->allow_upscale(false);
    }
    if (method_exists($image, 'set_jpeg_quality')) {
        $image->set_jpeg_quality(85);
    }
    if (method_exists($image, 'set_png_compression')) {
        $image->set_png_compression(7);
    }
    if (method_exists($image, 'set_webp_quality')) {
        $image->set_webp_quality(82);
    }
    $image->resize($filename, $maxSize);
}

function blog_handle_featured_image(string $field = 'featured_image_file'): string
{
    if (empty($_FILES[$field]) || !is_array($_FILES[$field]) || (int) ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return '';
    }

    $file = $_FILES[$field];
    if ((int) ($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK || (int) ($file['size'] ?? 0) > 8 * 1024 * 1024) {
        throw new \RuntimeException('The featured image could not be uploaded or is larger than 8 MB.');
    }

    $finfo = new \finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file((string) $file['tmp_name']);
    $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
    if (!isset($extensions[$mime])) {
        throw new \RuntimeException('The featured image must be a JPEG, PNG, WebP or GIF file.');
    }

    $relativeDir = 'assets/uploads/' . date('Y/m');
    $targetDir = plugin_path($relativeDir);
    if (!is_dir($targetDir) && !mkdir($targetDir, 0775, true) && !is_dir($targetDir)) {
        throw new \RuntimeException('The blog upload directory is not writable.');
    }

    $filename = bin2hex(random_bytes(12)) . '.' . $extensions[$mime];
    $target = $targetDir . '/' . $filename;
    if (!move_uploaded_file((string) $file['tmp_name'], $target)) {
        throw new \RuntimeException('The featured image could not be moved into the blog upload directory.');
    }

    try {
        blog_resize_featured_image($target, 1000);
    } catch (\Throwable $e) {
        @unlink($target);
        throw new \RuntimeException('The featured image was uploaded but could not be resized: ' . $e->getMessage(), 0, $e);
    }

    return plugin_http_path($relativeDir . '/' . $filename);
}

function blog_save_post(array $input, ?int $id = null): int
{
    $postType = (string) ($input['post_type'] ?? 'post');
    if (!in_array($postType, ['post', 'page', 'header', 'footer'], true)) {
        $postType = 'post';
    }

    $title = trim((string) ($input['title'] ?? ''));
    if ($title === '') {
        throw new \InvalidArgumentException(ucfirst($postType) . ' title is required.');
    }

    $blocks = json_decode((string) ($input['blocks_json'] ?? '[]'), true);
    if (!is_array($blocks)) {
        throw new \InvalidArgumentException('The block document is invalid.');
    }
    $compiled = blog_compile_blocks($blocks, $postType);

    $palette = json_decode((string) ($input['palette_json'] ?? ''), true);
    $palette = is_array($palette) ? blog_validate_palette($palette) : blog_global_palette();

    $existing = $id !== null ? blog_get_post_by_id($id) : false;
    if ($id !== null && !$existing) {
        throw new \RuntimeException('The requested content no longer exists.');
    }
    if ($existing) {
        $postType = in_array((string) ($existing->post_type ?? ''), ['post', 'page', 'header', 'footer'], true)
            ? (string) $existing->post_type
            : $postType;
    }

    $status = (string) ($input['status'] ?? 'draft');
    if (!in_array($status, ['draft', 'published', 'scheduled', 'archived'], true)) {
        $status = 'draft';
    }
    if (in_array($status, ['published', 'scheduled'], true) && !blog_can('publish-blog-posts') && $postType === 'post') {
        $status = 'draft';
    }

    $publishedAt = blog_normalize_datetime((string) ($input['published_at'] ?? ''));
    if ($status === 'published' && $publishedAt === null) {
        $publishedAt = date('Y-m-d H:i:s');
    }
    if ($status === 'scheduled' && $publishedAt === null) {
        throw new \InvalidArgumentException('Scheduled content needs a publication date.');
    }

    $slugInput = trim((string) ($input['slug'] ?? '')) ?: $title;
    if ($postType === 'page' && in_array(blog_slugify($slugInput), ['blog', 'admin', 'login', 'logout', 'signup', 'account', '404'], true)) {
        throw new \InvalidArgumentException('That page slug is reserved by the application.');
    }
    $slug = blog_unique_slug($slugInput, $id, $postType);
    $categoryId = (int) ($input['category_id'] ?? 0);
    $categoryId = $categoryId > 0 ? $categoryId : null;
    $featured = trim((string) ($input['featured_image'] ?? ''));
    $uploaded = blog_handle_featured_image();
    if ($uploaded !== '') {
        $featured = $uploaded;
    }

    $authorId = $existing && trim((string) $existing->author_id) !== ''
        ? (string) $existing->author_id
        : blog_current_user_id();

    $tables = blog_tables();
    $data = [
        'author_id' => $authorId,
        'category_id' => $categoryId,
        'title' => $title,
        'slug' => $slug,
        'excerpt' => trim((string) ($input['excerpt'] ?? '')),
        'featured_image' => $featured,
        'status' => $status,
        'post_type' => $postType,
        'is_featured' => isset($input['is_featured']) ? 1 : 0,
        'blocks_json' => json_encode($compiled['blocks'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        'content_html' => $compiled['html'],
        'content_css' => $compiled['css'],
        'content_js' => $compiled['js'],
        'palette_json' => json_encode($palette, JSON_UNESCAPED_SLASHES),
        'seo_title' => trim((string) ($input['seo_title'] ?? '')),
        'seo_description' => trim((string) ($input['seo_description'] ?? '')),
        'published_at' => $publishedAt,
    ];

    $db = blog_db();
    $pdo = $db->getConnection();
    $pdo->beginTransaction();
    try {
        if ($id === null) {
            $db->query("INSERT INTO {$tables['posts']} (author_id, category_id, title, slug, excerpt, featured_image, status, post_type, is_featured, blocks_json, content_html, content_css, content_js, palette_json, seo_title, seo_description, published_at, created_at, updated_at) VALUES (:author_id, :category_id, :title, :slug, :excerpt, :featured_image, :status, :post_type, :is_featured, :blocks_json, :content_html, :content_css, :content_js, :palette_json, :seo_title, :seo_description, :published_at, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)", $data);
            if ($db->has_error) {
                throw new \RuntimeException($db->error ?: 'The content could not be created.');
            }
            $id = (int) $db->insert_id;
        } else {
            $data['id'] = $id;
            $db->query("UPDATE {$tables['posts']} SET author_id = :author_id, category_id = :category_id, title = :title, slug = :slug, excerpt = :excerpt, featured_image = :featured_image, status = :status, post_type = :post_type, is_featured = :is_featured, blocks_json = :blocks_json, content_html = :content_html, content_css = :content_css, content_js = :content_js, palette_json = :palette_json, seo_title = :seo_title, seo_description = :seo_description, published_at = :published_at, updated_at = CURRENT_TIMESTAMP WHERE id = :id", $data);
            if ($db->has_error) {
                throw new \RuntimeException($db->error ?: 'The content could not be updated.');
            }
        }

        if (in_array($postType, ['post', 'page'], true)) {
            blog_sync_tags((int) $id, (string) ($input['tags'] ?? ''), $db);
        } else {
            $db->query("DELETE FROM {$tables['post_tags']} WHERE post_id = :post_id", ['post_id' => (int) $id]);
        }
        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }

    do_action('thunder_blog_post_saved', ['post_id' => (int) $id, 'is_new' => !$existing, 'post_type' => $postType]);
    return (int) $id;
}

function blog_sync_tags(int $postId, string $tagText, ?Database $db = null): void
{
    $db ??= blog_db();
    $tables = blog_tables();
    $db->query("DELETE FROM {$tables['post_tags']} WHERE post_id = :post_id", ['post_id' => $postId]);
    $names = array_values(array_unique(array_filter(array_map('trim', preg_split('/[,\n]+/', $tagText) ?: []))));

    foreach (array_slice($names, 0, 40) as $name) {
        $slug = blog_slugify($name);
        $tag = $db->get_row("SELECT id FROM {$tables['tags']} WHERE slug = :slug LIMIT 1", ['slug' => $slug]);
        if (!$tag) {
            $db->query("INSERT INTO {$tables['tags']} (name, slug, created_at) VALUES (:name, :slug, CURRENT_TIMESTAMP)", ['name' => blog_text_substr($name, 0, 100), 'slug' => $slug]);
            $tagId = (int) $db->insert_id;
        } else {
            $tagId = (int) $tag->id;
        }
        $db->query("INSERT INTO {$tables['post_tags']} (post_id, tag_id) VALUES (:post_id, :tag_id)", ['post_id' => $postId, 'tag_id' => $tagId]);
    }
}

function blog_delete_post(int $id): bool
{
    $tables = blog_tables();
    $db = blog_db();
    $db->query("DELETE FROM {$tables['post_tags']} WHERE post_id = :id", ['id' => $id]);
    $ok = $db->query("DELETE FROM {$tables['posts']} WHERE id = :id", ['id' => $id]);
    if ($ok) {
        do_action('thunder_blog_post_deleted', ['post_id' => $id]);
    }
    return (bool) $ok;
}

function blog_save_category(array $input): int
{
    $tables = blog_tables();
    $id = (int) ($input['category_id'] ?? 0);
    $name = trim((string) ($input['name'] ?? ''));
    if ($name === '') {
        throw new \InvalidArgumentException('A category name is required.');
    }
    $slug = blog_slugify((string) ($input['slug'] ?? '') ?: $name);
    $description = trim((string) ($input['description'] ?? ''));
    $db = blog_db();

    $duplicate = $db->get_row("SELECT id FROM {$tables['categories']} WHERE slug = :slug AND id != :id LIMIT 1", ['slug' => $slug, 'id' => $id]);
    if ($duplicate) {
        throw new \InvalidArgumentException('That category slug is already in use.');
    }

    if ($id > 0) {
        $db->query("UPDATE {$tables['categories']} SET name = :name, slug = :slug, description = :description, updated_at = CURRENT_TIMESTAMP WHERE id = :id", compact('name', 'slug', 'description', 'id'));
        return $id;
    }

    $db->query("INSERT INTO {$tables['categories']} (name, slug, description, created_at, updated_at) VALUES (:name, :slug, :description, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)", compact('name', 'slug', 'description'));
    return (int) $db->insert_id;
}

function blog_delete_category(int $id): bool
{
    $tables = blog_tables();
    $db = blog_db();
    $db->query("UPDATE {$tables['posts']} SET category_id = NULL WHERE category_id = :id", ['id' => $id]);
    return (bool) $db->query("DELETE FROM {$tables['categories']} WHERE id = :id", ['id' => $id]);
}

function blog_scan_looks(): array
{
    $looks = [];
    foreach (glob(plugin_path('looks/*/look.json')) ?: [] as $file) {
        $manifest = json_decode((string) file_get_contents($file), true);
        if (!is_array($manifest)) {
            continue;
        }
        $slug = basename(dirname($file));
        $looks[$slug] = $manifest + ['name' => ucwords(str_replace('-', ' ', $slug))];
    }
    return $looks;
}

function blog_post_url(object $post): string
{
    $type = (string) ($post->post_type ?? 'post');
    if ($type === 'page') {
        return ROOT . '/' . rawurlencode((string) $post->slug);
    }
    if ($type === 'post') {
        return ROOT . '/blog/' . rawurlencode((string) $post->slug);
    }
    return ROOT . '/';
}

function blog_page_number(): int
{
    return max(1, (int) ($_GET['p'] ?? 1));
}

function blog_text_length(string $value): int
{
    return function_exists('mb_strlen') ? mb_strlen($value) : strlen($value);
}

function blog_text_substr(string $value, int $start, ?int $length = null): string
{
    if (function_exists('mb_substr')) {
        return $length === null ? mb_substr($value, $start) : mb_substr($value, $start, $length);
    }
    return $length === null ? substr($value, $start) : substr($value, $start, $length);
}

function blog_excerpt(object $post, int $length = 180): string
{
    $excerpt = trim((string) ($post->excerpt ?? ''));
    if ($excerpt === '') {
        $excerpt = trim(strip_tags((string) ($post->content_html ?? '')));
    }
    if (blog_text_length($excerpt) <= $length) {
        return $excerpt;
    }
    return rtrim(blog_text_substr($excerpt, 0, $length - 1)) . '…';
}


function blog_content_type_label(string $postType, bool $plural = false): string
{
    $labels = [
        'post' => ['Post', 'Posts'],
        'page' => ['Page', 'Pages'],
        'header' => ['Header', 'Headers'],
        'footer' => ['Footer', 'Footers'],
    ];
    return $labels[$postType][$plural ? 1 : 0] ?? ($plural ? 'Content' : 'Content');
}

function blog_admin_list_url(string $postType): string
{
    return match ($postType) {
        'page' => ROOT . '/admin/blog/pages',
        'header', 'footer' => ROOT . '/admin/blog/designs',
        default => ROOT . '/admin/blog',
    };
}

function blog_admin_create_url(string $postType): string
{
    return match ($postType) {
        'page' => ROOT . '/admin/blog/pages/create',
        'header', 'footer' => ROOT . '/admin/blog/designs/create/' . $postType,
        default => ROOT . '/admin/blog/create',
    };
}

function blog_admin_edit_url(object|int $post): string
{
    $item = is_object($post) ? $post : blog_get_post_by_id((int) $post);
    if (!$item) {
        return ROOT . '/admin/blog';
    }
    $type = (string) ($item->post_type ?? 'post');
    return match ($type) {
        'page' => ROOT . '/admin/blog/pages/' . (int) $item->id . '/edit',
        'header', 'footer' => ROOT . '/admin/blog/designs/' . (int) $item->id . '/edit',
        default => ROOT . '/admin/blog/' . (int) $item->id . '/edit',
    };
}

function blog_activate_design(string $type, int $id): bool
{
    if (!in_array($type, ['header', 'footer'], true)) {
        throw new \InvalidArgumentException('Invalid design type.');
    }
    if ($id > 0) {
        $design = blog_get_post_by_id($id);
        if (!$design || (string) ($design->post_type ?? '') !== $type) {
            throw new \InvalidArgumentException('The selected design is unavailable.');
        }
    }
    return blog_save_setting('active_' . $type . '_id', (string) max(0, $id));
}

function blog_active_design(string $type): object|false
{
    if (!in_array($type, ['header', 'footer'], true)) {
        return false;
    }
    $id = (int) blog_setting('active_' . $type . '_id', 0);
    if ($id < 1) {
        return false;
    }
    $design = blog_get_post_by_id($id);
    return $design && (string) ($design->post_type ?? '') === $type ? $design : false;
}

function blog_document_uses_saved_menus(object|array $post): bool
{
    $json = (string) blog_row_value($post, 'blocks_json', '[]');
    $blocks = json_decode($json, true);
    if (!is_array($blocks)) {
        return false;
    }

    foreach ($blocks as $block) {
        if (!is_array($block)) {
            continue;
        }
        $manifest = blog_block_manifest((string) ($block['slug'] ?? ''));
        if ($manifest && !empty($manifest['menu_sources'])) {
            return true;
        }
    }

    return false;
}

function blog_runtime_compiled_content(object $post): array
{
    $compiled = [
        'html' => (string) ($post->content_html ?? ''),
        'css' => (string) ($post->content_css ?? ''),
        'js' => (string) ($post->content_js ?? ''),
    ];

    // Menus are reusable data sources. Recompile menu-enabled documents on each
    // render so a single menu edit updates every block that sources that menu.
    if (!blog_document_uses_saved_menus($post)) {
        return $compiled;
    }

    $blocks = json_decode((string) ($post->blocks_json ?? '[]'), true);
    if (!is_array($blocks)) {
        return $compiled;
    }

    $type = (string) ($post->post_type ?? 'post');
    if (!in_array($type, ['post', 'page', 'header', 'footer'], true)) {
        $type = 'post';
    }

    $runtime = blog_compile_blocks($blocks, $type);
    return [
        'html' => (string) ($runtime['html'] ?? $compiled['html']),
        'css' => (string) ($runtime['css'] ?? $compiled['css']),
        'js' => (string) ($runtime['js'] ?? $compiled['js']),
    ];
}

function blog_render_compiled_content(object $post, array $context = []): string
{
    $palette = blog_post_palette($post);
    $compiled = blog_runtime_compiled_content($post);
    $html = $compiled['html'];
    if (function_exists('do_shortcode')) {
        $html = \do_shortcode($html, array_merge(['post' => $post], $context));
    }
    $output = '<div class="thunder-blog__compiled-content" style="' . htmlspecialchars(blog_palette_variables($palette), ENT_QUOTES, 'UTF-8') . '">';
    if (trim($compiled['css']) !== '') {
        $output .= '<style>' . $compiled['css'] . '</style>';
    }
    $output .= $html;
    if (trim($compiled['js']) !== '') {
        $output .= '<script>' . str_ireplace('</script', '<\/script', $compiled['js']) . '</script>';
    }
    return $output . '</div>';
}

function blog_render_active_design(string $type): string
{
    $design = blog_active_design($type);
    if (!$design) {
        return '';
    }
    return '<div class="thunder-blog__active-design thunder-blog__active-design--' . htmlspecialchars($type, ENT_QUOTES, 'UTF-8') . '">' .
        blog_render_compiled_content($design, ['design_type' => $type]) .
        '</div>';
}

function blog_try_render_page(): void
{
    $segments = URL();
    if (!is_array($segments) || count(array_filter($segments, static fn ($value): bool => (string) $value !== '')) !== 1) {
        return;
    }
    $slug = blog_slugify((string) page());
    if ($slug === '' || in_array($slug, ['blog', 'admin', 'login', 'logout', 'signup', 'account', '404'], true)) {
        return;
    }
    $pagePost = blog_get_post_by_slug($slug, false, 'page');
    if (!$pagePost) {
        return;
    }
    blog_db()->query('UPDATE ' . blog_tables()['posts'] . ' SET view_count = view_count + 1 WHERE id = :id', ['id' => (int) $pagePost->id]);
    $author = blog_author($pagePost->author_id);
    blog_render_frontend('page.php', [
        'page_title' => trim((string) ($pagePost->seo_title ?: $pagePost->title)),
        'meta_description' => trim((string) ($pagePost->seo_description ?: blog_excerpt($pagePost, 155))),
        'post' => $pagePost,
        'author' => $author,
        'palette' => blog_post_palette($pagePost),
    ]);
    exit;
}

function blog_shortcode_layouts(): array
{
    return [
        'small' => 'Small sidebar list',
        'medium' => 'Medium cards',
        'large' => 'Large feature cards',
        'grid' => 'Compact grid',
        'list' => 'Horizontal list',
    ];
}

function blog_shortcode_orders(): array
{
    return [
        'latest' => 'Latest',
        'popular' => 'Popular in 30 days',
        'most_viewed' => 'Most viewed',
        'featured' => 'Featured',
        'popular_week' => 'Popular this week',
        'oldest' => 'Oldest first',
    ];
}

function blog_render_posts_shortcode(array $attrs = [], array $context = []): string
{
    $layouts = blog_shortcode_layouts();
    $orders = blog_shortcode_orders();
    $layout = isset($layouts[(string) ($attrs['layout'] ?? '')]) ? (string) $attrs['layout'] : 'medium';
    $order = isset($orders[(string) ($attrs['order'] ?? '')]) ? (string) $attrs['order'] : 'latest';
    $limit = max(1, min(50, (int) ($attrs['limit'] ?? 6)));
    $category = blog_slugify((string) ($attrs['category'] ?? ''));
    $tag = blog_slugify((string) ($attrs['tag'] ?? ''));

    $result = blog_get_posts([
        'page' => 1,
        'per_page' => $limit,
        'post_type' => 'post',
        'category_slug' => $category,
        'tag_slug' => $tag,
        'order' => $order,
        'featured' => $order === 'featured',
    ]);
    $posts = $result['items'];
    $class = 'tb-shortcode-posts tb-shortcode-posts--' . $layout;
    ob_start();
    ?>
    <div class="<?= htmlspecialchars($class, ENT_QUOTES, 'UTF-8') ?>">
        <?php foreach ($posts as $post): ?>
            <article class="tb-shortcode-posts__item">
                <?php if (!empty($post->featured_image)): ?>
                    <a class="tb-shortcode-posts__media" href="<?= htmlspecialchars(blog_post_url($post), ENT_QUOTES, 'UTF-8') ?>"><img class="tb-shortcode-posts__image" src="<?= htmlspecialchars((string) $post->featured_image, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars((string) $post->title, ENT_QUOTES, 'UTF-8') ?>"></a>
                <?php endif; ?>
                <div class="tb-shortcode-posts__body">
                    <?php if (!empty($post->category_name)): ?><span class="tb-shortcode-posts__category"><?= htmlspecialchars((string) $post->category_name, ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                    <h3 class="tb-shortcode-posts__title"><a href="<?= htmlspecialchars(blog_post_url($post), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $post->title, ENT_QUOTES, 'UTF-8') ?></a></h3>
                    <?php if ($layout !== 'small'): ?><p class="tb-shortcode-posts__excerpt"><?= htmlspecialchars(blog_excerpt($post, $layout === 'large' ? 220 : 120), ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
                    <span class="tb-shortcode-posts__meta"><?= htmlspecialchars(get_date($post->published_at ?: $post->created_at), ENT_QUOTES, 'UTF-8') ?> · <?= (int) $post->view_count ?> views</span>
                </div>
            </article>
        <?php endforeach; ?>
        <?php if (!$posts): ?><p class="tb-shortcode-posts__empty">No matching posts were found.</p><?php endif; ?>
    </div>
    <style>
    .tb-shortcode-posts{display:grid;gap:20px;color:var(--tb-color-text)}
    .tb-shortcode-posts__item{min-width:0;overflow:hidden;border:1px solid var(--tb-color-border);border-radius:16px;background:var(--tb-color-surface)}
    .tb-shortcode-posts__media{display:block;background:var(--tb-color-background)}
    .tb-shortcode-posts__image{width:100%;height:100%;display:block;object-fit:cover}
    .tb-shortcode-posts__body{padding:18px}
    .tb-shortcode-posts__category{color:var(--tb-color-primary);font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.08em}
    .tb-shortcode-posts__title{margin:7px 0 8px;font-size:20px;line-height:1.2}
    .tb-shortcode-posts__title a{color:var(--tb-color-secondary);text-decoration:none}
    .tb-shortcode-posts__excerpt{margin:0 0 12px;color:var(--tb-color-muted);line-height:1.6}
    .tb-shortcode-posts__meta{color:var(--tb-color-muted);font-size:12px}
    .tb-shortcode-posts--small{display:block}
    .tb-shortcode-posts--small .tb-shortcode-posts__item{display:grid;grid-template-columns:84px minmax(0,1fr);border:0;border-bottom:1px solid var(--tb-color-border);border-radius:0;background:transparent}
    .tb-shortcode-posts--small .tb-shortcode-posts__media{height:72px;margin:10px 0}
    .tb-shortcode-posts--small .tb-shortcode-posts__body{padding:10px 0 10px 14px}
    .tb-shortcode-posts--small .tb-shortcode-posts__title{font-size:15px}
    .tb-shortcode-posts--medium,.tb-shortcode-posts--grid{grid-template-columns:repeat(2,minmax(0,1fr))}
    .tb-shortcode-posts--medium .tb-shortcode-posts__media,.tb-shortcode-posts--grid .tb-shortcode-posts__media{height:190px}
    .tb-shortcode-posts--large{grid-template-columns:1fr}
    .tb-shortcode-posts--large .tb-shortcode-posts__item{display:grid;grid-template-columns:minmax(260px,42%) minmax(0,1fr)}
    .tb-shortcode-posts--large .tb-shortcode-posts__media{min-height:280px}
    .tb-shortcode-posts--list{display:block}
    .tb-shortcode-posts--list .tb-shortcode-posts__item{display:grid;grid-template-columns:180px minmax(0,1fr);margin-bottom:18px}
    .tb-shortcode-posts--list .tb-shortcode-posts__media{height:100%}
    @media(max-width:700px){.tb-shortcode-posts--medium,.tb-shortcode-posts--grid{grid-template-columns:1fr}.tb-shortcode-posts--large .tb-shortcode-posts__item,.tb-shortcode-posts--list .tb-shortcode-posts__item{grid-template-columns:1fr}.tb-shortcode-posts--large .tb-shortcode-posts__media,.tb-shortcode-posts--list .tb-shortcode-posts__media{height:220px;min-height:0}}
    </style>
    <?php
    return (string) ob_get_clean();
}


function blog_menus(bool $withItems = false): array
{
    $tables = blog_tables();
    try {
        $rows = blog_db()->query("SELECT * FROM {$tables['menus']} ORDER BY name ASC, id ASC");
        $menus = is_array($rows) ? $rows : [];
        if ($withItems) {
            foreach ($menus as $menu) {
                $menu->items = blog_menu_tree((int) $menu->id);
            }
        }
        return $menus;
    } catch (Throwable) {
        return [];
    }
}

function blog_get_menu(int $id): object|false
{
    if ($id < 1) return false;
    try {
        return blog_db()->get_row('SELECT * FROM ' . blog_tables()['menus'] . ' WHERE id = :id LIMIT 1', ['id' => $id]);
    } catch (Throwable) {
        return false;
    }
}

function blog_menu_items(int $menuId): array
{
    if ($menuId < 1) return [];
    try {
        $rows = blog_db()->query('SELECT * FROM ' . blog_tables()['menu_items'] . ' WHERE menu_id = :menu_id ORDER BY sort_order ASC, id ASC', ['menu_id' => $menuId]);
        return is_array($rows) ? $rows : [];
    } catch (Throwable) {
        return [];
    }
}

function blog_menu_item_url(object|array $item): string
{
    $type = (string) blog_row_value($item, 'item_type', 'custom');
    $objectId = (int) blog_row_value($item, 'object_id', 0);
    if (in_array($type, ['post', 'page'], true) && $objectId > 0) {
        $content = blog_get_post_by_id($objectId);
        if ($content && (string) ($content->post_type ?? '') === $type) return blog_post_url($content);
    }
    $url = trim((string) blog_row_value($item, 'url', '#'));
    if ($url === '') return '#';
    if (str_starts_with($url, '{{ROOT}}')) {
        $suffix = substr($url, 8);
        if ($suffix === '' || str_starts_with($suffix, '/')) {
            return rtrim((string) ROOT, '/') . ($suffix === '' ? '' : $suffix);
        }
        return '#';
    }
    if (preg_match('~^(https?://|mailto:|tel:|/|#)~i', $url)) return $url;
    return '#';
}

function blog_menu_tree(int $menuId): array
{
    $items = blog_menu_items($menuId);
    $byParent = [];
    foreach ($items as $item) {
        $parent = max(0, (int) ($item->parent_id ?? 0));
        $byParent[$parent][] = $item;
    }
    $build = function (int $parent, array $trail = []) use (&$build, &$byParent): array {
        $result = [];
        foreach ($byParent[$parent] ?? [] as $item) {
            $id = (int) $item->id;
            if (in_array($id, $trail, true)) continue;
            $item->resolved_url = blog_menu_item_url($item);
            $item->children = $build($id, array_merge($trail, [$id]));
            $result[] = $item;
        }
        return $result;
    };
    return $build(0);
}

function blog_menu_choices(): array
{
    $choices = [];
    foreach (blog_menus(false) as $menu) $choices[(string) $menu->id] = (string) $menu->name;
    return $choices;
}

function blog_menu_designer_payload(): array
{
    $payload = [];
    foreach (blog_menus(true) as $menu) {
        $normalize = function (array $items) use (&$normalize): array {
            return array_map(static function ($item) use (&$normalize): array {
                return [
                    'id' => (int) $item->id,
                    'title' => (string) $item->title,
                    'url' => (string) ($item->resolved_url ?? blog_menu_item_url($item)),
                    'target' => (string) ($item->target ?? '_self'),
                    'children' => $normalize((array) ($item->children ?? [])),
                ];
            }, $items);
        };
        $payload[] = ['id' => (int) $menu->id, 'name' => (string) $menu->name, 'items' => $normalize((array) $menu->items)];
    }
    return $payload;
}

function blog_linkable_content(string $type): array
{
    if (!in_array($type, ['post', 'page'], true)) return [];
    try {
        $rows = blog_db()->query('SELECT id, title, slug, status, post_type FROM ' . blog_tables()['posts'] . ' WHERE post_type = :post_type ORDER BY title ASC, id DESC LIMIT 1000', ['post_type' => $type]);
        return is_array($rows) ? $rows : [];
    } catch (Throwable) {
        return [];
    }
}

function blog_save_menu_name(string $name, ?int $id = null): int
{
    $name = trim($name);
    if ($name === '') throw new \InvalidArgumentException('Menu name is required.');
    $base = blog_slugify($name) ?: 'menu';
    $candidate = $base;
    $number = 2;
    $table = blog_tables()['menus'];
    $db = blog_db();
    while (true) {
        $existing = $db->get_row("SELECT id FROM {$table} WHERE slug = :slug LIMIT 1", ['slug' => $candidate]);
        if (!$existing || (int) $existing->id === (int) $id) break;
        $candidate = $base . '-' . $number++;
    }
    if ($id) {
        $db->query("UPDATE {$table} SET name = :name, slug = :slug, updated_at = CURRENT_TIMESTAMP WHERE id = :id", ['name'=>$name,'slug'=>$candidate,'id'=>$id]);
        if ($db->has_error) throw new \RuntimeException($db->error ?: 'Menu name could not be updated.');
        return $id;
    }
    $db->query("INSERT INTO {$table} (name, slug, created_at, updated_at) VALUES (:name, :slug, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)", ['name'=>$name,'slug'=>$candidate]);
    if ($db->has_error) throw new \RuntimeException($db->error ?: 'Menu could not be created.');
    return (int) $db->insert_id;
}

function blog_save_menu_item(int $menuId, array $input): int
{
    if (!blog_get_menu($menuId)) throw new \InvalidArgumentException('Menu not found.');
    $type = in_array((string) ($input['item_type'] ?? ''), ['custom','post','page'], true) ? (string) $input['item_type'] : 'custom';
    $objectId = max(0, (int) ($input['object_id'] ?? 0));
    $title = trim((string) ($input['title'] ?? ''));
    $url = trim((string) ($input['url'] ?? ''));
    if (in_array($type, ['post','page'], true)) {
        $content = blog_get_post_by_id($objectId);
        if (!$content || (string) ($content->post_type ?? '') !== $type) throw new \InvalidArgumentException('Select a valid ' . $type . '.');
        if ($title === '') $title = (string) $content->title;
        $url = blog_post_url($content);
    }
    if ($title === '') throw new \InvalidArgumentException('Menu item title is required.');
    if ($type === 'custom' && !preg_match('~^(https?://|mailto:|tel:|/|#|\{\{ROOT\}\}(?:/|$))~i', $url)) throw new \InvalidArgumentException('Enter a valid custom link. You may also start with {{ROOT}}/.');
    $target = (string) ($input['target'] ?? '_self') === '_blank' ? '_blank' : '_self';
    $table = blog_tables()['menu_items'];
    $db = blog_db();
    $last = $db->get_row("SELECT MAX(sort_order) AS maximum FROM {$table} WHERE menu_id = :menu_id", ['menu_id'=>$menuId]);
    $sort = (int) ($last->maximum ?? 0) + 10;
    $db->query("INSERT INTO {$table} (menu_id,parent_id,item_type,object_id,title,url,target,sort_order,created_at,updated_at) VALUES (:menu_id,0,:item_type,:object_id,:title,:url,:target,:sort_order,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP)", ['menu_id'=>$menuId,'item_type'=>$type,'object_id'=>$objectId ?: null,'title'=>$title,'url'=>$url,'target'=>$target,'sort_order'=>$sort]);
    if ($db->has_error) throw new \RuntimeException($db->error ?: 'Menu item could not be added.');
    return (int) $db->insert_id;
}

function blog_update_menu_items(int $menuId, array $items): void
{
    $existing = blog_menu_items($menuId);
    $byId = [];
    foreach ($existing as $item) $byId[(int) $item->id] = $item;
    $allowed = array_keys($byId);
    $parents = [];
    foreach ($allowed as $id) {
        $data = is_array($items[$id] ?? null) ? $items[$id] : [];
        $parent = max(0, (int) ($data['parent_id'] ?? ($byId[$id]->parent_id ?? 0)));
        $parents[$id] = ($parent !== $id && in_array($parent, $allowed, true)) ? $parent : 0;
    }
    foreach ($parents as $id => $parent) {
        $seen = [$id];
        while ($parent > 0) {
            if (in_array($parent, $seen, true)) { $parents[$id] = 0; break; }
            $seen[] = $parent;
            $parent = (int) ($parents[$parent] ?? 0);
        }
    }

    $db = blog_db(); $table = blog_tables()['menu_items'];
    foreach ($items as $id => $data) {
        $id = (int) $id; if (!isset($byId[$id]) || !is_array($data)) continue;
        $title = trim((string) ($data['title'] ?? '')); if ($title === '') continue;
        $sort = max(0, (int) ($data['sort_order'] ?? 0));
        $target = (string) ($data['target'] ?? '_self') === '_blank' ? '_blank' : '_self';
        $url = (string) ($byId[$id]->url ?? '#');
        if ((string) ($byId[$id]->item_type ?? '') === 'custom') {
            $candidate = trim((string) ($data['url'] ?? $url));
            if (preg_match('~^(https?://|mailto:|tel:|/|#|\{\{ROOT\}\}(?:/|$))~i', $candidate)) $url = $candidate;
        }
        $db->query("UPDATE {$table} SET title=:title,url=:url,parent_id=:parent_id,sort_order=:sort_order,target=:target,updated_at=CURRENT_TIMESTAMP WHERE id=:id AND menu_id=:menu_id", ['title'=>$title,'url'=>$url,'parent_id'=>(int) ($parents[$id] ?? 0),'sort_order'=>$sort,'target'=>$target,'id'=>$id,'menu_id'=>$menuId]);
    }
}

function blog_delete_menu_item(int $menuId, int $itemId): void
{
    $table = blog_tables()['menu_items']; $db = blog_db();
    $db->query("UPDATE {$table} SET parent_id = 0 WHERE menu_id = :menu_id AND parent_id = :item_id", ['menu_id'=>$menuId,'item_id'=>$itemId]);
    $db->query("DELETE FROM {$table} WHERE menu_id = :menu_id AND id = :item_id", ['menu_id'=>$menuId,'item_id'=>$itemId]);
}

function blog_delete_menu(int $menuId): void
{
    $tables = blog_tables(); $db = blog_db();
    $db->query("DELETE FROM {$tables['menu_items']} WHERE menu_id = :id", ['id'=>$menuId]);
    $db->query("DELETE FROM {$tables['menus']} WHERE id = :id", ['id'=>$menuId]);
}

function blog_render_manifest_menu(array $source, int $menuId, string $instanceId): string
{
    $menu = $menuId > 0 ? blog_get_menu($menuId) : false;
    $items = $menu ? blog_menu_tree($menuId) : [];
    if (!$items) return (string) ($source['empty'] ?? '');
    $renderItems = function (array $nodes, int $level = 0) use (&$renderItems, $source, $instanceId): string {
        $parts = [];
        foreach ($nodes as $item) {
            $children = $renderItems((array) ($item->children ?? []), $level + 1);
            $template = $children !== '' ? (string) ($source['parent'] ?? $source['item'] ?? '') : (string) ($source['item'] ?? '');
            $parts[] = blog_replace_tokens($template, [
                '{{title}}'=>htmlspecialchars((string) $item->title, ENT_QUOTES, 'UTF-8'),
                '{{url}}'=>htmlspecialchars((string) ($item->resolved_url ?? blog_menu_item_url($item)), ENT_QUOTES, 'UTF-8'),
                '{{target}}'=>htmlspecialchars((string) ($item->target ?? '_self'), ENT_QUOTES, 'UTF-8'),
                '{{children}}'=>$children,
                '{{item_id}}'=>(string) (int) $item->id,
                '{{level}}'=>(string) $level,
                '{{instance_id}}'=>htmlspecialchars($instanceId, ENT_QUOTES, 'UTF-8'),
            ]);
        }
        return implode('', $parts);
    };
    $itemsHtml = $renderItems($items);
    return blog_replace_tokens((string) ($source['container'] ?? '{{items}}'), ['{{items}}'=>$itemsHtml,'{{menu_name}}'=>htmlspecialchars((string) $menu->name, ENT_QUOTES, 'UTF-8'),'{{instance_id}}'=>htmlspecialchars($instanceId, ENT_QUOTES, 'UTF-8')]);
}

function blog_json_response(array $data, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}
