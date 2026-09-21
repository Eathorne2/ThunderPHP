---
title: "Hook System: Actions and Filters"
slug: "hook-system-actions-and-filters"
description: "Reference for registering callbacks with `add_action()` and `add_filter()` and executing them with `do_action()` and `do_filter()`."
published: true
order: 7
source_id: 123
keywords: ["hook", "system", "actions", "filters", "reference", "registering", "callbacks", "add_action", "add_filter", "executing", "them", "do_action", "do_filter", "functions", "thunderphp", "list", "covered", "vs", "string", "mixed", "func", "int", "priority", "route"]
---

**Functions covered**

- `add_action()`
- `do_action()`
- `add_filter()`
- `do_filter()`

The hook system is the core of plugin communication in ThunderPHP.

**Actions vs filters**

- Actions run code.
- Filters receive data, modify it, and return it.

**`add_action(string $hook, mixed $func, int $priority = 10, string $route = '')`**

Registers a callback to run when the named action hook is fired.

```php
add_action('controller', function() {
    // controller logic
});
```

You can also bind it to a named route.

```php
add_action('controller', function() {
    // only on this route
}, 10, 'post.edit');
```

**`do_action(string $hook, array $data = [])`**

Executes all callbacks registered for the named action hook.

```php
do_action('before_view');
do_action('view');
do_action('after_view');
```

Callbacks are executed in ascending priority order. Lower numbers run earlier.

**`add_filter(string $hook, mixed $func, int $priority = 10, string $route = '')`**

Registers a callback that receives and returns data.

```php
add_filter('user_roles', function($roles) {
    $roles[] = 'editor';
    return $roles;
});
```

**`do_filter(string $hook, mixed $data = '')`**

Runs all callbacks for the named filter hook and returns the final modified data.

```php
$data = do_filter('my_hook', $data);
```

**Priority behavior**

If a priority is already used, the function internally increments until it finds an empty slot. That means every callback is preserved.

**Context behavior**

When callbacks run, ThunderPHP temporarily switches app context to the plugin that registered the callback, then restores the previous context afterward. This is what lets helpers like `plugin_id()` and `current_look()` continue to work correctly inside hooks.

**Recipe: add admin menu links**

```php
add_filter('basic-admin_before_admin_links', function($links) {
    $vars = get_value();

    $obj = (object)[];
    $obj->title  = 'Blog';
    $obj->link   = ROOT . '/' . $vars['admin_route'] . '/' . $vars['plugin_route'];
    $obj->icon   = 'fa-solid fa-file-lines';
    $obj->parent = 0;

    $links[] = $obj;
    return $links;
});
```

**Recipe: route-specific view callback**

```php
add_action('view', function() {
    include current_look('frontend/post-view.php');
}, 10, 'post.view');
```

**Common mistake**

A filter must return data.

```php
add_filter('user_roles', function($roles) {
    $roles[] = 'admin';
    return $roles;
});
```
