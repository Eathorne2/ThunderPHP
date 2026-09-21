---
title: "Write Hook-Driven Controllers"
slug: "write-hook-driven-controllers"
description: "Register controller hooks and move heavy logic into included files when needed."
published: true
order: 6
source_id: 245
keywords: ["write", "hook-driven", "controllers", "register", "controller", "hooks", "move", "heavy", "logic", "included", "files", "needed", "plugin", "dev", "tutorials", "build", "first"]
---

ThunderPHP controllers are execution blocks, not necessarily MVC controller classes.

In `plugin.php`, register route-scoped controller logic:

```php
add_action('controller', function($data){
    include plugin_path('controllers/frontend/blog-index.php');
}, 10, 'blog.index');

add_action('controller', function($data){
    include plugin_path('controllers/frontend/blog-view.php');
}, 10, 'blog.view');
```

For admin routes:

```php
add_action('controller', function($data){
    include plugin_path('controllers/admin/blog-admin.php');
}, 10, 'blog.admin.index');
```

This keeps `plugin.php` readable while staying within ThunderPHP's hook-driven lifecycle.
