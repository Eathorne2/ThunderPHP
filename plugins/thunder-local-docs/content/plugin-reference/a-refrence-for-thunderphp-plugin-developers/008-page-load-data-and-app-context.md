---
title: "Page-Load Data and App Context"
slug: "page-load-data-and-app-context"
description: "How to share request-lifetime values with set_value, get_value, APP, and APP_SET."
published: true
order: 8
source_id: 231
keywords: ["page-load", "data", "app", "context", "share", "request-lifetime", "values", "set_value", "get_value", "app_set", "plugin", "reference", "refrence", "thunderphp", "developers"]
---

ThunderPHP includes helpers for storing data for the current request only.

**set_value() and get_value()**

```php
set_value([
    'plugin_route' => 'blog',
    'admin_route'  => 'admin',
    'tables'       => [
        'posts_table' => 'bb_posts'
    ]
]);
```

Later:

```php
$vars = get_value();
$plugin_route = get_value('plugin_route');
```

These values exist only during the current page load. They do not persist across redirects.

**APP() and APP_SET()**

`APP()` returns app-wide information for the current page load, such as context, loaded plugins, permissions, roles, and compatibility information.

`APP_SET()` lets you add page-load app data without storing it in session state.

**When To Use Which**

- use `set_value()` for plugin-local request data
- use `APP_SET()` for broader app context during the current request
- do not rely on either one after redirects
