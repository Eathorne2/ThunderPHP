---
title: "Execution Lifecycle and 404 Behavior"
slug: "execution-lifecycle-and-404-behavior"
description: "The main execution order, intended 404 safeguard, and how plugin authors can override it."
published: true
order: 6
source_id: 229
keywords: ["execution", "lifecycle", "behavior", "main", "order", "intended", "safeguard", "plugin", "authors", "override", "reference", "refrence", "thunderphp", "developers", "controllers", "blocks", "hook", "views"]
---

The core hook order is:

1. `roles`
2. `permissions`
3. `before_controller`
4. `controller`
5. `after_controller`
6. `before_view`
7. `view`
8. `after_view`

This is the normal request lifecycle ThunderPHP expects plugins to work within.

**Controllers Are Execution Blocks**

In ThunderPHP, controllers are not necessarily MVC controller classes.

They are usually hook-driven execution blocks, often plain procedural code. For larger plugins, it is perfectly fine to include controller files from `controllers/admin` or `controllers/frontend` to keep `plugin.php` clean.

**404 Safeguard**

If no `view` hook is defined or run for the matched route, the framework treats that as a missing view and loads the 404 flow. This is an intended safeguard.

For routes that intentionally return JSON, files, or other direct output, it is normal to end the request with `die()` or `exit()` to avoid falling into the 404 behavior.

**Override Hook**

The newer framework behavior adds:

```php
do_action('before_404_redirect');
```

This gives plugin authors an easier interception point before the redirect or fallback 404 handling occurs.

**Plugin 404 Views**

If no plugin provides a custom 404 view, the framework falls back to the default 404 view. If a plugin defines its own 404 rendering for that route, the plugin version is used instead.
