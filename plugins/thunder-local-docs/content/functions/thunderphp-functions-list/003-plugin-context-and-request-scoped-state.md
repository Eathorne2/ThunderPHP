---
title: "Plugin Context and Request-Scoped State"
slug: "plugin-context-and-request-scoped-state"
description: "Helpers for reading the current plugin context and passing data around during the current request."
published: true
order: 3
source_id: 119
keywords: ["plugin", "context", "request-scoped", "state", "helpers", "reading", "current", "passing", "data", "around", "during", "request", "functions", "thunderphp", "list", "covered", "set_value", "string", "array", "key", "mixed", "value", "important", "behavior"]
---

**Functions covered**

- `set_value()`
- `get_value()`
- `plugin_id()`
- `get_context()`

**`set_value(string|array $key, mixed $value = '')`**

Stores data in a plugin-isolated request-scoped store. This behaves like a plugin-local global variable for the current page load.

```php
set_value('title', 'Blog Posts');
set_value('admin_route', 'admin');
```

You can also set many values at once.

```php
set_value([
    'plugin_route' => 'blog',
    'admin_route'  => 'admin',
    'tables'       => [
        'posts' => 'bb_posts',
        'tags'  => 'bb_tags',
    ],
]);
```

**Important behavior**

Values are isolated per plugin. That prevents one plugin from overwriting another plugin's temporary values.

**`get_value(string $key = '')`**

Retrieves values previously stored with `set_value()`.

```php
echo get_value('title');
```

If called with no key, it returns all values for the current plugin.

```php
$vars = get_value();
```

**`plugin_id()`**

Returns the current plugin id from its `config.json`.

```php
echo plugin_id();
```

This is useful when building dynamic hook names.

```php
$hook = plugin_id() . '_main_content';
```

**`get_context()`**

Returns the full current plugin context object.

```php
$ctx = get_context();
echo $ctx->id ?? '';
echo $ctx->look ?? 'main';
echo $ctx->path ?? '';
```

Typical values in context include plugin id, filesystem path, HTTP path, and selected look.

**Recipe: initialize plugin runtime values**

```php
set_value([
    'plugin_route' => 'blog',
    'admin_route'  => 'admin',
    'posts_table'  => 'bb_posts',
]);
```

**Recipe: use context to inspect the active look**

```php
$ctx = get_context();
$look = $ctx->look ?? 'main';
```
