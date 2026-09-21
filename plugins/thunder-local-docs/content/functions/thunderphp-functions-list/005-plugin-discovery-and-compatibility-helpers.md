---
title: "Plugin Discovery and Compatibility Helpers"
slug: "plugin-discovery-and-compatibility-helpers"
description: "Functions for checking whether plugins exist and for inspecting missing or outdated dependencies."
published: true
order: 5
source_id: 121
keywords: ["plugin", "discovery", "compatibility", "helpers", "functions", "checking", "whether", "plugins", "exist", "inspecting", "missing", "outdated", "dependencies", "thunderphp", "list", "covered", "plugin_exists", "string", "plugin_id", "missing_plugins", "outdated_plugins", "show_plugins", "recipe", "warn"]
---

**Functions covered**

- `plugin_exists()`
- `missing_plugins()`
- `outdated_plugins()`
- `show_plugins()`

**`plugin_exists(string $plugin_id)`**

Checks whether an active plugin exists by its plugin id.

It returns either the plugin's `config.json` object or `false`.

```php
$plugin = plugin_exists('basic-admin');

if($plugin) {
    echo $plugin->name;
}
```

This is useful when integrating with optional dependencies.

**`missing_plugins()`**

Returns an array describing plugins requested as dependencies but not currently available.

```php
$missing = missing_plugins();
```

This is useful for diagnostics, installer pages, or admin warnings.

**`outdated_plugins()`**

Returns an array of plugins that are incompatible with the current core version.

```php
$old = outdated_plugins();
```

**`show_plugins()`**

Displays loaded plugin names using the debugging helper `dd()`.

```php
show_plugins();
```

Because it prints directly, it is best used only during development.

**Recipe: warn when an optional dependency is not installed**

```php
$plugin = plugin_exists('basic-admin');

if(!$plugin) {
    message('fail', 'Basic Admin plugin is required for this feature');
}
```
