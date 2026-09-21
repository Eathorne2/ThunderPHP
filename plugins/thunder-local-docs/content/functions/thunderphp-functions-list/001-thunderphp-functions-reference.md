---
title: "ThunderPHP Functions Reference"
slug: "thunderphp-functions-reference"
description: "Overview of the public helper functions available to plugins and how they fit into the ThunderPHP plugin lifecycle."
published: true
order: 1
source_id: 117
keywords: ["thunderphp", "functions", "reference", "overview", "public", "helper", "available", "plugins", "fit", "plugin", "lifecycle", "list", "purpose", "read", "common", "expectations", "quick", "example"]
---

**Purpose**

This reference documents the public helper functions exposed by ThunderPHP for plugin development. It is based on the provided `functions.php` file and the plugin architecture notes.

These helpers mainly cover:

- URL and path handling
- plugin context and shared state
- hooks and routing
- roles and permissions
- forms and CSRF
- messages, dates, images, and debugging

**How to read this reference**

- Use these functions inside your plugin files, views, controllers, and hook callbacks.
- Functions that depend on plugin context such as `plugin_id()`, `plugin_path()`, `current_look()`, and `set_value()` are aware of the current plugin.
- Functions like `APP()` operate at application scope and are shared between plugins.

**Common expectations**

- Plugin metadata comes from `config.json`.
- The currently executing plugin is tracked through the app context.
- Route names come from `config.json` route definitions.
- Hook callbacks are usually registered in `plugin.php`.

**Quick example**

```php
set_value([
    'plugin_route' => 'blog',
    'admin_route'  => 'admin',
]);

add_action('controller', function() {
    if(page() == 'admin' && user_can('edit posts')) {
        // controller logic
    }
});
```
