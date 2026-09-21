# Thunder Admin

Thunder Admin is the reusable administration foundation for ThunderPHP. Version 1.5.0 includes the **Classic Sidebar**, **Compact Sidebar**, **Horizontal Navigation**, **Dark Control Panel**, **Glass Dashboard**, and **Executive Workspace** looks.

## Included

- Responsive admin shell
- Six interchangeable administration looks
- Desktop and mobile navigation appropriate to each look
- Plugin-isolated menu groups
- Unlimited nested menu levels
- All registered links are displayed without access checks
- Active-link detection and breadcrumbs
- Global search slot
- Notifications dropdown
- Logged-in user menu with `/logout`
- Dashboard widgets
- Page and topbar action slots
- Plugin-owned look and colour-palette settings
- Flash messages that are erased after display and preserve `<br>` line breaks

## Install

Extract or install `thunder-admin.zip` into the ThunderPHP plugins directory, activate it, and run its migration:

```text
php thunder migrate thunder-admin
```

The migration creates `thunder_admin_settings`. It stores only Thunder Admin settings: the active admin look and the selected/custom palette for each look.

The dashboard and settings pages are available at:

```text
/admin
/admin/settings
```

## Admin access

A user must be logged in. Access is then granted when at least one condition is true:

- user ID is `1`
- the user has the `view-admin-area` permission
- the user has the `admin` role

The primary user override prevents account ID `1` from being locked out.

## Register navigation links

Use the registering plugin ID as the outer array key. Use a link's `slug` as the `parent` of nested links.

```php
add_filter('admin_before_links', function (array $links): array {
    $links[plugin_id()] = [
        (object) [
            'title'  => 'Thunder Billing',
            'link'   => ROOT . '/admin/billing',
            'icon'   => 'fa-solid fa-receipt',
            'parent' => '',
            'slug'   => 'billing',
            'order'  => 10,
        ],
        (object) [
            'title'  => 'Invoices',
            'link'   => ROOT . '/admin/billing/invoices',
            'icon'   => 'fa-solid fa-file-invoice',
            'parent' => 'billing',
            'slug'   => 'invoices',
            'order'  => 20,
            'badge'  => '4',
        ],
    ];

    return $links;
});
```

Required fields are `title`, `link`, and `slug`. Optional fields are `icon`, `parent`, `order`, `badge`, and `target`.

Thunder Admin displays every registered link. Each plugin is responsible for deciding whether it should register a link.

## Render an admin page

Register the route in the plugin's `config.json`, process it through `controller`, and render the page body through `admin_main_content`.

```php
add_action('admin_main_content', function (): void {
    require current_look('admin/invoices.php');
}, 10, 'billing.invoices');
```

Thunder Admin supplies the surrounding shell.

## Dashboard widgets

```php
add_action('admin_dashboard_widgets', function (): void {
    ?>
    <section class="ta-widget">
        <h2 class="ta-widget__title">Recent invoices</h2>
        <p class="ta-widget__text">Your widget content.</p>
    </section>
    <?php
});
```

## Notifications

```php
add_filter('admin_notifications', function (array $notifications): array {
    $notifications[] = (object) [
        'title'      => 'Invoice overdue',
        'message'    => 'Invoice #104 is past its due date.',
        'link'       => ROOT . '/admin/billing/invoices/104',
        'icon'       => 'fa-solid fa-file-invoice',
        'type'       => 'warning',
        'created_at' => date('Y-m-d H:i:s'),
        'read'       => false,
    ];

    return $notifications;
});
```

## Flash messages

Every look reads and erases messages during rendering:

```php
$success = function_exists('message') ? \message('success', '', true) : null;
$fail = function_exists('message') ? \message('fail', '', true) : null;
```

Literal `<br>`, `<br/>`, and `<br />` tags are retained for line formatting. Other message markup is escaped.

## User information

Thunder Admin reads the current user from `Core\Session`. Plugins may add profile links or adjust displayed information with `admin_user_info`. A Logout link to `ROOT . '/logout'` is always appended by Thunder Admin.

```php
add_filter('admin_user_info', function (array $user): array {
    $user['links'][] = [
        'title' => 'Profile',
        'link'  => ROOT . '/admin/profile',
        'icon'  => 'fa-solid fa-user',
    ];

    return $user;
});
```

## Included looks

### Classic Sidebar

A traditional full-width sidebar with a collapsible desktop state, nested navigation and responsive mobile drawer.

### Compact Sidebar

A permanent icon rail that uses less desktop space. Its expand button opens the full navigation over the content without shifting the main area. Nested links are available in the expanded panel, and mobile devices receive a full off-canvas menu.

### Horizontal Navigation

Application branding sits above a full-width horizontal menu. The menu row stays fixed at the top while scrolling, desktop submenus open on mouse hover, and smaller screens retain click-based vertical off-canvas navigation.

### Dark Control Panel

A high-contrast command-centre layout with dark surfaces, a collapsible sidebar, system-status details and palettes designed specifically for low-light administration work.

### Glass Dashboard

A luminous glassmorphism workspace with floating frosted navigation, translucent cards, soft gradient orbs, and palette presets designed specifically for the glass surfaces.

### Executive Workspace

A refined business workspace with a floating opaque sidebar, restrained typography, formal page panels and executive palette presets.

## Looks and colour palettes

Thunder Admin discovers folders under `looks/`. Every look must include `look.json`, its own admin views/assets, a `default_palette`, and a `palettes` object.

The settings page automatically lists installed looks and the presets declared by the selected look. The Custom option stores a separate custom palette for that look.

Thunder Admin resolves its own view files with the saved look. Other plugins keep their own looks and settings pages; they do not extend Thunder Admin's settings screen.

## Admin asset hooks

Admin-only CSS and JavaScript should use these compatibility hooks:

```php
add_action('html_admin_head', function (): void {
    // CSS links or head scripts
});

add_action('html_admin_footer', function (): void {
    // JavaScript loaded before </body>
});
```

Thunder Admin intentionally does not call the redundant `admin_head`, `admin_footer_scripts`, `html_head`, or `html_footer` asset hooks inside the admin shell.

## Public hooks

### Filters

- `admin_before_links`
- `admin_after_links`
- `admin_render_shell`
- `admin_page_title`
- `admin_breadcrumbs`
- `admin_notifications`
- `admin_user_info`
- `admin_global_search`
- `admin_body_classes`

### Actions

- `admin_main_content`
- `admin_dashboard_widgets`
- `admin_topbar_actions`
- `admin_page_actions`
- `admin_footer`
- `html_admin_head`
- `html_admin_footer`
- `admin_notifications_header`
- `admin_notifications_footer`
- `admin_user_menu_links`

## Font Awesome

Every Thunder Admin look loads the framework-bundled Font Awesome stylesheet from:

```text
ROOT/assets/css/all.min.css
```

Menu icons should use the bundled Font Awesome class names, for example `fa-solid fa-file-invoice`.

## Application branding

Every look reads application branding from:

- `APP_NAME`
- `APP_DESCRIPTION`
- `APP_LOGO`


## Dark Control Panel layout note

The Dark Control Panel sidebar remains `position: fixed`. Do not override it to `position: relative`, because doing so returns the sidebar to normal document flow and pushes `.ta-main` below the full sidebar height.
