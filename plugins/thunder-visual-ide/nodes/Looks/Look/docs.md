# Look

Defines an editable `look.json`, owns the shared form theme, and groups View and asset files structurally.

At least one Look node is required for compilation. Connect Views and Reusable Views to the Look that should contain them. When a View has no explicit Look connection, the compiler falls back to the first Look node.

## Shared form theme

Use **Choose Form Theme…** in the Look inspector to select one component theme, palette, and color-token set for every connected View.

The palette selector includes both palettes bundled with the selected form design and the reusable color presets from the HTML Snippet Browser. Reusable presets can stay active while switching form designs, allowing the same colors to be compared across different form appearances.

The compiler creates `frontend/theme-colors.php` inside the Look by default. It also registers:

```php
add_action('before_view', function (): void {
    $themeFile = current_look('frontend/theme-colors.php');
    if (is_file($themeFile)) {
        require_once $themeFile;
    }
}, 100);
```

The file path, hook name, priority, and whether the hook is generated are editable on the Look node. `require_once` prevents the shared theme output from being repeated when several Views render during the same request.
