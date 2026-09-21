---
title: "Set Plugin Context Values"
slug: "set-plugin-context-values"
description: "Store route names, table names, and shared plugin settings for the current request."
published: true
order: 4
source_id: 243
keywords: ["set", "plugin", "context", "values", "store", "route", "names", "table", "shared", "settings", "current", "request", "dev", "tutorials", "build", "first"]
---

Near the top of `plugin.php`, store shared values needed throughout the plugin.

```php
set_value([
    'plugin_route' => 'blog',
    'admin_route'  => 'admin',
    'tables'       => [
        'posts_table' => 'basic_blog_posts'
    ]
]);
```

This keeps paths and table names consistent across controllers, models, and views during the current page load. Remember that these values do not survive redirects.
