---
title: "Hooks, Actions, and Filters"
slug: "hooks-actions-and-filters"
description: "How plugins register behavior and how route-scoped hooks work."
published: true
order: 5
source_id: 228
keywords: ["hooks", "actions", "filters", "plugins", "register", "behavior", "route-scoped", "work", "plugin", "reference", "refrence", "thunderphp", "developers", "rules", "means", "practice"]
---

ThunderPHP plugins interact with the core through two global functions:

```php
add_action('hook_name', function($data) {}, int $priority = 10, string $route = ''): void
add_filter('hook_name', function($data) { return $data; }, int $priority = 10, string $route = ''): void
```

**Rules**

- actions do not need to return anything
- filters must return the modified data
- lower priority numbers run first
- the optional `route` parameter limits the callback to a named route

**What This Means In Practice**

Instead of directly wiring features together, plugins expose and consume behavior through hook points.

Example filter:

```php
add_filter('permissions', function($permissions){
    $permissions[] = 'view posts';
    $permissions[] = 'add post';
    return $permissions;
});
```

Example action:

```php
add_action('controller', function($data){
    // process request
}, 10, 'post.view');
```

This makes plugins easier to turn on, turn off, and reuse without editing many unrelated files.
