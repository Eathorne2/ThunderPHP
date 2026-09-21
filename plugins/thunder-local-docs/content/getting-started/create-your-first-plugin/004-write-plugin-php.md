---
title: "Write plugin.php"
slug: "write-plugin-php"
description: "Register the plugin hooks and keep the file focused on setup and route wiring."
published: true
order: 4
source_id: 257
keywords: ["write", "plugin", "php", "register", "hooks", "keep", "file", "focused", "setup", "route", "wiring", "getting", "started", "create", "first", "important", "note"]
---

In `plugin.php`, set some request values and register the hooks:

```php
<?php

namespace HelloWorld;

defined('ROOTPATH') or die("Direct script access denied");

set_value([
    'plugin_route' => 'hello',
    'admin_route' => 'admin',
]);

add_action('controller', function($data){
    if(is_route('hello.index'))
    {
        // frontend logic if needed
    }
}, 10, 'hello.index');

add_action('view', function($data){
    include current_look('frontend/index.php');
}, 10, 'hello.index');

add_filter('admin_before_links', function($links){
    $vars = get_value();

    $obj = (object)[];
    $obj->title = 'Hello World';
    $obj->link = ROOT . '/'.$vars['admin_route'].'/'.$vars['plugin_route'];
    $obj->icon = 'fa-solid fa-house';
    $obj->parent = 0;
    $links[] = $obj;

    return $links;
});

add_action('admin_main_content', function($data){
    include current_look('admin/index.php');
}, 10, 'hello.admin');
```

**Important Note**

The standard admin hooks here are:

- `admin_before_links`
- `admin_main_content`

Custom plugin-specific hooks should use the `{plugin_id}_hook_name` style to avoid name collisions with other plugins.
