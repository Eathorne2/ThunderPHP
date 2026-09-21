<?php

namespace ThunderBlog;

defined('ROOTPATH') or die('Direct script access denied');

blog_require_permission('manage-blog-settings');
$error = '';
$tables = blog_database_tables();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST)) {
        $error = 'The form token expired. Refresh the page and try again.';
    } else {
        try {
            $looks = blog_scan_looks();
            $look = (string) ($_POST['look'] ?? 'editorial');
            if (!isset($looks[$look])) throw new \InvalidArgumentException('The selected look is unavailable.');

            $preset = (string) ($_POST['palette'] ?? 'indigo');
            $presets = blog_palette_presets();
            if ($preset !== 'custom' && !isset($presets[$preset])) $preset = 'indigo';

            $customPalette = [];
            foreach ($presets['indigo']['colors'] as $key => $fallback) {
                $customPalette[$key] = blog_sanitize_color((string) ($_POST['color_' . str_replace('-', '_', $key)] ?? $fallback), $fallback);
            }

            $userTable = (string) ($_POST['user_table'] ?? 'auth_users');
            if (!in_array($userTable, $tables, true)) throw new \InvalidArgumentException('Select an existing users table.');
            $columnNames = blog_column_names($userTable);
            $requiredColumns = [
                'user_primary_key' => 'Primary key',
                'user_display_column' => 'Display-name column',
            ];
            $optionalColumns = ['user_email_column', 'user_avatar_column', 'user_profile_column'];
            $mapping = ['user_table' => $userTable];
            foreach ($requiredColumns as $field => $label) {
                $value = (string) ($_POST[$field] ?? '');
                if (!in_array($value, $columnNames, true)) throw new \InvalidArgumentException("{$label} must belong to the selected users table.");
                $mapping[$field] = $value;
            }
            foreach ($optionalColumns as $field) {
                $value = (string) ($_POST[$field] ?? '');
                if ($value !== '' && !in_array($value, $columnNames, true)) throw new \InvalidArgumentException('Every selected author column must belong to the selected users table.');
                $mapping[$field] = $value;
            }

            $blogPageEyebrow = trim((string) ($_POST['blog_page_eyebrow'] ?? 'Stories, ideas and updates'));
            $blogPageTitle = trim((string) ($_POST['blog_page_title'] ?? 'The Blog'));
            $blogPageSubtitle = trim((string) ($_POST['blog_page_subtitle'] ?? ''));
            $searchPageEyebrow = trim((string) ($_POST['search_page_eyebrow'] ?? 'Stories, ideas and updates'));
            $searchPageTitle = trim((string) ($_POST['search_page_title'] ?? 'Search the Blog'));
            $searchPageSubtitle = trim((string) ($_POST['search_page_subtitle'] ?? ''));
            if ($blogPageEyebrow === '') $blogPageEyebrow = 'Stories, ideas and updates';
            if ($searchPageEyebrow === '') $searchPageEyebrow = 'Stories, ideas and updates';
            if ($blogPageTitle === '') $blogPageTitle = 'The Blog';
            if ($searchPageTitle === '') $searchPageTitle = 'Search the Blog';
            $blogPageEyebrow = blog_text_substr($blogPageEyebrow, 0, 120);
            $searchPageEyebrow = blog_text_substr($searchPageEyebrow, 0, 120);
            $blogPageTitle = blog_text_substr($blogPageTitle, 0, 180);
            $searchPageTitle = blog_text_substr($searchPageTitle, 0, 180);
            $blogPageSubtitle = blog_text_substr($blogPageSubtitle, 0, 500);
            $searchPageSubtitle = blog_text_substr($searchPageSubtitle, 0, 500);

            $settings = array_merge([
                'look' => $look,
                'palette' => $preset,
                'custom_palette' => json_encode($customPalette, JSON_UNESCAPED_SLASHES),
                'posts_per_page' => (string) max(1, min(50, (int) ($_POST['posts_per_page'] ?? 9))),
                'show_author' => isset($_POST['show_author']) ? '1' : '0',
                'show_date' => isset($_POST['show_date']) ? '1' : '0',
                'show_category' => isset($_POST['show_category']) ? '1' : '0',
                'show_views' => isset($_POST['show_views']) ? '1' : '0',
                'blog_page_eyebrow' => $blogPageEyebrow,
                'blog_page_title' => $blogPageTitle,
                'blog_page_subtitle' => $blogPageSubtitle,
                'search_page_eyebrow' => $searchPageEyebrow,
                'search_page_title' => $searchPageTitle,
                'search_page_subtitle' => $searchPageSubtitle,
            ], $mapping);

            foreach ($settings as $key => $value) blog_save_setting($key, (string) $value);
            do_action('thunder_blog_settings_saved', ['settings' => $settings]);
            message('success', 'Thunder Blog settings were saved.');
            redirect('admin/blog/settings');
            exit;
        } catch (\Throwable $e) {
            $error = $e->getMessage();
        }
    }
}

$settings = blog_settings(true);
$selectedTable = in_array((string) ($settings['user_table'] ?? ''), $tables, true) ? (string) $settings['user_table'] : ($tables[0] ?? '');
$columns = $selectedTable !== '' ? blog_table_columns($selectedTable) : [];
set_value('thunder_blog_admin', [
    'settings' => $settings,
    'looks' => blog_scan_looks(),
    'palette_presets' => blog_palette_presets(),
    'database_tables' => $tables,
    'user_columns' => $columns,
    'author_preview' => blog_author_mapping_preview(),
    'schema_api_url' => ROOT . '/admin/blog/api/schema',
    'error' => $error,
]);
