<?php

namespace ThunderAdmin;

$looks = available_admin_looks();
$settings = admin_settings();

if (strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')) === 'POST') {
    $validCsrf = function_exists('csrf_verify')
        && \csrf_verify($_POST, 'thunder_admin_settings');

    if (!$validCsrf) {
        if (function_exists('message')) {
            \message('fail', 'The settings form expired. Please try again.');
        }

        redirect_admin_settings();
    }

    $look = trim((string) ($_POST['active_look'] ?? ''));
    if (!isset($looks[$look])) {
        $look = active_admin_look($settings);
    }

    $definition = $looks[$look] ?? [];
    $palettes = is_array($definition['palettes'] ?? null) ? $definition['palettes'] : [];
    $palette = trim((string) ($_POST['palette'][$look] ?? ''));

    if ($palette !== 'custom' && !isset($palettes[$palette])) {
        $palette = trim((string) ($definition['default_palette'] ?? array_key_first($palettes) ?? 'custom'));
    }

    $customInput = $_POST['custom'][$look] ?? [];
    $customInput = is_array($customInput) ? $customInput : [];
    $fallback = custom_palette_colors($look, $settings);
    $customColors = sanitize_palette_colors($customInput, $fallback);

    $saved = save_admin_settings([
        'active_look'                => $look,
        'palette_' . $look           => $palette,
        'custom_palette_' . $look    => (string) json_encode($customColors, JSON_UNESCAPED_SLASHES),
    ]);

    if (function_exists('message')) {
        \message(
            $saved ? 'success' : 'fail',
            $saved
                ? 'Thunder Admin settings were saved.'
                : 'Thunder Admin could not save its settings. Run the plugin migration and try again.'
        );
    }

    redirect_admin_settings();
}

$settings = admin_settings(true);
$activeLook = active_admin_look($settings);

$lookSettings = [];
foreach ($looks as $lookId => $definition) {
    $lookSettings[$lookId] = [
        'palette' => admin_palette_id($lookId, $settings),
        'custom'  => custom_palette_colors($lookId, $settings),
    ];
}

set_value([
    'admin_page_title'       => 'Admin Settings',
    'admin_page_description' => 'Choose the Thunder Admin look and its colour palette.',
    'admin_available_looks'  => $looks,
    'admin_active_look'      => $activeLook,
    'admin_look_settings'    => $lookSettings,
    'admin_palette_fields'   => palette_fields(),
]);
