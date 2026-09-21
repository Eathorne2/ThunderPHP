---
title: "Admin Conventions and Cross-Plugin Hooks"
slug: "admin-conventions-and-cross-plugin-hooks"
description: "How admin content and sidebar links are typically injected."
published: true
order: 15
source_id: 238
keywords: ["admin", "conventions", "cross-plugin", "hooks", "content", "sidebar", "links", "typically", "injected", "plugin", "reference", "refrence", "thunderphp", "developers"]
---

Unless a project defines otherwise, admin pages are assumed to live under `page() === 'admin'`.

In many ThunderPHP setups, the expected admin content hook is:

```php
admin_main_content
```

And the typical sidebar link injection filter is:

```php
admin_before_links
```

Example:

```php
add_filter('admin_before_links', function($links){
    $vars = get_value();

    $obj = (object)[];
    $obj->title = 'User Access';
    $obj->link = ROOT . '/'.$vars['admin_route'].'/'.$vars['plugin_route'];
    $obj->icon = 'fa-solid fa-file-lines';
    $obj->parent = 0;
    $links[] = $obj;

    return $links;
});
```

This pattern is useful because admin systems are themselves be plugins, and feature plugins can integrate into them through standard hook contracts instead of hard coupling.
