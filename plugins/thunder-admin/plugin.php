<?php

namespace ThunderAdmin;

require_once __DIR__ . '/functions.php';

set_value([
    'admin_route' => 'admin',
    'admin_plugin_id' => PLUGIN_ID,
]);


add_action('before_controller', function (): void {
    if (!function_exists('page') || \page() !== 'admin') {
        return;
    }

    $access = admin_access_status();

    if ($access['allowed']) {
        return;
    }

    deny_admin_access($access['logged_in']);
}, 1);

add_filter('admin_before_links', function (array $links): array {
    $links[PLUGIN_ID] = array_merge($links[PLUGIN_ID] ?? [], [
        (object) [
            'title'      => 'Dashboard',
            'link'       => admin_url(),
            'icon'       => 'fa-solid fa-gauge-high',
            'parent'     => '',
            'slug'       => 'dashboard',
            'order'      => 1,
        ],
        (object) [
            'title'      => 'Settings',
            'link'       => admin_url('settings'),
            'icon'       => 'fa-solid fa-gear',
            'parent'     => '',
            'slug'       => 'settings',
            'order'      => 1000,
        ],
    ]);

    return $links;
}, 1);

add_filter('admin_user_info', function (array $user): array {
    return array_replace(current_user_info(), $user);
}, 1);

add_filter('admin_global_search', function (array $search): array {
    return array_replace([
        'enabled'     => true,
        'action'      => admin_url(),
        'method'      => 'get',
        'name'        => 'admin_search',
        'placeholder' => 'Search admin…',
        'value'       => isset($_GET['admin_search']) ? trim((string) $_GET['admin_search']) : '',
    ], $search);
}, 1);

add_filter('admin_notifications', function (array $notifications): array {
    return $notifications;
}, 1);

add_filter('admin_page_title', function (string $title): string {
    return $title;
}, 1);

add_filter('admin_body_classes', function (array $classes): array {
    $classes[] = 'ta-page';
    return array_values(array_unique($classes));
}, 1);

add_action('controller', function (): void {
    require __DIR__ . '/controllers/admin/dashboard.php';
}, 10, 'admin.dashboard');

add_action('controller', function (): void {
    require __DIR__ . '/controllers/admin/settings.php';
}, 10, 'admin.settings');

add_action('admin_main_content', function (): void {
    require admin_look_path('admin/dashboard.php');
}, 10, 'admin.dashboard');

add_action('admin_main_content', function (): void {
    require admin_look_path('admin/settings.php');
}, 10, 'admin.settings');

add_action('admin_dashboard_widgets', function (): void {
    $missing = function_exists('missing_plugins') ? \missing_plugins() : [];
    $outdated = function_exists('outdated_plugins') ? \outdated_plugins() : [];
    ?>
    <section class="ta-widget ta-widget--welcome">
        <div class="ta-widget__icon ta-widget__icon--primary">
            <i class="fa-solid fa-bolt" aria-hidden="true"></i>
        </div>
        <div class="ta-widget__content">
            <p class="ta-widget__eyebrow">Thunder Admin</p>
            <h2 class="ta-widget__title">Your administration foundation is ready.</h2>
            <p class="ta-widget__text">Install companion plugins and their registered links, pages and dashboard widgets will appear here automatically.</p>
        </div>
    </section>

    <section class="ta-stat-card">
        <div class="ta-stat-card__icon"><i class="fa-solid fa-puzzle-piece" aria-hidden="true"></i></div>
        <div class="ta-stat-card__content">
            <span class="ta-stat-card__value"><?= e(is_array($missing) ? count($missing) : 0) ?></span>
            <span class="ta-stat-card__label">Missing plugins</span>
        </div>
    </section>

    <section class="ta-stat-card">
        <div class="ta-stat-card__icon"><i class="fa-solid fa-rotate" aria-hidden="true"></i></div>
        <div class="ta-stat-card__content">
            <span class="ta-stat-card__value"><?= e(is_array($outdated) ? count($outdated) : 0) ?></span>
            <span class="ta-stat-card__label">Outdated plugins</span>
        </div>
    </section>
    <?php
}, 1, 'admin.dashboard');

add_action('view', function (): void {
    if (!function_exists('page') || \page() !== 'admin') {
        return;
    }

    $renderShell = do_filter('admin_render_shell', true);
    if (!$renderShell) {
        return;
    }

    $adminSettings = admin_settings();
    $activeLook = active_admin_look($adminSettings);
    $activePalette = resolved_admin_palette($activeLook, $adminSettings);

    $links = do_filter('admin_before_links', []);
    $links = do_filter('admin_after_links', is_array($links) ? $links : []);
    $adminLinks = normalize_links(is_array($links) ? $links : []);
    $activeLink = mark_active_links($adminLinks);
    $adminMenu = build_menu($adminLinks);

    $defaultTitle = $activeLink?->title ?? 'Administration';
    $pageTitle = (string) (get_value('admin_page_title') ?? $defaultTitle);
    $pageTitle = do_filter('admin_page_title', $pageTitle);

    $breadcrumbs = build_breadcrumbs($adminLinks, $activeLink);
    $breadcrumbs = do_filter('admin_breadcrumbs', $breadcrumbs);

    $user = do_filter('admin_user_info', []);
    $user = is_array($user) ? array_replace(current_user_info(), $user) : current_user_info();

    $notifications = do_filter('admin_notifications', []);
    $notifications = normalize_notifications(is_array($notifications) ? $notifications : []);

    $search = do_filter('admin_global_search', []);
    $search = is_array($search) ? $search : [];

    $bodyClasses = do_filter('admin_body_classes', []);
    $bodyClasses = is_array($bodyClasses) ? $bodyClasses : [];

    $taViewPaths = [
        'shell'         => admin_look_path('admin/shell.php', $activeLook),
        'sidebar'       => admin_look_path('admin/partials/sidebar.php', $activeLook),
        'topbar'        => admin_look_path('admin/partials/topbar.php', $activeLook),
        'breadcrumbs'   => admin_look_path('admin/partials/breadcrumbs.php', $activeLook),
        'notifications' => admin_look_path('admin/partials/notifications.php', $activeLook),
        'user_info'     => admin_look_path('admin/partials/user-info.php', $activeLook),
        'flash'         => admin_look_path('admin/partials/flash-messages.php', $activeLook),
        'footer'        => admin_look_path('admin/partials/footer.php', $activeLook),
    ];

    $taAssets = [
        'css' => admin_look_http('assets/css/admin.css', $activeLook),
        'js'  => admin_look_http('assets/js/admin.js', $activeLook),
    ];

    set_value([
        'admin_links'         => $adminLinks,
        'admin_menu'          => $adminMenu,
        'admin_active_link'   => $activeLink,
        'admin_breadcrumbs'   => $breadcrumbs,
        'admin_user'          => $user,
        'admin_notifications' => $notifications,
        'admin_search'        => $search,
        'admin_page_title'    => $pageTitle,
        'admin_body_classes'  => $bodyClasses,
        'admin_settings'      => $adminSettings,
        'admin_active_look'   => $activeLook,
        'admin_palette'       => $activePalette,
        'admin_palette_style' => palette_css_variables($activePalette['colors']),
        'ta_view_paths'        => $taViewPaths,
        'ta_assets'            => $taAssets,
    ]);

    require $taViewPaths['shell'];
}, 10);
