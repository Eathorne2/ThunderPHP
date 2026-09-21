---
title: "Add Admin Integration"
slug: "add-admin-integration"
description: "Hook the plugin into an admin plugin and render admin content the expected way."
published: true
order: 9
source_id: 248
keywords: ["add", "admin", "integration", "hook", "plugin", "render", "content", "expected", "way", "dev", "tutorials", "build", "first"]
---

Many ThunderPHP projects use a plugin-based admin system.

Add a sidebar link:

```php
add_filter('admin_before_links', function($links){
    $vars = get_value();

    $obj = (object)[];
    $obj->title = 'Blog';
    $obj->link = ROOT . '/'.$vars['admin_route'].'/'.$vars['plugin_route'];
    $obj->icon = 'fa-solid fa-newspaper';
    $obj->parent = 0;
    $links[] = $obj;

    return $links;
});
```

Render the admin page:

```php
add_action('admin_main_content', function($data){
    include current_look('admin/index.php');
}, 10, 'blog.admin.index');
```

This is a good example of how ThunderPHP plugins compose with each other through hooks instead of depending on a fixed admin panel.
