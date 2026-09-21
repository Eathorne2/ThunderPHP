---
title: "Routes and Named Route Thinking"
slug: "routes-and-named-route-thinking"
description: "Why named routes are preferred and how they make plugins cleaner."
published: true
order: 4
source_id: 266
keywords: ["routes", "named", "route", "thinking", "why", "preferred", "make", "plugins", "cleaner", "getting", "started", "matter"]
---

ThunderPHP supports coarse plugin loading with `routes.on` and `routes.off`, but the preferred route system is the named route list in `routes.routes`.

Example:

```php
{
    "routes": {
        "on": ["blog", "admin"],
        "off": [],
        "routes":[
            {
                "method":"GET",
                "pattern":"/blog",
                "name":"blog.index"
            },
            {
                "method":"GET",
                "pattern":"/blog/{slug}",
                "name":"blog.view"
            }
        ]
    }
}
```

**Why Named Routes Matter**

They let you scope logic directly to a route:

```php
add_action('controller', function($data){
    include plugin_path('controllers/frontend/index.php');
}, 10, 'blog.index');
```

They also make route checks easier:

```php
if(is_route('blog.view'))
{
    $slug = get_param('slug');
}
```

This approach scales much better than relying only on the first URL segment.
